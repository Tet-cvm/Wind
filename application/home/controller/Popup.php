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

    public function dialog()
    {
        $return = array(
            'status'  => true,
            'popup'   => true,
            'app'     => 'aaa://',
            'circle'  => '#ce3030',
            'ikon'    => 'https://mdqygl.cn/Test/Logo.png'
        );
        return json_encode($return);
    }
}
