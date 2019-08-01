<?php
//视频接口
class PicApi extends Api
{
    //获取视频列表
    public function pic_list()
    {

        $list = D('pic_object')->limit(20)->order("rand()")->findAll();
        
        if (!empty($list)) {
            foreach ($list as $ak => $av) {
                $object_id = $av['id'];
                $url_list = D('pic')->where('object_id='.$object_id)->field('url')->find();
                $list[$ak]['images'] = $url_list;
            }
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
        $sql = ' AND uid='.$this->user_id;
        $list = model('Feed')->video_list('postvideo', $this->since_id, $this->max_id, $this->count, $this->page, $sql, 'feed_id DESC') ;
        foreach ($list as $k => &$v) {
            $timeline = D('video')->where('video_id='.$v['video_id'])->getField('timeline');
            $v['timeline'] = model('Video')->timeline_format($timeline);
        }

        return $list;
    }
    
    //获取短视频列表
    public function short_video_list()
    {
//         $this->user_id = empty($this->user_id) ? $this->mid : $this->user_id;
//         $sql = ' AND uid='.$this->user_id;
//         $list = model('Feed')->video_list('postvideo', $this->since_id, $this->max_id, $this->count, $this->page, $sql, 'feed_id DESC') ;
//         foreach ($list as $k => &$v) {
//             $timeline = D('video')->where('video_id='.$v['video_id'])->getField('timeline');
//             $v['timeline'] = model('Video')->timeline_format($timeline);
//         }
        
        //$list = Array('1'=>'one','2'=>'two','3'=>'three');
        //$uid = 4;
        //$avatar = model('Avatar')->init($uid)->getUserAvatar();
        $list = D('svideo')->limit(20)->order("rand()")->findAll();//
        //$list = model('Svideo')->limit(20)->order("rand()")->findAll();//
         if (!empty($list)) {
             foreach ($list as $ak => $av) {
                $uid = $av['user_id'];
                $uname = model('User')->where('uid='.$uid)->field('uname')->find();
                $avatar = model('Avatar')->init($uid)->getUserAvatar();
                $user['uname'] = $uname['uname'];
                $user['avatar'] = $avatar['avatar_tiny'];
                //= $av['uid']
                // $data['data'] = $list;
                $list[$ak]['user'] = $user;
             }
         }
        //$result['message'] = 'sucess';
        //$result['data'] = $list; //$data['data'];
        return $list;
    }
    
    //获取短视频列表
    public function buy()
    {
        //1.判断用户积分是否大于商品价格
        //2.购买成功后需要加入购买记录
        //3.付费图片和不付费图片要标记出来,购买成功只请求不付费图片
        
        list($id, $uid, $num, $addres, $say, $type) = Common::getInput(array('id', 'uid', 'num', 'addres', 'say', 'type'));
        list($name, $phone) = Common::getInput(array('name', 'phone'));
        
        /* # 参数过滤处理 */
        $id = intval($id);
        $uid = intval($uid);
        $num = intval($num);
        $type = intval($type);
        $addres = t($addres);
        $say = t($say);
        $name = t($name);
        $phone = t($phone);
        
        /* # 获取当前用户积分 */
        $score = model('Credit')->getUserCredit($this->mid);
        $score = $score['credit']['score']['value'];
        
        
        /* # 判断积分是否充足 */
//         if ($gift['score'] > $score) {
//             return array(
//                 'status' => -4,
//                 'mesage' => '您的积分余额不足，请先充值积分，或者做任务获得积分。',);
//         }

        error_log('---- upload face ----'.$score,3,'./log/picBuy1.log');

        return array(
            'status' => 1,
            'mesage' => '恭喜您，成功的为您的好友送出了礼物！您可以去充值或者完成任务获得更多积分哦！',
        );
    }

    //赞某条分享
    public function add_pic_digg()
    {
        $object_id = intval($this->data['object_id']);
        $res = model('PicObject')->addDigg($object_id, $this->mid);
        return $res;
    }
}
