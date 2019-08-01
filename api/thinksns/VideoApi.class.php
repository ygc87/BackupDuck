<?php

//视频接口
class VideoApi extends Api
{
    //获取视频列表
    public function video_list()
    {
        // $list = D('video')->order('video_id desc')->findPage(10);
        // $map['type'] = 'postvideo';
        // $list = model('Feed')->getList($map);
        // foreach ($list['data'] as $k => &$v) {
        // 	$v['image_path'] = SITE_URL.$v['image_path'];
        // 	$v['video_path'] = SITE_URL.$v['video_path'];
        // }
        $list = model('Feed')->video_list('postvideo', $this->since_id, $this->max_id, $this->count, $this->page, '', true);
        foreach ($list as $k => &$v) {
            $timeline = D('video')->where('video_id=' . $v['video_id'])->getField('timeline');
            $v['timeline'] = model('Video')->timeline_format($timeline);
        }

        return $list;
    }

    public function showVideo()
    {
        $feed = model('Feed')->getFeedInfo($this->id, true);    //getFeedInfo获取指定分享的信息，用于资源模型输出???
        $diggarr = model('FeedDigg')->checkIsDigg($this->id, $this->mid);
        $feed['is_digg'] = $diggarr[$this->id] ? 1 : 0;

        return $feed;
    }

    //获取视频列表
    public function my_video_list()
    {
        // $list = D('video')->order('video_id desc')->findPage(10);
        // $map['type'] = 'postvideo';
        // $list = model('Feed')->getList($map);
        // foreach ($list['data'] as $k => &$v) {
        // 	$v['image_path'] = SITE_URL.$v['image_path'];
        // 	$v['video_path'] = SITE_URL.$v['video_path'];
        // }
        $this->user_id = empty($this->user_id) ? $this->mid : $this->user_id;
        $sql = ' AND uid=' . $this->user_id;
        $list = model('Feed')->video_list('postvideo', $this->since_id, $this->max_id, $this->count, $this->page, $sql, 'feed_id DESC');
        foreach ($list as $k => &$v) {
            $timeline = D('video')->where('video_id=' . $v['video_id'])->getField('timeline');
            $v['timeline'] = model('Video')->timeline_format($timeline);
        }

        return $list;
    }

