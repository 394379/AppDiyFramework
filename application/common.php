<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

use think\Db;

if(!function_exists('getAuthGroupName'))
{
  function getAuthGroupName($id)
  {
    return Db::table('yc_auth_group')->where('id',$id)->value('title');
  }

}

if(!function_exists('getRulesList'))
{
  function getRulesList($id)
  {
    return Db::table('yc_auth_group')->where('id',$id)->value('rules');
  }
}

if(!function_exists('pluginCount'))
{
    function pluginCount($appkey)
    {
        return Db::table('yc_plugin')->where('appkey',$appkey)->count();
    }
}



