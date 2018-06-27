<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/2
 * Time: 13:29
 */
namespace app\guest\model;

use think\Model;

class guestModel extends Model
{
    protected $pk = 'id';

    protected $table = 'yc_guest';

    protected $field = true;

    // 开启自动写入时间戳
    protected $autoWriteTimestamp = true;

    protected $createTime = 'create_time';

    protected $updateTime = 'update_time';
}
