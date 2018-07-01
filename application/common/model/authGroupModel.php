<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/2
 * Time: 13:57
 */

namespace app\common\model;

use think\Model;

class authGroupModel extends Model
{
    protected $pk = 'id';

    protected $table = 'yc_auth_group';

    protected $field = true;

    // 开启自动写入时间戳
    protected $autoWriteTimestamp = true;

    protected $createTime = 'create_time';

    protected $updateTime = 'update_time';

}