    //获取短视频列表
    public function short_video_list()
    {


        $userid= $this->mid;//登录用户的userid
        $video_count=D('svideo')->count();//视频库中视频总数量

        $id_arr=range(0,$video_count-1);//生成视频Id数组
        shuffle($id_arr);//打乱顺序
        $id_arr=array_slice($id_arr,0,10);//获取前10个
        $video_list= D('svideo')->where('Id in ('.implode(',',$id_arr).')')->findAll();
        $uids=array();//视频发布者 uid 数组
        $sourceids=array();//视频资源 id 数组
        foreach ($video_list as $item) {
            $uids[]=$item['user_id'];
           $sourceids[]=$item['Id'];
        }
        //发布视频者的用户名查询
        $inuids=implode(',',$uids);
        $unames_arr=model('User')->field('uid,uname')->where('uid in ('.$inuids.')')->findAll();
        $uname_assoc=array();
        foreach ($unames_arr as $index=>$item){
            $uname_assoc[$item['uid']]=$item['uname'];
        }
        unset($unames_arr);

        $insourceids=implode(',',$sourceids);
        //判断用户是否赞过视频
       $digg_arr= D('svideo_digg')->field('svideo_id')->where('uid='.$userid.' and svideo_id in('.$insourceids.')')->findAll();

       $digg_index_arr=array();
        foreach ($digg_arr as $index=>$item){
            $digg_index_arr[]=$item['svideo_id'];
        }
        unset($digg_arr);

        //判断用户是否收藏过视频
        $collection_arr=D('collection')->field('source_id')->where('uid='.$userid.' and source_table_name = \'svideo\' and source_id in ('.$insourceids.')')->findAll();
        $collection_index_arr=array();
        foreach ($collection_arr as $index=>$item){
            $collection_index_arr[]=$item['source_id'];
        }
        unset($collection_arr);
        //判断用户是否已经购买过该视频
        $buy_arr= D('svideo_pay_record')->field('video_id')->where('uid='.$userid.' and video_id in ('.$insourceids.')')->findAll();
        $buy_index_arr=array();
        foreach ($buy_arr as $index=>$item){
            $buy_index_arr[]=$item['video_id'];
        }
        unset($buy_arr);
        $svideo_discount=D('vip_type')->where('id=6')->field('svideo_discount')->getField('svideo_discount');
        $svideo_discount=floatval($svideo_discount);
        if (!empty($video_list)) {
            foreach ($video_list as $ak => $av) {
                $uid = $av['user_id'];
                $avatar = model('Avatar')->init($uid)->getUserAvatar();
                $user['uname']=$uname_assoc[$uid];
                $user['avatar'] = $avatar['avatar_tiny'];
                $video_list[$ak]['user'] = $user;
                $video_list[$ak]['category_type'] = "svideo";

                //判断用户是否已经赞过该视频

                if (in_array($av['Id'],$digg_index_arr)){
                    $video_list[$ak]['is_digg'] = 1;
                }else{
                    $video_list[$ak]['is_digg'] = 0;
                }

                //判断用户是否已经收藏过该视频

                if (in_array($av['Id'],$collection_index_arr)){
                    $video_list[$ak]['is_collected'] = 1;
                }else{
                    $video_list[$ak]['is_collected'] = 0;
                }

                //判断用户是否购买过该视频
                if (in_array($av['Id'],$buy_index_arr)){
                    $video_list[$ak]['is_payed'] = 1;
                }else{
                    $video_list[$ak]['is_payed'] = 0;
                }
                $vip_charge=$svideo_discount*$av['charge'];
                $video_list[$ak]['dialog_info']='购买视频需要'.$av['charge'].'积分，vip用户仅需'.$vip_charge.'积分';
                $video_list[$ak]['dialog_title']='是否购买视频';

                $video_list[$ak]['tips_msg']='购买视频需要'.$av['charge'].'积分，vip用户仅需'.$vip_charge.'积分';
                $video_list[$ak]['tips_title']='是否购买视频';
            }
        }
        $isvip_info=model('User')->where('uid='.$this->mid)->field('is_vip,vip_endtime')->find();
        if ($isvip_info['is_vip']){
            if ($isvip_info['vip_endtime']<time()){//vip已经过期
                model('User')->where('uid='.$this->mid)->update(array('is_vip'=>0,'vip_endtime'=>0));

            }else{
                foreach ($video_list as $k=>$v){
                    $video_list[$k]['charge']=intval($v['charge']*$svideo_discount);
                    $video_list[$k]['dialog_info']='VIP购买视频仅需'.$video_list[$k]['charge'].'积分';
                    $video_list[$k]['tips_msg']='VIP购买视频仅需'.$video_list[$k]['charge'].'积分';
                }

            }
        }
        $siteconfig=model('Xdata')->get('admin_Config:site',false);

        if ($siteconfig['adv_open']) {
            //添加广告item

            $ad_list = D('ad_content')->where("is_active=1 and display_type=1 and category='video'")->findAll();
            $ad_count=count($ad_list);
            $ad1_index=mt_rand(0,$ad_count-1);
            $ad2_index=$ad1_index+1;
            if ($ad2_index>=$ad_count){
                $ad2_index=0;
            }

            $ad1=$ad_list[$ad1_index];
            $ad2=$ad_list[$ad2_index];

            $ad1_user['uname'] = $ad1['avatar_name'];
            $ad1_user['avatar'] = $ad1['avatar_url'];
            $ad1_item = array();

            $ad1_item['label'] = "2";
            $ad1_item['category_type'] = "ad";
            $ad1_item['title'] = $ad1['title'];
            $ad1_item['content'] ='http://'.$_SERVER['HTTP_HOST']. $ad1['display_image'];
            $ad1_item['mp4_url']=$ad1['ad_url'];
            $ad1_item['ad_button']=$ad1['ad_button'];
            $ad1_item['user'] = $ad1_user;

            $ad2_user['uname'] = $ad2['avatar_name'];
            $ad2_user['avatar'] = $ad2['avatar_url'];
            $ad2_item = array();

            $ad2_item['label'] = "2";
            $ad2_item['category_type'] = "ad";
            $ad2_item['title'] = $ad2['title'];
            $ad2_item['content'] ='http://'.$_SERVER['HTTP_HOST']. $ad2['display_image'];
            $ad2_item['mp4_url']=$ad2['ad_url'];
            $ad2_item['ad_button']=$ad2['ad_button'];
            $ad2_item['user'] = $ad2_user;


           $temp1= $video_list[0];
            $temp2= $video_list[2];
           $video_list[0]=$ad1_item;
            array_push($video_list, $temp1);
           if ($ad_count>1){
               $video_list[2]=$ad2_item;
               array_push($video_list, $temp2);
           }



        }

        return $video_list;

    }

