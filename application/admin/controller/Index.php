<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/2
 * Time: 12:31
 */
namespace app\admin\controller;
use app\common\controller\Base;
use app\common\model\guestModel;
use think\facade\Request;


class Index extends Base
{
    public function index()
    {
        $this->noLogin();
        $this->assign('siteName','元诚软件后台');
        $this->assign('moudle','index');
        return $this->fetch('index');
    }

//    留言管理
    public function guestList()
    {
        $this->noLogin();
        $guestInfo = guestModel::order('id','desc')->paginate(10);
        if ($guestInfo==null)
        {
            $this->error('错误');
        }else{
            //halt($userInfo);
            $this->assign('action','guestlist');
            $this->view->assign('guestInfo',$guestInfo);
        }
        $this->view->assign('siteName','元诚软件简易后台v0.01');
        return $this->view->fetch('guestlist');
    }

    public function guestDel()
    {
        $guest_id = Request::param('id');

        if(guestModel::where('id',$guest_id)->delete()){
            return $this->success('删除成功','guestlist','','1');
        }
        $this->error('删除失败');
    }


}
