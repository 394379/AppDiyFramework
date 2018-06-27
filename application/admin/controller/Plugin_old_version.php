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
use think\Db;
use app\common\service\fileService;
use think\facade\Env;


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

        $url = "http://api.yc78.com/index/plugin";
        //$url = "http://api.ycsoft.cm/index/plugin";
        //$res = file_get_contents($url);//重新采用curl来实现
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_ENCODING, "gzip");
        $res = curl_exec($ch);
        curl_close($ch);

        $resInfo = json_decode($res,true);

        //halt($resInfo);

        $this->assign('plugins',$resInfo);
        return $this->fetch('market');
    }

    //插件安装（重写安装最新20180517）
    public function install()
    {
        //判断登录与权限
        $this->noLogin();
        //在线下载插件,思路下载到指定目录
        //参数准备
        $SD = DIRECTORY_SEPARATOR;//系统分隔符
        $rootDir = env('ROOT_PATH');
        $targetDir =implode($SD,array(realpath($rootDir),'plugins','tmp',md5(input('pkg'))));//临时目录
        $url = 'http://api.yc78.com/uploads/'.$_GET['url'];
        $fileName = $_GET['name'].'_'.$_GET['version'].'.zip';
        //echo $url;
        //下载文件
        $fileService = new FileService();
        $fileInfo = $fileService->getFile($fileService->dir_replace($url),$targetDir,$fileName,0);
        if($fileInfo==""){$this->error('插件下载失败!请检查网络。');}
        //halt($fileInfo);
        //解压
        $zip = new \ZipArchive;
        $res = $zip->open($fileInfo['save_path']);
        //halt($res);
        if($res === TRUE) {
            $zip->extractTo($targetDir);
            $zip->close();
            $fileService->unlink_file($fileInfo['save_path']);//删除压缩包
        } else {
            $this->error("解压安装包出错！");exit;
        }

        //读取配置
        //插件配置文件
        $pluginXmlFile = implode($SD,array($targetDir,'plugin.xml'));
        $xml = (array)simplexml_load_file($pluginXmlFile); //强制转换为数组
        //读取插件基础配置
        $nameNodes = $xml['name'];
        $descriptionNodes = $xml['description'];
        $versionNodes = $xml['version'];
        $appKeyNodes = $xml['appkey'];
        //检查安装条件
        $appKey = $appKeyNodes;
        $appKeyCount = pluginModel::where(array('appkey'=>$appKey))->count();
        if($appKeyCount>0){$this->error("插件已经存在");exit;}
        //生成基础数据
        $pluginData['name'] = $nameNodes;
        $pluginData['description'] = $descriptionNodes;
        $pluginData['version'] = $versionNodes;
        $pluginData['appkey'] = $appKey;
        //读取插件数据库脚本配置
        $dbscript = array();
        $dbscriptNodes = $xml['dbscript'];
        $dbscript = (array)$dbscriptNodes;
        //生成脚本数据
        $pluginData['dbscript'] = json_encode($dbscript);
        $pluginData['instime'] = time();
        $pluginData['uptime'] = time();
        //执行安装数据库脚本
        if(strlen($dbscript['install'])>10){
            $arrSql = explode(';',$dbscript['install']);
            foreach ($arrSql as $value){
                if(trim($value)!==""){
                    Db::execute($value);
                }
            }
        }
        //插件信息写入数据库
        pluginModel::create($pluginData);

        //创建模块目录
        $modelDir = implode($SD,array(realpath($rootDir),'application',$pluginData['name']));//模块目录
        $fileService->create_dir($modelDir,0777);
        //复制目录及文件
        if($fileService->handle_dir($targetDir,$modelDir,'copy',true)){
            //删除临时解压目录
            $fileService->deleteDir($targetDir);
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
        $plugin = pluginModel::get(input('id'));
        $dbscript = json_decode($plugin['dbscript'],true);
        //halt($plugin);
        if(empty($dbscript))
        {
            $this->error('脚本为空，卸载失败','','','1');
        }

        //执行卸载脚本
        if(strlen($dbscript['uninstall'])>10){
            //M()->execute($dbscript['uninstall']);
            $arrSql = explode(';',$dbscript['uninstall']);
            foreach ($arrSql as $value){
                if(trim($value)!=""){
                    Db::execute($value);
                }
            }
            //Db::execute($dbscript['uninstall']);
        }

        //加载文件服务
        $fileService = new FileService();

        $rootDir = env('ROOT_PATH');

        //删除插件功能文件
        $modelDir = implode($SD,array(realpath($rootDir),'application',$plugin['name']));//模块目录
        $fileService->deleteDir($modelDir);

        //删除插件信息
        pluginModel::destroy($plugin['id']);

        $this->success("插件卸载成功！",'plugin/market','','1');
    }

    //升级插件（还没作升级测试）
    public function upgrade(){

        $this->noLogin();

        //系统分隔符
        $SD = DIRECTORY_SEPARATOR;

        $pluginId = input('id');//插件ID
        $newVersion = input('version');//升级版本号
        //halt($newVersion);

        $plugin = pluginModel::get($pluginId);

        $rootDir = env('ROOT_PATH');

        $targetFileName = strtolower($plugin['name']).'_'.$newVersion.'.zip';//新插件文件名
        $packageFile = implode($SD,array(realpath($rootDir),'Plugins','package',$targetFileName));//插件文件
        $extractDir = implode($SD,array(realpath($rootDir),'Plugins','tmp',md5($targetFileName)));//临时目录

        //halt($newVersion);
        $zip = new \ZipArchive;
        $res = $zip->open($packageFile);
        if($res === TRUE) {
            $zip->extractTo($extractDir);
            $zip->close();
        } else {
            $this->error("解压安装包出错！");exit;
        }

        //插件配置文件
        $pluginXmlFile = implode($SD,array($extractDir,'plugin.xml'));
        $xmlDoc = new \DOMDocument();
        $xmlDoc->load($pluginXmlFile);

        //读取插件基础配置
        $nameNodes = $xmlDoc->getElementsByTagName("name");
        $descriptionNodes = $xmlDoc->getElementsByTagName("description");
        $versionNodes = $xmlDoc->getElementsByTagName("version");
        $appKeyNodes = $xmlDoc->getElementsByTagName("appkey");

        //生成基础数据
        $pluginData['id'] = $pluginId;
        $pluginData['name'] = $nameNodes[0]->nodeValue;
        $pluginData['description'] = $descriptionNodes[0]->nodeValue;
        $pluginData['version'] = $versionNodes[0]->nodeValue;
        $pluginData['appkey'] = $appKeyNodes[0]->nodeValue;

        //读取插件菜单配置
        $menus = array();
        $menuNodes = $xmlDoc->getElementsByTagName("menu");
        foreach($menuNodes[0]->childNodes as $node){
            if($node->tagName){
                $menu['uri'] = $node->attributes['url']->nodeValue;
                $menu['title'] = $node->nodeValue;
                array_push($menus,$menu);
            }
        }

        //生成菜单数据
        $pluginData['menu'] = json_encode($menus);

        //读取插件数据库脚本配置
        $dbscript = array();
        $dbscriptNodes = $xmlDoc->getElementsByTagName("dbscript");
        foreach($dbscriptNodes[0]->childNodes as $node){
            if($node->tagName){
                $dbscript[$node->tagName] = $node->nodeValue;
            }
        }

        //生成脚本数据
        $pluginData['dbscript'] = json_encode($dbscript);

        //插件更新时间
        $pluginData['uptime'] = time();

        //更新数据库插件信息
        $this->pluginModel->data($pluginData)->save();

        //执行数据库升级脚本
        if(strlen($dbscript['update'])>10){
            M()->execute($dbscript['update']);
        }

        //加载文件服务
        $fileService = new FileService();

        //获取模块目录
        $modelDir = implode($SD,array(realpath(__ROOT__),'application',$pluginData['name']));//模块目录

        //删除模块目录
        $fileService->deleteDir($modelDir);

        //创建模块目录
        $fileService->makeDir($modelDir);

        //复制功能文件
        $functionFiles = scandir($extractDir);
        foreach($functionFiles as $functionFile){
            $srouceDir = implode($SD,array($extractDir,$functionFile));
            $targetDir = implode($SD,array($modelDir,$functionFile));

            if(is_dir($srouceDir) && $functionFile!='.' && $functionFile!='..'){
                $fileService->makeDir($targetDir);

                //判断目录中有无文件
                $files = scandir($srouceDir);
                if(sizeof($files)>2){
                    //复制目录中的文件
                    $fileService->copyFile($srouceDir,$targetDir);
                }
            }
        }

        //删除临时解压目录
        $fileService->deleteDir($extractDir);

        $this->success("插件升级成功！");
    }

    //删除插件
    public function delete(){

        $this->noLogin();

        $SD = DIRECTORY_SEPARATOR;//系统分隔符
        $roorDir = env('ROOT_PATH');

        $packageFile = implode($SD,array(realpath($roorDir),'Plugins','package',input('pkg')));//插件文件
        //halt($packageFile);
        if(unlink($packageFile)){
            $this->success("删除安装包成功！",'plugin/market','','1');
        }

    }

}