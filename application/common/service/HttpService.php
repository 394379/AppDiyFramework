<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/24
 * Time: 23:42
 */

namespace app\common\Service;


class HttpService
{

    /**
     * 远程获取返回json的API数据
     *
     * @param Url
     * @return Obj
     */
    public function download($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_ENCODING, "gzip");
        $res = curl_exec($ch);
        curl_close($ch);

        return json_decode($res,true);
    }
}