<?php
namespace app\home\controller;
use think\Config;
use think\Model;
use think\Db;
use think\Request;

header("Access-Control-Allow-Origin: *");
class Member extends Model
{
    public function index()
    {

    }

    public function list()
    {
        $request = Request::instance();
        $method  = $request->method();
        if ($method == 'POST') {
            $member = Db::name('member')->field('id, nick, account, icon, time')->select();
            $return = array();
            foreach($member as $key => $row) {
                $return[$key]['id'] = $row['id'];
                $return[$key]['nick'] = $row['nick'];
                $return[$key]['account'] = $row['account'];
                if ($row['icon'] == '') {
                    $return[$key]['icon'] = null;
                } else {
                    $return[$key]['icon'] = (Config::get('app_base_url') . 'uploads/' . $row['icon']);
                }
                
                $return[$key]['time'] = date('Y-m-d', $row['time']);
            }
            return json_encode($return);
        } else {
            return false;
        }

    }
}
