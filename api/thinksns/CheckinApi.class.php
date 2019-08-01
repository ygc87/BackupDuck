<?php

/**
 * 频道应用API接口
 * @author zivss guolee226@gmail.com
 * @version  TS3.0
 */
class CheckinApi extends Api
{
    /**
     * 获取所有频道分类
     * @return json 所有频道分类
     */
    public function get_check_info()
    {
        $uid = $this->mid;
        $data = model('Cache')->get('check_info_' . $uid . '_' . date('Ymd'));

        if (!$data) {
            $map['uid'] = $uid;

            $map['ctime'] = array('gt', strtotime(date('Ymd')));

            $res = D('check_info')->where($map)->find();

            //是否签到
            $data['ischeck'] = $res ? true : false;

            $checkinfo = D('check_info')->where('uid=' . $uid)->order('ctime desc')->limit(1)->find();
            if ($checkinfo) {
                if ($checkinfo['ctime'] > (strtotime(date('Ymd')) - 86400)) {
                    $data['con_num'] = $checkinfo['con_num'];
                } else {
                    $data['con_num'] = 0;
                }
                $data['total_num'] = $checkinfo['total_num'];
            } else {
                $data['con_num'] = 0;
                $data['total_num'] = 0;
            }
            $data['day'] = date('m.d');
            $checkin_record=D('credit_record')->where('uid='.$this->mid.' and cid=236')->order('ctime desc')->find();
            $msg=$checkin_record['detail'];
            $msg=json_decode($msg,true);
            $msg='签到赠送'.$msg['score'].'积分';
            $data['msg']=$msg;
            model('Cache')->set('check_info_' . $uid . '_' . date('Ymd'), $data);
        }

        return $data;
    }

    /**
     * 获取指定分类下的分享
     * @return json 指定分类下的分享
     */
    public function checkin()
    {
        $uid = $this->mid;

        $map['ctime'] = array('gt', strtotime(date('Ymd')));

        $map['uid'] = $uid;

        $ischeck = D('check_info')->where($map)->find();
        //未签到
        if (!$ischeck) {
            //清理缓存
            model('Cache')->set('check_info_' . $uid . '_' . date('Ymd'), null);

            $map['ctime'] = array('lt', strtotime(date('Ymd')));
            $last = D('check_info')->where($map)->order('ctime desc')->find();
            $data['uid'] = $uid;
            $data['ctime'] = $_SERVER['REQUEST_TIME'];
            //是否有签到记录
            if ($last) {
                //是否是连续签到
                if ($last['ctime'] > (strtotime(date('Ymd')) - 86400)) {
                    $data['con_num'] = $last['con_num'] + 1;
                } else {
                    $data['con_num'] = 1;
                }
                $data['total_num'] = $last['total_num'] + 1;
            } else {
                $data['con_num'] = 1;
                $data['total_num'] = 1;
            }

            if (D('check_info')->add($data)) {
//            model('Credit')->setUserCredit($uid, 'check_in', 1, array(
//                'user' => $GLOBALS['ts']['user']['uname'],
//                'content' => '签到',
//            ));
                $credit = mt_rand(1, 3);
                $vipinfo = model('User')->where('uid=' . $this->mid)->Field('is_vip,vip_endtime')->find();

                if ($vipinfo['is_vip'] && $vipinfo['vip_endtime'] > time()) {
                    $credit = $credit * 2;
                }
                model('Credit')->setUserCredit($uid, array('name' => 'check_in', 'score' => $credit), 1, array(
                    'user' => $GLOBALS['ts']['user']['uname'],
                    'content' => '签到',
                ));
                //更新连续签到和累计签到的数据
                $connum = D('user_data')->where('uid=' . $uid . " and `key`='check_connum'")->find();
                if ($connum) {
                    $connum = D('check_info')->where('uid=' . $uid)->getField('max(con_num)');
                    D('user_data')->setField('value', $connum, "`key`='check_connum' and uid=" . $uid);
                    D('user_data')->setField('value', $data['total_num'], "`key`='check_totalnum' and uid=" . $uid);
                } else {
                    $connumdata['uid'] = $uid;
                    $connumdata['value'] = $data['con_num'];
                    $connumdata['key'] = 'check_connum';
                    D('user_data')->add($connumdata);

                    $totalnumdata['uid'] = $uid;
                    $totalnumdata['value'] = $data['total_num'];
                    $totalnumdata['key'] = 'check_totalnum';
                    D('user_data')->add($totalnumdata);
                }
                $result = $this->get_check_info();
                $result['msg']='签到成功，赠送'.$credit.'积分';
                $result['status']=1;
                return $result;
            }else{
                return array('status'=>0,'msg'=>'签到失败');
            }

        }else{
            return array('status'=>0,'msg'=>'您今日已签到');
        }


    }
}
