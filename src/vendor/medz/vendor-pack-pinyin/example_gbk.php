<?php
/**
 * Created by PhpStorm.
 * User: jifei
 * Date: 15/6/25
 */
include_once 'Pinyin.php';
echo Pinyin::getPinyin("���Ϻ�",'gb2312');
echo Pinyin::getShortPinyin("���Ϻ�",'gb2312');