<?php
/*
 * 基础控制器
 */
namespace app\common\controller;

use app\common\model\authGroupModel;
use think\Controller;
use think\facade\Session;
use app\common\model\authRuleModel;

class Base extends Controller
{
    protected function initialize()
    {

        $this->menu();

    }

    //系统菜单
    public function menu()
    {
        if(Session::get('user_rules')!="" || Session::get('user_rules')!="NULL");
        {
            $pBarInfo = authRuleModel::where('pid', 0)
                ->where('status', 1)
                ->where('id', 'in', Session::get('user_rules'))
                ->select();
            $aBarInfo = authRuleModel::all(Session::get('user_rules'));
            //halt($pBarInfo);
            $this->assign('pBarInfo', $pBarInfo);
            $this->assign('aBarInfo', $aBarInfo);
        }
    }

    //权限判断
    public function Permission()
    {
//        define('MODULE_NAME',strtolower(request()->controller()));
//        define('ACTION_NAME',strtolower(request()->action()));
        $moduleName = strtolower(request()->module());
        $controllerName = strtolower(request()->controller());
        $actionName = strtolower(request()->action());

        $this->hrefId = authRuleModel::where('href',$moduleName.'/'.$controllerName.'/'.$actionName)->value('id');

        $this->arrRulesId=explode(",", Session::get('user_rules'));
        //halt(Session::get('user_rules'));
        if(!in_array($this->hrefId,$this->arrRulesId)){
            $this->error('您无此操作权限');
        }

    }


    //防止重复登录
    public function isLogin()
    {
        if(Session::has('user_id')){
            $this->error('已经登录','settings/index','',1);
        }

    }

    //验证登录失效
    public function noLogin()
    {
        if(!Session::has('user_id')){
            $this->error('请登录','login/login','',1);
        }
        $this->Permission();
    }



}
