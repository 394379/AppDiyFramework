<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/19
 * Time: 15:46
 */

use app\cms\model\categoryModel;
use app\cms\model\likeModel;

if(!function_exists('getCategoryName'))
{
    function getCategoryName($id)
    {
        return categoryModel::where('id',$id)->value('name');
    }

}
if(!function_exists('getCategoryTpl'))
{
    function getCategoryTpl($id)
    {
        return categoryModel::where('id',$id)->value('tpl');
    }

}
if(!function_exists('getArticleContent'))
{
    function getArticleContent($content)
    {
        return mb_substr(strip_tags($content),0,230)."...";
    }

}

if(!function_exists('getArticleLike'))
{
    function getArticleLike($id)
    {
        return likeModel::count('article_id',$id);
    }

}

if(!function_exists('getTplName'))
{
    function getTplName($name)
    {
        if ($name=='article')
        {
            return '文章模板';
        }elseif($name=='down')
        {
            return '下载模板';
        }elseif($name=='xiaoshuo')
        {
            return '小说模板';
        }elseif($name=='product')
        {
            return '产品模板';
        }
        elseif($name=='team')
        {
            return '团队模板';
        }
        elseif($name=='job')
        {
            return '招聘模板';
        }else
        {
            return '未知';
        }
    }
}

if(!function_exists('getCategoryTpl'))
{
    function getCategoryTpl($id)
    {
        return categoryModel::where('id',$id)->value('tpl');
    }

}