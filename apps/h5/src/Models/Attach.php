<?php

namespace App\H5\Model;

use Ts\Bases\Model;

/**
 * 数据库附件查询模型.
 *
 * @author Seven Du <lovevipdsw@outlook.com>
 **/
class Attach extends Model
{
    /**
     * 表名称.
     *
     * @var string
     */
    protected $table = 'attach';

    /**
     * 主键名称.
     *
     * @var string
     */
    protected $primaryKey = 'attach_id';

    protected $appends = array('path');

    /**
     * 关闭软删除.
     *
     * @var bool
     */
    protected $softDelete = false;

    // protected $appends = array('path');

    public function scopeByType($query, $type)
    {
        return $query->where('attach_type', '=', $type);
    }

    public function scopeByUser($query, $uid)
    {
        return $query->where('uid', '=', $uid);
    }

    /**
     * 属性获取方法.
     *
     * @param string $name 属性名称
     *
     * @return miexd
     *
     * @author Seven Du <lovevipdsw@outlook.com>
     * @datetime 2016-03-21T13:23:49+0800
     * @homepage http://medz.cn
     */
    public function __get($name)
    {
        if (in_array(strtolower($name), array('imagePath'))) {
            return call_user_func(array($this, $name));
        }

        return parent::__get($name);
    }

    /**
     * 图片附件路径关系方法.
     *
     * @param int    $width  裁剪的宽度
     * @param string $height 裁剪的高度
     *
     * @return string 图片地址【完整】
     *
     * @author Seven Du <lovevipdsw@outlook.com>
     * @datetime 2016-03-22T11:11:17+0800
     * @homepage http://medz.cn
     */
    public function imagePath($width = 0, $height = 'auto')
    {
        $filename = $this->save_path.$this->save_name;
        $filename = getImageUrl($filename, $width, $height, true);

        return $filename;
    }

    public function getPathAttribute()
    {
        $filename = $this->save_path.$this->save_name;

        return getAttachUrl($filename);
    }
} // END class Attach extends Model