    //获取已购买短视频列表
    public function payed_video_list()
    {       $count=intval($this->count);
            $page=intval($this->page);
            $limit=($page-1)*$count;
        $tablename=$this->data['source_table_name'];
        if ($tablename=='svideo'){
            $payed_list = D('svideo_pay_record')->limit($limit.','.$count)->where('uid='.$this->mid)->order('ctime desc')->select();
        }elseif ($tablename=='lvideo'){
            $payed_list = D('lvideo_pay_record')->limit($limit.','.$count)->where('uid='.$this->mid)->order('ctime desc')->select();
        }

        $video_ids=array();
        foreach ($payed_list as $i=>$item){
            $video_ids[]=$item['video_id'];
        }
        $in_video_ids=implode(',',$video_ids);
        if ($tablename=='svideo'){
            $video_list=D('svideo')->where('Id in ('.$in_video_ids.')')->findAll();
        }elseif($tablename=='lvideo'){
            $video_list=D('lvideo')->where('Id in ('.$in_video_ids.')')->findAll();
        }
        $video_list_order=array();
        foreach ($video_ids as $i=>$k){
            foreach ($video_list as $index=>$value){

                if ($value['Id']==$k){
                    $video_list_order[]=$value;
                }
            }
        }
        $video_list=$video_list_order;
        $uids=array();
        foreach ($video_list as $k=>$value){
            $uids[]=$value['user_id'];
        }
        $in_uids=implode(',',$uids);
        $uname_arr = model('User')->where('uid in ('.$in_uids.')')->field('uid,uname')->findAll();
        $uname_asso=array();
        foreach ($uname_arr as $key=>$value){
            $uname_asso[$value['uid']]=$value['uname'];
        }

        //判断用户是否赞过视频
        if ($tablename=='svideo'){

            $digg_arr= D('svideo_digg')->field('svideo_id')->where('uid='.$this->mid.' and svideo_id in('.$in_video_ids.')')->findAll();
        }elseif($tablename=='lvideo'){
            $digg_arr= D('lvideo_digg')->field('lvideo_id')->where('uid='.$this->mid.' and lvideo_id in('.$in_video_ids.')')->findAll();
        }

        $digg_index_arr=array();
        foreach ($digg_arr as $index=>$item){
            $digg_index_arr[]=$item['svideo_id'];
        }
        unset($digg_arr);

        //判断用户是否收藏过视频
        $collection_arr=D('collection')->field('source_id')->where('uid='.$this->mid.' and source_table_name = \''.$tablename.'\' and source_id in ('.$in_video_ids.')')->findAll();
        $collection_index_arr=array();
        foreach ($collection_arr as $index=>$item){
            $collection_index_arr[]=$item['source_id'];
        }
        unset($collection_arr);
        if (!empty($video_list)) {
            foreach ($video_list as $ak => $av) {
                $uid = $av['user_id'];
                //$uname = model('User')->where('uid=' . $uid)->field('uname')->find();
                $avatar = model('Avatar')->init($uid)->getUserAvatar();
                $user['uname'] = $uname_asso[$uid];
                $user['avatar'] = $avatar['avatar_tiny'];

                $video_list[$ak]['user'] = $user;
                if ($tablename=='svideo'){
                    $video_list[$ak]['category_type'] = "svideo";
                }elseif($tablename=='lvideo'){
                    $video_list[$ak]['category_type'] = "lvideo";
                }




                //判断用户是否已经赞过该视频
                if (in_array($av['Id'],$digg_index_arr)) {
                    $video_list[$ak]['is_digg'] = 1;
                } else {
                    $video_list[$ak]['is_digg'] = 0;
                }

                //判断用户是否已经收藏过该视频
                if (in_array($av['Id'],$collection_index_arr)) {
                    $video_list[$ak]['is_collected'] = 1;
                } else {
                    $video_list[$ak]['is_collected'] = 0;
                }



                //判断用户是否已经购买过该视频

                    $video_list[$ak]['is_payed'] = 1;

            }
        }
        $next_page=$page+1;
        return array('next_page'=>$next_page,'object_list'=>$video_list);
    }

