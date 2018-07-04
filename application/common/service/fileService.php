<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/15
 * Time: 23:28
 */

namespace app\common\Service;

/*** 文件服务 ***/
class FileService {

    /**
     * 将xml转为array
     * @param string $xml
     * @return array[]
     */
    public function xmlToArray($xml)
    {
        if(!$xml){
            return false;
        }
        //将XML转为array
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $data;
    }

    /**
     * 远程网络文件下载到服务器指定目录
     * @param string $url
     * @param string $save_dir
     * @param string $filename
     * @param int $type=0 0:curl方式,1:readfile方式,2file_get_contents方式
     * @return array[]
     */
    function getFile($url, $save_dir = '', $filename = '', $type = 0) {
        if (trim($url) == '') {
            return false;
        }
        if (trim($save_dir) == '') {
            $save_dir = './';
        }
        if (0 !== strrpos($save_dir, '/')) {
            $save_dir.= '/';
        }
        //创建保存目录
        if (!file_exists($save_dir) && !mkdir($save_dir, 0777, true)) {
            return false;
        }
        //获取远程文件所采用的方法
        if ($type===0) {
            $ch = curl_init();
            $timeout = 5;
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $content = curl_exec($ch);
            curl_close($ch);
        }
        if($type===1){
            ob_start();
            readfile($url);
            $content=ob_get_contents();
            ob_end_clean();
        }
        if($type===2){
            $content=file_get_contents($url);
        }
        //echo $content;
        $size = strlen($content);
        //文件大小
        $fp2 = @fopen($save_dir . $filename, 'a');
        fwrite($fp2, $content);
        fclose($fp2);
        unset($content, $url);
        return array(
            'file_name' => $filename,
            'save_path' => $save_dir . $filename,
            'file_size' => $size
        );
    }

    //删除目录
    public function deleteDir($dir){
        if(strtoupper(substr(PHP_OS, 0, 3)) == 'WIN'){
            $cmd = "rmdir /s/q " . $dir;
        }else{
            $cmd = "rm -Rf " . $dir;
        }

        exec($cmd);
    }

    //复制目录中的文件
    public function copyFile($src,$target){
        if(strtoupper(substr(PHP_OS, 0, 3)) == 'WIN'){
            $cmd = implode(" ",array("copy",$src,$target));
        }else{
            $cmd = implode(" ",array("cp -R",$src.'/*',$target));
        }

        exec($cmd);
    }

    /**
     * 创建多级目录
     * @param string $dir
     * @param int $mode
     * @return boolean
     */
    public function create_dir($dir,$mode=0777)
    {
        return is_dir($dir) or ($this->create_dir(dirname($dir)) and mkdir($dir, $mode));
    }

    /**
     * 文件保存路径处理
     * @return string
     */
    public function check_path($path)
    {
        return (preg_match('/\/$/',$path)) ? $path : $path . '/';
    }

    /**
     * 删除文件
     * @param string $path
     * @return boolean
     */
    public function unlink_file($path)
    {
        $path = $this->dir_replace($path);
        if (file_exists($path))
        {
            return unlink($path);
        }
    }

    /**
     * 删除非空目录(目测不能删除，不知为什么)
     * 说明:只能删除非系统和特定权限的文件,否则会出现错误
     * @param string $dirName 目录路径
     * @param boolean $is_all 是否删除所有
     * @param boolean $del_dir 是否删除目录
     * @return boolean
     */
    public function remove_dir($dir_path,$is_all=FALSE)
    {
        $dirName = $this->dir_replace($dir_path);
        $handle = @opendir($dirName);
        while (($file = @readdir($handle)) !== FALSE)
        {
            if($file != '.' && $file != '..')
            {
                $dir = $dirName . '/' . $file;
                if($is_all)
                {
                    is_dir($dir) ? $this->remove_dir($dir) : $this->unlink_file($dir);
                }
                else
                {
                    if(is_file($dir))
                    {
                        $this->unlink_file($dir);
                    }
                }
            }
        }
        closedir($handle);
        return @rmdir($dirName);
    }

    /**
     * 替换相应的字符
     * @param string $path 路径
     * @return string
     */
    public function dir_replace($path)
    {
        return str_replace('//','/',str_replace('\\','/',$path));
    }

