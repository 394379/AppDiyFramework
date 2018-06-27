<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/2
 * Time: 12:31
 */
namespace app\admin\controller;
use app\common\controller\Base;
use app\common\model\authRuleModel;
use app\common\model\authGroupModel;
use app\common\model\userModel;
use think\facade\Session;
use think\facade\Request;
use ycsoft\leftNav;

class Auth extends Base
{
    /*
     * 用户表
     */
    //用户列表
    public function userList()
    {
      $this->noLogin();
      $userInfo = userModel::paginate(10);
      if ($userInfo==null)
      {
          $this->error('错误');
      }else{
          $this->view->assign('userInfo',$userInfo);
      }
      $this->assign('moudle','auth');
      return $this->fetch('userList');
    }

    //用户添加
    public function userAdd()
    {
        $this->noLogin();
        if(request()->isPost()){
            $data=Request::param();
            //halt($data);
            $data['password'] = sha1($data['password']);
            $result = userModel::create($data);
            if($result==null){
                $this->error('数据添加失败');
            }else{
                $this->success('添加成功','auth/userList','','1');
            }
        }else {
            $this->noLogin();
            $userInfo = userModel::all();
            if($userInfo==null)
            {
                $this->error('错误');
            }else {
                $this->assign('userInfo',$userInfo);
                $this->assign('groupInfo',authGroupModel::order('id','desc')->select());
            }
        }
        $this->assign('moudle','auth');
        return $this->fetch('userAdd');
    }

    //用户编辑
    public function userEdit()
    {
        $this->noLogin();
        if(request()->isPost()){
            $data=Request::param();
            $id=$data['id'];
            if(strlen($data['password'])==40)
            {
                unset($data['password']);
            }else{
                $data['password'] = sha1($data['password']);
            }

            $result = userModel::update($data,$id);
            if($result==null){
                $this->error('数据更新失败');
            }else{
                $this->success('更新成功','auth/userList','','1');
            }
        }else {
            $this->noLogin();
            $data=Request::param();
            $id=$data['id'];
            $userInfo = userModel::get($id);
            //halt($ruleInfo);
            if($userInfo==null)
            {
                $this->error('错误');
            }else {
                $this->assign('userInfo',$userInfo);
                $this->assign('groupInfo',authGroupModel::order('id','desc')->select());
            }
        }
        $this->assign('moudle','auth');
        return $this->fetch('userEdit');
    }

    //用户删除
    public function userDel()
    {
        $this->noLogin();
        $id = Request::param('id');

        if(userModel::where('id',$id)->delete()){
            return $this->success('删除成功','auth/userList','','1');
        }
        $this->error('删除失败');
    }

    /*
     * 用户组表
     */
    //用户组列表
    public function usergroup()
    {
      $this->noLogin();
      $groupInfo = authGroupModel::all();
      if($groupInfo==null)
      {
        $this->error('错误');
      }else {
        $this->assign('groupInfo',$groupInfo);
      }

      $this->assign('moudle','auth');
      return $this->fetch('userGroup');
    }

    //用户组添加
    public function groupAdd()
    {
        $this->noLogin();
        if(request()->isPost()){
            //coding...
            $this->noLogin();
            $data=Request::param();
            $result = authGroupModel::create($data);
            if($result==null){
                $this->error('数据添加失败');
            }else{
                $this->success('添加成功','auth/userGroup','','1');
            }
        }else {
            $this->noLogin();
            $groupInfo = authRuleModel::all();
            if($groupInfo==null)
            {
                $this->error('错误');
            }else {
                $this->assign('groupInfo',$groupInfo);
            }
        }
        $this->assign('moudle','auth');
        return $this->fetch('groupAdd');
    }

    //用户组编辑
    public function groupEdit()
    {
        $this->noLogin();
        if(request()->isPost()){
            $data=Request::param();
            $id=$data['id'];
            $result = authGroupModel::update($data,$id);
            if($result==null){
                $this->error('数据更新失败');
            }else{
                $this->success('更新成功','auth/userGroup','','1');
            }
        }else {
            $this->noLogin();
            $data=Request::param();
            $id=$data['id'];
            $groupInfo = authGroupModel::get($id);
            //halt($groupInfo);
            if($groupInfo==null)
            {
                $this->error('错误');
            }else {
                $this->assign('groupInfo',$groupInfo);
                $this->assign('groupInfoList',authGroupModel::all());
            }
        }
        $this->assign('moudle','auth');
        return $this->fetch('groupEdit');
    }

