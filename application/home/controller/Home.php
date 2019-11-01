<?php
namespace app\home\controller;
use think\Config;
use think\Model;
use think\Db;
use think\Request;

header("Access-Control-Allow-Origin: *");

class Home
{
    public function index()
    {
        return 'Home';
    }

    public function movie()
    {
        $request = Request::instance();
        $method  = $request->method();
        if ($method == 'POST') {
            $data = Db::name('item')->field('id, name, poster')->select();
            $_data = array();
            foreach($data as $key => $row) {
                $_data[$key]['id'] = $row['id'];
                $_data[$key]['name'] = $row['name'];
                $_data[$key]['poster'] = (Config::get('app_base_url') . 'movie/' . $row['poster']);
            }
            $return = array(
                'status'  => true,
                'data'    => $_data,
            );
            return json_encode($return);
        } else {
            return false;
        }
    }

    public function play()
    {
        $request = Request::instance();
        $method  = $request->method();
        if ($method == 'POST') {
            $param = $request->param();
            $id = $param['id'];
            $uniqueid = $param['uniqueid'];
            $member = Db::name('member')->where("uniqueid='$uniqueid'")->field('unionid')->select();
            $unionid = $member[0]['unionid'];

            if (Db::name('collect')->where("unionid='$unionid' AND item='$id'")->value('item')) {
                $collect = true;
            } else {
                $collect = false;
            }

            $data = Db::name('item')->where("id='$id'")->select();

            $return = array();
            foreach($data as $key => $row) {
                $series = explode('|', $row['series']);
                $return[$key]['id'] = $row['id'];
                $return[$key]['collect'] = $collect;
                $return[$key]['name'] = $row['name'];
                $return[$key]['poster'] = (Config::get('app_base_url') . 'movie/' . $row['poster']);
                $return[$key]['domains'] = json_decode($row['uri'], true);
                $return[$key]['series'] = $series;
                $return[$key]['describe'] = $row['describe'];
                $return[$key]['star'] = $row['star'];
                $return[$key]['score'] = $row['score'];
                $return[$key]['time'] = date('Y-m-d', $row['time']);
            }

            $_return = array(
                'status'  => true,
                'data'    => $return[0],
            );
            return json_encode($_return);
        } else {
            return false;
        }
    }

    public function history()
    {
        $request = Request::instance();
        $method  = $request->method();
        if ($method == 'POST') {
            $param = $request->param();
            $item = $param['id'];
            $uniqueid = $param['uniqueid'];
            $data = Db::name('member')->where("uniqueid='$uniqueid'")->field('unionid')->select();
            $unionid = $data[0]['unionid'];

            if (Db::name('history')->where("unionid='$unionid'")->value('unionid')) { // 有数据记录
                if ((Db::name('history')->where("unionid='$unionid' AND item='$item'")->value('item'))) { // 同一视频记录
                    Db::name('history')->where("unionid='$unionid' AND item='$item'")->update(['time' => time()]);
                } else { // 插入新记录
                    $data = [
                        'unionid' => $unionid,
                        'item' => $item,
                        'time' => time()
                    ];
                    Db::name('history')->insert($data);

                    // 限制记录12条
                    $count = Db::name('history')->where("unionid='$unionid'")->count();
                    if ($count > 24) {
                        $data = Db::name('history')->where("unionid='$unionid'")->order('time')->select();
                        $id = $data[0]['id'];
                        Db::name('history')->where("id='$id'")->delete();
                    }
                }
            } else { // 没有数据记录
                $data = [
                    'unionid' => $unionid,
                    'item' => $item,
                    'time' => time()
                ];
                Db::name('history')->insert($data);
            }
            return json_encode(true);
        } else {
            return false;
        }
    }

    public function record()
    {
        $request = Request::instance();
        $method  = $request->method();
        if ($method == 'POST') {
            $param = $request->param();
            $uniqueid = $param['uniqueid'];
            $data = Db::name('member')->where("uniqueid='$uniqueid'")->field('unionid')->select();
            $unionid = $data[0]['unionid'];

            $record = Db::name('history')->where("unionid='$unionid'")->select();
            $result = array();
            foreach($record as $key => $row) {
                $item = $row['item'];
                $find = Db::name('item')->where("id='$item'")->field('id, name, poster')->find();
                $find['time'] = date('m-d h:m', $row['time']);
                $result[$key] = $find;
            }

            $_result = array();
            foreach($result as $keys => $rows) {
                $_result[$keys]['id'] = $rows['id'];
                $_result[$keys]['name'] = $rows['name'];
                $_result[$keys]['time'] = $rows['time'];
                $_result[$keys]['poster'] = (Config::get('app_base_url') . 'movie/' . $rows['poster']);
            }

            $return = array(
                'status'  => true,
                'data' => $_result
            );
            return json_encode($return);
        }
    }

    public function collect()
    {
        $request = Request::instance();
        $method  = $request->method();
        if ($method == 'POST') {
            $param = $request->param();
            $item = $param['id'];
            $uniqueid = $param['uniqueid'];
            $data = Db::name('member')->where("uniqueid='$uniqueid'")->field('unionid')->select();
            $unionid = $data[0]['unionid'];

            if (Db::name('collect')->where("unionid='$unionid' AND item='$item'")->value('item')) { // 已收藏
                if (Db::name('collect')->where("unionid='$unionid' AND item='$item'")->delete()) {
                    $return = array(
                        'status'  => true,
                        'collect' => false,
                        'message' => '取消收藏~'
                    );
                    return json_encode($return);
                }
            } else { // 未收藏
                $data = [
                    'unionid' => $unionid,
                    'item' => $item,
                    'time' => time()
                ];

                if (Db::name('collect')->insert($data)) {
                    $return = array(
                        'status'  => true,
                        'collect' => true,
                        'message' => '收藏成功~'
                    );
                    return json_encode($return);
                }
            }
        } else {
            return false;
        }
    }
}
