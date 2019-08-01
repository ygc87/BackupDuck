<?php

namespace App\H5\Controller;

defined('SITE_PATH') || exit('Forbidden');

use App\H5\Common;
use App\H5\Base\Controller;
use App\H5\Model;
use App\H5\Model\Feed;
use App\H5\Model\Channel;

Common::import(APPS_PATH.'/admin/Lib/Action/AdministratorAction.class.php');

/**
 * H5后台管理控制器
 *
 * @package Apps\H5\Controller\Admin
 **/
class Admin
{
    /*=================================非Action区域========================================*/
    /**
     * 初始化操作
     *
     * @author Seven Du <lovevipdsw@vip.qq.com>
     **/
    public function _initialize()
    {
        // $this->pageTitle['config']       = '常规配置';
    }

} // END class Admin extends Controller
class_alias('\Apps\h5\Controller\Admin', 'AdminAction');