    //用户组删除
    public function groupDel()
    {
        $this->noLogin();
        $id = Request::param('id');

        //halt($id);

        if(authGroupModel::where('id',$id)->delete()){
            return $this->success('删除成功','auth/userGroup','','1');
        }
        $this->error('删除失败');
    }

    //用户组权限配置
    public function groupConfigRule()
    {
        $this->noLogin();
        $nav = new leftNav();
        $RuleInfo=authRuleModel::all(function($query){
            $query->field('id,pid,title')->where('status',1)->order('sort','asc');
        });
        $groupInfo=authGroupModel::all(function($query){
            $query->where('id',input('id'))->value('rules');
        });
        $groupConfigRuleInfo = $nav->auth($RuleInfo,$pid=0,$groupInfo);
        $groupConfigRuleInfo[] = array(
            "id"=>0,
            "pid"=>0,
            "title"=>"全部",
            "open"=>true
        );
//        halt($groupConfigRuleInfo);
        $groupConfigRuleInfo = json_encode($groupConfigRuleInfo,true);
        $this->assign('groupConfigRuleInfo',$groupConfigRuleInfo);
        $this->assign('moudle','auth');
        return $this->fetch('groupConfigRule');
    }

    //权限保存
    public function groupSetaccess(){

        $this->noLogin();
        $rules = input('post.rules');

        if(empty($rules)){
            return array('msg'=>'请选择权限!','code'=>0);
        }
        $data = input('post.');
        $where['id'] = $data['group_id'];
//        halt($data);
        if(authGroupModel::update($data,$where)){
            //获取帐号权限
            $result = userModel::get(Session::get('user_id'));
            $rules = getRulesList($result['group_id']);
            //halt($result['group_id']);
            Session::set('user_rules',$rules);

            return array('msg'=>'权限配置成功!','url'=>url('auth/userGroup'),'code'=>1);
        }else{
            return array('msg'=>'保存错误','code'=>0);
        }
    }

    /*
     * 权限表
     */
    //权限列表
    public function userrule()
    {
      $this->noLogin();
      $ruleInfo = authRuleModel::order('id','desc')->paginate(10);
      if($ruleInfo==null)
      {
        $this->error('错误');
      }else {
        $this->assign('ruleInfo',$ruleInfo);
      }

      $this->assign('moudle','auth');
      return $this->fetch('userRule');
    }

    //权限添加
    public function ruleAdd()
    {
        $this->noLogin();
      if(request()->isPost()){
        //coding...
        $data=Request::param();
        $result = authRuleModel::create($data);
        if($result==null){
            $this->error('数据添加失败');
        }else{
            $this->success('添加成功','auth/userRule','','1');
        }
      }else {
        $this->noLogin();

        $nav = new Leftnav();//规则菜单

        $ruleInfo = authRuleModel::all();
        if($ruleInfo==null)
        {
          $this->error('错误');
        }else {
            $ruleInfo= $nav->menu($ruleInfo);//规则菜单
            //halt($ruleInfo);
          $this->assign('ruleInfo',$ruleInfo);
        }
      }
      $this->assign('moudle','auth');
      return $this->fetch('ruleAdd');
    }

    //权限编辑
    public function ruleEdit()
    {
        $this->noLogin();
      if(request()->isPost()){
        $data=Request::param();
        $id=$data['id'];
        $result = authRuleModel::update($data,$id);
        if($result==null){
            $this->error('数据更新失败');
        }else{
            $this->success('更新成功','auth/userRule','','1');
        }
      }else {
          $this->noLogin();

          $nav = new Leftnav(); //规则菜单

          $data=Request::param();
          $id=$data['id'];
          $ruleInfo = authRuleModel::get($id);
        //halt($ruleInfo);
        if($ruleInfo==null)
        {
            $this->error('错误');
        }else {
            //$ruleInfo= $nav->menu($ruleInfo);
            $this->assign('ruleInfo',$ruleInfo);
            $this->assign('ruleInfoList',$nav->menu(authRuleModel::all()));//规则菜单
        }
      }
      $this->assign('moudle','auth');
      return $this->fetch('ruleEdit');
    }

    //权限删除
    public function ruleDel()
    {
        $this->noLogin();
        $id = Request::param('id');

        if(authRuleModel::where('id',$id)->delete()){
            return $this->success('删除成功','auth/userRule','','1');
        }
        $this->error('删除失败');
    }


}
