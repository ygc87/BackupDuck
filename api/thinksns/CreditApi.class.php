<?php
/**
 * 
 * @author jason
 *
 */
class CreditApi extends Api
{
    /**
     * 获取当前用户积分 --using
     *
     * @return int 用户积分
     */
    public function credit_my()
    {
        $credit = model('Credit')->getUserCredit($this->mid);

        return array(
                'score' => $credit ['credit'] ['score'] ['value'],
        );
    }

    /*
     * 积分详情
    */
    public function detail()
    {
        if ($this->data['max_id'] > 0) {
            $where = 'rid<'.intval($this->data['max_id'])." AND uid={$this->mid} AND detail like '%score%'";
        } else {
            $where = "uid={$this->mid} AND detail like '%score%'";
        }
        $count = $this->data['count'] > 0 ? intval($this->data['count']) : 20;
        $page=intval($this->data['page']);
        $limit=($page-1)*$count;
        $creditRecord = D('credit_record')->where($where)->order('rid DESC')->limit($limit.','.$count)->findAll();
        $data = array();
        foreach ($creditRecord as $i => $val) {
            $rs['rid'] = $val['rid'];
            $rs['uid'] = $val['uid'];
            $rs['action'] = $val['action'];
            $rs['ctime'] = $val['ctime'];
            $detail = @json_decode($val['detail'], true);
            $rs['score'] = (string) $detail['score'];
            $rs['score'] = trim($rs['score'], '+');
            $rs['score'] = $rs['score'] > 0 ? "+{$rs['score']}" : "{$rs['score']}";
            $data[] = $rs;
        }

        return $data;
    }

    /*
     * 积分转账
    */
    public function transfer()
    {
        $data['fromUid'] = $this->mid;
        $data['toUid'] = $this->data['to_uid'];
        $data['num'] = $this->data['num'];
        $data['desc'] = t($this->data['desc']);
        if ($data['toUid'] && $data['num'] > 0) {
            $result = model('Credit')->startTransfer($data);
        } else {
            $result = false;
        }

        return array(
            'status' => $result ? 1 : 0,
            'mesage' => $result ? '积分转账成功！' : '积分转账失败',
        );
    }

    /*
     * 积分规则
    */
    public function rule()
    {
        $list = M('credit_setting')->order('type ASC')->findAll();
        $creditType = M('credit_type')->order('id ASC')->findAll();
        $creditType = array_column($creditType ?: array(), 'alias', 'name');
        foreach ($list as &$rs) {
            //if ($rs['score']){
            //    $rs['score'] = $rs['score'] > 0 ? "+{$rs['score']}" : "{$rs['score']}";
            //}else{
           //     $rs['score']=$rs['info'];
            //}
            $rs['score']=$rs['info'];
            //$rs['experience'] = $rs['experience'] > 0 ? "+{$rs['experience']}" : "{$rs['experience']}";
            $rs['score_alias'] = (string) $creditType['score'];
            //$rs['experience_alias'] = (string) $creditType['experience'];
            unset($rs['experience'],$rs['id'], $rs['type'], $rs['cycle'], $rs['cycle_times'], $rs['des'], $rs['info']);
        }

        return $list;
    }

    /*
     * 设置用户积分
    */
    public function setCredit()
    {
        $action = @(string) $this->data['name'];
        @model('Credit')->setUserCredit($this->mid, $action);

        return 1;
    }

