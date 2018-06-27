<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/17
 * Time: 21:52
 */

namespace app\cms\controller;

use app\common\controller\Base;
use app\cms\model\articleModel;
use app\cms\model\categoryModel;

use think\facade\Request;

class Index extends Base
{
    public function index()
    {
        //$this->redirect('cms/index',['tpl'=>1],'302','1');
        //获取栏目分类
        $cateArticleInfo = categoryModel::where('tpl','article')->order('id','asc')->select();
        $this->assign('cateArticleInfo',$cateArticleInfo);
        $cateDownInfo = categoryModel::where('tpl','down')->order('id','asc')->select();
        $this->assign('cateDownInfo',$cateDownInfo);
        $cateXiaoshuoInfo = categoryModel::where('tpl','xiaoshuo')->order('id','asc')->select();
        $this->assign('cateXiaoshuoInfo',$cateXiaoshuoInfo);

        //获取最新文章列表
        $articleNewList = articleModel::order('id','desc')->limit(10)->select();
        $this->assign('articleNewList',$articleNewList);

        //获取热点文章列表
        $articleHotList = articleModel::where('is_hot',1)->order('id','desc')->limit(10)->select();
        $this->assign('articleHotList',$articleHotList);

        //获取置顶文章列表
        $articleTopList = articleModel::where('is_top',1)->order('id','desc')->limit(10)->select();
        $this->assign('articleTopList',$articleTopList);

        return $this->fetch('index');
    }

    public function articleDetail()
    {
        $tpl=Request::param('tpl');
        //获取一篇文章,并插入pv记录.
        $id = Request::param('id');
        $articleInfo = articleModel::get(function ($query) use ($id){
            $query->where('id',$id)->setInc('pv');
        });
        $this->assign('articleInfo',$articleInfo);

        //获取栏目分类
        $categoryInfo = categoryModel::where('tpl',$tpl)->order('id','asc')->select();
        $this->assign('categoryInfo',$categoryInfo);

        //获取最新文章列表
        $articleNewList = articleModel::order('id','desc')->limit(10)->select();
        $this->assign('articleNewList',$articleNewList);

        //获取热点文章列表
        $articleHotList = articleModel::where('is_hot',1)->order('id','desc')->limit(10)->select();
        $this->assign('articleHotList',$articleHotList);

        return $this->fetch('articleDetail');
    }

    public function downDetail()
    {
        $tpl=Request::param('tpl');
        //获取一篇文章,并插入pv记录.
        $id = Request::param('id');
        $articleInfo = articleModel::get(function ($query) use ($id){
            $query->where('id',$id)->setInc('pv');
        });
        $this->assign('articleInfo',$articleInfo);

        //获取栏目分类
        $categoryInfo = categoryModel::where('tpl',$tpl)->order('id','asc')->select();
        $this->assign('categoryInfo',$categoryInfo);

        //获取最新文章列表
        $articleNewList = articleModel::order('id','desc')->limit(10)->select();
        $this->assign('articleNewList',$articleNewList);

        //获取热点文章列表
        $articleHotList = articleModel::where('is_hot',1)->order('id','desc')->limit(10)->select();
        $this->assign('articleHotList',$articleHotList);

        return $this->fetch('downDetail');
    }

    public function xiaoshuoDetail()
    {
        //获取一篇文章,并插入pv记录.
        $id = Request::param('id');
        $articleInfo = articleModel::get(function ($query) use ($id){
            $query->where('id',$id)->setInc('pv');
        });
        $this->assign('articleInfo',$articleInfo);

        //获取栏目分类
        $categoryInfo = categoryModel::order('id','asc')->select();
        $this->assign('categoryInfo',$categoryInfo);

        //获取最新文章列表
        $articleNewList = articleModel::order('id','desc')->limit(10)->select();
        $this->assign('articleNewList',$articleNewList);

        //获取热点文章列表
        $articleHotList = articleModel::where('is_hot',1)->order('id','desc')->limit(10)->select();
        $this->assign('articleHotList',$articleHotList);

        return $this->fetch('xiaoshuoDetail');
    }

    public function categoryList()
    {
        $tpl=Request::param('tpl');
        //某栏目列表
        $id=Request::param('id');
        if($id!="")
        {
            $articleTopList = articleModel::where('cate_id',$id)->where('is_top',1)->order('id','desc')->select();
        }
        else
        {
            $articleTopList = articleModel::where('is_top',1)->order('id','desc')->select();
        }
        $this->assign('articleTopList',$articleTopList);

        //获取栏目分类
        $categoryInfo = categoryModel::where('tpl',$tpl)->order('id','asc')->select();
        $this->assign('categoryInfo',$categoryInfo);

        //获取最新文章列表
        $articleNewList = articleModel::order('id','desc')->limit(10)->select();
        $this->assign('articleNewList',$articleNewList);

        //获取热点文章列表
        $articleHotList = articleModel::where('is_hot',1)
            ->order('id','desc')->limit(10)->select();
        $this->assign('articleHotList',$articleHotList);

        return $this->fetch('categoryList');
    }
}