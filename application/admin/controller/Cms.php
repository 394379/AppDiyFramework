<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/17
 * Time: 20:37
 */

namespace app\admin\controller;

use app\common\controller\Base;
use app\cms\model\articleModel;

class Cms extends Base
{
    //文章列表
    public function index()
    {
        $this->noLogin();//权限验证

        return $this->fetch('articleList');
    }

    //文章添加
    public function articleAdd()
    {
        $this->noLogin();//权限验证

        return $this->fetch('articleAdd');
    }

    //文章编辑
    public function articleEdit()
    {
        $this->noLogin();//权限验证

        return $this->fetch('articleEdit');
    }
}