<?php
/**
 * Created by PhpStorm.
 * User: Dylan.L
 * Date: 2018/5/24
 * Time: 16:49
 */

define('ROOT_FILE', 'index.php');

$_GET['app'] = 'api';
$_GET['mod'] = 'Credit';
$_GET['act'] = 'save_charge';

$_REQUEST = array_merge($_REQUEST, $_GET);

require __DIR__.'/index.php';
