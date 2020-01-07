<?php
namespace app\home\controller;
use think\Config;
use think\Model;
use think\Db;
use think\Request;

header("Access-Control-Allow-Origin: *");
class Movie extends Model
{
    public function index()
    {

    }

    public function list()
    {
        $request = Request::instance();
        $method  = $request->method();
        if ($method == 'POST') {
            $data = Db::name('item')->select();
            $return = array();
            foreach($data as $key => $row) {
                $series = explode('|', $row['series']);
                $return[$key]['id'] = $row['id'];
                $return[$key]['name'] = $row['name'];
                $return[$key]['poster'] = $row['poster'];
                $return[$key]['img'] = (Config::get('app_base_url') . 'movie/' . $row['poster']);
                $return[$key]['domains'] = json_decode($row['uri'], true);
                $return[$key]['series'] = $series;
                $return[$key]['describe'] = $row['describe'];
                $return[$key]['star'] = $row['star'];
                $return[$key]['score'] = $row['score'];
                $return[$key]['time'] = date('Y-m-d', $row['time']);
            }
            return json_encode($return);
        } else {
            return false;
        }
    }

    public function search()
    {
        $request = Request::instance();
        $method  = $request->method();
        if ($method == 'POST') {
            $param = $request->param();
            $search = $param['search'];
            $data = Db::name('item')->where('id|name|star', 'like', '%' . $search . '%')->select();

            if (!empty($data)) {
                $_search = array();
                foreach($data as $key => $row) {
                    $series = explode('|', $row['series']);
                    $_search[$key]['id'] = $row['id'];
                    $_search[$key]['name'] = $row['name'];
                    $_search[$key]['poster'] = $row['poster'];
                    $_search[$key]['img'] = (Config::get('app_base_url') . 'movie/' . $row['poster']);
                    $_search[$key]['domains'] = json_decode($row['uri'], true);
                    $_search[$key]['series'] = $series;
                    $_search[$key]['describe'] = $row['describe'];
                    $_search[$key]['star'] = $row['star'];
                    $_search[$key]['score'] = $row['score'];
                    $_search[$key]['time'] = date('Y-m-d', $row['time']);
                }

                $return = array(
                    'status'  => true,
                    'data'    => $_search,
                    'message' => '数据查询成功~'
                );
                return json_encode($return);
            } else {
                $return = array(
                    'status'  => false,
                    'message' => '未查找到数据~'
                );
                return json_encode($return);
            }
        } else {
            return false;
        }
    }

    public function tag()
    {
        $request = Request::instance();
        $method  = $request->method();
        if ($method == 'POST') {
            $param = $request->param();
            $tag = $param['tag'];
            $data = Db::name('item')->where('series', 'like', '%' . $tag . '%')->select();

            if (!empty($data)) {
                $_tag = array();
                foreach($data as $key => $row) {
                    $series = explode('|', $row['series']);
                    $_tag[$key]['id'] = $row['id'];
                    $_tag[$key]['name'] = $row['name'];
                    $_tag[$key]['poster'] = $row['poster'];
                    $_tag[$key]['img'] = (Config::get('app_base_url') . 'movie/' . $row['poster']);
                    $_tag[$key]['domains'] = json_decode($row['uri'], true);
                    $_tag[$key]['series'] = $series;
                    $_tag[$key]['describe'] = $row['describe'];
                    $_tag[$key]['star'] = $row['star'];
                    $_tag[$key]['score'] = $row['score'];
                    $_tag[$key]['time'] = date('Y-m-d', $row['time']);
                }

                $return = array(
                    'status'  => true,
                    'data'    => $_tag,
                    'message' => '数据查询成功~'
                );
                return json_encode($return);
            } else {
                $return = array(
                    'status'  => false,
                    'message' => '未查找到数据~'
                );
                return json_encode($return);
            }
        } else {
            return false;
        }

    }

    public function upload()
    {
        $files = request()->file('image');
        foreach($files as $file) {
            $info = $file->move(ROOT_PATH . 'public' . DS . 'movie');
            if($info){
                return json_encode($info->getSaveName());
            }else{
                return null;
            }
        }
    }

