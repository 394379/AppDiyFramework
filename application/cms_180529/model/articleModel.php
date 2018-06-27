<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/17
 * Time: 21:12
 */

namespace app\cms\model;

use think\Model;


class articleModel extends Model
{
    protected $pk = 'id';

    protected $table = 'yc_cms_article';

    protected $field = true;

    // 开启自动写入时间戳
    protected $autoWriteTimestamp = true;

    protected $createTime = 'create_time';

    protected $updateTime = 'update_time';

}