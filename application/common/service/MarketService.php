<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/4
 * Time: 13:30
 */

namespace app\common\Service;
use app\common\controller\Base;
use app\common\service\FileService;
use app\common\model\pluginModel;
use think\Db;

class MarketService extends Base
{
    /**
     * 插件安装
     * @param $url
     * @param $tmpDir
     * @param $targetDir
     * @param $fileName
     * @return string
     */
    public function pInstall($url,$tmpDir,$targetDir,$fileName){
        //下载文件
        $fileService = new FileService();
        $fileInfo = $fileService->getFile($fileService->dir_replace($url),$tmpDir,$fileName,0);
        //halt($fileInfo);
        if($fileInfo=="" || empty($fileInfo)){
            return $fileService->resMsg(false,'插件下载失败!请检查网络。');
        }
        //解压
        if($fileService->unZip($fileInfo['save_path'],$tmpDir)===false){
            return $fileService->resMsg(false,'解压安装包出错！');
        }
        //读取插件配置文件
        $res = $fileService->readXml($tmpDir);
        //halt($res);
        if($res===false){
            return $fileService->resMsg(false,'读取异常！');
        }
        //检查安装条件
        $appKeyCount = pluginModel::where(array('appkey'=>$res['appkey']))->count();
        if($appKeyCount>0){
            return $fileService->resMsg(false,'插件已经存在');
        }
        //插件信息写入数据库
        pluginModel::create($res);
        //执行安装数据库脚本
        if(strlen(json_decode($res['dbscript'],true)['install'])>10){
            $arrSql = explode(';',json_decode($res['dbscript'],true)['install']);
            foreach ($arrSql as $value){
                if(trim($value)!==""){
                    Db::execute($value);
                }
            }
        }

        //创建模块目录
        $fileService->create_dir($targetDir,0777);
        //复制目录及文件
        if($fileService->handle_dir($tmpDir,$targetDir,'copy',true)){
            //删除临时解压目录
            if($fileService->remove_dir($tmpDir,true)){
                return $fileService->resMsg(true,'插件安装成功！');
            }else{
                return $fileService->resMsg(false,'插件安装成功！临时目录删除「失败」！');
            }
        }else{
            return $fileService->resMsg(false,'插件安装失败！文件复制失败！');
        }

    }

    /**
     * 插件卸载
     * @param $plugin_id
     * @param $targetDir
     * @return string
     */
    public function pUninstall($plugin_id,$targetDir){

        $fileService = new FileService();
        //获取插件信息
        $plugin = pluginModel::get($plugin_id);
        $dbscript = json_decode($plugin['dbscript'],true);

        if(empty($dbscript)){
            return $fileService->resMsg(false,'插件删除失败！脚本为空卸载「失败」！');
        }

        //执行卸载脚本
        if(strlen($dbscript['uninstall'])>10){
            $arrSql = explode(';',$dbscript['uninstall']);
            foreach ($arrSql as $value){
                if(trim($value)!=""){
                    Db::execute($value);
                }
            }
        }else{
            return $fileService->resMsg(false, '插件删除失败！脚本执行「失败」！');
        }
        pluginModel::destroy($plugin['id']);
        //删除插件目录文件服务
        if($fileService->remove_dir($targetDir,true)){
            //删除插件信息
            return $fileService->resMsg(true, '插件删除成功！');
        }else{
            return $fileService->resMsg(false, '插件删除成功！插件目录删除「失败」！');
        }

    }

    /**
     * 插件更新
     * @param $url
     * @param $tmpDir
     * @param $targetDir
     * @param $fileName
     * @return string
     */
    public function pUpgrade($url,$tmpDir,$targetDir,$fileName){
        //下载文件
        $fileService = new FileService();
        $fileInfo = $fileService->getFile($fileService->dir_replace($url),$tmpDir,$fileName,0);
        if($fileInfo=="" || $fileInfo=="NULL"){$this->error('插件下载失败!请检查网络。');}
        //解压
        $zip = new \ZipArchive;
        $res = $zip->open($fileInfo['save_path']);
        //halt($res);//在API平台打开debug和trace的时候，会报错
        if($res === TRUE) {
            $zip->extractTo($tmpDir);
            $zip->close();
            $fileService->unlink_file($fileInfo['save_path']);//删除压缩包
        } else {
            $fileService->unlink_file($fileInfo['save_path']);//删除压缩包
            $this->error("解压安装包出错！");
            exit;
        }
        //读取插件配置文件
        $pluginXmlFile = implode('/',array($tmpDir,'plugin.xml'));
        //$xml = (array)simplexml_load_file($pluginXmlFile); //强制转换为数组
        $xml = (array)simplexml_load_string(file_get_contents($pluginXmlFile)); //强制转换为数组
        //读取插件基础配置
        $nameNodes = $xml['name'];
        $descriptionNodes = $xml['description'];
        $versionNodes = $xml['version'];
        $appKeyNodes = $xml['appkey'];
        //检查安装条件
        $appKey = $appKeyNodes;
//        $appKeyCount = pluginModel::where(array('appkey'=>$appKey))->count();
//        if($appKeyCount>0){$this->error("插件已经存在",'/admin/plugin/market','','1');exit;}
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

        $pluginId = pluginModel::where('name',$pluginData['name'])->value('id');
        //插件信息写入数据库
        pluginModel::update($pluginData,$pluginId);

        //执行安装数据库脚本
        if(strlen($dbscript['update'])>10){
            $arrSql = explode(';',$dbscript['update']);
            foreach ($arrSql as $value){
                if(trim($value)!==""){
                    Db::execute($value);
                }
            }
        }


        //创建模块目录
        $fileService->create_dir($targetDir,0777);
        //复制目录及文件
        if($fileService->handle_dir($tmpDir,$targetDir,'copy',true)){
            //删除临时解压目录
            $fileService->remove_dir($tmpDir,true);
            return true;
        }else{
            return false;
        }
    }
}