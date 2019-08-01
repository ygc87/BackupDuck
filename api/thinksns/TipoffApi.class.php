<?php
/**
 *
 * @author jason
 *
 */
class TipoffApi extends Api
{
    /**
     * 公共爆料列表
     * @param int user_id 用户UID
     * @param int count 每页显示条数
     * @param int page 显示第几页
     * @param int order 排序（1:时间，2:评论，3:奖金）
     * @return array 微吧列表
     */
    public function get_tipofflist()
    {
        $this->user_id = empty($this->user_id) ? $this->mid : $this->user_id;
        $data = D('Tipoff', 'tipoff')->get_tipofflist_forapi($this->since_id, $this->max_id, $this->count, $this->page, $this->data['order'], $this->user_id, 1);
        if ($data) {
            return $data;
        } else {
            return array();
        }
    }

    /**
     * 用户爆料列表
     * @param int user_id 用户UID
     * @param int count 每页显示条数
     * @param int page 显示第几页
     * @param int order 排序（1:时间，2:评论，3:奖金）
     * @return array 微吧列表
     */
    public function get_tipofflistbyuid()
    {
        $this->user_id = empty($this->user_id) ? $this->mid : $this->user_id;
        if (!$this->user_id) {
            return false;
        }
        $data = D('Tipoff', 'tipoff')->get_tipofflist_forapi($this->since_id, $this->max_id, $this->count, $this->page, $this->data['order'], $this->user_id, 2);
        if ($data) {
            return $data;
        } else {
            return array();
        }
    }

    /**
     * 用户管理爆料列表
     * @param int user_id 用户UID
     * @param int count 每页显示条数
     * @param int page 显示第几页
     * @param int order 排序（1:时间，2:评论，3:奖金）
     * @return array 微吧列表
     */
    public function get_tipofflistbyuidmanage()
    {
        $this->user_id = empty($this->user_id) ? $this->mid : $this->user_id;
        if (!$this->user_id) {
            return false;
        }
        if (!(model('UserGroup')->isAdmin($this->user_id) || CheckPermission('tipoff_admin', 'tipoff_setStatus'))) {
            return false;
        }
        $data = D('Tipoff', 'tipoff')->get_tipofflist_forapi($this->since_id, $this->max_id, $this->count, $this->page, $this->data['order'], $this->user_id, 3);
        if ($data) {
            return $data;
        } else {
            return array();
        }
    }

    /**
     * 爆料信息详情
     * @param int id 爆料ID
     * @return array 爆料信息数组
     */
    public function get_tipoffbyid()
    {
        if (empty($this->data['id'])) {
            return array();
        }
        $this->user_id = empty($this->user_id) ? $this->mid : $this->user_id;
        $data = D('Tipoff', 'tipoff')->get_tipoffbyid_forapi($this->data['id'], $this->user_id);
        if ($data) {
            return $data;
        } else {
            return array();
        }
    }

    /**
     * 提交爆料
     * @param int user_id 用户UID
     * @param string source_url 来源网址
     * @param string key 来源网址密匙
     * @param string content 爆料内容
     * @param string contact 联系方式
     * @return bool 是否发布成功
     */
    public function post_tipoff($data)
    {
        //授权验证
        $isValid = $this->get_source_key($this->data['key'], $this->data['source_url']);
        if ($isValid['res']) {
            $data['source_url'] = $this->data['source_url'];
            $data['source_title'] = $isValid['source_title'];
        } else {
            return false;
        }
        $this->user_id = empty($this->user_id) ? $this->mid : $this->user_id;
        $data['content'] = h($this->data['content']);
        $data['content'] = preg_replace("/@(.+?)([\s|:]|$)/is", '', $data['content']);  //
        if (!$data['content']) {
            return false;
        }
        $data['contact'] = h($this->data['contact']);
        if (!$this->user_id && !$data['contact']) {
            return false;
        }
        if (!$this->user_id) {
            $data['uid'] = D('Tipoff', 'tipoff')->getAnonyUid();
        } else {
            $data['uid'] = $this->user_id;
        }
        $data['create_time'] = time();
        $data['publish_time'] = $data['create_time'];
        //爆料对象
        $deal_users = D('Tipoff', 'tipoff')->getMentionUsers(h($this->data['content']));
        $data['deal_users'] = isset($deal_users) ? implode(',', $deal_users) : '';

        $res = D('Tipoff')->add($data);
        if ($res) {
            if ($data['deal_users']) {
                $config['source_url'] = U('tipoff/Index/detail', array('id' => $res));
                foreach ($deal_users as $v) {
                    model('Notify')->sendNotify($v, 'tipoff_deal', $config);
                }
            }

            return true;
        } else {
            return false;
        }
    }

    /**
     * 授权验证
     * @param string key 来源网站密匙
     * @param string key 来源网站网址
     * @return bool 是否有效
     */
    public function get_source_key($key, $source_url)
    {
        $map['source_url'] = $source_url;
        $map['source_key'] = $key;
        if ($res = D('tipoff_source')->where($map)->find()) {
            if ($res['status'] == 1) {
                return array('res' => true, 'source_title' => $res['title']);
            } else {
                return array('res' => false);
            }
        } else {
            return array('res' => false);
        }
    }

    public function upload()
    {
        $d['attach_type'] = 'tipoff';
        $info = model('Attach')->upload($d);
        if ($info['status']) {
            $data['type'] = 'file'; //图片类型分享
            $data['attach_ids'] = '|'.$info['info'][0]['attach_id'].'|';

            return $this->post_tipoff($data);
        } else {
            return false; //上传失败
        }
    }

    /**
     * 设置爆料状态
     */
    public function setTipoffStatus()
    {
        $this->user_id = empty($this->user_id) ? $this->mid : $this->user_id;
        if (!$this->user_id) {
            return false;
        }
        if ($this->checkPermission($this->user_id, $this->data['id'])) {
            $data['status'] = $this->data['status'];
            $data['info'] = $this->data['info'];
            $res = D('Tipoff', 'tipoff')->where('tipoff_id='.$this->data['id'])->save($data);
            if ($res !== false) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * 验证用户是否有处理某个爆料的权限
     */
    public function checkPermission($uid, $id)
    {
        if (model('UserGroup')->isAdmin($this->mid)) {
            return true;
        } else {
            $tipoffDetail = D('Tipoff', 'tipoff')->find($id);
            if ($tipoffDetail['deal_users']) {
                if (in_array('$uid', explode(',', $tipoffDetail['deal_users']))) {
                    return true;
                } else {
                    return false;
                }
            } else {
                if (in_array('in', D('Tipoff', 'tipoff')->getDealUsers())) {
                    return true;
                } else {
                    return false;
                }
            }
        }
    }
}
