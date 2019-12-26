<?php
namespace app\home\controller;
use think\Db;

class Index
{
    public function index()
    {
        $data = Db::name('user')->select();
        var_dump($data);
    }

    public function world()
    {
        return 'world';
    }

    public function aaa()
    {
        return 'aaa';
    }
}
