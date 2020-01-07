<?php
namespace app\home\controller;
use think\Config;
use think\Model;
use think\Db;
use think\Request;

header("Access-Control-Allow-Origin: *");

class Popup
{
    public function index()
    {
        return 'index';
    }

    /*
    $bezel  弹窗广告
    $starry 大图广告
    $chasm  底部广告
    */
    public function dialog()
    {
        $bezel = array(
            'status' => true,
            'app'    => 'aaa://',
            'circle' => '#ce3030',
            'image'  => 'https://mdqygl.cn/Test/Logo.png'
        );

        $starry = array(
            'status' => true,
            'image'  => 'https://mdqygl.cn/Test/Logo.png',
            'url'    => 'http://slogger.cn/cc.html'
        );

        $chasm = array (
            'status' => true,
            'image'  => 'https://mdqygl.cn/Test/Logo.png',
            'url'    => 'http://slogger.cn/aa.html'
        );


        $return = array(
            'status'  => true,
            'bezel'   => $bezel,
            'starry'  => $starry,
            'chasm'   => $chasm
        );
        return json_encode($return);
    }
}
