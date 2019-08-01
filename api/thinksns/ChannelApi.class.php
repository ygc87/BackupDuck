<?php
/**
 * 频道应用API接口
 * @author zivss guolee226@gmail.com
 * @version  TS3.0
 */
class ChannelApi extends Api
{
    /**
     * 频道首页
     * @param int user_id 用户ID
     * @param int page 第几页
     * @param int count 视频条数
     * @return json 频道分类+热门视频
     */
    public function channel()
    {
        $page = $this->data['page'] ? intval($this->data['page']) : 1;
        $count = $this->data['count'] ? intval($this->data['count']) : 6;
        if ($page == 1) {
            $this->user_id = empty($this->user_id) ? $this->mid : $this->user_id;
            // $data['channels'] = D('ChannelApi', 'channel')->getChannels($this->user_id,6);
            $data['channels'] = D('ChannelFollow', 'channel')->getFollowList($this->user_id);
            foreach ($data['channels'] as $k => &$v) {
                $ext = unserialize($v['ext']);
                $v['attach'] = $ext['attach'];
            }
            // 组装附件信息
            $attachIds = getSubByKey($data['channels'], 'attach');
            $attachIds = array_filter($attachIds);
            $attachIds = array_unique($attachIds);
            $attachInfos = model('Attach')->getAttachByIds($attachIds);
            $attachData = array();

            foreach ($attachInfos as $attach) {
                $attachData[$attach['attach_id']] = $attach;
            }

            foreach ($data['channels'] as &$value) {
                if (!empty($value['attach']) && !empty($attachData[$value['attach']])) {
                    $value['icon_url'] = getImageUrl($attachData[$value['attach']]['save_path'].$attachData[$value['attach']]['save_name']);
                } else {
                    $value['icon_url'] = null;
                }
                unset($value['ext'], $value['attach'], $value['user_bind'], $value['topic_bind']);
                if ($this->user_id) {
                    $value['followStatus'] = intval(D('ChannelFollow', 'channel')->getFollowStatus($this->user_id, $value['channel_category_id']));
                }
            }
        }
        $data['videos'] = D('ChannelApi', 'channel')->getChannelFeed('', 'postvideo', $count, $page);

        return $data;
    }

    // /**
    //  * 获取所有频道分类
    //  * @return json 所有频道分类
    //  */
    // public function get_all_channel(){
    // 	$this->user_id = empty($this->user_id) ? $this->mid : $this->user_id;
    // 	$data = D('ChannelApi', 'channel')->getAllChannel($this->user_id);
    // 	return $data;
    // }
    //
    /**
     * 获取所有频道分类
     * @param int user_id 用户ID
     * @param int page 第几页
     * @param int count 视频条数
     * @param string keywords 搜索关键字
     * @return json 所有频道分类
     */
    public function get_all_channel()
    {
        $this->user_id = empty($this->user_id) ? $this->mid : $this->user_id;
        $page = $this->data['page'] ? intval($this->data['page']) : 1;
        $count = $this->data['count'] ? intval($this->data['count']) : 12;
        if ($this->data['keywords']) {
            $sql = "title like '%".t($this->data['keywords'])."%'";
        }
        $data = D('ChannelApi', 'channel')->getChannels($this->user_id, $count, $page, '', $sql);

        return $data;
    }

    /**
     * 热门视频
     * @param int page 第几页
     * @param int count 分享条数
     * @return json 视频分享
     */
    public function get_videos()
    {
        $page = $this->data['page'] ? intval($this->data['page']) : 1;
        $count = $this->data['count'] ? intval($this->data['count']) : 20;
        $videos = D('ChannelApi', 'channel')->getChannelFeed('', 'postvideo', $count, $page);

        return $videos;
    }

    /**
     * 获取频道分类下的分享
     * @param int category_id 频道分类ID
     * @param int page 第几页
     * @param int count 分享条数
     * @param int type 分享类型 0-全部 1-原创 2-转发 3-图片 4-附件 5-视频
     * @return json 指定分类下的分享
     */
    public function get_channel_feed()
    {
        $cid = intval($this->data['category_id']);
        if (is_null($cid)) {
            return array();
        }
        $type_arr = array('0', 'post', 'repost', 'postimage', 'postfile', 'postvideo');
        $type = $type_arr[intval($this->data['type'])];
        $order = 'c.feed_id DESC';
        $page = $this->data['page'] ? intval($this->data['page']) : 1;
        $count = $this->data['count'] ? intval($this->data['count']) : 6;
        $data = D('ChannelApi', 'channel')->getChannelFeed($cid, $type, $count, $page, $order, $sql);

        return $data;
    }

    /**
     * 频道关注或取消关注
     * @param int user_id 用户ID
     * @param int cid 频道分类ID
     * @param int type 1-关注 0-取消关注
     * @return json
     */
    public function channel_follow()
    {
        $this->user_id = empty($this->user_id) ? $this->mid : $this->user_id;
        $cid = intval($this->data['cid']);
        $type = intval($this->data['type']);
        if ($type == 1) {  //加关注
            $action = 'add';
            $info = '订阅';
        } else {
            $action = 'del';
            $info = '取消订阅';
        }
        $res = D('ChannelFollow', 'channel')->upFollow($this->user_id, $cid, $action);
        if ($res) {
            $data['status'] = 1;
            $data['info'] = $info.'成功';
        } else {
            $data['status'] = 0;
            $data['info'] = $info.'失败';
        }

        return $data;
    }
}
