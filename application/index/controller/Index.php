<?php
/*
 *
 */
namespace app\index\controller;
use app\common\controller\Base;
use app\common\model\guestModel;
use app\common\model\siteModel;
use think\facade\Request;

class Index extends Base
{
    public function index()
    {
        $siteInfo = siteModel::get(1);

        //halt($siteInfo);
        $siteName = $siteInfo['sitename'];
        $desc = $siteInfo['desc'];
        $keywords = $siteInfo['keywords'];
        $address = $siteInfo['address'];
        $email = $siteInfo['email'];
        $phone = $siteInfo['phone'];

        $this->assign('siteName',$siteName);
        $this->assign('description',$desc);
        $this->assign('keywords',$keywords);
        $this->assign('address',$address);
        $this->assign('email',$email);
        $this->assign('phone',$phone);

        return $this->view->fetch('index');
    }
    //前台留言
    public function guestSave()
    {
        //$data=Request::param();
        //halt($data);
        if(Request::isAjax()){
            $data=Request::param();
            //halt($data);
            if(guestModel::create($data,true)){
                return ['status'=>1,'message'=>'留言成功！'];
            }
            return ['status'=>0,'message'=>'留言失败'];
        }else{
            return ['status'=>0,'message'=>'留言失败'];
        }

    }


}
