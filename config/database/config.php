<?php

// 数据库配置文件 - 临时

return array(
    'collation' => 'utf8_unicode_ci',
    'read'=>array(
        0=>array(
            'driver'=>'mysql',
            'host'=>'read1.appdb.com',
            'database' => 'thinksns_xiaohuangya',
            'username' => 'rmdcms',
	    //'username'=>'xiaohuangya',
            'password' => 'md123!!.euKE$',
	    //'password'=>'Irk818$#12',
            'charset'=> 'utf8',
            'port' => 3306,
            'prefix'=> 'ts_'
        ),
        1=>array(
            'driver'=>'mysql',
            'host'=>'read2.appdb.com',
            'database' => 'thinksns_xiaohuangya',
            'username' => 'rmdcms',
	    //'username'=>'xiaohuangya',
            'password' => 'md123!!.euKE$',
	    //'password'=>'Irk818$#12',
            'charset'=> 'utf8',
            'port' => 3306,
            'prefix'=> 'ts_'

        )),
    'write'=>array(
        'driver'=>'mysql',
        'host'=>'master.appdb.com',
        'database' => 'thinksns_xiaohuangya',
        'username' => 'mdcms',
        'password' => 'Intech#2018$',
        'charset'=> 'utf8',
        'port' => 3306,
        'prefix'=> 'ts_'
    )

);
