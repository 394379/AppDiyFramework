<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/2
 * Time: 12:58
 */

namespace app\admin\controller;
use app\common\controller\Base;

use app\common\model\casesModel;
use think\facade\Request;

class Cases extends Base
{
    public function index()
    {

    }

    public function caseadd()
    {
        $this->assign('action','caselist');
        return $this->fetch('case/caseadd');
    }

    public function caseSave()
    {
        if(Request::isPost()){
            $data = Request::post();
            $file = Request::file('img');
            $info = $file->validate([
                'size'=>1000000,
                'ext'=>'jpeg,jpg,png,gif',
            ])->move('uploads/');
            if($info){
                $data['img']=$info->getSaveName();
            }else{
                $this->error($file->getError());
            }

            if(casesModel::create($data)){
                $this->success('案例发布成功','index/caselist','','1');
            }else{
                $this->error('案例发布失败','','','1');
            }

        }else{
            $this->error('请求类型错误！');
        }
    }

    public function caseedit()
    {
        $id = Request::param('id');

        $caseInfo = casesModel::get($id);

        $this->assign('caseInfo',$caseInfo);
        $this->assign('action','caselist');
        return $this->fetch('case/caseedit');
    }

    public function caseUpdate()
    {
        if(Request::isPost()){
            $data = Request::post();
            $id = $data['id'];
            $file = Request::file('img');
            if($file!=null){
                $info = $file->validate([
                    'size'=>1000000,
                    'ext'=>'jpeg,jpg,png,gif',
                ])->move('uploads/');
                if($info){
                    $data['img']=$info->getSaveName();
                }else{
                    $this->error($file->getError());
                }
            }

            //halt($data);

            if(casesModel::update($data,$id)){
                $this->success('案例更新成功','index/caselist','','1');
            }else{
                $this->error('案例更新失败','','','1');
            }

        }else{
            $this->error('请求类型错误！');
        }
    }
}