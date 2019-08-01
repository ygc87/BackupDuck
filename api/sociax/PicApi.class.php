<?php
//图片接口
class PicApi extends Api
{
    //获取图片列表
    public function pic_list()
    {
        $userid= $this->mid;//登录用户的userid
        $video_count=D('pic_object')->count();//套图库中套图总数量

        $id_arr=range(0,$video_count-1);//生成套图Id数组
        shuffle($id_arr);//打乱顺序
        $id_arr=array_slice($id_arr,0,10);//获取前10个
        $list= D('pic_object')->where('id in ('.implode(',',$id_arr).')')->findAll();

        $sourceids=array();//套图资源 id 数组
        foreach ($list as $item) {
            $sourceids[]=$item['id'];
        }


        $insourceids=implode(',',$sourceids);
        //判断用户是否赞过该套图
        $digg_arr= D('pic_object_digg')->field('object_id')->where('uid='.$userid.' and object_id in('.$insourceids.')')->findAll();

        $digg_index_arr=array();
        foreach ($digg_arr as $index=>$item){
            $digg_index_arr[]=$item['object_id'];
        }
        unset($digg_arr);

        //判断用户是否收藏过套图
        $collection_arr=D('collection')->field('source_id')->where('uid='.$userid.' and source_table_name = pic_object and source_id in ('.$insourceids.')')->findAll();
        $collection_index_arr=array();
        foreach ($collection_arr as $index=>$item){
            $collection_index_arr=$item['source_id'];
        }
        unset($collection_arr);
        //判断用户是否已经购买过该视频
        $buy_arr= D('pic_pay_record')->field('object_id')->where('uid='.$userid.' and object_id in ('.$insourceids.')')->findAll();
        $buy_index_arr=array();
        foreach ($buy_arr as $index=>$item){
            $buy_index_arr[]=$item['object_id'];
        }
        unset($buy_arr);
        $userScore = M('credit_user')->where("uid={$this->mid}")->getField('score');


        $pic_result=D('pic')->where('object_id in ('.$insourceids.')')->field('object_id,url')->findAll();
        $pic_urls=array();
        foreach ($pic_result as $index=>$item){
            $pic_urls[$item['object_id']][]=$item['url'];
        }
        if (!empty($list)) {
            foreach ($list as $ak => $av) {
                $object_id = $av['id'];
                
                $data = array(
                    'uid' => $this->mid,
                    'object_id' => $object_id,
                );
                
                //$url_list = array();
                //是否购买过该主题
                if(in_array($object_id,$buy_index_arr)){
                    $list[$ak]['is_payed'] = 1;
                    $url_list = $pic_urls[$object_id];
                }else{
                    $list[$ak]['is_payed'] = 0;
                    $count = 3;
                    $url_list = array_slice($pic_urls[$object_id],0,$count);
                }
                foreach ($url_list as $k=>$v){
                    $url_list[$k]=array('url'=>$v);
                }
                //$pic_list = array();
               // foreach ($url_list as $bk => $bv) {
                //    $pic_list[$bk]= $bv;
                //}
                
                $list[$ak]['images'] = $url_list;
                $list[$ak]['status']=1;
                $list[$ak]['msg']='';
                //判断是否已经赞过该主题
                if(in_array($object_id,$digg_index_arr)){
                    $list[$ak]['is_digg'] = 1;
                }else{
                    $list[$ak]['is_digg'] = 0;
                }
                //判断是否已经收藏过该主题
                if(in_array($object_id,$collection_index_arr)){
                    $list[$ak]['is_collected'] = 1;
                }else{
                    $list[$ak]['is_collected'] = 0;
                }
                

                $list[$ak]['user_score'] = $userScore;
                
            }
        }

        $isvip_info=model('User')->where('uid='.$this->mid)->field('is_vip,vip_endtime')->find();

        if ($isvip_info['is_vip']){
            if ($isvip_info['vip_endtime']<time()){//vip已经过期
                model('User')->where('uid='.$this->mid)->update(array('is_vip'=>0,'vip_endtime'=>0));
            }else{
                $pic_discount=D('vip_type')->where('id=6')->field('pic_discount')->getField('pic_discount');
                $pic_discount=(float)$pic_discount;
                foreach ($list as &$v){
                    $v['charge']=intval($v['charge']*$pic_discount);
                    $v['dialog_title']='观看视频消费'.$v['charge'].'积分';
                }

            }
        }
        return $list;


    }
    