    //获取长视频列表
    public function long_video_list()
    {

        $userid= $this->mid;//登录用户的userid
        $video_count=D('lvideo')->count();//视频库中视频总数量

        $id_arr=range(0,$video_count-1);//生成视频Id数组
        shuffle($id_arr);//打乱顺序
        $id_arr=array_slice($id_arr,0,10);//获取前10个
        $video_list= D('lvideo')->where('Id in ('.implode(',',$id_arr).')')->findAll();

        $uids=array();//视频发布者 uid 数组
        $sourceids=array();//视频资源 id 数组
        foreach ($video_list as $item) {
            $uids[]=$item['user_id'];
            $sourceids[]=$item['Id'];
        }
        //发布视频者的用户名查询
        $inuids=implode(',',$uids);
        $unames_arr=model('User')->field('uid,uname')->where('uid in ('.$inuids.')')->findAll();
        $uname_assoc=array();
        foreach ($unames_arr as $index=>$item){
            $uname_assoc[$item['uid']]=$item['uname'];
        }
        unset($unames_arr);


        $insourceids=implode(',',$sourceids);
        //判断用户是否赞过视频
        $digg_arr= D('lvideo_digg')->field('lvideo_id')->where('uid='.$userid.' and lvideo_id in('.$insourceids.')')->findAll();

        $digg_index_arr=array();
        foreach ($digg_arr as $index=>$item){
            $digg_index_arr[]=$item['lvideo_id'];
        }
        unset($digg_arr);

        //判断用户是否收藏过视频
        $collection_arr=D('collection')->field('source_id')->where('uid='.$userid.' and source_table_name = \'lvideo\' and source_id in ('.$insourceids.')')->findAll();
        $collection_index_arr=array();
        foreach ($collection_arr as $index=>$item){
            $collection_index_arr[]=$item['source_id'];
        }
        unset($collection_arr);
        //判断用户是否已经购买过该视频
        $buy_arr= D('lvideo_pay_record')->field('video_id')->where('uid='.$userid.' and video_id in ('.$insourceids.')')->findAll();
        $buy_index_arr=array();
        foreach ($buy_arr as $index=>$item){
            $buy_index_arr[]=$item['video_id'];
        }
        unset($buy_arr);
        $lvideo_discount=D('vip_type')->where('id=6')->field('lvideo_discount')->getField('lvideo_discount');
        if (!empty($video_list)) {
            foreach ($video_list as $ak => $av) {
                $uid = $av['user_id'];
                $avatar = model('Avatar')->init($uid)->getUserAvatar();
                $user['uname'] = $uname_assoc[$uid];;
                $user['avatar'] = $avatar['avatar_tiny'];
                $video_list[$ak]['user'] = $user;
                $video_list[$ak]['category_type'] = "lvideo";

                //判断用户是否已经赞过该视频

                if (in_array($av['Id'],$digg_index_arr)){
                    $video_list[$ak]['is_digg'] = 1;
                }else{
                    $video_list[$ak]['is_digg'] = 0;
                }

                //判断用户是否已经收藏过该视频

                if (in_array($av['Id'],$collection_index_arr)){
                    $video_list[$ak]['is_collected'] = 1;
                }else{
                    $video_list[$ak]['is_collected'] = 0;
                }

                //判断用户是否购买过该视频
                if (in_array($av['Id'],$buy_index_arr)){
                    $video_list[$ak]['is_payed'] = 1;
                }else{
                    $video_list[$ak]['is_payed'] = 0;
                }
                $vip_charge=$lvideo_discount*$av['charge'];
                $video_list[$ak]['dialog_info']='购买视频需要'.$av['charge'].'积分，vip用户仅需'.$vip_charge.'积分';
                $video_list[$ak]['dialog_title']='是否购买视频';

                $video_list[$ak]['tips_msg']='购买视频需要'.$av['charge'].'积分，vip用户仅需'.$vip_charge.'积分';
                $video_list[$ak]['tips_title']='是否购买视频';
            }
        }
        $isvip_info=model('User')->where('uid='.$this->mid)->field('is_vip,vip_endtime')->find();

        if ($isvip_info['is_vip']){
            if ($isvip_info['vip_endtime']<time()){//vip已经过期
                model('User')->where('uid='.$this->mid)->update(array('is_vip'=>0,'vip_endtime'=>0));
            }else{
                $lvideo_discount=D('vip_type')->where('id=6')->field('lvideo_discount')->getField('lvideo_discount');
                $lvideo_discount=(float)$lvideo_discount;
                foreach ($video_list as $k=>$v){
                    $video_list[$k]['charge']=intval($v['charge']*$lvideo_discount);
                    $video_list[$ak]['dialog_info']='VIP购买视频仅需'.$video_list[$k]['charge'].'积分';
                    $video_list[$k]['tips_msg']='VIP购买视频仅需'.$video_list[$k]['charge'].'积分';
                }

            }
        }
        $siteconfig=model('Xdata')->get('admin_Config:site');
        if ($siteconfig['adv_open']) {

            $ad_list = D('ad_content')->where("is_active=1 and display_type=1 and category='video'")->findAll();
            $ad_count=count($ad_list);
            $ad1_index=mt_rand(0,$ad_count-1);
            $ad2_index=$ad1_index+1;
            if ($ad2_index>=$ad_count){
                $ad2_index=0;
            }

            $ad1=$ad_list[$ad1_index];
            $ad2=$ad_list[$ad2_index];

            $ad1_user['uname'] = $ad1['avatar_name'];
            $ad1_user['avatar'] = $ad1['avatar_url'];
            $ad1_item = array();

            $ad1_item['label'] = "2";
            $ad1_item['category_type'] = "ad";
            $ad1_item['title'] = $ad1['title'];
            $ad1_item['content'] ='http://'.$_SERVER['HTTP_HOST']. $ad1['display_image'];
            $ad1_item['mp4_url']=$ad1['ad_url'];
            $ad1_item['ad_button']=$ad1['ad_button'];
            $ad1_item['user'] = $ad1_user;

            $ad2_user['uname'] = $ad2['avatar_name'];
            $ad2_user['avatar'] = $ad2['avatar_url'];
            $ad2_item = array();

            $ad2_item['label'] = "2";
            $ad2_item['category_type'] = "ad";
            $ad2_item['title'] = $ad2['title'];
            $ad2_item['content'] ='http://'.$_SERVER['HTTP_HOST']. $ad2['display_image'];
            $ad2_item['mp4_url']=$ad2['ad_url'];
            $ad2_item['ad_button']=$ad2['ad_button'];
            $ad2_item['user'] = $ad2_user;


            $temp1= $video_list[0];
            $temp2= $video_list[2];
            $video_list[0]=$ad1_item;
            array_push($video_list, $temp1);
            if ($ad_count>1){
                $video_list[2]=$ad2_item;
                array_push($video_list, $temp2);
            }
        }
        return $video_list;
    }

