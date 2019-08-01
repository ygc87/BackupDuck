<?php

function curl_post_https($url,$data){ // 模拟提交数据函数
    $headers=array('Content-Type: application/json');
    $curl = curl_init(); // 启动一个CURL会话
    curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0); // 从证书中检查SSL加密算法是否存在
    curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
    curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
    curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
    curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
    curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    $tmpInfo = curl_exec($curl); // 执行操作
    if (curl_errno($curl)) {
        echo 'Errno'.curl_error($curl);//捕抓异常
    }
    curl_close($curl); // 关闭CURL会话
    return $tmpInfo; // 返回数据，json格式
}
function curl_get_https($url){
    $curl = curl_init(); // 启动一个CURL会话
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);  // 从证书中检查SSL加密算法是否存在
    $tmpInfo = curl_exec($curl);     //返回api的json对象
    //关闭URL请求
    curl_close($curl);
    return $tmpInfo;    //返回json对象
}


function get_sign($str){
    return strtoupper(md5($str));
}

//function get_client_ip($type = 0)
//    {
//        $type = $type ? 1 : 0;
//        static $ip = NULL;
//        if ($ip !== NULL)
//        {
//            return $ip[$type];
//        }
//        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
//        {
//            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
//            $pos = array_search('unknown', $arr);
//            if (false !== $pos)
//            {
//                unset($arr[$pos]);
//            }
//            $ip = trim($arr[0]);
//        }
//        else if (isset($_SERVER['HTTP_CLIENT_IP']))
//        {
//            $ip = $_SERVER['HTTP_CLIENT_IP'];
//        }
//        else if (isset($_SERVER['REMOTE_ADDR']))
//        {
//            $ip = $_SERVER['REMOTE_ADDR'];
//        }
//        // IP地址合法验证
//        $long = sprintf("%u", ip2long($ip));
//        $ip = $long ? array($ip, $long) : array('0.0.0.0', 0);
//        return $ip[$type];
//    }


/*{"version":"1.0","merId":"10001","notify":"","orderId":"T1491445348661","redirectUr l":"","remark":"hello","sign":"503374D0333610080A03F602C5A3994E","totalMoney
":"1","tradeType":"alipay ","describe":"测试商品"，" fromtype ":" wap"}

{value}要替换成接收到的值，{key}要替换成平台分配的接入密钥，可在商户后台获取. merId={value}&orderId={value}&totalMoney={value}&tradeType={value}&
{key}使用 md5 签名上面拼接的字符串即可生成 32 位密文再转换成大写
*
*/
function addorder($merId,$key,$orderNo,$money,$notify,$redirectUrl,$remark){
    //$url="http://pay.liqiangxiechang.com:8000/ltPayBusiness/order/prepareOrder";

    $url="http://mms.teanmar.com:8000/ltPayBusiness/order/prepareOrder";
    $data['version']='1.0';
    $data['merId']=$merId;
    $data['notify']=$notify; //传入数据通知地址
    $data['orderId']=$orderNo; //生成订单号必须唯一
    $data['redirectUrl']=$redirectUrl; //支付跳转地址
    $data['remark']=$remark;
    $data['totalMoney']=$money;
    $data['tradeType']='wecaht_app';//wechat_app微信    alipay_wap 支付宝
    $data['describe']='vip';//商品信息
    $data['fromtype']='wap2';
    $data['ip']=get_client_ip();
    $str='merId='.$data['merId'].'&orderId='.$data['orderId'].'&totalMoney='.$data['totalMoney'].'&tradeType='.$data['tradeType'].'&'.$key;
    //$str='merId=".$data['merId']."&orderId="$data['orderId']"&totalMoney=".$data['totalMoney']."&tradeType=alipay&ac8d6f81091e49cfb4769de549dbb770";
    $data['sign']=get_sign($str);
    $data=json_encode($data);
    $rs =curl_post_https($url, $data);
    $res= json_decode($rs,true);
    //直接跳转
   return $res;
}


//一、下单接口
// $merId="10003";//商户号
// $key="ac8d6f81091e49cfb4769de549dbb770";//key
// $orderNo = "O".time();//改成你自己的订单格式
// $money=$_REQUEST("money");//支付金额，分
// $notifyUrl = "http://www.baidu.com";//异步回调地址
// $redirectUrl="http://www.baidu.com";//同步跳转地址
// $remark="res";//透传参数
//addorder($merId,$key,$orderNo,$money,$notifyUrl,$redirectUrl,$remark);