    public function arr($ser)
    {
        $arr = '';
        for ($i = 0; $i < count($ser); $i++) {
            if ($i == 0) {
                $arr = $arr . $ser[$i];
            } else {
                $arr = $arr . '|' . $ser[$i];
            }
        }
        return $arr;
    }

    public function add()
    {
        $request = Request::instance();
        $method  = $request->method();
        if ($method == 'POST') {
            $param = $request->param();
            $name = $param['name'];
            if (!Db::name('item')->where("name='$name'")->value('name')) {
                $series = action('Movie/arr', [$param['series']]);
                $data = [
                    'name' => $param['name'],
                    'poster' => $param['img'],
                    'uri' => json_encode($param['domains']),
                    'series' => $series,
                    'describe' => $param['describe'],
                    'star' => $param['star'],
                    'score' => $param['score'],
                    'time' => time()
                ];
    
                if (Db::name('item')->insert($data)) {
                    $add = Db::name('item')->where("name='$name'")->select();
                    $_add = array();
                    foreach($add as $key => $row) {
                        $series = explode('|', $row['series']);
                        $_add[$key]['id'] = $row['id'];
                        $_add[$key]['name'] = $row['name'];
                        $_add[$key]['img'] = (Config::get('app_base_url') . 'movie/' . $row['poster']);
                        $_add[$key]['domains'] = json_decode($row['uri'], true);
                        $_add[$key]['series'] = $series;
                        $_add[$key]['describe'] = $row['describe'];
                        $_add[$key]['star'] = $row['star'];
                        $_add[$key]['score'] = $row['score'];
                        $_add[$key]['time'] = date('Y-m-d', $row['time']);
                    }
    
                    $return = array(
                        'status'  => true,
                        'message' => '数据新增成功~',
                        'data' => $_add
                    );
                    return json_encode($return);
                } else {
                    $return = array(
                        'status'  => false,
                        'message' => '数据新增失败~'
                    );
                    return json_encode($return);
                }
            } else {
                $return = array(
                    'status'  => false,
                    'message' => '电影已存在~'
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
            $id = $param['id'];
            if (Db::name('item')->where("name='$name' AND id!='$id'")->value('name') == NUll) {
                $series = action('Movie/arr', [$param['series']]);
                $data = [
                    'name' => $param['name'],
                    'poster' => $param['img'],
                    'uri' => json_encode($param['domains']),
                    'series' => $series,
                    'describe' => $param['describe'],
                    'star' => $param['star'],
                    'score' => $param['score'],
                    'time' => time()
                ];

                if (Db::name('item')->where("id='$id'")->update($data)) {
                    $edit = Db::name('item')->where("id='$id'")->select();
                    $_edit = array();
                    foreach($edit as $key => $row) {
                        $series = explode('|', $row['series']);
                        $_edit[$key]['id'] = $row['id'];
                        $_edit[$key]['name'] = $row['name'];
                        $_edit[$key]['poster'] = $row['poster'];
                        $_edit[$key]['img'] = (Config::get('app_base_url') . 'movie/' . $row['poster']);
                        $_edit[$key]['domains'] = json_decode($row['uri'], true);
                        $_edit[$key]['series'] = $series;
                        $_edit[$key]['describe'] = $row['describe'];
                        $_edit[$key]['star'] = $row['star'];
                        $_edit[$key]['score'] = $row['score'];
                        $_edit[$key]['time'] = date('Y-m-d', $row['time']);
                    }

                    $return = array(
                        'status'  => true,
                        'data'    => $_edit,
                        'message' => '数据修改成功~'
                    );
                    return json_encode($return);
                } else {
                    $return = array(
                        'status'  => false,
                        'message' => '数据修改失败~'
                    );
                    return json_encode($return);
                }
            } else {
                $return = array(
                    'status'  => false,
                    'message' => '数据已存在~'
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
            if (Db::name('item')->where('id', $param['id'])->delete()) {
                $return = array(
                    'status'  => true,
                    'message' => '数据删除成功~'
                );
                return json_encode($return);
            } else {
                $return = array(
                    'status'  => false,
                    'message' => '数据删除失败~'
                );
                return json_encode($return);
            }
        } else {
            return false;
        }
    }
}