    /*
     * 充值，创建一个订单
    */
    public function createCharge()
    {
        $appid = 10345; //商户appid
        $appkey = "7c30278abbb63acfa9dff4061aeb3a46";//
        $price = floatval($this->data['money']);
        if ($price < 1) {
            return array('status' => 0, 'mesage' => '充值金额不正确');
        }
        $shop_name=$this->data['shop_name'];
        if (!$shop_name){
            return array('status'=>0,'message'=>'商品名称不能为空');
        }
        $type = $this->data['type'];
        $types = array('alipay', 'weixin');
        if (!in_array($type,$types)) {
            return array('status' => 0, 'mesage' => '充值方式不支持');
        }

        $charge_one= D('credit_charge')->where('uid='.$this->mid.' and charge_value = 1')->find();
        if ($charge_one&&$price==1){
            return array('status' => 0, 'mesage' => '一元充值仅限首次');
        }
        //$chargeConfigs = model('Xdata')->get('admin_Config:charge');
        //if (!in_array($type, $chargeConfigs['charge_platform'])) {
          //  return array('status' => 0, 'mesage' => '充值方式不支持');
        //}

        $serial_number="{$appid}_". time() . mt_rand(1000, 9999);
        $data ['serial_number'] = $serial_number;
        $data ['charge_type'] = 1;
        $data ['charge_value'] = $price;
        $data ['uid'] = $this->mid;
        $data ['ctime'] = time();
        $data ['status'] = 0;
        //$data ['charge_sroce'] = intval($price * abs(intval($chargeConfigs['charge_ratio'])));
        $data ['charge_order'] = '';
        $result = D('credit_charge')->add($data);

        if ($result) {
            $notify_url='http://www.djk777.com/dunpai_notify.php';

            require TS_ROOT.'/yunpay.php';
            $res=addorder($appid,$appkey,$serial_number,intval($price*100),$notify_url,'',$this->mid);
            if ($res['code']){
                return array('status'=>0,'message'=>$res['errMsg']);
            }else{

                return array('status'=>1,'message'=>str_replace('\\','',$res['object']['wxPayWay']),'order_id'=>$serial_number);
            }

        } else {
            $res = array();
            $res ['status'] = 0;
            $res ['message'] = '充值创建失败';

            return $res;
        }
    }

    public function saveCharge()
    {
        $number = (string) $this->data['serial_number'];
        $status = intval($this->data['status']);
        $sign = (string) $this->data['sign'];
        $verify = md5($number.'&'.$status.'&'.md5(C('SECURE_CODE')));
        if ($number && $sign && ($status == 1 || $status == 2) && $sign == $verify) {
            if ($status == 1) {
                if (model('Credit')->charge_success(t($number))) {
                    return array('status' => 1, 'mesage' => '保存成功');
                }
            } else {
                $map = array(
                    'uid' => $this->mid,
                    'serial_number' => t($number),
                    'status' => 0, // 这个条件不能删，删了就有充值漏洞
                );
                if (D('credit_charge')->where($map)->setField('status', 2)) {
                    return array('status' => 1, 'mesage' => '保存成功');
                }
            }

            return array('status' => 0, 'mesage' => '保存失败');
        } else {
            return array('status' => 0, 'mesage' => '参数错误');
        }
    }

    // ?? 啥用的 -> 谢伟20150925
    public function save_charge()
    {


        //修改成自己的key
        $key = "7c30278abbb63acfa9dff4061aeb3a46";

        //获取参数
        $code = $_REQUEST['code'];  //状态 0 成功
        $remark = $_REQUEST["remark"];  // 自定义参数
        $merId = $_REQUEST["merId"];    //商户编号
        $orderId = $_REQUEST["orderId"];    //商户订单号
        $tradeId = $_REQUEST["tradeId"];    //第三方订单号
        $payWay = $_REQUEST["payWay"];    //支付类型
        $money = $_REQUEST["money"];    //价格 分
        $time = $_REQUEST["time"];    //计费时间
        $sign = $_REQUEST["sign"];    //签名串
        //2 签名校验
        $my_str = "code" . $code . "merId" . $merId . "money" . $money . "orderId" . $orderId . "payWay" . $payWay .
            "remark" . $remark . "time" . $time . "tradeId" . $tradeId . $key;

        $my_sign = strtoupper(md5($my_str));


        $uid=$remark;


        if ($sign != $my_sign) {
            error_log('签名不正确',3,'./log/credit_charge.log');
            return 'fail';
        }

        $is_charged= D('credit_charge')->where($map = array('uid' =>intval($uid), 'serial_number' => $orderId, 'status' => 0))->find();
        if ($is_charged){
            $result=D('pay_price')->where('money='.intval(intval($money)/100))->field('id,money,credit,send_credit')->find();

            $charge_score=intval($result['credit'])+intval($result['send_credit']);
            error_log('充值积分:'.$charge_score,3,'./log/credit_charge.log');

            $des ['content'] = '充值了'.$charge_score.'积分'.'赠送'.$result['send_credit'].'积分';

            model('Credit')->setUserCredit(intval($uid), array(
                'name' => '积分充值'.intval($money/100),
                'score' => $charge_score,
                'des'=>$des,
            ), 1, $des);

            model('Credit')->cleanCache(intval($uid));
            model('User')->cleanCache(intval($uid));

            $map = array(
                'uid' =>intval($uid),
                'serial_number' => $orderId,
                'status' => 0, // 这个条件不能删，删了就有充值漏洞
            );

            D('credit_charge')->where($map)->save(array('charge_sroce'=>$charge_score,'status'=>1));
            error_log('充值成功',3,'./log/credit_charge.log');
            echo'success';
            exit;
        }else{
            echo 'have charged';
            exit;
        }

    }
    public function get_charge()
    {
        $arr = array(
                array(
                        'value' => 0.01,
                        'score' => 5,
                ),
                array(
                        'value' => 10,
                        'score' => 100,
                ),
                array(
                        'value' => 20,
                        'score' => 250,
                ),
                array(
                        'value' => 50,
                        'score' => 650,
                ),
                array(
                        'value' => 100,
                        'score' => 1200,
                ),
        );

        return $arr;
    }

