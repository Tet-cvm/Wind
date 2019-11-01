<?php
namespace app\home\controller;
use think\Config;
use think\Model;
use think\Db;
use think\Request;

header("Access-Control-Allow-Origin: *");

class Signin extends Model
{
    public function index()
    {
        // echo Env::get('app_debug');
        echo Config::get('app_base_url');
        // return 'Signin'; 
    }

    public function union()
    {
        $union = Db::name('member')->order('id desc')->limit(1)->value('unionid');
        $_union = (int)$union+=1;
        $str = (string)$_union;
        $unionid = '';

        for ($i = 0; $i < (6 - strlen($str)); $i++) {
            $unionid = $unionid . '0';
        }
        return $unionid . $str;
    }

    public function register()
    {
        $request = Request::instance();
        $method  = $request->method();

        if ($method == 'POST') {
            $param = $request->param();
            $brand = $param['brand'];
            $system = $param['system'];
            $uniqueid = $param['uniqueid'];
            $account = $param['account'];
            $password = $param['password'];

            if (!Db::name('member')->where("uniqueid='$uniqueid'")->value("uniqueid")) { // 设备未注册
                $union = action('Signin/union');
                $data = [
                    'unionid'  => $union,
                    'brand'    => $brand,
                    'system'   => $system,
                    'uniqueid' => $uniqueid,
                    'account'  => $account,
                    'password' => password_hash($password, 1),
                    'icon'     => '',
                    'level'    => 1,
                    'time'     => time()
                ];

                if (Db::name('member')->insert($data)) {
                    $key = Db::name('member')->where("account='$account'")->value('unionid');
                    $encode = action('Common/authcode', [$key, 'ENCODE']);
                    $return = array(
                        'status'  => true,
                        'key'     => $encode,
                        'message' => '注册成功~'
                    );
                    return json_encode($return);
                } else {
                    $return = array(
                        'status'  => false,
                        'message' => '数据错误~'
                    );
                    return json_encode($return);
                }
            } else { // 设备注册过
                if (Db::name('member')->where("account='$account'")->value("account")) { // 账号存在
                    $hash = Db::name('member')->where("account='$account'")->value('password');
                    if (password_verify($password, $hash)) {
                        $key = Db::name('member')->where("account='$account'")->value('unionid');
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
                } else { // 账号不存在
                    $return = array(
                        'status'  => false,
                        'message' => '设备已注册过账号~'
                    );
                    return json_encode($return);
                }
            }
        } else {
            return false;
        }
    }

    public function member()
    {
        $request = Request::instance();
        $method  = $request->method();

        if ($method == 'POST') {
            $param = $request->param();
            $uniqueid = $param['uniqueid'];
            if (Db::name('member')->where("uniqueid='$uniqueid'")->value("uniqueid")) { // 设备已注册
                $user = Db::name('member')->where("uniqueid='$uniqueid'")->field('nick, icon, level, sex, signature')->select();
                $_user = $user[0];

                // 默认头像
                if (!($_user['icon'])) {
                    $_user['icon'] = '';
                } else {
                    $_user['icon'] = (Config::get('app_base_url') . 'icon/' . $_user['icon']);
                }
                // 默认昵称
                if (!($_user['nick'])) {
                    $_user['nick'] = '\ (•◡•) /';
                }
                // 默认性别
                if ($_user['sex'] == 0) {
                    $_user['sex'] = true;
                } else {
                    $_user['sex'] = false;
                }
                // 默认签名
                if (!($_user['signature'])) {
                    $_user['signature'] = '这家伙很懒。';
                }

                $return = array(
                    'status'  => true,
                    'data'    => $_user,
                );
                return json_encode($return);
            } else { // 设备未注册
                $return = array(
                    'status'  => false,
                );
                return json_encode($return);
            }

        } else {
            return false;
        }
    }

    public function upload()
    {
        $request = Request::instance();
        $param = $request->param();
        $uniqueid = $param['uniqueid'];

        $files = request()->file('file');
        $info = $files->move(ROOT_PATH . 'public' . DS . 'icon');
        $icon = $info->getSaveName();

        $data = [
            'icon' => $icon,
        ];
        Db::name('member')->where("uniqueid='$uniqueid'")->update($data);
    }

    public function modify()
    {
        $request = Request::instance();
        $method  = $request->method();

        if ($method == 'POST') {
            $param = $request->param();
            $uniqueid = $param['uniqueid'];
            $type = $param['type'];

            switch($type)
            {
                case 0:
                    $data = [
                        'nick' => $param['nick'],
                    ];
                break;
                case 1:
                    $data = [
                        'sex' => $param['sex'],
                    ];
                break;
                case 2:
                    $data = [
                        'signature' => $param['signature'],
                    ];
                break;
            }

            if (Db::name('member')->where("uniqueid='$uniqueid'")->update($data)) {
                $return = array(
                    'status'  => true,
                    'message' => '数据更新成功~'
                );
                return json_encode($return);
            } else {
                $return = array(
                    'status'  => false,
                    'message' => '网络错误~'
                );
                return json_encode($return);
            }
        } else {
            return false;
        }
    }

}
