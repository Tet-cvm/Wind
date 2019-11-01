<?php
namespace app\home\controller;
use think\Model;
use think\Db;
use think\Request;

header("Access-Control-Allow-Origin: *");

class Login
{
    public function index()
    {
        return 'login';
    }

    public function login()
    {
        $request = Request::instance();
        $method  = $request->method();
        if ($method == 'POST') {
            $param = $request->param();
            $username = $param['username'];
            $password = $param['password'];

            if (Db::name('user')->where("name='$username'")->value('name')) {
                $hash = Db::name('user')->where("name='$username'")->value('password');
                if (password_verify($password, $hash)) {
                    $key = Db::name('user')->where("name='$username'")->value('unionid');
                    $encode = action('Common/authcode', [$key, 'ENCODE']);
                    $return = array(
                        'status'  => true,
                        'key'     => $encode,
                        'message' => '登录成功~'
                    );
                    return json_encode($return);
                } else {
                    $return = array(
                        'status'  => false,
                        'message' => '账户密码错误~'
                    );
                    return json_encode($return);
                }
            } else {
                $return = array(
                    'status'  => false,
                    'message' => '账户密码错误~'
                );
                return json_encode($return);
            }
        } else {
            return false;
        }
    }

    public function cookie()
    {
        $request = Request::instance();
        $method  = $request->method();
        if ($method == 'POST') {
            $param = $request->param();
            $unionid = $param['key'];
            $decode = action('Common/authcode', [$unionid, 'DECODE']);

            if (Db::name('user')->where("unionid='$decode'")->value('unionid')) {
                $data = Db::name('user')->field('name, icon')->where("unionid='$decode'")->find();
                $data['icon'] = ('http://172.18.28.82:8081/' . 'uploads/' . $data['icon']);
                $return = array(
                    'status'  => true,
                    'data'    => $data
                );
                return json_encode($return);
            } else {
                $return = array(
                    'status'  => false,
                    'message' => '数据错误~'
                );
                return json_encode($return);
            }
        } else {
            return false;
        }
    }
}
