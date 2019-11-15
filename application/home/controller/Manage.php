<?php
namespace app\home\controller;
use think\Config;
use think\Model;
use think\Db;
use think\Request;

header("Access-Control-Allow-Origin: *");

class Manage extends Model
{
    public function index()
    {

    }

    public function list()
    {
        $data = Db::name('user')->field('password', true)->select();
        $return = array();
        foreach($data as $key => $row) {
            $return[$key]['id'] = $row['id'];
            $return[$key]['unionid'] = $row['unionid'];
            $return[$key]['role'] = $row['role'];
            $return[$key]['name'] = $row['name'];
            $return[$key]['time'] = date('Y-m-d', $row['time']);
            $return[$key]['icon'] = (Config::get('app_base_url') . 'manage/' . $row['icon']);
            $return[$key]['img'] = $row['icon'];
        }
        return json_encode($return);
    }

    public function upload()
    {
        $files = request()->file('image');
        foreach($files as $file) {
            $info = $file->move(ROOT_PATH . 'public' . DS . 'manage');
            if($info){
                return json_encode($info->getSaveName());
            }else{
                return null;
            }
        }
    }

    public function union()
    {
        $union = Db::name('user')->order('id desc')->limit(1)->value('unionid');
        $_union = (int)$union+=1;
        $str = (string)$_union;
        $unionid = '';

        for ($i = 0; $i < (6 - strlen($str)); $i++) {
            $unionid = $unionid . '0';
        }
        return $unionid . $str;
    }

    public function add()
    {
        $request = Request::instance();
        $method  = $request->method();
        if ($method == 'POST') {
            $param = $request->param();
            $name = $param['name'];
            if (!Db::name('user')->where("name='$name'")->value('name')) {
                $union = action('Manage/union');
                $data = [
                    'unionid' => $union,
                    'icon' => $param['img'],
                    'role' => (int)$param['role'],
                    'name' => $param['name'],
                    'password'  => password_hash($param['pwd'], 1),
                    'time' => time()
                ];
    
                if (Db::name('user')->insert($data)) {
                    $add = Db::name('user')->where("unionid='$union'")->field('password', true)->select();
                    $_add = array();
                    foreach($add as $key => $row) {
                        $_add[$key]['id'] = $row['id'];
                        $_add[$key]['unionid'] = $row['unionid'];
                        $_add[$key]['role'] = $row['role'];
                        $_add[$key]['name'] = $row['name'];
                        $_add[$key]['time'] = date('Y-m-d', $row['time']);
                        $_add[$key]['icon'] = (Config::get('app_base_url') . 'manage/' . $row['icon']);
                        $_add[$key]['img'] = $row['icon'];
                    }
    
                    $return = array(
                        'status'  => true,
                        'message' => '新增用户成功~',
                        'data' => $_add
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
                $return = array(
                    'status'  => false,
                    'message' => '用户已存在~'
                );
                return json_encode($return);
            }
        } else {
            return false;
        }
    }

    public function edit()
    {
        $request = Request::instance();
        $method  = $request->method();

        if ($method == 'POST') {
            $param = $request->param();
            $name = $param['name'];
            $union = $param['unionid'];
            if (Db::name('user')->where("unionid='$union'")->value('unionid')) {
                if (Db::name('user')->where("name='$name' AND unionid!='$union'")->value('name') == NUll) {
                    if ($param['pwd'] == '******') {
                        $data = [
                            'icon' => $param['img'],
                            'name' => $param['name'],
                            'role' => (int)$param['role'],
                            'time' => time()
                        ];
                    } else {
                        $data = [
                            'icon' => $param['img'],
                            'name' => $param['name'],
                            'role' => (int)$param['role'],
                            'password'  => password_hash($param['pwd'], 1),
                            'time' => time()
                        ];
                    }

                    if (Db::name('user')->where("unionid='$union'")->update($data)) {
                        $edit = Db::name('user')->where("unionid='$union'")->field('password', true)->select();
                        $_edit = array();
                        foreach($edit as $key => $row) {
                            $_edit[$key]['id'] = $row['id'];
                            $_edit[$key]['unionid'] = $row['unionid'];
                            $_edit[$key]['role'] = $row['role'];
                            $_edit[$key]['name'] = $row['name'];
                            $_edit[$key]['time'] = date('Y-m-d', $row['time']);
                            $_edit[$key]['icon'] = (Config::get('app_base_url') . 'manage/' . $row['icon']);
                            $_edit[$key]['img'] = $row['icon'];
                        }

                        $return = array(
                            'status'  => true,
                            'data'    => $_edit,
                            'message' => '用户信息修改成功~'
                        );
                        return json_encode($return);
                    } else {
                        $return = array(
                            'status'  => false,
                            'message' => '用户信息修改失败~'
                        );
                        return json_encode($return);
                    }
                } else {
                    $return = array(
                        'status'  => false,
                        'message' => '用户名已存在~'
                    );
                    return json_encode($return);
                }
            } else {
                $return = array(
                    'status'  => false,
                    'message' => '用户不存在~'
                );
                return json_encode($return);
            }
        } else {
            return false;
        }
    }

    public function del()
    {
        $request = Request::instance();
        $method  = $request->method();
        if ($method == 'POST') {
            $param = $request->param();
            if ($param['unionid'] == '000001') {
                $return = array(
                    'status'  => false,
                    'message' => '默认账户不能删除~'
                );
                return json_encode($return);
            } else {
                if (Db::name('user')->where('unionid', $param['unionid'])->delete()) {
                    $return = array(
                        'status'  => true,
                        'message' => '用户删除成功~'
                    );
                    return json_encode($return);
                } else {
                    $return = array(
                        'status'  => false,
                        'message' => '用户删除失败~'
                    );
                    return json_encode($return);
                }
            }
        } else {
            return false;
        }
    }
}