    //获取已收藏图片主题列表
    public function collected_object_list()
    {
        $userid=$this->mid;
        $map['uid'] = $this->mid;
        $map['source_table_name'] = 'pic_object';
        $count=intval($this->count);
        $limit=(intval($this->page)-1)*$count;
        $list = D('collection')->limit($limit.','.$count)->where($map)->order('ctime desc')->findAll();

        $sourceids=array();//套图资源 id 数组
        foreach ($list as $item) {

            $sourceids[]=$item['source_id'];
        }

        $insourceids=implode(',',$sourceids);


        //判断用户是否赞过该套图
        $digg_arr= D('pic_object_digg')->field('object_id')->where('uid='.$userid.' and object_id in('.$insourceids.')')->findAll();

        $digg_index_arr=array();
        foreach ($digg_arr as $index=>$item){
            $digg_index_arr[]=$item['object_id'];
        }
        unset($digg_arr);


        //判断用户是否已经购买过该视频
        $buy_arr= D('pic_pay_record')->field('object_id')->where('uid='.$userid.' and object_id in ('.$insourceids.')')->findAll();
        $buy_index_arr=array();
        foreach ($buy_arr as $index=>$item){
            $buy_index_arr[]=$item['object_id'];
        }
        unset($buy_arr);
        $pic_object_list=D('pic_object')->where('id in('.$insourceids.')')->field('id,title,digg_count,collect,charge,pic_count')->findAll();

        $pic_result=D('pic')->where('object_id in ('.$insourceids.')')->field('object_id,url')->findAll();
        $pic_urls=array();
        foreach ($pic_result as $index=>$item){
            $pic_urls[$item['object_id']][]=$item['url'];
        }
        $userScore = M('credit_user')->where("uid={$this->mid}")->getField('score');//find();
        if (!empty($pic_object_list)) {
            foreach ($pic_object_list as $ak => $av) {
                $object_id = $av['id'];

                foreach ($list as $i=>$val){
                    if ($object_id==$val['source_id']){
                        $video_list[$ak]['ctime']=$list[$i]['ctime'];
                        break;
                    }
                }
                
                $url_list = array();
                //是否购买过该主题
                if(in_array($object_id,$buy_index_arr)){
                    $pic_object_list[$ak]['is_payed'] = 1;
                    $url_list = $pic_urls[$object_id];
                }else{
                    $pic_object_list[$ak]['is_payed'] = 0;
                    $count = 3;
                    $url_list = array_slice($pic_urls[$object_id],0,$count);
                }
                
                $pic_list = array();
                foreach ($url_list as $bk => $bv) {
                    $pic_list[$bk]= array('url'=>$bv);
                }

                $pic_object_list[$ak]['images'] = $pic_list;
                

                
                //判断是否已经赞过该主题
                if(in_array($object_id,$digg_index_arr)){
                    $pic_object_list[$ak]['is_digg'] = 1;
                }else{
                    $pic_object_list[$ak]['is_digg'] = 0;
                }

                $pic_object_list[$ak]['is_collected'] = 1;



                $pic_object_list[$ak]['user_score'] = $userScore;
            }
        }
        //按收藏时间排序
        usort($pic_object_list,function ($a,$b){
            if ($a['ctime']==$b['ctime'])return 0;
            return $b['ctime']-$a['ctime'];
        });
        $next_page=$this->page+1;
        return array('next_page'=>$next_page,'object_list'=>$pic_object_list);
    }
    
