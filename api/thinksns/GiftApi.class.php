<?php

include SITE_PATH.'/apps/gift/Common/common.php';
//礼物接口
class GiftApi extends Api
{
    //查看礼物
    public function showAll()
    {
        $result = M('Gift')->where(array('status' => '1'))->findAll();
        foreach ($result as $k => $v) {
            unset($result[$k]['categoryId']);
        }

        return $result;
    }
    //查看礼物
    public function show()
    {
        if (!intval($this->data['id'])) {
            return 0;
        }
        $result = M('Gift')->where(array('status' => '1', 'id' => intval($this->data['id'])))->find();
        unset($result['categoryId']);

        return $result;
    }
    //赠送礼物
    public function sendGift()
    {
        $toUserId = trim(t($this->data['uids']), ',');
        //获取附加信息
        $sendInfo['sendInfo'] = t($this->data['sendInfo']);
        //获取发送方式
        $sendInfo['sendWay'] = ($this->data['sendWay']) ? intval($this->data['sendWay']) : 1;
        $giftId = intval($this->data['giftId']);
        $giftInfo = M('Gift')->where('id='.$giftId)->find();
        if (empty($toUserId) || empty($giftId) || empty($this->mid)) {
            return 0;
        } else {
            $usergift = D('UserGift', 'gift');
            $usergift->setGift(D('Gift', 'gift'));
            $result = $usergift->sendGift($toUserId, $this->mid, $sendInfo, $giftInfo);

            return 1;
        }
    }
    
    /**
     * 列表获取礼物
     *
     * @request int p 页码，默认值是1页
     * @request int cate 分类，值只有1和2，1代表虚拟礼物，2代表实体礼物，不传代表全部
     * @request int num 每页返回的数据条数 默认20条
     * @return array
     * @author Seven Du <lovevipdsw@vip.qq.com>
     **/
    public function getList()
    {
        //list($cate, $num) = Common::getInput(array('cate', 'num'));
        //list($cate, $num) = array(intval($cate), intval($num));
    
        //$where = '`cate` IN (1, 2)';
        //if ($cate >= 1 && $cate <= 2) {
            //$where = '`cate` = '.$cate;
        //}
        //$where .= ' AND `isDel` != 1';
    
        /* # 设置每页返回的数据条数 */
        //$num || $num = 20;
    
        //$data = GiftModel::getInstance()->where($where)->order('`id` DESC')->findPage($num);
        $page=intval($this->page);
        $count=intval($this->count);
        $limit=($page-1)*$count;
        $data = D('gift')->limit($limit.','.$count)->findAll();//
    
        /* # 判断页数是否超出 */
        //if (Common::getInput('page') > $data['totalPages']) {
        //$data['data'] = array();
        //}
        error_log('---- gift get list id = ----'.$data['id'],3,'gift.log');
    
        $gift_list = array();

        foreach ($data as $key => $value) {
           // $value['image'] = getImageUrlByAttachId($value['image']);
            //$value['count'] = LogModel::getInstance()->getUserCount($value['id']);
            //$data['data'][$key] = $value;
            //$gift_list[$key] = $value;
            $data[$key]['image']='http://'.$_SERVER['HTTP_HOST'].$value['image'];
        }
    
        return $data;
        //return $gift_list;
    }

    public function buy_gift(){
        $uid=$this->mid;
        $gift_id=$this->data['gift_id'];
        //$is_payed=D('gift_pay_record')->where('uid='.$uid.' and gift_id='.$gift_id)->count();
        //if($is_payed){
           // return array('status' => 0,'msg' => '您已经购买过，请在已购买中查看');
        //}
        $isvip_info=model('User')->where('uid='.$this->mid)->field('is_vip,vip_endtime')->find();

        $user_score = M('credit_user')->where("uid={$this->mid}")->getField('score');
        $gift=D('gift')->where('id='.$gift_id)->find();
        $spend_score=$user_score-intval($gift['score']);
        if ($spend_score<0){
            return array('status' => -1, 'msg' => '您的积分余额不足，请添加积分后购买！');
        }
        M('credit_user')->where('uid=' . $this->mid)->save(array('score' => $spend_score));
        model('Credit')->cleanCache($this->mid);
        model('User')->cleanCache($this->mid);
        if ($gift['type']=='vip'){
            if($isvip_info['vip_endtime']>time()){
                $vip_endtime=$isvip_info['vip_endtime'];
            }else{
                $vip_endtime=time();
            }
            if (strpos($gift['name'],'1')!==false){
                $vip_endtime+=24*3600;
            }elseif (strpos($gift['name'],'7')!==false){
                $vip_endtime+=24*3600*7;
            }elseif(strpos($gift['name'],'30')!==false){
                $vip_endtime+=24*3600*30;
            }elseif(strpos($gift['name'],'90')!==-false){
                $vip_endtime+=24*3600*90;
            }
            model('User')->where('uid='.$this->mid)->data(array('is_vip'=>1,'vip_endtime'=>$vip_endtime))->save();
        }
        D('gift')->where('id='.$gift_id)->setInc('sale_count');
    //添加视频购买记录
        $map['gift_id'] = $gift_id;
        $map['uid'] = $this->mid;
        $map['ctime'] = time();
        //插入数据，
        $result = D('gift_pay_record')->add($map);
        if ($result){
            //D('gift')->where('id='.$gift_id)->setInc('sale_count');
            //积分变更记录
            $add['uid'] = intval($this->mid);
            $add['action'] = '购买商品';
            $add['des'] = '购买'.$gift['name'];
            $add['change'] = '积分<font color="red">' . $gift['score'] . '</font>';
            $add['ctime'] = time();
            $add['detail'] = '{"score":"-' . $gift['score'] . '"}';
            D('credit_record')->add($add);
            $sale_count=D('gift')->where('id='.$gift_id)->getField('sale_count');
            return array('status' => 1, 'msg' =>'购买成功','count'=>$sale_count);
        }else{
            //购买失败，返回原因
            return array('status' => 0, 'msg' => $result);
        }

    }
    
}
