<?php

class OauthApi extends Api
{
    //获取RequestKey
    public function request_key()
    {
        return array($this->getRequestKey());
    }

    //获取8位RequestKey
    private function getRequestKey()
    {
        return 'THINKSNS';    //不要修改
    }

    //认证方法
    public function authorize()
    {
        if (!function_exists('mcrypt_module_open')) {
            $message['message'] = '服务器错误:缺少加密扩展mcrypt';
            $message['code'] = '00000';
            exit(json_encode($message));
        }

        $_REQUEST = array_merge($_GET, $_POST);
        dump($_REQUEST);
        if (!empty($_REQUEST['uid']) && !empty($_REQUEST['passwd'])) {
            $_REQUEST['uid'] = addslashes($_REQUEST['uid']);
            $_REQUEST['passwd'] = addslashes($_REQUEST['passwd']);


            $username = t($_REQUEST['uid']);
            $password = t($_REQUEST['passwd']);

            //判断帐号类型
            if ($this->isValidEmail($username)) {
                $map = "email='{$username}' AND is_del=0";
            } else {
                $map = "(login = '{$username}' or uname='{$username}') AND is_del=0";
            }
           

            //根据帐号获取用户信息
            $user = model('User')->where($map)->field('uid,email,uname,password,login_salt,is_audit,is_active')->find();
            //判断密码是否正确
            if ($user && md5($password.$user['login_salt']) == $user['password']) {

                //如果未激活提示未激活
                if ($user['is_audit'] != 1) {
                    $message['message'] = '您的帐号尚未通过审核';
                    $message['code'] = '00002';
                    exit(json_encode($message));
                }
                if ($user['is_active'] != 1) {
                    $message['message'] = '您的帐号尚未激活,请进入邮箱激活';
                    $message['code'] = '00002';
                    exit(json_encode($message));
                }
                //记录token
                if ($login = D('')->table(C('DB_PREFIX').'login')->where('uid='.$user['uid']." AND type='location'")->find()) {
                    $data['oauth_token'] = $login['oauth_token'];
                    $data['oauth_token_secret'] = $login['oauth_token_secret'];
                    $data['uid'] = $user['uid'];
                    $data['uname'] = $user['uname'];
                } else {
                    $data['oauth_token'] = getOAuthToken($user['uid']);
                    $data['oauth_token_secret'] = getOAuthTokenSecret();
                    $data['uid'] = $user['uid'];
                    $savedata['type'] = 'location';
                    $savedata = array_merge($savedata, $data);
                    D('')->table(C('DB_PREFIX').'login')->add($savedata);
                }

                return $data;
            } else {
                $this->verifyError();
            }
        } else {
            $this->verifyError();
        }
    }

    //注销帐号，刷新token
    public function logout()
    {
        $_REQUEST = array_merge($_GET, $_POST);
        if (!empty($_REQUEST['uid'])) {
            //帐号、密码通过加密
            $username = desdecrypt(t($_REQUEST['uid']), $this->getRequestKey());
        }
        //判断帐号类型
        if ($this->isValidEmail($username)) {
            $map['email'] = $username;
        } else {
            $map['uname'] = $username;
        }
        //判断密码是否正确
        $user = model('User')->where($map)->field('uid')->find();
        if ($user) {
            $data['oauth_token'] = getOAuthToken($user['uid']);
            $data['oauth_token_secret'] = getOAuthTokenSecret();
            $data['uid'] = $user['uid'];
            D('')->table(C('DB_PREFIX').'login')->where('uid='.$user['uid']." AND type='location'")->save($data);

            return 1;
        } else {
            return 0;
        }
    }

    /**
     * 发送短信验证码找回密码
     */
    public function send_findpwd_code()
    {
        $smsDao = model('Sms');
        $login = t($this->data['login']);
        if (!$this->isValidPhone($login)) {
            $login = model('User')->where("uname='".$login."'")->getField('login');
            if (!$login) {
                return array('status' => 0, 'msg' => '该帐号尚未注册');
            }
        }
        $status = $smsDao->sendPasswordCode($login);
        $msg = $smsDao->getError();
        $return = array('status' => intval($status), 'msg' => $msg);

        return $return;
    }