    //获取已购图片主题列表
    public function buy_object_list()
    {
        $page=intval($this->page);
        $count=intval($this->count);
        $limit=($page-1)*$count;
        $list = D('pic_pay_record')->limit($limit.','.$count)->where('uid='.$this->mid)->order('ctime desc')->findAll();
        $userid= $this->mid;//登录用户的userid


        $sourceids=array();//套图资源 id 数组
        foreach ($list as $item) {

            $sourceids[]=$item['object_id'];
        }

        $insourceids=implode(',',$sourceids);
        //判断用户是否赞过该套图
        $digg_arr= D('pic_object_digg')->field('object_id')->where('uid='.$userid.' and object_id in('.$insourceids.')')->findAll();

        $digg_index_arr=array();
        foreach ($digg_arr as $index=>$item){
            $digg_index_arr[]=$item['object_id'];
        }
        unset($digg_arr);

        //判断用户是否收藏过套图
        $collection_arr=D('collection')->field('source_id')->where('uid='.$userid.' and source_table_name = pic_object and source_id in ('.$insourceids.')')->findAll();
        $collection_index_arr=array();
        foreach ($collection_arr as $index=>$item){
            $collection_index_arr=$item['source_id'];
        }
        unset($collection_arr);

        $userScore = M('credit_user')->where("uid={$this->mid}")->getField('score');


        $pic_result=D('pic')->where('object_id in ('.$insourceids.')')->field('object_id,url')->findAll();
        $pic_urls=array();
        foreach ($pic_result as $index=>$item){
            $pic_urls[$item['object_id']][]=$item['url'];
        }

        $picobject_list = D('pic_object')->where('id in('.$insourceids.')')->field('id,title,digg_count,collect,charge,pic_count')->findAll();

        $picobject_list_order=array();
        foreach ($sourceids as $i=>$k){
            foreach ($picobject_list as $index=>$value){

                if ($value['id']==$k){
                    $picobject_list_order[]=$value;
                }
            }
        }
        $picobject_list=$picobject_list_order;
        if (!empty($picobject_list)) {
            foreach ($picobject_list as $ak => $av) {
                $object_id = $av['id'];
                $url_list = $pic_urls[$object_id];
                $pic_list = array();
                foreach ($url_list as $bk => $bv) {
                    $pic_list[$bk]= array('url'=>$bv);
                }

                $picobject_list[$ak]['images'] = $pic_list;


                $picobject_list[$ak]['is_payed'] = 1;

                
                //判断是否已经赞过该主题
                if(in_array($object_id,$digg_index_arr)){
                    $picobject_list[$ak]['is_digg'] = 1;
                }else{
                    $picobject_list[$ak]['is_digg'] = 0;
                }
                

                
                if(in_array($object_id,$collection_index_arr)){
                    $picobject_list[$ak]['is_collected'] = 1;
                }else{
                    $picobject_list[$ak]['is_collected'] = 0;
                }


                $picobject_list[$ak]['user_score'] = $userScore;
            }
        }
        $next_page=$page+1;

        return array('next_page'=>$next_page,'object_list'=>$picobject_list);
    }
    
