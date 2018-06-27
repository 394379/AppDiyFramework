<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/2
 * Time: 12:31
 */
namespace app\admin\controller;
use app\common\controller\Base;
use app\common\model\casesModel;
use app\common\model\siteModel;
use app\common\model\userModel;
use think\facade\Request;
use think\facade\Session;

class Settings extends Base
{
    public function index()
    {
        $this->noLogin();
        $this->view->assign('siteName','元诚软件后台');
        $this->view->assign('moudle','settings');
        return $this->fetch('index');
    }

    //模板管理
    public function templete()
    {
        $this->noLogin();
        $this->view->assign('siteName','元诚软件后台');
        $this->view->assign('moudle','settings');
        return $this->fetch('templete');
    }

    //站点设置
    public function main()
    {
        $this->noLogin();
        $siteInfo = siteModel::get(['id'=>1]);
        //halt($siteInfo);
        $this->assign('moudle','settings');
        $this->view->assign('siteInfo',$siteInfo);
        $this->view->assign('title','网站后台');
        $this->view->assign('siteName','元诚软件简易后台v0.01');
        return $this->view->fetch('main');
    }

    //站点设置保存
    public function siteSave()
    {
        $this->noLogin();
        $data=Request::param();

        $result = siteModel::update($data);

        if($result==null){

            $this->error('数据更新失败');

        }else{

            $this->success('更新成功','settings/main','','1');

        }
    }

    //案例列表
    public function caseList()
    {
        $this->noLogin();
        $caseInfo = casesModel::all();
        if ($caseInfo==null)
        {
            $this->error('错误');
        }else{
            //halt($userInfo);
            $this->assign('action','caselist');
            $this->view->assign('caseInfo',$caseInfo);
        }
        $this->view->assign('siteName','元诚软件简易后台v0.01');
        return $this->view->fetch('caselist');
    }


}
