<?php
/*
 * 找伙伴
 */
class FindPeopleApi extends Api
{
    /**
     * 按中心查找
     * @param  array $uids 用户ID数组
     * @return array 用户相关数组
     */
    public function getAll()
    {
        $count = $this->count ? $this->count : 20;
        $page = $this->page ? intval($this->page) : 1;
        $start = ($page - 1) * $count;
        $end = $count;

        $this->user_id = empty($this->user_id) ? $this->mid : $this->user_id;

        $key = $this->data['key'] ? t($this->data['key']) : null;

        $map['uid'] = array('NEQ', $this->user_id);
        if ($key) {
            $map['uname'] = array('LIKE', '%'.$key.'%');
        }

        $list = model('User')->field('uid')->where($map)->limit("{$start},{$end}")->order('uid DESC')->findAll();
        // dump(D()->getLastSql());
        // dump($list);exit;
        $uids = getSubByKey($list, 'uid');
        // return $this->formatByFirstLetter($uids);
        return $this->getUserInfos($uids);
    }

    /**
     * 查找周边
     * @param  array $uids 用户ID数组
     * @return array 用户相关数组
     */
    public function getByLocation()
    {
        // $key = $this->data['key']?t($this->data['key']):null;
        // $uids = Model('FindPeople')->getByCenterForAPI($this->user_id, $this->count, $this->page, $key);
        // return $this->getUserInfos($uids);

        //经度latitude
        //纬度longitude
        //距离distance
        $latitude = floatval($this->data['latitude']);
        $longitude = floatval($this->data['longitude']);
        //根据经度、纬度查询周边用户 1度是 111 公里
        //根据ts_mobile_user 表查找，经度和纬度在一个范围内。
        //latitude < ($latitude + 1) AND latitude > ($latitude - 1)
        //longitude < ($longitude + 1) AND longitude > ($longitude - 1)
        $limit = 20;
        $this->data['limit'] && $limit = intval($this->data['limit']);
        $map['last_latitude'] = array('between', ($latitude - 1).','.($latitude + 1));
        $map['last_longitude'] = array('between', ($longitude - 1).','.($longitude + 1));
        $map['uid'] = array('neq', $this->mid);
        $data = D('mobile_user')->where($map)->field('uid')->findpage($limit);
        $data['data'] = $this->getUserInfos(getSubByKey($data['data'], 'uid'), $data['data']);

        return $data['data'] ? $data : 0;
    }

    /**
     * 查找官方用户
     * @param  array $uids 用户ID数组
     * @return array 用户相关数组
     */
    public function getByOfficial()
    {
        $list = D('People', 'people')->getPeople('', 'official');

        return $list['data'];
    }

    /**
     * 根据手机号返回用户关注状态
     * @param int user_id 当前用户uid
     * @param string tel 以逗号连接的手机号码串
     * @return array
     */
    public function get_follow_state_by_tel()
    {
        $this->user_id = empty($this->user_id) ? $this->mid : $this->user_id;
        $tel_array = explode(',', $this->data['tel']);
        $data = array();
        if ($tel_array) {
            foreach ($tel_array as $k => $v) {
                if ($uid = model('User')->where(array('login' => t($v)))->getField('uid')) {
                    $data[$v]['uid'] = $uid;
                    if (D('user_follow')->where(array('uid' => $this->user_id, 'fid' => $uid))->count()) {   //我关注了TA
                        $data[$v]['following'] = 1;
                    } else {
                        $data[$v]['following'] = 0;
                    }
                    if (D('user_follow')->where(array('uid' => $uid, 'fid' => $this->user_id))->count()) {   //TA关注了我
                        $data[$v]['followed'] = 1;
                    } else {
                        $data[$v]['followed'] = 0;
                    }
                } else {
                    $data[$v]['uid'] = 0;
                }
            }
        }

        return $data;
    }

