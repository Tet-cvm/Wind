<?php
namespace app\home\controller;
use think\Model;
use think\Db;
use think\Request;

header("Access-Control-Allow-Origin: *");

class Report
{
    public function index()
    {
        return 'report';
    }

    public function report()
    {
        $request = Request::instance();
        $method  = $request->method();
        if ($method == 'POST') {
            $param = $request->param();

            $userid = $param['userid'];

            if ($userid) {
                $userid = action('Common/authcode', [$userid, 'DECODE']);
            }
            
            $data = [
                'sceneid'  => $param['sceneid'],
                'userid'   => $userid,
                'acttype'  => $param['acttype'],
                'uniqueid' => $param['uniqueid'],
                'brand'    => $param['brand'],
                'type'     => $param['type'],
                'time'     => time()
            ];

            if (Db::name('report')->insert($data)) {
                $return = array(
                    'status'  => true,
                    'message' => '数据上报成功~'
                );
                return json_encode($return);
            } else {
                $return = array(
                    'status'  => false,
                    'message' => '数据上报错误~'
                );
                return json_encode($return);
            }
        } else {
            return false;
        }




    }
}
