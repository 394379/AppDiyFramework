<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/15
 * Time: 14:36
 */

namespace app\admin\controller;
use app\common\controller\Base;
use app\common\model\userModel;
use app\common\model\siteModel;
use think\facade\Request;
use think\facade\Session;
use think\captcha\Captcha;

class Login extends Base
{

    //登录界面渲染
    public function login()
    {
        $this->isLogin();
        $this->view->assign('title','登录');
        return $this->view->fetch('login');
    }
    //验证码
    public function verify()
    {
        $captcha = new Captcha();
        return $captcha->entry();
    }
    //登录校验
    public function loginCheck()
    {
        $data = Request::param();

        if( !captcha_check($data['captcha'] ))
        {
            return $this->error('验证码错误，请重试！','login/login','','1');
        }

        $result = userModel::get(function ($query) use ($data){
            $query->where('email',$data['email'])
                ->where('password',sha1($data['password']));
        }); //闭包查询
        //halt($result);

        if($result==null)
        {
            return $this->error('错误，请重试！');
        }
        else
        {
            Session::set('user_id',$result->id);
            Session::set('user_email',$result->email);
            Session::set('remote_user_id','');
            Session::set('remote_user_email','');

            //获取帐号权限
            $rules = getRulesList($result['group_id']);
            //halt($result['group_id']);
            Session::set('user_rules',$rules);

            //获取站点信息
            $siteInfo=siteModel::get(1);
            Session::set('sitename',$siteInfo['sitename']);
            Session::set('company',$siteInfo['company']);
            Session::set('years',$siteInfo['years']);


            return $this->success('登录成功','index/index','',1);
        }

    }
    //登出
    public function logout()
    {
//        Session::clear();
        Session::delete('user_id');
        Session::delete('user_email');
        Session::delete('user_rules');
        $this->success('退出成功','login/login','',1);
    }

}