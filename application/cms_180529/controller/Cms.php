<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/17
 * Time: 20:37
 */

namespace app\cms\controller;

use app\common\controller\Base;
use app\cms\model\articleModel;
use app\cms\model\categoryModel;

use think\facade\Request;

class Cms extends Base
{
    //内容
    //内容列表
    public function cmsList()
    {
        $this->noLogin();//权限验证
        if(request()->isPost()){
            $search_name=input(Request::param('search'));
            $search = ['query'=>[]];
            $search['query']['search_name']=$search_name;

            $articleInfo=articleModel::where('title','like',"%{$search_name}%")
                ->order('id','desc')->paginate(10,false,$search);
            $this->assign('articleInfo',$articleInfo);
            $this->assign('search',$search_name);
        }else{
            $articleInfo=articleModel::order('id','desc')->paginate(10);
            $this->assign('articleInfo',$articleInfo);
            $this->assign('search','');
        }
        return $this->fetch('cmsList');
    }

    //内容添加
    public function articleAdd()
    {
        $this->noLogin();//权限验证
        if(request()->isPost()){
            //coding...
            $data=Request::param();

            $file = Request::file('title_img');

            if($file!="")
            {
                $info = $file->validate([
                    'size'=>1000000,
                    'ext'=>'jpg,png,gif,jpeg',
                ])->move('uploads/');
                if($info){
                    $data['title_img']=$info->getSaveName();
                }else{
                    $this->error($file->getError());
                }
            }

            $result = articleModel::create($data);
            if($result==null){
                $this->error('内容添加失败');
            }else{
                $this->success('内容添加成功','cms/cms/cmsList','','1');
            }
        }else {

            $cateInfo = categoryModel::order('id','desc')->select();
            $this->assign('cateInfo',$cateInfo);
            return $this->fetch('articleAdd');
        }
    }
    public function downAdd()
    {
        $this->noLogin();//权限验证
        if(request()->isPost()){
            //coding...
            $data=Request::param();

            $file = Request::file('title_img');

            if($file!="") {
                $info = $file->validate([
                    'size' => 1000000,
                    'ext' => 'zip,rar,7z',
                ])->move('uploads/');
                if ($info) {
                    $data['title_img'] = $info->getSaveName();
                } else {
                    $this->error($file->getError());
                }
            }

            $result = articleModel::create($data);
            if($result==null){
                $this->error('内容添加失败');
            }else{
                $this->success('内容添加成功','cms/cms/cmsList','','1');
            }
        }else {

            $cateInfo = categoryModel::order('id','desc')->select();
            $this->assign('cateInfo',$cateInfo);
            return $this->fetch('downAdd');
        }
    }
    public function xiaoshuoAdd()
    {
        $this->noLogin();//权限验证
        if(request()->isPost()){
            //coding...
            $data=Request::param();

            $file = Request::file('title_img');

            if($file!="") {
                $info = $file->validate([
                    'size' => 1000000,
                    'ext' => 'jpg,png,gif,jpeg',
                ])->move('uploads/');
                if ($info) {
                    $data['title_img'] = $info->getSaveName();
                } else {
                    $this->error($file->getError());
                }
            }

            $result = articleModel::create($data);
            if($result==null){
                $this->error('内容添加失败');
            }else{
                $this->success('内容添加成功','cms/cms/cmsList','','1');
            }
        }else {

            $cateInfo = categoryModel::order('id','desc')->select();
            $this->assign('cateInfo',$cateInfo);
            return $this->fetch('articleAdd');
        }
    }

    //内容编辑
    public function articleEdit()
    {
        $this->noLogin();//权限验证
        if(request()->isPost()){
            //coding...
            $data=Request::param();
            $id = $data['id'];
            $file = Request::file('title_img');

            if($file!=null){
                $info = $file->validate([
                    'size'=>1000000,
                    'ext'=>'jpg,png,gif,jpeg',
                ])->move('uploads/');
                if($info){
                    $data['title_img']=$info->getSaveName();
                }else{
                    $this->error($file->getError());
                }
            }

            $result = articleModel::update($data,$id);
            if($result==null){
                $this->error('内容更新失败','','','1');
            }else{
                $this->success('内容更新成功','cms/cms/cmsList','','1');
            }
        }else {

            $this->assign('articleInfo',articleModel::get(Request::param('id')));
            $this->assign('cateInfo',categoryModel::order('id','desc')->select());
            return $this->fetch('articleEdit');
        }

    }

    //内容删除
    public function articleDel()
    {
        $this->noLogin();
        $id = Request::param('id');
        if(articleModel::destroy($id))
        {
            $this->success('删除成功！','cms/cms/cmsList','','1');
        }else
        {
            $this->error('删除失败！','','','1');
        }
    }

    //栏目
    //栏目列表
    public function categoryList()
    {
        $this->noLogin();//权限验证

        $categoryInfo = categoryModel::order('id','desc')->paginate(10);
        $this->assign('categoryInfo',$categoryInfo);
        return $this->fetch('categoryList');
    }

    //栏目添加
    public function categoryAdd()
    {
        $this->noLogin();//权限验证
        if(request()->isPost()){
            $data=Request::param();
            $result = categoryModel::create($data);
            if($result==null){
                $this->error('添加失败!');
            }else{
                $this->success('添加成功','cms/cms/categoryList','','1');
            }
        }else{
            return $this->fetch('categoryAdd');
        }


    }

    //栏目编辑
    public function categoryEdit()
    {
        $this->noLogin();//权限验证
        if(request()->isPost()){
            $data=Request::param();
            $id = $data['id'];
            $result = categoryModel::update($data,$id);
            if($result==null){
                $this->error('更新失败!');
            }else{
                $this->success('更新成功','cms/cms/categoryList','','1');
            }
        }else{
            $cateInfo = categoryModel::get(Request::param('id'));
            $this->assign('cateInfo',$cateInfo);
            return $this->fetch('categoryEdit');
        }

    }

    //栏目删除
    public function categoryDel()
    {
        $this->noLogin();
        $id = Request::param('id');
        if(categoryModel::destroy($id))
        {
            $this->success('删除成功！','cms/cms/categoryList','','1');
        }else
        {
            $this->error('删除失败！','','','1');
        }
    }



}