    /**
     * 保存用户密码
     * @return number
     */
    public function save_user_pwd()
    {
        $login = t($this->data['login']);
        if (!$this->isValidPhone($login)) {
            $login = model('User')->where("uname='".$login."'")->getField('login');
            if (!$login) {
                return array('status' => 0, 'msg' => '该帐号尚未注册');
            }
        }
        $regCode = intval($this->data['code']);
        $pwd = $this->data['pwd'];
        $smsDao = model('Sms');
        if ($smsDao->checkPasswordCode($login, $regCode)) {
            if ($smsDao->sendPassword($login, $pwd) !== false) {
                $return = array('status' => 1, 'msg' => '修改成功');
            } else {
                $return = array('status' => 0, 'msg' => '修改失败');
            }
        } else {
            $msg = $smsDao->getError();
            $return = array('status' => 0, 'msg' => $msg);
        }

        return $return;
    }

    //验证字符串是否是email
    public function isValidEmail($email)
    {
        return preg_match("/[_a-zA-Z\d\-\.]+@[_a-zA-Z\d\-]+(\.[_a-zA-Z\d\-]+)+$/i", $email) !== 0;
    }

    //验证字符串是否是email
    public function isValidPhone($phone)
    {
        return preg_match("/^[1][358]\d{9}$/", $phone) !== 0;
    }

    public function checkRegisterCode()
    {
        $login = t($this->data['login']);
        $regCode = intval($this->data['regCode']);

        if (!model('Sms')->checkRegisterCode($login, $regCode)) {
            $return = array('status' => 0, 'msg' => '验证码错误');
        } else {
            $return = array('status' => 1, 'msg' => '验证通过');
        }

        return $return;
    }

    public function register_type()
    {
        $registerConfig = model('Xdata')->get('admin_Config:register');

        return $registerConfig['account_type'];
    }

