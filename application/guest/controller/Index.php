<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/23
 * Time: 12:31
 */

namespace app\guest\controller;
use think\Controller;
use app\guest\model\guestModel;
use think\facade\Request;

class Index extends Controller
{
    public function index()
    {
        if(Request::isAjax()){
            $data = Request::param();
            if(guestModel::create($data,true)){
                return ['status'=>1,'message'=>'留言成功！'];
            }
            return ['status'=>0,'message'=>'留言失败'];
        }else{
            return $this->fetch('index');
        }

    }

}