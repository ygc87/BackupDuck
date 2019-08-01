<?php

/** *  * @author jason * */include SITE_PATH.'/api/uc_client/client.php';class BaseApi extends Api
{
    public $discuzURl = 'http://i/dz3';

    public $version = 1;

    public function __construct()
    {
        parent::__construct();
    }

    public function setDiscuzUrl($url)
    {
        $this->discuzURl = $url;
    }

    public function setVersion($version)
    {
        $this->version = $version;
    }

    public function getContentFormDiscuz($module, $opt = '', $moth = 'GET')
    {
        $qry_str = 'module='.$module.'&version='.$this->version.$opt;

        $tuCurl = curl_init();

        if ($moth = 'GET') {
            curl_setopt($tuCurl, CURLOPT_URL, $this->discuzURl.'/api/mobile/index.php?'.$qry_str);
        } else {
            curl_setopt($tuCurl, CURLOPT_URL, $this->discuzURl);

            curl_setopt($tuCurl, CURLOPT_POST, 1);

            curl_setopt($tuCurl, CURLOPT_POSTFIELDS, $qry_str);
        }

        foreach ($_COOKIE as $k => $v) {
            $cookieStr .= $k.'='.URLencode($v).'; ';
        }

        $cookieStr = rtrim($cookieStr, '; ');      //dump('**********************');      //dump($cookieStr);exit;

      curl_setopt($tuCurl, CURLOPT_HEADER, 0);

        curl_setopt($tuCurl, CURLOPT_COOKIE, $cookieStr);

        curl_setopt($tuCurl, CURLOPT_CONNECTTIMEOUT, 30);

        curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 1);

        $tuData = curl_exec($tuCurl);

        if (curl_errno($tuCurl)) {
            return 'Curl error: '.curl_error($tuCurl);
        }

        curl_close($tuCurl);

        $tuData = json_decode($tuData, true);

        return isset($tuData['Variables']) ? $tuData['Variables'] : $tuData;
    }
}