    //获取收藏视频的列表
    public function get_collected_list()
    {
        return model('Collection')->getCollectionVideoList($this->mid, $this->since_id, $this->max_id, $this->count, $this->page,  $this->data['source_table_name']);
    }

    //添加视频的收藏
    public function add_collected()
    {
        $data['source_table_name'] = $this->data['source_table_name']; // feed
        $data['source_id'] = $this->data['source_id'];     //140
        $data['source_app'] = $this->data['source_app']; //public
        $res = model('Collection')->addVideoCollection($data);
        return $res;
    }

    //取消视频的收藏
    public function del_collected()
    {

        if (model('Collection')->delVideoCollection($this->data['source_id'], $this->data['source_table_name'],$this->mid)) {
            return array('status' => 1, 'msg' => $this->data['source_id']);
        }

        return array('status' => 0, 'msg' => '取消收藏失败');
    }


    //buy short video
    public function buy_video()
    {

        $video_id = $this->data['video_id'];
        $payed=D('svideo_pay_record')->where('video_id='.$video_id.' and uid='.$this->mid)->count();

        if ($payed){
            return array('status' => 0,'msg' => '您已经购买过，请在已购买视频中查看');
        }
        $is_freemonth=$this->freemonth($video_id,'svideo');
        if ($is_freemonth){
            return array('status' => 1, 'msg' => $is_freemonth['mp4_url']);
        }

        //获取用户积分和视频积分，然后进行增减。
        $user_score = M('credit_user')->where("uid={$this->mid}")->getField('score');
        //获取视频积分
        $video_info = D('svideo')->where('Id=' . $video_id)->find();

        $isvip_info=model('User')->where('uid='.$this->mid)->field('is_vip,vip_endtime')->find();
        if ($isvip_info['is_vip']){
            if ($isvip_info['vip_endtime']<time()){//vip已经过期
                model('User')->where('uid='.$this->mid)->save(array('is_vip'=>0,'vip_endtime'=>0));
            }else{
                $svideo_discount=D('vip_type')->where('id=6')->field('svideo_discount')->getField('svideo_discount');
                $svideo_discount=(float)$svideo_discount;
                $video_info['charge']=intval($video_info['charge']*$svideo_discount);
            }
        }
        //相减过后的积分存到数据库里
        //error_log('---- thinksns buy_video charge ----' . $video_info['charge'], 3, './log/buy_video_charge.log');
        $spend_score = $user_score - $video_info['charge'];
        if ($spend_score < 0) {
            return array('status' => -1, 'msg' => '您的积分余额不足，请前往充值页面充值后购买！');
        }

        M('credit_user')->where('uid=' . $this->mid)->save(array('score' => $spend_score));
        model('Credit')->cleanCache($this->mid);
        model('User')->cleanCache($this->mid);

        //添加视频购买记录
        $map['video_id'] = $video_id;
        $map['uid'] = $this->mid;
        $map['ctime'] = time();
        //插入数据，
        $result = D('svideo_pay_record')->add($map);

        //积分变更记录
        $add['uid'] = intval($this->mid);
        $add['action'] = '购买短视频';
        $add['des'] = '';
        $add['change'] = '积分<font color="red">' . $video_info['charge'] . '</font>';
        $add['ctime'] = time();
        $add['detail'] = '{"score":"-' . $video_info['charge'] . '"}';
        D('credit_record')->add($add);

        error_log('---- thinksns buy_video ----' . $video_id, 3, './log/buy_video1.log');
        //购买成功，返回视频地址，记录已购表，改变视频类型(未付费，已付费，VIP)，再请求视频列表时应把付费和未付费区分开来
        //获取视频播放地址
        if ($result) {
            error_log('---- thinksns buy_video mp4 ----' . $video_info['mp4_url'], 3, './log/buy_video_mp4.log');
            return array('status' => 1, 'msg' => $video_info['mp4_url']);
        }

        //购买失败，返回原因
        return array('status' => 0, 'msg' => $result);
    }