    /**
     * 获取用户相关信息
     * @param  array $uids 用户ID数组
     * @return array 用户相关数组
     */
    public function getUserInfos($uids, $data)
    {
        // 获取用户基本信息
        $userInfos = model('User')->getUserInfoByUids($uids);
        $userDataInfo = model('UserData')->getUserKeyDataByUids('follower_count', $uids);

        // 获取其他用户统计数据
            // 获取关注信息
        $followStatusInfo = model('Follow')->getFollowStateByFids($GLOBALS['ts']['mid'], $uids);
        // 获取用户组信息
        $userGroupInfo = model('UserGroupLink')->getUserGroupData($uids);
        if (empty($data)) {
            foreach ($uids as $k => $v) {
                $data[$k]['uid'] = $v;
            }
        }

        // 组装数据
        foreach ($data as &$value) {
            $value = array_merge($value, $userInfos[$value['uid']]);
            $value['user_data'] = $userDataInfo[$value['uid']];
            $value['follow_state'] = $followStatusInfo[$value['uid']];
            $value['user_group'] = $userGroupInfo[$value['uid']];
        }

        return $data;
    }
    public function getUserInfo($uid, $data)
    {
        // 获取用户基本信息
        $userInfos = model('User')->getUserInfo($uid);
        $userDataInfo = model('UserData')->getUserKeyDataByUids('follower_count', array($uid));

        dump($userDataInfo);
        exit;
        // 获取其他用户统计数据
            // 获取关注信息
        $followStatusInfo = model('Follow')->getFollowStateByFids($GLOBALS['ts']['mid'], array($uid));
        // 获取用户组信息
        $userGroupInfo = model('UserGroupLink')->getUserGroupData($uids);
        if (empty($data)) {
            foreach ($uids as $k => $v) {
                $data[$k]['uid'] = $v;
            }
        }

        // 组装数据
        foreach ($data as &$value) {
            $value = array_merge($value, $userInfos[$value['uid']]);
            $value['user_data'] = $userDataInfo[$value['uid']];
            $value['follow_state'] = $followStatusInfo[$value['uid']];
            $value['user_group'] = $userGroupInfo[$value['uid']];
        }

        return $data;
    }

    public function formatByFirstLetter($uids)
    {
        $peoplelist = array('A' => array(), 'B' => array(), 'C' => array(), 'D' => array(), 'E' => array(), 'F' => array(), 'G' => array(), 'H' => array(), 'I' => array(), 'J' => array(), 'K' => array(), 'L' => array(), 'M' => array(), 'N' => array(), 'O' => array(), 'P' => array(), 'Q' => array(), 'R' => array(), 'S' => array(), 'T' => array(), 'U' => array(), 'V' => array(), 'W' => array(), 'X' => array(), 'Y' => array(), 'Z' => array());
        $list = array();
        foreach ($uids as $k => $v) {
            $user_info = $this->getUserInfos(array($v));
            $list[$k] = $user_info[0];
            $first_letter = getFirstLetter($list[$k]['uname']);
            switch ($first_letter) {
                case 'A':
                    $peoplelist['A'][] = $list[$k];
                    break;
                case 'B':
                    $peoplelist['B'][] = $list[$k];
                    break;
                case 'C':
                    $peoplelist['C'][] = $list[$k];
                    break;
                case 'D':
                    $peoplelist['D'][] = $list[$k];
                    break;
                case 'E':
                    $peoplelist['E'][] = $list[$k];
                    break;
                case 'F':
                    $peoplelist['F'][] = $list[$k];
                    break;
                case 'G':
                    $peoplelist['G'][] = $list[$k];
                    break;
                case 'H':
                    $peoplelist['H'][] = $list[$k];
                    break;
                case 'I':
                    $peoplelist['I'][] = $list[$k];
                    break;
                case 'J':
                    $peoplelist['J'][] = $list[$k];
                    break;
                case 'K':
                    $peoplelist['K'][] = $list[$k];
                    break;
                case 'L':
                    $peoplelist['L'][] = $list[$k];
                    break;
                case 'M':
                    $peoplelist['M'][] = $list[$k];
                    break;
                case 'N':
                    $peoplelist['N'][] = $list[$k];
                    break;
                case 'O':
                    $peoplelist['O'][] = $list[$k];
                    break;
                case 'P':
                    $peoplelist['P'][] = $list[$k];
                    break;
                case 'Q':
                    $peoplelist['Q'][] = $list[$k];
                    break;
                case 'R':
                    $peoplelist['R'][] = $list[$k];
                    break;
                case 'S':
                    $peoplelist['S'][] = $list[$k];
                    break;
                case 'T':
                    $peoplelist['T'][] = $list[$k];
                    break;
                case 'U':
                    $peoplelist['U'][] = $list[$k];
                    break;
                case 'V':
                    $peoplelist['V'][] = $list[$k];
                    break;
                case 'W':
                    $peoplelist['W'][] = $list[$k];
                    break;
                case 'X':
                    $peoplelist['X'][] = $list[$k];
                    break;
                case 'Y':
                    $peoplelist['Y'][] = $list[$k];
                    break;
                case 'Z':
                    $peoplelist['Z'][] = $list[$k];
                    break;
            }
            unset($first_letter);
        }

        return $peoplelist;
    }
}
