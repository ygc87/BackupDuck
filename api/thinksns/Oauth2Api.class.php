<?php

class Oauth2Api extends Api
{
    //https://api.weibo.com/oauth2/access_token?client_id=YOUR_CLIENT_ID&client_secret=YOUR_CLIENT_SECRET&grant_type=password&username=USER_NAME&password=PASSWORD
    //https://api.weibo.com/oauth2/access_token?client_id=YOUR_CLIENT_ID&client_secret=YOUR_CLIENT_SECRET&grant_type=authorization_code&redirect_uri=YOUR_REGISTERED_REDIRECT_URI&code=CODE
    public function access_token()
    {
        //grant_type=authorization_code
        if ($_POST['grant_type'] == 'authorization_code') {
            //通过code获取这个登录用户的oauth_token 在 ts_oauth_login 表
            $map['code'] = $_POST['code'];
            $oauth = D('OauthLgoin')->where($map)->find();
            $return['oauth_token'] = $oauth['oauth_token'];
            $return['expires_in'] = 3600;

            return $return;
        //grant_type=password,username\password\client_id\client_secret
        } elseif ($_POST['grant_type'] == 'password') {
            $map['username'] = $_POST['username'];
            $map['password'] = $_POST['password'];
            $map['client_id'] = $_POST['client_id'];
            $map['client_secret'] = $_POST['client_secret'];
            $this->createRequestTokenByPassword($map['username'], $map['password'], $map['client_id'], $map['client_secret']);
        }
    }
    //response_type=code,返回code.再通过code返回access_token
    //https://api.weibo.com/oauth2/authorize?client_id=YOUR_CLIENT_ID&response_type=code&redirect_uri=YOUR_REGISTERED_REDIRECT_URI
    //response_type=token,直接返回access_token
    //https://api.weibo.com/oauth2/authorize?client_id=YOUR_CLIENT_ID&response_type=token&redirect_uri=YOUR_REGISTERED_REDIRECT_URI
    public function authorize()
    {
        //如果未登录，展示登录页
        display('oauth_response_user_check');
        //如果已登录，展示授权页
        display('oauth_response_user_check');
    }
    //生成用于申请token的code
    private function checkToken($token)
    {
        $login = D('OauthLogin')->where('uid='.$uid.' AND api_key='.$api_key)->find();

        return $login['oauth_code'];
    }
    //生成用于申请token的code
    private function refreshToken($refresh_token)
    {
        //
    }
    //生成用于申请token的code
    private function getRequestCode($api_key, $uid)
    {
        $login = D('OauthLogin')->where('uid='.$uid.' AND api_key='.$api_key)->find();

        return $login['oauth_code'];
    }
    //生成token
    private function getRequestTokenByCode($code)
    {
        $login = D('OauthLogin')->where('uid='.$uid.' AND api_key='.$api_key)->find();

        return $login['oauth_token'];
    }
    //生成token
    private function getRequestTokenByPassword($username, $password, $api_key, $secret_key)
    {
        $map['uname'] = $username;
        $map['passwd'] = $password;
        $user = D('User')->where($map)->field('uid')->find();
        if (!$user) {
            return false;
        }
        $data['oauth_token'] = getOAuthToken($user['uid'], $api_key, $secret_key);
        $data['oauth_code'] = getOAuthCode($user['uid'], $api_key, $secret_key);
        $data['api_key'] = $api_key;
        $data['uid'] = $user['uid'];
        $result = D('OauthLogin')->add($data);
        if ($result) {
            return $data['oauth_token'];
        } else {
            return false;
        }
    }
    //生成token
    private function createRequestTokenByPassword($username, $password, $api_key, $secret_key)
    {
        $map['uname'] = $username;
        $map['passwd'] = $password;
        $user = D('User')->where($map)->field('uid')->find();
        if ($user) {
            $data['oauth_token'] = getOAuthToken($user['uid'], $api_key, $secret_key);
            $data['oauth_code'] = getOAuthCode($user['uid'], $api_key, $secret_key);
            $data['api_key'] = $api_key;
            $data['uid'] = $user['uid'];

            return D('OauthLogin')->add($data);
        } else {
            return false;
        }
    }
    //生成token
    private function createRequestTokenByUID($uid, $api_key, $secret_key)
    {
        $map['uname'] = $username;
        $map['passwd'] = $password;
        $user = D('User')->where($map)->field('uid')->find();
        if ($user) {
            $data['oauth_token'] = getOAuthToken($user['uid'], $api_key, $secret_key);
            $data['oauth_code'] = getOAuthCode($user['uid'], $api_key, $secret_key);
            $data['api_key'] = $api_key;
            $data['uid'] = $user['uid'];

            return D('OauthLogin')->add($data);
        } else {
            return false;
        }
    }
}
