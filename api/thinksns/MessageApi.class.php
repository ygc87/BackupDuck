<?php

class MessageApi extends Api
{
    //上传资源
    public function upload()
    {
        $d['attach_type'] = 'message_image';
        $d['upload_type'] = 'image';
        $info = model('Attach')->upload($d, $d);
        $data['attach_ids'] = getSubByKey($info['info'], 'attach_id');
        if (!$data['attach_ids']) {
            return false;
        }
        $data['forApi'] = 1;
    }
    // 回复私信
    public function reply()
    {
        if ($_POST) {
            $d['attach_type'] = 'message_image';
            $d['upload_type'] = 'image';
            $info = model('Attach')->upload($d, $d);
            $data['attach_ids'] = getSubByKey($info['info'], 'attach_id');
        } else {
            if (empty($this->data['id']) || empty($this->data['content'])) {
                return false;
            }
        }
        ////隐私判断开始
        $message['member'] = model('Message')->getMessageMembers(intval($this->data['id']), 'member_uid');
        $message['to'] = array();
        // 添加发送用户ID
        foreach ($message['member'] as $v) {
            $this->mid != $v['member_uid'] && $message['to'][] = $v;
        }
        $UserPrivacy = model('UserPrivacy')->getPrivacy($this->mid, $message['to'][0]['user_info']['uid']);
        if ($UserPrivacy['message'] != 0) {
            return 0;
        }
        ////隐私判断结束
        return (int) model('Message')->replyMessage($this->data['id'], $this->data['content'], $this->mid, $data['attach_ids']);
    }
    // 删除私信
    public function destroy()
    {
        return (int) model('Message')->deleteMessageByListId($this->mid, t($this->data['list_id']));
    }
    public function del_notify()
    {
        $res = model('Notify')->deleteNotify($this->data['id']);
        if ($res) {
            return 1;
        } else {
            return 0;
        }
    }
}
