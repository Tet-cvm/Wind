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
                $return[$key]['series'] = $series;
                $return[$key]['describe'] = $row['describe'];
                $return[$key]['star'] = $row['star'];
                $return[$key]['score'] = $row['score'];
                $return[$key]['time'] = date('Y-m-d', $row['time']);

                $itemid = $row['id'];
                $source = Db::name('source')->where("itemid='$itemid'")->select();
                $_source = array();
                for ($k=0; $k<count($source); $k++) {
                    $_source[$k]['id']   = $source[$k]['id'];
                    $_source[$k]['key']   = $source[$k]['gather'];
                    $_source[$k]['value'] = $source[$k]['uri'];
                }
                $return[$key]['domains'] = $_source;
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
                    $_search[$key]['series'] = $series;
                    $_search[$key]['describe'] = $row['describe'];
                    $_search[$key]['star'] = $row['star'];
                    $_search[$key]['score'] = $row['score'];
                    $_search[$key]['time'] = date('Y-m-d', $row['time']);

                    $source = Db::name('source')->where('itemid', $row['id'])->select();
                    $_source = array();
                    for ($k=0; $k<count($source); $k++) {
                        $_source[$k]['id']   = $source[$k]['id'];
                        $_source[$k]['key']   = $source[$k]['gather'];
                        $_source[$k]['value'] = $source[$k]['uri'];
                    }
                    $_search[$key]['domains'] = $_source;
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
                    $_tag[$key]['series'] = $series;
                    $_tag[$key]['describe'] = $row['describe'];
                    $_tag[$key]['star'] = $row['star'];
                    $_tag[$key]['score'] = $row['score'];
                    $_tag[$key]['time'] = date('Y-m-d', $row['time']);

                    $source = Db::name('source')->where('itemid', $row['id'])->select();
                    $_source = array();
                    for ($k=0; $k<count($source); $k++) {
                        $_source[$k]['id']   = $source[$k]['id'];
                        $_source[$k]['key']   = $source[$k]['gather'];
                        $_source[$k]['value'] = $source[$k]['uri'];
                    }
                    $_tag[$key]['domains'] = $_source;
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
                    'name'   => $param['name'],
                    'poster' => $param['img'],
                    'series' => $series,
                    'describe' => $param['describe'],
                    'star'  => $param['star'],
                    'score' => $param['score'],
                    'time'  => time()
                ];
    
                if (Db::name('item')->insert($data)) {
                    $add = Db::name('item')->where("name='$name'")->select();
                    $_add = array();
                    foreach($add as $key => $row) {
                        $series = explode('|', $row['series']);
                        $_add[$key]['id'] = $row['id'];
                        $_add[$key]['name'] = $row['name'];
                        $_add[$key]['poster'] = $row['poster'];
                        $_add[$key]['img'] = (Config::get('app_base_url') . 'movie/' . $row['poster']);
                        $_add[$key]['series'] = $series;
                        $_add[$key]['describe'] = $row['describe'];
                        $_add[$key]['star'] = $row['star'];
                        $_add[$key]['score'] = $row['score'];
                        $_add[$key]['time'] = date('Y-m-d', $row['time']);
                    }

                    // 新增资源
                    $psource = $param['domains'];
                    $isource = array();
                    for ($i=0; $i<count($psource); $i++) {
                        $isource[$i]['itemid'] = $_add[0]['id'];
                        $isource[$i]['gather'] = $psource[$i]['key'];
                        $isource[$i]['uri'] = $psource[$i]['value'];
                    }

                    if (Db::name('source')->insertAll($isource)) {
                        $eid = $_add[0]['id'];
                        $asource = Db::name('source')->where("itemid='$eid'")->select();
                        $_asource = array();
                        foreach($asource as $key => $row) {
                            $_asource[$key]['id'] = $row['id'];
                            $_asource[$key]['key'] = $row['gather'];
                            $_asource[$key]['value'] = $row['uri'];
                        }

                        $_add[0]['domains'] = $_asource;
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
                    'series' => $series,
                    'describe' => $param['describe'],
                    'star' => $param['star'],
                    'score' => $param['score'],
                    'time' => time()
                ];

                if (Db::name('item')->where("id='$id'")->update($data)) {
                    if (Db::name('source')->where('itemid', $id)->delete()) {
                        $esource = $param['domains'];
                        $_esource = array();
                        for ($k=0; $k<count($esource); $k++) {
                            $_esource[$k]['itemid'] = $id;
                            $_esource[$k]['gather'] = $esource[$k]['key'];
                            $_esource[$k]['uri'] = $esource[$k]['value'];
                        }

                        Db::name('source')->insertAll($_esource);
                    } else {
                        $return = array(
                            'status'  => false,
                            'message' => '数据错误~'
                        );
                        return json_encode($return);
                    }

                    $edit = Db::name('item')->where("id='$id'")->select();
                    $_edit = array();
                    foreach($edit as $key => $row) {
                        $series = explode('|', $row['series']);
                        $_edit[$key]['id'] = $row['id'];
                        $_edit[$key]['name'] = $row['name'];
                        $_edit[$key]['poster'] = $row['poster'];
                        $_edit[$key]['img'] = (Config::get('app_base_url') . 'movie/' . $row['poster']);
                        $_edit[$key]['series'] = $series;
                        $_edit[$key]['describe'] = $row['describe'];
                        $_edit[$key]['star'] = $row['star'];
                        $_edit[$key]['score'] = $row['score'];
                        $_edit[$key]['time'] = date('Y-m-d', $row['time']);
                    }

                    $gsource = Db::name('source')->where("itemid='$id'")->select();
                    $_gsource = array();
                    for ($k=0; $k<count($gsource); $k++) {
                        $_gsource[$k]['key']   = $gsource[$k]['gather'];
                        $_gsource[$k]['value'] = $gsource[$k]['uri'];
                    }
                    $_edit[0]['domains'] = $_gsource;

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
                if (Db::name('source')->where('itemid', $param['id'])->delete()) {
                    $return = array(
                        'status'  => true,
                        'message' => '数据删除成功~'
                    );
                    return json_encode($return);
                } else {
                    $return = array(
                        'status'  => false,
                        'message' => '数据删除错误~'
                    );
                    return json_encode($return);
                }
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

    /**
     * Excel批量上传接口
    **/
    public function xlsx()
    {
        vendor("PHPExcel.PHPExcel");  // 导入类
        $objPHPExcel = new \PHPExcel();
        $file = request()->file('upload');
        $info = $file->validate(['ext' => 'xlsx'])->move(ROOT_PATH . 'public/xlsx/');
        if ($info) {
            $exclePath = $info->getSaveName();
            $file_name = ROOT_PATH . 'public/xlsx/' . DS . $exclePath;
            $objReader = \PHPExcel_IOFactory::createReader("Excel2007");
            $obj_PHPExcel = $objReader->load($file_name, $encode = 'utf-8');
            $excel_array = $obj_PHPExcel->getSheet(0)->toArray();

            if ($excel_array[0][0] == 'Item') {
                array_shift($excel_array);
                array_shift($excel_array);
                $movieObj = array();

                foreach($excel_array as $key => $row) {
                    if ($row[0]) {
                        $data = [
                            'name'     => $row[0],
                            'poster'   => $row[1],
                            'series'   => $row[2],
                            'describe' => $row[3],
                            'star'     => $row[4],
                            'score'    => (float)$row[5],
                            'time'     => time()
                        ];
                        $movieObj[$key] = $data;
                    }
                }

                if (Db::name('item')->insertAll($movieObj)) {
                    $return = array(
                        'status'  => true,
                        'message' => '数据导入成功~'
                    );
                    return json_encode($return);
                } else {
                    $return = array(
                        'status'  => false,
                        'message' => '数据导入失败~'
                    );
                    return json_encode($return);
                }
            }

            if ($excel_array[0][0] == 'Source') {
                array_shift($excel_array);
                array_shift($excel_array);
                $movieObj = array();

                foreach($excel_array as $key => $row) {
                    if ($row[0]) {
                        $name = $row[0];
                        $id = Db::name('item')->where("name='$name'")->value('id');

                        $data = [
                            'itemid' => $id,
                            'gather' => $row[1],
                            'uri'    => $row[2],
                        ];
                        $movieObj[$key] = $data;
                    }
                }

                if (Db::name('source')->insertAll($movieObj)) {
                    $return = array(
                        'status'  => true,
                        'message' => '数据导入成功~'
                    );
                    return json_encode($return);
                } else {
                    $return = array(
                        'status'  => false,
                        'message' => '数据导入失败~'
                    );
                    return json_encode($return);
                }
            }
        } else {
            $return = array(
                'status'  => false,
                'message' => $file->getError()
            );
            return json_encode($return);
        }
    }
}
