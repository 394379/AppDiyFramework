<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/2
 * Time: 12:31
 */
namespace app\admin\controller;
use app\common\controller\Base;
use app\common\model\pluginModel;
use app\common\service\MarketService;
use app\common\service\HttpService;
use app\admin\validate\Login;
use think\facade\Env;
use think\facade\Request;
use think\facade\Session;
use think\facade\Config;


class Plugin extends Base
{
    //插件列表（已安装）
    public function pluginlist()
    {
        $this->noLogin();
        $pluginsList = pluginModel::order('name')->select();
        $this->assign("pluginsList",$pluginsList);
        $this->assign('moudle','plugin');
        return $this->fetch('pluginList');
    }
    //插件市场（远程数据）
    public function market()
    {
        $this->noLogin();
        $url = Config::get('app_api');
        $httpService = new HttpService();
        $resInfo = $httpService->download($url."plugin");
        $this->assign('plugins',$resInfo);
        return $this->fetch('market');
    }
    //远程验证
    public function remoteCheck()
    {
        $this->noLogin();
        $data = Request::param();
        $validate = new Login();
        if(!$validate->check($data)){$this->error($validate->getError(),'/admin/plugin/market','','1');exit;}
        $user_email=$data['email'];
        $user_password=$data['password'];
        $url = Config::get('app_loginCheck')."remoteCheck?user_email=".$user_email."&user_password=".$user_password;
        $httpService = new HttpService();
        $resInfo = $httpService->download($url);
        //dump($resInfo);
        if($resInfo==="EMAIL_FALSE"){$this->error('邮箱格式错误！','/admin/plugin/market','','1');}
        if($resInfo!=0){
            Session::set('remote_user_id',$resInfo[0]['id']);
            Session::set('remote_user_email',$resInfo[0]['email']);
            $this->success('登录市场成功！','/admin/plugin/market','','1');
        }else{
            $this->error('用户名或密码错误，请重新登录！','/admin/plugin/market','','1');
        }
    }
    //市场登出
    public function remoteLogout()
    {
        Session::set('remote_user_id','');
        Session::set('remote_user_email','');
        if(Session::get('remote_user_id')=="" && Session::get('remote_user_email')==""){
            $this->success('市场退出成功！','/admin/plugin/market','','1');
        }
    }
    //插件安装（重写安装最新20180705）
    public function install()
    {
        //判断登录与权限
        $this->noLogin();
        if(Session::get('remote_user_id')=="" && Session::get('remote_user_email')==""){
            $this->error('市场未登录！','/admin/plugin/market','','1');exit;
        }
        //异常操作检查
        $remoteUrl = Config::get('app_api')."getVerZip?user_id=".Session::get('remote_user_id')."&name=".input('name');
        $httpService = new HttpService();
        $resInfo = $httpService->download($remoteUrl);
        if($resInfo=="NULL" || $resInfo=="NOLOGIN"){
            $this->error('异常操作！','/admin/plugin/market','','1');exit;
        }
        //在线下载插件,参数准备
        $SD = DIRECTORY_SEPARATOR;//系统分隔符
        $rootDir = Env::get('runtime_path');//tmp目录
        $installDir = Env::get('root_path');
        $tmpDir =implode($SD,array(realpath($rootDir),'plugins','tmp',md5(input('pkg'))));//临时目录
        $url = Config::get('app_fileUpload').$resInfo[0]['verzip'][0]['url'];
        $fileName = $resInfo[0]['name'].'_'.$resInfo[0]['verzip'][0]['version'].'.zip';
        $targetDir = implode($SD,array(realpath($installDir),'application',$resInfo[0]['name']));//模块目录

        //安装操作
        $marketService = new MarketService();
        if($marketService->pInstall($url,$tmpDir,$targetDir,$fileName)){
            $this->success("插件安装成功！",'plugin/pluginList','','1');
        }else{
            $this->error("插件安装失败！",'plugin/market','','1');
        }
    }
    //卸载插件
    public function uninstall(){

        $this->noLogin();

        //系统分隔符
        $SD = DIRECTORY_SEPARATOR;

        //获取插件信息
        $plugin_id = input('id');
        $rootDir = env('ROOT_PATH');
        $plugin = pluginModel::get($plugin_id);
        $targetDir = implode($SD,array(realpath($rootDir),'application',$plugin['name']));//模块目录

        //卸载操作
        $marketService = new MarketService();
        if($marketService->pUninstall($plugin_id,$targetDir)){
            $this->success("插件卸载成功！",'plugin/market','','1');
        }else{
            $this->error("插件卸载失败！",'plugin/pluginList','','1');
        }
    }
    //升级插件（还没作升级测试）
    public function upgrade(){

        $this->noLogin();

        if(Session::get('remote_user_id')=="" && Session::get('remote_user_email')==""){
            $this->error('市场未登录！','/admin/plugin/market','','1');exit;
        }
        //异常操作检查
        $remoteUrl = Config::get('app_api')."getVerZip?user_id=".Session::get('remote_user_id')."&name=".input('name');
        $httpService = new HttpService();
        $resInfo = $httpService->download($remoteUrl);
        if($resInfo=="NULL" || $resInfo=="NOLOGIN"){
            $this->error('异常操作！请升级后再试！','/admin/plugin/market','','1');exit;
        }

        //系统分隔符
        $SD = DIRECTORY_SEPARATOR;
        //$pluginId = input('id');//插件ID
        $newVersion = input('version');//升级版本号
        //halt($newVersion);

        //$plugin = pluginModel::get($pluginId);
        $rootDir = Env::get('runtime_path');//tmp目录
        $installDir = Env::get('root_path');

        $fileName = strtolower($resInfo[0]['name']).'_'.$newVersion.'.zip';//新插件文件名
        //$packageFile = implode($SD,array(realpath($rootDir),'plugins','package',$targetFileName));//插件文件
        $tmpDir = implode($SD,array(realpath($rootDir),'plugins','tmp',md5($fileName)));//临时目录
        //获取模块目录
        $targetDir = implode($SD,array(realpath($installDir),'application',$resInfo[0]['name']));//模块目录
        $url = Config::get('app_fileUpload').$resInfo[0]['verzip'][0]['url'];
        //升级操作
        $marketService = new MarketService();
        if($marketService->pInstall($url,$tmpDir,$targetDir,$fileName)){
            $this->success("插件升级成功！",'plugin/pluginList','','1');
        }else{
            $this->error("插件升级失败！",'plugin/market','','1');
        }

    }
    //微信API调用
    public function orderpay(){

        $this->noLogin();
        if(empty(Session::get('remote_user_id')) || Session::get('remote_user_id')==""){
            $this->error('市场没登录！！','/admin/plugin/market','','1');exit;
        }
        $apiUrl = Config::get('app_wxpay');
        $data = Request::param();
        $remote_user_id = $data['remote_user_id'];
        $version=$data['version'];
        $product_id=$data['product_id'];
        $body=$data['body'];
        $total_fee=$data['total_fee'];
        if($total_fee!=0) {
            $httpService = new HttpService();
            $buy_url = $apiUrl."buyCheck?user_id=".Session::get('remote_user_id')."&plugin_id=".$product_id;
            $resBuy = $httpService->download($buy_url);
            //halt($resBuy);
            if($resBuy===0){
                $url = $apiUrl."orderpay?user_id=".$remote_user_id."&version=" . $version . "&product_id=" . $product_id . "&body=" . $body . "&total_fee=" . $total_fee;
                $resInfo = $httpService->download($url);
                if(self::orderCheck($apiUrl,$resInfo[0]['out_trade_no'])==1){
                    $this->redirect('/admin/plugin/install?name='.$data['body']);
                }else {
                    $this->assign('out_trade_no', $resInfo[0]['out_trade_no']);
                    $this->assign('Name', $resInfo[0]['Name']);
                    $this->assign('totalPrice', $resInfo[0]['totalPrice']);
                    $this->assign('url', $apiUrl.$resInfo[0]['url']);
                    $this->assign('plugin_id',$data['product_id']);
                    $this->assign('version_id',$data['version']);
                }
            }else{
                $this->redirect('/admin/plugin/install?name='.$data['body']);
            }
        }else{
            $this->redirect('/admin/plugin/install?name='.$data['body']);
        }

        return $this->fetch('order');

    }
    //查询订单
    public function orderCheck($apiUrl,$order_id)
    {
        $url = $apiUrl."orderpay?order_id=".input($order_id);
        $httpService = new HttpService();
        $resInfo = $httpService->download($url);
        echo $resInfo;
    }

}
