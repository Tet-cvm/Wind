<?php
namespace app\home\controller;
use think\Model;
use think\Db;
use think\Request;

header("Access-Control-Allow-Origin: *");

class Active
{
    public function index()
    {
        return 'Active';
    }

    public function cloud()
    {
        $request = Request::instance();
        $method  = $request->method();
        if ($method == 'POST') {
            $data = Db::name('active')->select();
            $url = null;
            $status = null;
            foreach ($data as $key => $row) {
                $url = $row['url'];
                $status = $row['status'];
            }

            if ($url && $status == 0) {
                $return = array(
                    'status'  => true,
                    'url'    => $url,
                );
                return json_encode($return);
            } else {
                $return = array(
                    'status'  => false,
                );
                return json_encode($return);
            }
        } else {
            return false;
        }
    }
}
