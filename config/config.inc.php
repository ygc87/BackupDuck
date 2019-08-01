<?php
if (!defined('SITE_PATH')) exit();
return array(
//'driver'=>'mysql',	// 数据库常用配置
	//'DB_TYPE'       => 'mysql',       // 数据库类型

	//'DB_HOST'       => '222.186.190.124',    // 数据库服务器地址
	//'DB_NAME'       => 'thinksns_xiaohuangya',    // 数据库名
	//'DB_USER'       => 'root',// 数据库用户名
	//'DB_PWD'        => 'hill930129',// 数据库密码

	//'DB_PORT'       => 3306,        // 数据库端口
	//'DB_PREFIX'     => 'ts_',// 数据库表前缀（因为漫游的原因，数据库表前缀必须写在本文件）
	//'DB_CHARSET'    => 'utf8',      // 数据库编码
	'SECURE_CODE'   => '94015991403ff2ad7',  // 数据加密密钥
	'COOKIE_PREFIX' => 'TS4_',      // # cookie
    'APP_DEBUG'         =>  false,   //打开调试模式
    'SHOW_PAGE_TRACE'   =>  false,    //显示页面堆栈
);