    //buy long video
    public function buy_long_video()
    {
        $video_id = $this->data['video_id'];
        $payed=D('svideo_pay_record')->where('video_id='.$video_id.' and uid='.$this->mid)->count();

        if ($payed){
            return array('status' => 0,'msg' => '您已经购买过，请在已购买视频中查看');
        }
        $is_freemonth=$this->freemonth($video_id,'lvideo');
        if ($is_freemonth){
            return array('status' => 1, 'msg' => $is_freemonth['mp4_url']);
        }
        //获取用户积分和视频积分，然后进行增减。
        $user_score = M('credit_user')->where("uid={$this->mid}")->getField('score');
        //获取视频积分
        $video_info = D('lvideo')->where('Id=' . $video_id)->find();

        $isvip_info=model('User')->where('uid='.$this->mid)->field('is_vip,vip_endtime')->find();
        if ($isvip_info['is_vip']){
            if ($isvip_info['vip_endtime']<time()){//vip已经过期
                model('User')->where('uid='.$this->mid)->update(array('is_vip'=>0,'vip_endtime'=>0));
            }else{
                $lvideo_discount=D('vip_type')->where('id=6')->field('lvideo_discount')->getField('lvideo_discount');
                $lvideo_discount=(float)$lvideo_discount;
                $video_info['charge']=intval($video_info['charge']*$lvideo_discount);
            }
        }


        //相减过后的积分存到数据库里
        $spend_score = $user_score - $video_info['charge'];
        if ($spend_score <= 0) {
            return array('status' => -1, 'msg' => '您的积分余额不足，请前往充值页面充值后购买！');
        }

        M('credit_user')->where('uid=' . $this->mid)->save(array('score' => $spend_score));
        model('Credit')->cleanCache($this->mid);
        model('User')->cleanCache($this->mid);

        //添加视频购买记录
        $map['video_id'] = $video_id;
        $map['uid'] = $this->mid;
        $map['ctime'] = time();
        //插入数据，
        $result = D('lvideo_pay_record')->add($map);

        //积分变更记录
        $add['uid'] = intval($this->mid);
        $add['action'] = '购买长视频';
        $add['des'] = '';
        $add['change'] = '积分<font color="red">' . $video_info['charge'] . '</font>';
        $add['ctime'] = time();
        $add['detail'] = '{"score":"-' . $video_info['charge'] . '"}';
        D('credit_record')->add($add);

        error_log('---- thinksns buy_video ----' . $video_id, 3, './log/buy_video1.log');
        //购买成功，返回视频地址，记录已购表，改变视频类型(未付费，已付费，VIP)，再请求视频列表时应把付费和未付费区分开来
        //获取视频播放地址
        if ($result) {
            error_log('---- thinksns buy_video mp4 ----' . $video_info['mp4_url'], 3, './log/buy_video_mp4.log');
            return array('status' => 1, 'msg' => $video_info['mp4_url']);
        }

        //购买失败，返回原因
        return array('status' => 0, 'msg' => $result);
    }

    //获取视频播放地址
    public function get_videourl()
    {
        $table_name=$this->data['source_table_name'];
        //判断用户是否购买
        $map = array(
            'uid' => $this->mid,
            'video_id' => $this->data['video_id'],
        );
        if($table_name=='svideo'){
           $is_buyed= D('svideo_pay_record')->where($map)->count();
        }elseif($table_name=='lvideo'){
            $is_buyed=D('lvideo_pay_record')->where($map)->count();
        }


        //判断用户是否已经购买过该视频
        if ($is_buyed) {

            $videourl = D($table_name)->where('Id=' . $map['video_id'])->field('mp4_url')->find();
           $videourl=$videourl['mp4_url'];
            return array('status' => 1, 'msg' => $videourl);
        } else {
            return array('status' => 0, 'msg' => '请进行购买后观看！');
        }

    }

    //获取视频播放地址
    public function get_long_videourl()
    {
        //判断用户是否购买
        $map = array(
            'uid' => $this->mid,
            'video_id' => $this->data['video_id'],
        );

        //判断用户是否已经购买过该视频
        if (D('lvideo_pay_record')->where($map)->count()) {
            $videourl = D('lvideo')->where('Id=' . $map['video_id'])->field('mp4_url')->find();
            return array('status' => 1, 'msg' => $videourl);
        } else {
            return array('status' => 0, 'msg' => '请进行购买后观看！');
        }
    }

    /**视频点赞接口
     * @return mixed
     */
    public function add_video_digg()
    {
        return model('VideoDigg')->addDigg($this->data['video_id'],$this->mid,$this->data['source_table_name']);
    }

