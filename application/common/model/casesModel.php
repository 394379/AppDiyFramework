<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/2
 * Time: 13:30
 */
namespace app\common\model;

use think\Model;

class casesModel extends Model
{
    protected $pk = 'id';

    protected $table = 'yc_cases';

    // 开启自动写入时间戳
    protected $autoWriteTimestamp = true;

    protected $createTime = 'create_time';

    protected $updateTime = 'update_time';
}