    public function get_pay_list(){
        $result=D('pay_price')->where('money<>8.8')->field('id,money,credit,send_credit')->findAll();

        $charge_one= D('credit_charge')->where('uid='.$this->mid.' and charge_value = 1')->find();
        if ($charge_one){
           foreach ($result as $k=>$v){
               if ($v['money']==1){
                   $result=array_slice($result,$k,1);
               }

           }


        }else{
            foreach ($result as $k=>$v){
                if ($v['money']==1){
                    $result[$k]['send_credit']='仅限首次';
                }

            }
        }
        return array('pay_type'=>array('weixin'=>1,'alipay'=>0),'pay_num'=>$result,'customer_service'=>'http://www.djk777.com/pic/customer_service.jpg');

    }

    public function get_pay_list_new(){
        $result=D('pay_price')->where('1=1')->field('id,money,credit,send_credit')->findAll();

        $charge_one= D('credit_charge')->where('uid='.$this->mid.' and charge_value = 1 and status=1')->find();
        if ($charge_one){
            foreach ($result as $k=>$v){
                if ($v['money']==1){
                    $result=array_slice($result,$k,1);
                    continue;
                }
                if ($v['money']==8.8){
                    $result[$k]['money_value']=$v['money'];
                    $result[$k]['money']=$v['money'].'元';
                    $result[$k]['credit']='包月免积分，仅限（6:00-18:00）';
                    $result[$k]['send_credit']='';

                    continue;
                }
                $result[$k]['money_value']=$v['money'];
                $result[$k]['send_credit']='赠送'.$v['send_credit'].'积分';
                $result[$k]['money']=$v['money'].'元';
                $result[$k]['credit']=$v['credit'].'积分';


            }


        }else{
            foreach ($result as $k=>$v){
                if ($v['money']==1){
                    $result[$k]['send_credit']='仅限首次';
                    continue;
                }
                if ($v['money']==8.8){
                    $result[$k]['money_value']=$v['money'];
                    $result[$k]['money']=$v['money'].'元';
                    $result[$k]['credit']='包月时段免积分观看,仅限(早上6:00-晚上18:00)';
                    $result[$k]['send_credit']='';
                    continue;
                }
                $result[$k]['money_value']=$v['money'];
                $result[$k]['send_credit']='赠送'.$v['send_credit'].'积分';
                $result[$k]['money']=$v['money'].'元';
                $result[$k]['credit']=$v['credit'].'积分';



            }
        }


        return array('pay_type'=>array('weixin'=>1,'alipay'=>0),'pay_num'=>$result,'customer_service'=>'http://www.djk777.com/pic/customer_service.jpg');

    }


    public function query_order(){


        $order_id=$this->data['order_id'];


        $map = array(
            'uid' =>intval($this->mid),
            'serial_number' => $order_id,
            'status' => 1, // 这个条件不能删，删了就有充值漏洞
        );

        $order=D('credit_charge')->where($map)->find();
        if ($order){
            if (!$order['msg']){
                $order['message']='订单未支付';
            }
            $order['message']=$order['msg'];
            unset($order['msg']);
            return $order;
        }else{
            return array('status'=>0,'message'=>'交易未完成');
        }

    }
}