    /**
     * 视频评论点赞接口
     * @return array
     */
    public function add_videocomment_digg(){
        $comment_id = intval($this->data ['comment_id']);
        $res = model('CommentDigg')->addDigg($comment_id, $this->mid);
        if ($res) {
            $digg_count=model('CommentVideo')->where('comment_id='.$comment_id)->getField('digg_count');
            return array(
                'status' => 1,
                'msg' => '点赞成功',
                'count'=>$digg_count
            );
        } else {
            $message=model('CommentDigg')->getError();
            if ($message==''||!isset($message)){
                $message='点赞失败';
            }
            return array(
                'status' => 0,
                'msg' => $message,
            );
        }
    }

    public function  nologin_video(){
        $page=intval($this->data['page']);
        if ($result=S('nologin_video_page'.$page)){

           return $result;
        }
        $limit=$page*10;
        $result=array();
        $video_list=D('free_video')->limit($limit.',10')->findAll();
        foreach ($video_list as $key=>&$value){
          $value['label']=0;
          $value['digg_count']=0;
        }
        $result['video']=$video_list;
        $ad_list = D('ad_content')->where("is_active=1 and display_type=3 and category='banner'")->limit(3)->findAll();
        foreach ($ad_list as $key=>&$value){
            $value['label']=2;
            $value['display_image']='http://'.$_SERVER['HTTP_HOST'].$value['display_image'];
        }
        $result['banner']=$ad_list;
        if (is_array($result)&&!empty($result)){
            S('nologin_video_page'.$page,$result);
        }else{
            $result=array();
        }


        return $result;
    }

