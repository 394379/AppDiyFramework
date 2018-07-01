<?php
/*
 *
 */
namespace app\demo\controller;
use think\Controller;
use app\demo\model\demoModel;

class Index extends Controller
{
    public function index()
    {
        $demo = demoModel::all();
        $this->assign('demo',$demo);
        return $this->fetch('index');
    }

}
