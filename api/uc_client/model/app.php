<?php

/*
    [UCenter] (C)2001-2099 Comsenz Inc.
    This is NOT a freeware, use is subject to license terms

    $Id: app.php 1059 2011-03-01 07:25:09Z monkey $
*/

!defined('IN_UC') && exit('Access Denied');

class appmodels
{
    public $db;
    public $base;

    public function __construct(&$base)
    {
        $this->_appmodel($base);
    }

    public function _appmodel(&$base)
    {
        $this->base = $base;
        $this->db = $base->db;
    }

    public function get_apps($col = '*', $where = '')
    {
        $arr = $this->db->fetch_all("SELECT $col FROM ".UC_DBTABLEPRE.'applications'.($where ? ' WHERE '.$where : ''), 'appid');
        foreach ($arr as $k => $v) {
            isset($v['extra']) && !empty($v['extra']) && $v['extra'] = unserialize($v['extra']);
            unset($v['authkey']);
            $arr[$k] = $v;
        }

        return $arr;
    }
}