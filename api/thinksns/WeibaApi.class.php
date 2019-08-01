<?php
/**
 *
 * @author jason
 *
 */
class WeibaApi extends Api
{
    /**
     * 微吧列表
     * @param int count 每页显示条数
     * @param int page 显示第几页
     * @param int user_id 用户ID
     * @return array 微吧列表
     */
    public function get_weibas()
    {
        $this->user_id = empty($this->user_id) ? $this->mid : $this->user_id;
        $data = D('Weiba', 'weiba')->get_weibas_forapi($this->since_id, $this->max_id, $this->count, $this->page, $this->user_id);
        if ($data) {
            return $data;
        } else {
            return array();
        }
    }

    /**
     * 关注微吧
     * @param int user_id 用户UID
     * @param int id 微吧ID
     * @return bool 是否关注成功 1-成功  0-失败
     */
    public function create()
    {
        $this->user_id = empty($this->user_id) ? $this->mid : $this->user_id;
        $res = D('Weiba', 'weiba')->doFollowWeiba($this->user_id, $this->id);
        if ($res) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * 取消关注微吧
     * @param int user_id 用户UID
     * @param int id 微吧ID
     * @return bool 是否关注成功 1-成功  0-失败
     */
    public function destroy()
    {
        $this->user_id = empty($this->user_id) ? $this->mid : $this->user_id;
        $res = D('Weiba', 'weiba')->unFollowWeiba($this->user_id, $this->id);
        if ($res) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * 帖子列表
     * @param int count 每页显示条数
     * @param int page 显示第几页
     * @param int id 所属微吧ID
     * @return array 帖子列表
     */
    public function get_posts()
    {
        $data = D('Weiba', 'weiba')->get_posts_forapi($this->count, $this->page, $this->id);
        if ($data) {
            return $data;
        } else {
            return array();
        }
    }

    /**
     * 发表帖子
     * @param int id 微吧ID
     * @param varchar title 帖子标题
     * @param varchar content 帖子内容
     * @param int user_id 帖子作者
     */
    public function create_post()
    {
        $this->user_id = empty($this->user_id) ? $this->mid : $this->user_id;
        if (empty($this->id) || empty($this->data['title']) || empty($this->data['content'])) {
            return 0;
        }
        $res = D('WeibaPost', 'weiba')->createPostForApi($this->id, $this->data['title'], $this->data['content'], $this->user_id);
        if ($res) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * 帖子详情
     * @param int id 帖子ID
     * @return array 帖子信息
     */
    public function post_detail()
    {
        $this->user_id = empty($this->user_id) ? $this->mid : $this->user_id;
        $data = D('weiba_post')->where('post_id='.$this->id)->find();
        if (D('weiba_favorite')->where('post_id='.$this->id.' AND uid='.$this->user_id)->find()) {
            $data['favorite'] = 1;
        } else {
            $data['favorite'] = 0;
        }
        if ($data) {
            return $data;
        } else {
            return array();
        }
    }

    /**
     * 帖子评论
     * @param int id 帖子ID
     * @param int count 每页显示条数
     * @param int page 显示第几页
     * @return array 评论列表
     */
    public function comment_list()
    {
        $map['post_id'] = $this->id;
        $data = D('WeibaReply', 'weiba')->getReplyListForApi($map, 'ctime Asc', $this->count, $this->page);
        if ($data) {
            return $data;
        } else {
            return array();
        }
    }

    /**
     * 评论帖子
     * @param int id 帖子ID
     * @param varchar content 评论内容
     * @param int user_id 评论者UID
     * @return bool 是否评论成功 1-成功 0-失败
     */
    public function comment_post()
    {
        if (empty($this->id) || empty($this->data['content'])) {
            return 0;
        }
        $this->user_id = empty($this->user_id) ? $this->mid : $this->user_id;
        $res = D('WeibaReply', 'weiba')->addReplyForApi($this->id, $this->data['content'], $this->user_id);
        if ($res) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * 回复评论
     * @param int id 评论ID
     * @param varchar content 回复内容
     * @param int user_id 回复者UID
     * @return bool 是否评论成功 1-成功 0-失败
     */
    public function reply_comment()
    {
        if (empty($this->id) || empty($this->data['content'])) {
            return 0;
        }
        $this->user_id = empty($this->user_id) ? $this->mid : $this->user_id;
        $res = D('WeibaReply', 'weiba')->addReplyToCommentForApi($this->id, $this->data['content'], $this->user_id);
        if ($res) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * 删除评论
     * @param int id 评论ID
     * @return bool 是否删除成功 1-成功 0-失败
     */
    public function delete_comment()
    {
        if (empty($this->id)) {
            return 0;
        }
        $res = D('WeibaReply', 'weiba')->delReplyForApi($this->id);
        if ($res) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * 我关注的微吧的帖子列表
     * @param int user_id 用户UID
     * @param int count 每页显示条数
     * @param int page 显示第几页
     */
    public function following_posts()
    {
        $this->user_id = empty($this->user_id) ? $this->mid : $this->user_id;
        $data = D('Weiba', 'weiba')->myWeibaForApi($this->count, $this->page, $this->user_id, 'myFollow');
        if ($data) {
            return $data;
        } else {
            return array();
        }
    }

    /**
     * 我发布的帖子列表
     * @param int user_id 用户UID
     * @param int count 每页显示条数
     * @param int page 显示第几页
     */
    public function posteds()
    {
        $this->user_id = empty($this->user_id) ? $this->mid : $this->user_id;
        $data = D('Weiba', 'weiba')->myWeibaForApi($this->count, $this->page, $this->user_id, 'myPost');
        if ($data) {
            return $data;
        } else {
            return array();
        }
    }

    /**
     * 我回复的帖子列表
     * @param int user_id 用户UID
     * @param int count 每页显示条数
     * @param int page 显示第几页
     */
    public function commenteds()
    {
        $this->user_id = empty($this->user_id) ? $this->mid : $this->user_id;
        $data = D('Weiba', 'weiba')->myWeibaForApi($this->count, $this->page, $this->user_id, 'myReply');
        if ($data) {
            return $data;
        } else {
            return array();
        }
    }

    /**
     * 搜索微吧
     * @param varchar keyword 搜索关键字
     * @param int count 每页显示条数
     * @param int page 显示第几页
     * @param int user_id 用户ID
     */
    public function search_weiba()
    {
        $this->user_id = empty($this->user_id) ? $this->mid : $this->user_id;
        $data = D('Weiba', 'weiba')->searchWeibaForApi($this->data['keyword'], $this->count, $this->page, $this->user_id);
        if ($data) {
            return $data;
        } else {
            return array();
        }
    }

    /**
     * 搜索帖子
     * @param varchar keyword 搜索关键字
     * @param int count 每页显示条数
     * @param int page 显示第几页
     */
    public function search_post()
    {
        $data = D('Weiba', 'weiba')->searchPostForApi($this->data['keyword'], $this->count, $this->page);
        if ($data) {
            return $data;
        } else {
            return array();
        }
    }

    /**
     * 收藏帖子
     * @param int id 帖子ID
     */
    public function post_favorite()
    {
        if (empty($this->id)) {
            return 0;
        }
        $res = D('WeibaPost', 'weiba')->favoriteForApi($this->id);
        //return $res;
        if ($res) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * 取消收藏
     * @param int id 帖子ID
     */
    public function post_unfavorite()
    {
        if (empty($this->id)) {
            return 0;
        }
        $res = D('WeibaPost', 'weiba')->unfavoriteForApi($this->id);
        //return $res;
        if ($res) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * 收藏列表
     * @param user_id 用户ID
     */
    public function favorite_list()
    {
        $this->user_id = empty($this->user_id) ? $this->mid : $this->user_id;
        $data = D('Weiba', 'weiba')->myWeibaForApi($this->count, $this->page, $this->user_id, 'myFavorite');
        if ($data) {
            return $data;
        } else {
            return array();
        }
    }
}
