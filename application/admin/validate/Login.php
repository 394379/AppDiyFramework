<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/5
 * Time: 0:06
 */

namespace app\admin\validate;
use think\Validate;

class Login extends Validate
{
    //验证规则
    protected $rule = [

        'email|邮箱'=>[
            'require'=>'require',
            'email'=>'email',
        ],
        'password|密码'=>[
            'require'=>'require',
            'min'=>3,
            'max'=>20,
        ],

    ];

}