    public function  welcome_ad(){
        $ad_list = D('ad_content')->where("is_active=1 and display_type=2 and category='welcome'")->find();
        if (!empty($ad_list)){
            $ad_list['label']=2;
            $ad_list['display_image']='http://'.$_SERVER['HTTP_HOST'].$ad_list['display_image'];
        }

        return $ad_list;
    }
public function play_record(){
        $data['ctime']=time();
        $data['video_id']=$this->data['video_id'];
        $data['video_title']=$this->data['video_title'];
        $data['uid']=$this->mid;
        $data['video_type']=$this->data['video_type'];
       $result= D('video_play_record')->add($data);
       if ($result){
           return array('status'=>1,'msg'=>'添加成功');
       }else{
           return array('status'=>0,'msg'=>'添加失败');
       }
}


public function  apk_download(){

        echo 'http://www.biiixx.com/';exit;
}

public function apk_update(){
       $apkversion_info= D('apk_update')->limit(1)->order('datetime desc')->findAll();
       return $apkversion_info[0];
}


public function  get_video_list(){
    $userid= $this->mid;//登录用户的userid
    $tablename=$this->data['source_table'];
    $digg_table=$tablename.'_digg';
    $video_count=D($tablename)->count();//视频库中视频总数量

    $id_arr=range(0,$video_count-1);//生成视频Id数组
    shuffle($id_arr);//打乱顺序
    $id_arr=array_slice($id_arr,0,10);//获取前10个
    $video_list= D($tablename)->where('Id in ('.implode(',',$id_arr).')')->findAll();
    $uids=array();//视频发布者 uid 数组
    $sourceids=array();//视频资源 id 数组
    foreach ($video_list as $item) {
        $uids[]=$item['user_id'];
        $sourceids[]=$item['Id'];
    }
    //发布视频者的用户名查询
    $inuids=implode(',',$uids);
    $unames_arr=model('User')->field('uid,uname')->where('uid in ('.$inuids.')')->findAll();
    $uname_assoc=array();
    foreach ($unames_arr as $index=>$item){
        $uname_assoc[$item['uid']]=$item['uname'];
    }
    unset($unames_arr);


    $insourceids=implode(',',$sourceids);
    //判断用户是否赞过视频
    $digg_arr= D($digg_table)->field($tablename.'_id')->where('uid='.$userid.' and '.$tablename.'_id in('.$insourceids.')')->findAll();

    $digg_index_arr=array();
    foreach ($digg_arr as $index=>$item){
        $digg_index_arr[]=$item['lvideo_id'];
    }
    unset($digg_arr);

    //判断用户是否收藏过视频
    $collection_arr=D('collection')->field('source_id')->where('uid='.$userid.' and source_table_name = \''.$tablename.'\' and source_id in ('.$insourceids.')')->findAll();
    $collection_index_arr=array();
    foreach ($collection_arr as $index=>$item){
        $collection_index_arr[]=$item['source_id'];
    }
    unset($collection_arr);
    //判断用户是否已经购买过该视频
    $buy_arr= D($tablename.'_pay_record')->field('video_id')->where('uid='.$userid.' and video_id in ('.$insourceids.')')->findAll();
    $buy_index_arr=array();
    foreach ($buy_arr as $index=>$item){
        $buy_index_arr[]=$item['video_id'];
    }
    unset($buy_arr);
    $video_discount=D('vip_type')->where('id=6')->field($tablename.'_discount')->getField($tablename.'_discount');
    if (!empty($video_list)) {
        foreach ($video_list as $ak => $av) {
            $uid = $av['user_id'];
            $avatar = model('Avatar')->init($uid)->getUserAvatar();
            $user['uname'] = $uname_assoc[$uid];;
            $user['avatar'] = $avatar['avatar_tiny'];
            $video_list[$ak]['user'] = $user;
            $video_list[$ak]['category_type'] = "lvideo";

            //判断用户是否已经赞过该视频

            if (in_array($av['Id'],$digg_index_arr)){
                $video_list[$ak]['is_digg'] = 1;
            }else{
                $video_list[$ak]['is_digg'] = 0;
            }

            //判断用户是否已经收藏过该视频

            if (in_array($av['Id'],$collection_index_arr)){
                $video_list[$ak]['is_collected'] = 1;
            }else{
                $video_list[$ak]['is_collected'] = 0;
            }

            //判断用户是否购买过该视频
            if (in_array($av['Id'],$buy_index_arr)){
                $video_list[$ak]['is_payed'] = 1;
            }else{
                $video_list[$ak]['is_payed'] = 0;
            }
            $vip_charge=$video_discount*$av['charge'];
            $video_list[$ak]['dialog_info']='购买视频需要'.$av['charge'].'积分，vip用户仅需'.$vip_charge.'积分';
            $video_list[$ak]['dialog_title']='是否购买视频';

            $video_list[$ak]['tips_msg']='购买视频需要'.$av['charge'].'积分，vip用户仅需'.$vip_charge.'积分';
            $video_list[$ak]['tips_title']='是否购买视频';
            $video_list[$ak]['channel_code']=$tablename;
        }
    }
    $isvip_info=model('User')->where('uid='.$this->mid)->field('is_vip,vip_endtime')->find();

    if ($isvip_info['is_vip']){
        if ($isvip_info['vip_endtime']<time()){//vip已经过期
            model('User')->where('uid='.$this->mid)->update(array('is_vip'=>0,'vip_endtime'=>0));
        }else{
            $lvideo_discount=D('vip_type')->where('id=6')->field('lvideo_discount')->getField('lvideo_discount');
            $lvideo_discount=(float)$lvideo_discount;
            foreach ($video_list as $k=>$v){
                $video_list[$k]['charge']=intval($v['charge']*$lvideo_discount);
                $video_list[$ak]['dialog_info']='VIP购买视频仅需'.$video_list[$k]['charge'].'积分';
                $video_list[$k]['tips_msg']='VIP购买视频仅需'.$video_list[$k]['charge'].'积分';
            }

        }
    }
    $siteconfig=model('Xdata')->get('admin_Config:site');
    if ($siteconfig['adv_open']) {

        $ad_list = D('ad_content')->where("is_active=1 and display_type=1 and category='video'")->findAll();
        $ad_count=count($ad_list);
        $ad1_index=mt_rand(0,$ad_count-1);
        $ad2_index=$ad1_index+1;
        if ($ad2_index>=$ad_count){
            $ad2_index=0;
        }

        $ad1=$ad_list[$ad1_index];
        $ad2=$ad_list[$ad2_index];

        $ad1_user['uname'] = $ad1['avatar_name'];
        $ad1_user['avatar'] = $ad1['avatar_url'];
        $ad1_item = array();

        $ad1_item['label'] = "2";
        $ad1_item['category_type'] = "ad";
        $ad1_item['title'] = $ad1['title'];
        $ad1_item['content'] ='http://'.$_SERVER['HTTP_HOST']. $ad1['display_image'];
        $ad1_item['mp4_url']=$ad1['ad_url'];
        $ad1_item['ad_button']=$ad1['ad_button'];
        $ad1_item['user'] = $ad1_user;

        $ad2_user['uname'] = $ad2['avatar_name'];
        $ad2_user['avatar'] = $ad2['avatar_url'];
        $ad2_item = array();

        $ad2_item['label'] = "2";
        $ad2_item['category_type'] = "ad";
        $ad2_item['title'] = $ad2['title'];
        $ad2_item['content'] ='http://'.$_SERVER['HTTP_HOST']. $ad2['display_image'];
        $ad2_item['mp4_url']=$ad2['ad_url'];
        $ad2_item['ad_button']=$ad2['ad_button'];
        $ad2_item['user'] = $ad2_user;


        $temp1= $video_list[0];
        $temp2= $video_list[2];
        $video_list[0]=$ad1_item;
        array_push($video_list, $temp1);
        if ($ad_count>1){
            $video_list[2]=$ad2_item;
            array_push($video_list, $temp2);
        }

    }
    return $video_list;
}

    public function getchannel(){
        $result=D('x_channel')->findAll();
        if($result){
            uasort($result,function ($a,$b){if ($a['sort']>$b['sort'])return 1;else{return -1;}});

            return $result;
        }
        return array();
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