    public function register()
    {
        error_log('---- thinksns OauthApi ----',3,'./log/register.log');
        $return = array();
        $regmodel = model('Register');
        $registerConfig = model('Xdata')->get('admin_Config:register');

        //昵称、密码、邮箱、手机号、性别，如果不正确返回错误信息
        $uname = t($this->data['uname']);
        $sex = intval($this->data['sex']);
        $password = $this->data['password'];
        $email = t($this->data['email']);
        $phone = t($this->data['phone']);
        $invite_code=t($this->data['invite_code']);
        $account=t($this->data['zhanghao']);
        if ($invite_code){
            $invite_user_exsits=model('User')->where('uid='.$invite_code)->count();//邀请人是否存在
            if (!$invite_user_exsits){
                $return = array('status' => 0, 'msg' => '邀请人不存在');
                return $return;
            }
        }

        $regCode = intval($this->data['regCode']);
        $data = array(
            'email' => $email,
        );
        //需要判断邮箱，用户名是否注册
        $result = D('User')->where($data)->count();
        error_log('---- thinksns OauthApi1 ----'.$result,3,'./log/EMAIL11.log');
        if($result > 0) {
            error_log('---- thinksns OauthApi1 ----',3,'./log/registerIN1.log');
            $return = array('status' => 0, 'msg' => '该邮箱已经被注册');
            return $return;
        }
        //需要判断用户名，用户名是否注册
        $result1 = model('User')->where("uname='{$uname}'")->count();
        error_log('---- thinksns OauthApi1 ----'.$result,3,'./log/UNAME11.log');
        if($result1 > 0) {
            error_log('---- thinksns OauthApi2 ----',3,'./log/registerIN2.log');
            $return = array('status' => 0, 'msg' => '该用户名已经被注册');
            return $return;
        }
        if ($account){

            if(strpos($account,' ')!==false){
                $return = array('status' => 0, 'msg' => '帐号不能包含空格');
                return $return ;
            }
            if (strlen($account)<6||strlen($account)>20){
                $return = array('status' => 0, 'msg' => '帐号长度必须大于6并且小于20');
                return $return ;
            }
            $match=preg_match('/[A-Za-z0-9]{6,20}/',$account);
            if (!$match){
                $return = array('status' => 0, 'msg' => '帐号只能包含数字和字母，不能包含其它字符');
                return $return ;
            }
            if (model('User')->where("zhanghao='{$account}'")->find()){
                $return = array('status' => 0, 'msg' => '帐号已经被注册');
                return $return;
            }
        }
        //邮箱验证
        if ($email && !$regmodel->isValidEmail($email)) {
            $msg = $regmodel->getLastError();
            $return = array('status' => 0, 'msg' => $msg);

            return $return;
        }
        //手机号验证
        if ($phone) {
            //测试用，暂时去掉
            // if ( !model('Sms')->checkRegisterCode( $phone , $regCode ) ){
            // 	$return = array('status'=>0, 'msg'=>'验证码错误');
            // }
            if (!$regmodel->isValidPhone($phone)) {
                $msg = $regmodel->getLastError();
                $return = array('status' => 0, 'msg' => $msg);

                return $return;
            }
        }
        //用户名验证
//        if (!$regmodel->isValidName($uname)) {
//            $msg = $regmodel->getLastError();
//            $return = array('status' => 0, 'msg' => $msg);
//
//            return $return;
//        }
        // 默认不准使用的昵称
        $protected_name = array('name', 'uname', 'admin', 'profile', 'space');
        $site_config = model('Xdata')->get('admin_Config:site');
        !empty($site_config['sys_nickname']) && $protected_name = array_merge($protected_name, explode(',', $site_config['sys_nickname']));
        // 预保留昵称
        foreach ($protected_name as $k => $v) {
            if (strstr($uname, $v)) {
                $msg = L('PUBLIC_NICKNAME_RESERVED');                // 抱歉，该昵称不允许被使用
                $return = array('status' => 0, 'msg' => $msg);

                return $return;
            }
        }
        //密码验证
        if (!$regmodel->isValidPassword($password, $password)) {
            $msg = $regmodel->getLastError();
            $return = array('status' => 0, 'msg' => $msg);

            return $return;
        }

        $login_salt = rand(11111, 99999);

        //如果需要激活，提示激活后才能使用
        //如果需要审核，给用户提示审核后才能登录
        $map['device_uuid']=$this->data['device_uuid']?$this->data['device_uuid']:'';
        $map['uname'] = $uname;
        $map['invite_code']=$invite_code;
        $map['sex'] = $sex;
        $map['login_salt'] = $login_salt;
        $map['password'] = md5(md5($password).$login_salt);
        $email && $map['login'] = $map['email'] = $email;
        $phone && $map['login'] = $phone;
        $account&&$map['zhanghao']=$account;
        $map['ctime'] = time();
        // 审核状态： 0-需要审核；1-通过审核
        $map['is_audit'] = $registerConfig['register_audit'] ? 0 : 1;
        if ($phone) {
            $map['is_active'] = 1;
        } else {
            $map['is_active'] = $registerConfig['need_active'] ? 0 : 1;
        }
        $map['is_init'] = 1; //手机端不需要初始化步骤
        $map['first_letter'] = getFirstLetter($uname);
        $map['reg_ip']=$this->getRegIp();
        //如果包含中文将中文翻译成拼音
        if (preg_match('/[\x7f-\xff]+/', $map['uname'])) {
            //昵称和呢称拼音保存到搜索字段
            $map['search_key'] = $map['uname'].' '.model('PinYin')->Pinyin($map['uname']);
        } else {
            $map['search_key'] = $map['uname'];
        }
        $user=model('User');
        $uid = $user->add($map);

        if ($uid) {

            //第三方登录数据写入
            if (isset($this->data['type'])) {
                $other['oauth_token'] = addslashes($this->data['access_token']);
                $other['oauth_token_secret'] = addslashes($this->data['refresh_token']);
                $other['type'] = addslashes($this->data['type']);
                $other['type_uid'] = addslashes($this->data['type_uid']);
                $other['uid'] = $uid;
                M('login')->add($other);
            }

            // 添加至默认的用户组
            $userGroup = empty($registerConfig['default_user_group']) ? C('DEFAULT_GROUP_ID') : $registerConfig['default_user_group'];
            model('UserGroupLink')->domoveUsergroup($uid, implode(',', $userGroup));

            // 添加双向关注用户
            $eachFollow = $registerConfig['each_follow'];
            if (!empty($eachFollow)) {
                model('Follow')->eachDoFollow($uid, $eachFollow);
            }
            // 添加默认关注用户
            $defaultFollow = $registerConfig['default_follow'];
            $defaultFollow = array_diff(explode(',', $defaultFollow), explode(',', $eachFollow));
            if (!empty($defaultFollow)) {
                model('Follow')->bulkDoFollow($uid, $defaultFollow);
            }

            if ($email && $map['is_active'] == 0) {   //发送激活邮件
                if (!$regmodel->sendActivationEmail($uid)) {
                    $msg = $regmodel->getLastError();
                    $return = array('status' => 0, 'msg' => $msg);

                    return $return;
                } else {
                    $msg = ',请进入邮箱进行激活';
                }
            }

            //注册成功,给用户添加20积分
            model('Credit')->setUserCredit($uid, 'register');
            $score = model('Credit')->getUserCredit($uid);
            $return = array('status' => 1, 'msg' => '恭喜您注册成功，获得30积分'.$msg);

            return $return;
        } else {
            $return = array('status' => 0, 'msg' => '注册失败');

            return $return;
        }
    }
    function getRegIp()
    {
        if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
            $ip = getenv('REMOTE_ADDR');
        } elseif (isset($_SERVER ['REMOTE_ADDR']) && $_SERVER ['REMOTE_ADDR'] && strcasecmp($_SERVER ['REMOTE_ADDR'], 'unknown')) {
            $ip = $_SERVER ['REMOTE_ADDR'];
        } else {
            $ip = 'unknown';
        }

