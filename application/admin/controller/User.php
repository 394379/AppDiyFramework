<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/2
 * Time: 13:43
 */

namespace app\admin\controller;

use app\common\controller\Base;
use app\common\model\userModel;
use think\facade\Request;

class User extends Base
{
//    public function index()
//    {
//
//    }
//
//    public function useradd()
//    {
//        $this->assign('action','userlist');
//        return $this->fetch('useradd');
//    }
//
//    public function useredit()
//    {
//        $id = Request::param('id');
//        $userInfo = userModel::get($id);
//        //halt($userInfo);
//        $this->assign('userInfo',$userInfo);
//        $this->assign('action','userlist');
//        return $this->fetch('useredit');
//    }
//
//    public function userSave()
//    {
//        $data = Request::param();
//
//        $data['password'] = sha1($data['password']);
//
//        $reault = userModel::create($data);
//
//        if($reault==null){
//            $this->error('添加失败');
//        }else{
//            $this->success('添加成功','index/userlist','','1');
//        }
//    }
//
//    public function userUpdate()
//    {
//        $data = Request::param();
//        //halt($data);
//        $id = $data['id'];
//        if(strlen($data['password'])==40)
//        {
//            unset($data['password']);
//        }else{
//            $data['password'] = sha1($data['password']);
//        }
//        //unset($data['id']);
//
//        //halt($data);
//
//        if(userModel::update($data,$id)){
//            $this->success('更新成功','index/userlist','','1');
//        }
//
//        $this->error('更新失败');
//
//    }
//
//    public function userDel()
//    {
//        $id = Request::param('id');
//
//        if(userModel::where('id',$id)->delete()){
//            return $this->success('删除成功','index/userlist','','1');
//        }
//        $this->error('删除失败');
//    }

}