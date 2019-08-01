<?php

/**
{
    //获取用户信息
    {
        if (empty($this->data['uid'])) {
            return false;
        }

        $content = $this->getContentFormDiscuz('profile', '&uid='.$this->data['uid']);
        $content = $content['space'];

        $data['uid'] = $content['uid'];
        $data['username'] = $content['username'];
        $data['avatar_small'] = $this->discuzURl.'/uc_server/avatar.php?uid='.$content['uid'].'&size=small';
        $data['avatar_middle'] = $this->discuzURl.'/uc_server/avatar.php?uid='.$content['uid'].'&size=middle';
        $data['avatar_big'] = $this->discuzURl.'/uc_server/avatar.php?uid='.$content['uid'].'&size=big';
        $data['thread_count'] = $content['threads'];
        $data['following_count'] = $content['following'];
        $data['follower_count'] = $content['follower'];
        $data['feed_count'] = $content['feeds'];

        return $data;
    }

    /**
    {
    }

    /**
    {
        if (empty($this->mid) || empty($this->user_id)) {
            return 0;
        }

        $r = uc_friend_add($this->mid, $this->user_id);

        return $r;
    }

    /**
    {
        if (empty($this->mid) || empty($this->user_id)) {
            return 0;
        }

        $r = uc_friend_delete($this->mid, $this->user_id);

        return $r;
    }

    public function user_friend()
    {
        return $this->_follows(1);
    }

/*	direction: 
{
    $this->user_id = empty($this->user_id) ? $this->mid : $this->user_id;

    $page = $this->data['page'] ? $this->data['page'] : 1;

    $limit = $this->data['limit'] ? $this->data['limit'] : 20;

    $total = uc_friend_totalnum($this->user_id, $direction);

    $list = uc_friend_ls($this->user_id, $page, $limit, $total, $direction);

    return $list;
}

    //获取我收藏的帖子
    {
        $page = $this->data['page'] ? $this->data['page'] : 1;
        $content = $this->getContentFormDiscuz('myfavthread', '&page='.$page);

        return $content;
    }

    //获取我收藏的版块
    {
        $content = $this->getContentFormDiscuz('myfavforum');

        return $content;
    }

    //获取我的帖子
    {
        $page = $this->data['page'] ? $this->data['page'] : 1;

        $content = $this->getContentFormDiscuz('mythread', '&page='.$page);

        return $content;
    }

    //获取我的私人消息
    {
        $page = $this->data['page'] ? $this->data['page'] : 1;

        $content = $this->getContentFormDiscuz('mypm', '&page='.$page);

        return $content;
    }

    //获取我的公共消息
    {
        $page = $this->data['page'] ? $this->data['page'] : 1;

        $content = $this->getContentFormDiscuz('publicpm', '&page='.$page);

        return $content;
    }

    //加发消息人
    {
        $page = $this->data['page'] ? $this->data['page'] : 1;
        $limit = $this->data['limit'] ? $this->data['limit'] : 20;
        $uid = $this->data['uid'] ? $this->data['uid'] : $this->mid;

        $content = $this->getContentFormDiscuz('friend', '&page='.$page.'&limit='.$limit.'&uid='.$uid);

        return $content;
    }

    //发消息操作
    {
        $touid = $this->data['touid'];
        $pmid = $this->data['pmid'];
        $opt = '&pmsubmit=yes&touid='.$touid.'&pmid='.$pmid;

        $content = $this->getContentFormDiscuz('sendpm', $opt, 'POST');

        return $content;
    }

    //退出登录
    {
    }

    //登录
    {
    }
}