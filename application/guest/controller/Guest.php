<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/23
 * Time: 12:02
 */

namespace app\guest\controller;

use app\common\controller\Base;
use app\guest\model\guestModel;
use think\facade\Request;


class Guest extends Base
{
    //留言管理
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
        $this->view->assign('siteName','元诚软件');
        return $this->view->fetch('guestlist');
    }
    //留言删除
    public function guestDel()
    {
        $this->noLogin();
        $guest_id = Request::param('id');
        if(guestModel::where('id',$guest_id)->delete()){
            return $this->success('删除成功','guestlist','','1');
        }
        $this->error('删除失败');
    }
}