        return addslashes($ip);
    }
    //发送注册验证码
    public function sendRegisterSMS()
    {

        // return array('status'=>0,'msg'=>'sms error');
        // exit;
        $regmodel = model('Register');
        $phone = t($_POST['phone']);
        $from = isset($_POST['from']) ? t($_POST['from']) : 'mobile';

        if ($phone && !$regmodel->isValidPhone($phone)) {
            $msg = $regmodel->getLastError();
            $return = array('status' => 0, 'msg' => $msg);

            return $return;
        }

        // if(M('User')->where("login='$phone'")->find()){
        // 	return array('status'=>0, 'msg'=>'手机号已被使用');
        // }

        $smsModel = model('Sms');
        $res = $smsModel->sendRegisterCode($phone, $from);
        if ($res) {
            $data['status'] = 1;
            $data['msg'] = '发送成功！';
        } else {
            $data['status'] = 0;
            $data['msg'] = $smsModel->getError();
        }

        return $data;
    }

    public function setDeviceToken()
    {
        $token = t($this->data['token']);

        $uid = D('mobile_token')->where('uid='.intval($_REQUEST['uid'])." and token='".$token."'")->getField('uid');
        $data['mtime'] = time();
        $data['token'] = $token;
        $data['device_type'] = t($this->data['device_type']);
        if ($uid) {
            $data['uid'] = $uid;
            $res = D('mobile_token')->add($data);
        } else {
            $res = D('mobile_token')->where('uid='.$uid)->save($data);
        }

        return $res ? 1 : 0;
    }

    public function getOtherLoginInfo()
    {
        $type = addslashes($this->data['type']);
        $type_uid = addslashes($this->data['type_uid']);
        $access_token = addslashes($this->data['access_token']);
        $refresh_token = addslashes($this->data['refresh_token']);
        $expire = intval($this->data['expire_in']);
        if (!empty($type) && !empty($type_uid)) {
            $user = M('login')->where("type_uid='{$type_uid}' AND type='{$type}'")->find();
            if ($user && $user['uid'] > 0) {
                if ($login = M('login')->where('uid='.$user['uid']." AND type='location'")->find()) {
                    $data['oauth_token'] = $login['oauth_token'];
                    $data['oauth_token_secret'] = $login['oauth_token_secret'];
                    $data['uid'] = $login['uid'];
                } else {
                    $data['oauth_token'] = getOAuthToken($user['uid']);
                    $data['oauth_token_secret'] = getOAuthTokenSecret();
                    $data['uid'] = $user['uid'];
                    $savedata['type'] = 'location';
                    $savedata = array_merge($savedata, $data);
                    $result = M('login')->add($savedata);
                    if (!$result) {
                        return -3;
                    }
                }

                return $data;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    public function feedback_center()
    {
        $feedbacktype = model('Feedback')->getFeedBackType();

        return $feedbacktype;
    }

    public function add_feedback()
    {
        $feedbacktype = D('')->table(C('DB_PREFIX').'feedback_type')->where('type_name = "'.t($this->data['select']).'"')->find();
        $map['feedbacktype'] = $feedbacktype['type_id'];
        $map['feedback'] = t($this->data['textarea']);
        $map['uid'] = $this->mid;
        $map['cTime'] = time();
        $map['type'] = 0;
        //新加数据
        $map['province'] = intval($this->data['province']);
        $map['city'] = intval($this->data['city']);
        $map['city_ids'] = t($this->data['city_ids']);
        $map['city_names'] = t($this->data['city_names']);
        $map['center'] = t($this->data['center']);
        $res = model('Feedback')->add($map);
        if ($res) {
            return 1;
        } else {
            return 0;
        }
    }

    public function getAreaList()
    {
        $category = model('CategoryTree')->setTable('area')->getNetworkList();

        foreach ($category as $key => $value) {
            $output_indexed[] = json_encode($value);
        }

        echo '['.implode(',', $output_indexed).']';
        exit;
    }

    public function getAreaByLetter()
    {
        $area = array('热门城市' => array(), 'A' => array(), 'B' => array(), 'C' => array(), 'D' => array(), 'E' => array(), 'F' => array(), 'G' => array(), 'H' => array(), 'I' => array(), 'J' => array(), 'K' => array(), 'L' => array(), 'M' => array(), 'N' => array(), 'O' => array(), 'P' => array(), 'Q' => array(), 'R' => array(), 'S' => array(), 'T' => array(), 'U' => array(), 'V' => array(), 'W' => array(), 'X' => array(), 'Y' => array(), 'Z' => array());
        $hot_areas = model('Xdata')->get('admin:globalarea');    //热门城市

        foreach ($hot_areas as $k => $v) {
            $hot_areas[$k]['id'] = $v['area_id'];
            $hot_areas[$k]['pid'] = model('Area')->where('area_id='.$v['area_id'])->getField('pid');
        }
        $area['热门城市'] = $hot_areas;

        $category = model('CategoryTree')->setTable('area')->getNetworkList();
        foreach ($category as $key => $value) {
            if ($value['child']) {
                foreach ($value['child'] as $k => $v) {
                    $first_letter = getFirstLetter($v['title']);
                    switch ($first_letter) {
                        case 'A':
                            $area['A'][$v['id']] = $v;
                            break;
                        case 'B':
                            $area['B'][$v['id']] = $v;
                            break;
                        case 'C':
                            $area['C'][$v['id']] = $v;
                            break;
                        case 'D':
                            $area['D'][$v['id']] = $v;
                            break;
                        case 'E':
                            $area['E'][$v['id']] = $v;
                            break;
                        case 'F':
                            $area['F'][$v['id']] = $v;
                            break;
                        case 'G':
                            $area['G'][$v['id']] = $v;
                            break;
                        case 'H':
                            $area['H'][$v['id']] = $v;
                            break;
                        case 'I':
                            $area['I'][$v['id']] = $v;
                            break;
                        case 'J':
                            $area['J'][$v['id']] = $v;
                            break;
                        case 'K':
                            $area['K'][$v['id']] = $v;
                            break;
                        case 'L':
                            $area['L'][$v['id']] = $v;
                            break;
                        case 'M':
                            $area['M'][$v['id']] = $v;
                            break;
                        case 'N':
                            $area['N'][$v['id']] = $v;
                            break;
                        case 'O':
                            $area['O'][$v['id']] = $v;
                            break;
                        case 'P':
                            $area['P'][$v['id']] = $v;
                            break;
                        case 'Q':
                            $area['Q'][$v['id']] = $v;
                            break;
                        case 'R':
                            $area['R'][$v['id']] = $v;
                            break;
                        case 'S':
                            $area['S'][$v['id']] = $v;
                            break;
                        case 'T':
                            $area['T'][$v['id']] = $v;
                            break;
                        case 'U':
                            $area['U'][$v['id']] = $v;
                            break;
                        case 'V':
                            $area['V'][$v['id']] = $v;
                            break;
                        case 'W':
                            $area['W'][$v['id']] = $v;
                            break;
                        case 'X':
                            $area['X'][$v['id']] = $v;
                            break;
                        case 'Y':
                            $area['Y'][$v['id']] = $v;
                            break;
                        case 'Z':
                            $area['Z'][$v['id']] = $v;
                            break;
                    }
                    unset($first_letter);
                }
            }
        }

        return $area;
    }

    public function getCityList()
    {
        $province_id = intval($this->data['pid']) ;
        $category = model('CategoryTree')->setTable('area')->getCategoryList($province_id);

        return $category;
    }

    /**
     * 更新视频对应的分享
     * @param ids transfer_id字符串 多个transfer_id之间用逗号连接 例如 5,6,7
     * @return [type] [description]
     */
    public function video_update_feed()
    {
        $transfer_ids = explode(',', $this->data['ids']);
        foreach ($transfer_ids as $k => $v) {
            if ($v) {
                D('video_transfer')->where('transfer_id='.$v)->setField('status', 1);
                $feed_id = D('video_transfer')->where('transfer_id='.$v)->getField('feed_id');
                model('Feed')->cleanCache(array($feed_id));
            }
        }

        return 1;
    }

    /**
     * 更新视频对应的分享
     * @param ids transfer_id字符串 多个transfer_id之间用逗号连接 例如 5,6,7
     * @return [type] [description]
     */
    public function runtask()
    {
        model('Schedule')->run();
    }



    /**
     * 发送注册验证码
     *
     * @return array
     * @author Medz Seven <lovevipdsw@vip.qq.com>
     **/
    public function send_register_code()
    {
        $phone = floatval($_REQUEST['phone']);

        /* # 检查是否可以已经被注册 */
        if (!model('User')->isChangePhone($phone)) {
//            $this->error(array(
//                'status' => 0,
//                'msg' => '该手机已经绑定，无法再次绑定',
//            ));
            return array(
                'status' => 0,
                'msg' => '该手机已经绑定，无法再次绑定',
            );

            /* # 检查是否发送失败 */
        } elseif (($sms = model('Sms')) and !$sms->sendCaptcha($phone, true)) {
//            $this->error(array(
//                'status' => 0,
//                'msg' => $sms->getMessage(),
//            ));
            return array(
                'status' => 0,
                'msg' => $sms->getMessage(),
            );
        }

        return array(
            'status' => 1,
            'msg' => '验证码发送成功！',
        );
    }



    /**
     * 发送短信验证码
     *
     * @return array
     * @author Medz Seven <lovevipdsw@vip.qq.com>
     **/
    public function sendCodeByPhone()
    {

        $login = t($this->data['login']);

        $phone_num = t($this->data['phone']);

        $where = '`is_del` = 0 AND (`uid` = \'__LOGIN__\' OR `phone` = \'__LOGIN__\' OR `email` = \'__LOGIN__\' OR `uname` = \'__LOGIN__\')';
        $where = str_replace('__LOGIN__', $login, $where);

        $phone = model('User')->where($where)->field('`phone`')->getField('phone');

        if (!$phone) {
            return array(
                'status' => 0,
                'message' => '该用户没有绑定手机号码，或者用户不存在！',
            );
        } elseif (!model('Sms')->sendCaptcha($phone, false)) {
            return array(
                'status' => -1,
                'message' => model('Sms')->getMessage(),
            );
        }

        return array(
            'status' => 1,
            'message' => '发送成功！',
        );
    }

    /**
     * 判断手机验证码是否正确
     *
     * @return array
     * @author Medz Seven <lovevipdsw@vip.qq.com>
     **/
    public function checkCodeByPhone()
    {
        $login = t($this->data['login']);
        $code = intval($this->data['code']);

        $where = '`is_del` = 0 AND (`uid` = \'__LOGIN__\' OR `phone` = \'__LOGIN__\' OR `email` = \'__LOGIN__\' OR `uname` = \'__LOGIN__\')';
        $where = str_replace('__LOGIN__', $login, $where);

        $phone = model('User')->where($where)->field('`phone`')->getField('phone');

        if (!$phone) {
            return array(
                'status' => 0,
                'message' => '用户不存在或者没有绑定手机号码',
            );
        } elseif (!$code) {
            return array(
                'status' => -1,
                'message' => '验证码不能为空',
            );
        } elseif (!model('Sms')->CheckCaptcha($phone, $code)) {
            return array(
                'status' => -2,
                'message' => model('Sms')->getMessage(),
            );
        }

        return array(
            'status' => 1,
            'message' => '验证码正确',
            'phonenum' => $phone,
        );
    }


    /**
     * 保存用户密码
     *
     * @return array
     * @author Medz Seven <lovevipdsw@vip.qq.com>
     **/
    public function saveUserPasswordByPhone()
    {
        $login = t($this->data['login']);
        $password = t($this->data['password']);
        $code = intval($this->data['code']);

        $where = '`is_del` = 0 AND (`uid` = \'__LOGIN__\' OR `phone` = \'__LOGIN__\' OR `email` = \'__LOGIN__\' OR `uname` = \'__LOGIN__\')';
        $where = str_replace('__LOGIN__', $login, $where);

        $phone = model('User')->where($where)->field('`phone`')->getField('phone');

        if (!$phone) {
            return array(
                'status' => 0,
                'message' => '用户不存在或者没有绑定手机号码',
            );
        } elseif (!$code) {
            return array(
                'status' => -1,
                'message' => '验证码不能为空',
            );
        } elseif (!model('Register')->isValidPasswordNoRepeat($password)) {
            return array(
                'status' => -2,
                'message' => model('Register')->getLastError(),
            );
        } elseif (!model('Sms')->CheckCaptcha($phone, $code)) {
            return array(
                'status' => -3,
                'message' => model('Sms')->getMessage(),
            );
        }

        $data = array();
        $data['login_salt'] = rand(10000, 99999);
        $data['password'] = model('User')->encryptPassword($password, $data['login_salt']);

        if (model('User')->where('`phone` = '.$phone)->save($data)) {
            return array(
                'status' => 1,
                'message' => '修改成功',
            );
        }

        return array(
            'status' => -4,
            'message' => '修改失败',
        );
    }
}