    /**
     * 文件操作(复制/移动)
     * @param string $old_path 指定要操作文件路径(需要含有文件名和后缀名)
     * @param string $new_path 指定新文件路径（需要新的文件名和后缀名）
     * @param string $type 文件操作类型
     * @param boolean $overWrite 是否覆盖已存在文件
     * @return boolean
     */
    public function handle_file($old_path,$new_path,$type='copy',$overWrite=FALSE)
    {
        $old_path = $this->dir_replace($old_path);
        $new_path = $this->dir_replace($new_path);
        if(file_exists($new_path) && $overWrite=FALSE)
        {
            return FALSE;
        }
        else if(file_exists($new_path) && $overWrite=TRUE)
        {
            $this->unlink_file($new_path);
        }

        $aimDir = dirname($new_path);
        $this->create_dir($aimDir);
        switch ($type)
        {
            case 'copy':
                return copy($old_path,$new_path);
                break;
            case 'move':
                return rename($old_path,$new_path);
                break;
        }
    }

    /**
     * 文件夹操作(复制/移动)
     * @param string $old_path 指定要操作文件夹路径
     * @param string $aimDir 指定新文件夹路径
     * @param string $type 操作类型
     * @param boolean $overWrite 是否覆盖文件和文件夹
     * @return boolean
     */
    public function handle_dir($old_path,$new_path,$type='copy',$overWrite=FALSE)
    {
        $new_path = $this->check_path($new_path);
        $old_path = $this->check_path($old_path);
        if (!is_dir($old_path)) return FALSE;

        if (!file_exists($new_path)) $this->create_dir($new_path);

        $dirHandle = opendir($old_path);

        if (!$dirHandle) return FALSE;

        $boolean = TRUE;

        while(FALSE !== ($file=readdir($dirHandle)))
        {
            if ($file=='.' || $file=='..') continue;

            if (!is_dir($old_path.$file))
            {
                $boolean = $this->handle_file($old_path.$file,$new_path.$file,$type,$overWrite);
            }
            else
            {
                $this->handle_dir($old_path.$file,$new_path.$file,$type,$overWrite);
            }
        }
        switch ($type)
        {
            case 'copy':
                closedir($dirHandle);
                return $boolean;
                break;
            case 'move':
                closedir($dirHandle);
                return rmdir($old_path);
                break;
        }
    }

    //创建目录
    public function makeDir($dir){
        mkdir ($dir);
    }

    /**
     * @param $file_path
     * @param $tmpDir
     */
    public function unZip($file_path,$tmpDir){
        //解压
        $zip = new \ZipArchive;
        $res = $zip->open($file_path);
        //halt($res);//在API平台打开debug和trace的时候，会报错
        if($res === TRUE) {
            $zip->extractTo($tmpDir);
            $zip->close();
            $this->unlink_file($file_path);//删除压缩包
            $msg="TRUE";
        } else {
            $this->unlink_file($file_path);//删除压缩包
            $msg="FALSE";
            exit;
        }
        return json($msg);
    }

    /**
     * @param $tmpDir
     * @param string $fileName
     * @return mixed
     */
    public function readXml($tmpDir,$fileName='plugin.xml'){
        //读取插件配置文件
        $pluginXmlFile = implode('/',array($tmpDir,$fileName));
        //$xml = (array)simplexml_load_file($pluginXmlFile); //强制转换为数组
        $xml = (array)simplexml_load_string(file_get_contents($pluginXmlFile)); //强制转换为数组

        //生成基础数据
        $pluginData['name'] = $xml['name'];
        $pluginData['description'] = $xml['description'];
        $pluginData['version'] = $xml['version'];
        $pluginData['appkey'] = $xml['appkey'];
        //读取插件数据库脚本配置
        $dbscript = array();
        $dbscriptNodes = $xml['dbscript'];
        $dbscript = (array)$dbscriptNodes;
        //生成脚本数据
        $pluginData['dbscript'] = json_encode($dbscript);
        $pluginData['instime'] = time();
        $pluginData['uptime'] = time();

        return $pluginData;
    }
}