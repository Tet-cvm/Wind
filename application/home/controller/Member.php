<?php
namespace app\home\controller;
use think\Model;
use think\Db;

header("Access-Control-Allow-Origin: *");
class Member extends Model
{
    public function index()
    {
        // 登录成功
        $list1 = array(
            'id'   => 0,
            'role' => 0,
            'key'  => 'asdfgsdserfg',
            'user_name' => '卢山',
            'user_icon' => 'https://mdqygl.cn/Test/BG.png'
        );

        $list2 = array(
            'id'   => 1,
            'role' => 1,
            'key'  => 'asdfgsdserfg',
            'user_name' => '卢山',
            'user_icon' => 'https://mdqygl.cn/Test/BG.png'
        );

        $list3 = array(
            'id'   => 1,
            'role' => 1,
            'key'  => 'asdfgsdserfg',
            'user_name' => '卢山',
            'user_icon' => 'https://mdqygl.cn/Test/BG.png'
        );

        $list4 = array(
            'id'   => 1,
            'role' => 1,
            'key'  => 'asdfgsdserfg',
            'user_name' => '卢山',
            'user_icon' => 'https://mdqygl.cn/Test/BG.png'
        );

        $list5 = array(
            'id'   => 1,
            'role' => 1,
            'key'  => 'asdfgsdserfg',
            'user_name' => '卢山',
            'user_icon' => 'https://mdqygl.cn/Test/BG.png'
        );

        $list6 = array(
            'id'   => 1,
            'role' => 1,
            'key'  => 'asdfgsdserfg',
            'user_name' => '卢山',
            'user_icon' => 'https://mdqygl.cn/Test/BG.png'
        );

        $list7 = array(
            'id'   => 1,
            'role' => 1,
            'key'  => 'asdfgsdserfg',
            'user_name' => '卢山',
            'user_icon' => 'https://mdqygl.cn/Test/BG.png'
        );

        $data = array($list1, $list2, $list3, $list4, $list5, $list6, $list7);

        return json_encode($data);
    }

    public function add() {
        $data = [
            'unionid' => '000009',
            'name' => 'lxm333',
            'password' => '66666',
            'icon' => 'uuuuu',
            'level' => 1,
            'time'  => 42345
        ];

        return Db::name('member')->insert($data);
    }

    public function del() {
        return Db::name('member')->where('id', 2)->delete();
    }

    public function edit() {
        return Db::name('member')->where('id', 3)->update(['level' => 2]);
    }
}