    //购买图片
    public function buy()
    {
        //1.判断用户积分是否大于商品价格
        //2.购买成功后需要加入购买记录
        //3.付费图片和不付费图片要标记出来,购买成功只请求不付费图片
    
        //list($id, $uid, $num, $addres, $say, $type) = Common::getInput(array('id', 'uid', 'num', 'addres', 'say', 'type'));
        //list($name, $phone) = Common::getInput(array('name', 'phone'));
    
        /* # 参数过滤处理 */
        //$id = intval($id);
        //$uid = intval($uid);
        //$num = intval($num);
        //$type = intval($type);
        //$addres = t($addres);
        //$say = t($say);
        //$name = t($name);
        //$phone = t($phone);
        $object_id = intval($this->data['pic_id']);
        $payed=D('pic_pay_record')->where('uid='.$this->mid.' and object_id='.$object_id)->find();
        if ($payed){
            return array( 'status' => 0,
                'msg' => '您已经购买过此套图',);
        }

        $is_freemonth=$this->freemonth($object_id,'pic_object');
        if ($is_freemonth){


                    $object_id = $is_freemonth['id'];
                    $url_list = D('pic')->where('object_id='.$object_id)->field('url')->findall();
                    $pic_list = array();
                    foreach ($url_list as $bk => $bv) {
                        $pic_list[$bk]= $bv;
                    }

                    $result_list = $is_freemonth;
                    $result_list['status']=1;
                    $result_list['msg']='恭喜您，成功的购买了套图！您可以去充值或者完成任务获得更多积分哦！';
                    $result_list['images'] = $pic_list;

            return array('status' => 1, 'msg' => $is_freemonth['mp4_url']);
        }
        /* # 获取当前用户积分 */
        $score = model('Credit')->getUserCredit($this->mid);
        $score = $score['credit']['score']['value'];
        
        $object_id = intval($this->data['pic_id']);
    
        error_log('----  $charge  ----'.object_id ,3,'./log/picBuy00.log');
        
        $charge = D('pic_object')->where('id='.$object_id)->getField('charge');
        $isvip_info=model('User')->where('uid='.$this->mid)->field('is_vip,vip_endtime')->find();
        if ($isvip_info['is_vip']){
            if ($isvip_info['vip_endtime']<time()){//vip已经过期
                model('User')->where('uid='.$this->mid)->update(array('is_vip'=>0,'vip_endtime'=>0));
            }else{
                $pic_discount=D('vip_type')->where('id=6')->field('pic_discount')->getField('pic_discount');
                $pic_discount=(float)$pic_discount;
                $charge=intval($charge*$pic_discount);
            }
        }
        
        /* # 判断积分是否充足 */
        if ($charge > $score) {
              return array(
                 'status' => -1,
                 'msg' => '您的积分余额不足，请前往充值页面充值后购买!',);
        }
        
        $spend_score = $score - $charge;
        
        M('credit_user')->where('uid='.$this->mid)->save(array('score' => $spend_score));
        
        //$creditUser = M('credit_user')->where("uid={$this->mid}")->getField('score');//find();
        
        model('Credit')->cleanCache($this->mid);
        model('User')->cleanCache($this->mid);
                
        $list = D('pic_object')->where('id='.$object_id)->findAll();
        
        $result_list = array();
        if (!empty($list)) {
            foreach ($list as $ak => $av) {
                $object_id = $av['id'];
                $url_list = D('pic')->where('object_id='.$object_id)->field('url')->findall();
                $pic_list = array();
                foreach ($url_list as $bk => $bv) {
                    $pic_list[$bk]= $bv;
                }
                
                $result_list = $av;
                $result_list['images'] = $pic_list;
            }
        }
        
        //图片交易记录
        $data['object_id']=$object_id;
        $data['uid']=$this->mid;
        $data['spend_score']= $charge;
        $data['ctime']= time();
        $result = D('pic_pay_record')->add($data);
        $des = "购买图片";
        
        //积分变更记录
        $add['uid'] = intval($this->mid);
        $add['action'] = '购买套图';
        $add['des'] = '';
        $add['change'] = '积分<font color="red">'.$charge.'</font>';
        $add['ctime'] = time();
        $add['detail'] = '{"score":"-'.$charge.'"}';
        D('credit_record')->add($add);
        $result_list['status']=1;
        $result_list['msg']='恭喜您，成功的购买了套图！您可以去充值或者完成任务获得更多积分哦！';
         return $result_list;


        //return $result_list;
    }
    
    //赞某条分享
    public function add_pic_digg()
    {
        $object_id = intval($this->data['object_id']);
        $res = model('PicObject')->addDigg($object_id, $this->mid);


        return $res;
    }
    
    //收藏一条资源
    public function pic_favorite_create()
    {
        $data['source_table_name'] = $this->data['source_table_name']; // feed
        $data['source_id'] = $this->data['source_id'];     //140
        $data['source_app'] = $this->data['source_app']; //public
        error_log('---- pic favorite crate  ----'.$data['source_table_name'].'---'.$data['source_id'].'---'.$data['source_app'].'-----',3,'./log/pic_favorite_create.log');
        $res = model('Collection')->addPicCollection($data);
        return $res;
    }
    
    //取消收藏
    public function pic_favorite_destroy()
    {
        if (model('Collection')->delCollection($this->data['source_id'], $this->data['source_table_name'])) {
            return 1;
        }
        return 0;
    }
    
    //取消图片的收藏
    public function del_collected(){
        if (model('Collection')->delPicCollection($this->data['source_id'], 'pic_object')) {
            return array('status' => 1, 'msg' => $this->data['source_id']);
        }
    
        return array('status' => 0, 'msg' => '取消收藏失败');
    }

    public function freemonth($source_id,$sourece_table){
        $hour=date('H',time());
        $hour=(int)$hour;
        if ($hour<6||$hour>17){
            return false;
        }
        $is_charged= D('credit_charge')->where('uid='.$this->mid.' and charge_value = 8.8 and status=1')->order('ctime desc')->find();
        if (!$is_charged) return false;

        $time_now=time();
        if($is_charged['ctime']+30*24*60*60<$time_now){
            return false;
        }
        $sourece_info = D($sourece_table)->where('Id=' . $source_id)->find();

        return $sourece_info;

    }
    
}
