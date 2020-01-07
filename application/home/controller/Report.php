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

    public function query($condition, $total)
    {
        $data = Db::name('report')
                ->whereTime('time', 'between', [$condition[$total][0], $condition[$total][1]])
                ->select();
        return $data;
    }

    public function compute()
    {
        $request = Request::instance();
        $method  = $request->method();
        if ($method == 'POST') {
            $time = date('Y-m-d', time());
            $interval = array(
                '01:00', '02:00', '03:00', '04:00', '05:00', '06:00', '07:00', '08:00', '09:00', '10:00', '11:00', '12:00',
                '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00', '21:00', '22:00', '23:00', '24:00', '00:00'
            );
    
            $condition = array();
            for ($count = 0; $count < count($interval) - 1; $count++) {
                $condition[$count] = array($time . ' ' .$interval[$count], $time . ' ' .$interval[$count + 1]);
            }
    
            $query = array();
            $sceneid = array(
                '00001', '00002', '00003', '00004', '00005', '00006', '00007', '00008', '00009'
            );
    
            for ($total = 0; $total < count($condition); $total++) {
                $data = action('Report/query', [$condition, $total]);
                $query[$total] = $data;
            }
    
            $home_show1   = array(0 ,0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
            $home_click   = array(0 ,0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
            $player_show1 = array(0 ,0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
            $player_show2 = array(0 ,0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
            $active_show1 = array(0 ,0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
            $user_show1   = array(0 ,0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
            $user_click1  = array(0 ,0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
            $user_click2  = array(0 ,0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
            $user_click3  = array(0 ,0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
            $user_click4  = array(0 ,0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
            $user_click5  = array(0 ,0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
            $login_show1  = array(0 ,0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
            $login_click1 = array(0 ,0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
            $info_show1   = array(0 ,0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
            $history_show1 = array(0 ,0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
            $history_click1 = array(0 ,0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
            $history_click2 = array(0 ,0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
            $collect_show1 = array(0 ,0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
            $collect_click1 = array(0 ,0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
            $collect_click2 = array(0 ,0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
            $collect_click3 = array(0 ,0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
            $about_show1 = array(0 ,0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
    
            for ($num = 0; $num < count($query); $num++) {
                if ($query[$num]) {
                    foreach($query[$num] as $key => $row) {
                        switch($row['sceneid'])
                        {
                            case '00001':
                                if ($row['acttype'] == 'show' && $row['type'] == 1) {
                                    $home_show1[$num]++;
                                }
                                if ($row['acttype'] == 'click') {
                                    $home_click[$num]++;
                                }
                            break;
                            case '00002':
                                if ($row['acttype'] == 'show' && $row['type'] == 1) {
                                    $player_show1[$num]++;
                                }
                                if ($row['acttype'] == 'show' && $row['type'] == 2) {
                                    $player_show2[$num]++;
                                }
                            break;
                            case '00003':
                                if ($row['acttype'] == 'show' && $row['type'] == 1) {
                                    $active_show1[$num]++;
                                }
                            break;
                            case '00004':
                                if ($row['acttype'] == 'show' && $row['type'] == 1) {
                                    $user_show1[$num]++;
                                }
                                if ($row['acttype'] == 'click' && $row['type'] == 1) {
                                    $user_click1[$num]++;
                                }
                                if ($row['acttype'] == 'click' && $row['type'] == 2) {
                                    $user_click2[$num]++;
                                }
                                if ($row['acttype'] == 'click' && $row['type'] == 3) {
                                    $user_click3[$num]++;
                                }
                                if ($row['acttype'] == 'click' && $row['type'] == 4) {
                                    $user_click4[$num]++;
                                }
                                if ($row['acttype'] == 'click' && $row['type'] == 5) {
                                    $user_click5[$num]++;
                                }
                            break;
                            case '00005':
                                if ($row['acttype'] == 'show' && $row['type'] == 1) {
                                    $login_show1[$num]++;
                                }
                                if ($row['acttype'] == 'click' && $row['type'] == 1) {
                                    $login_click1[$num]++;
                                }
                            break;
                            case '00006':
                                if ($row['acttype'] == 'show' && $row['type'] == 1) {
                                    $info_show1[$num]++;
                                }
                            break;
                            case '00007':
                                if ($row['acttype'] == 'show' && $row['type'] == 1) {
                                    $history_show1[$num]++;
                                }
                                if ($row['acttype'] == 'click' && $row['type'] == 1) {
                                    $history_click1[$num]++;
                                }
                                if ($row['acttype'] == 'click' && $row['type'] == 2) {
                                    $history_click2[$num]++;
                                }
                            break;
                            case '00008':
                                if ($row['acttype'] == 'show' && $row['type'] == 1) {
                                    $collect_show1[$num]++;
                                }
                                if ($row['acttype'] == 'click' && $row['type'] == 1) {
                                    $collect_click1[$num]++;
                                }
                                if ($row['acttype'] == 'click' && $row['type'] == 2) {
                                    $collect_click2[$num]++;
                                }
                                if ($row['acttype'] == 'click' && $row['type'] == 3) {
                                    $collect_click3[$num]++;
                                }
                            break;
                            case '00009':
                                if ($row['acttype'] == 'show' && $row['type'] == 1) {
                                    $about_show1[$num]++;
                                }
                            break;
    
                        }
                    }
                }
            }
    
            $return = array();
            $return['status'] = true;
            $return['home_show1']   = $home_show1;
            $return['home_click']   = $home_click;
            $return['player_show1'] = $player_show1;
            $return['player_show2'] = $player_show2;
            $return['active_show1'] = $active_show1;
            $return['user_show1']   = $user_show1;
            $return['user_click1']  = $user_click1;
            $return['user_click2']  = $user_click2;
            $return['user_click3']  = $user_click3;
            $return['user_click4']  = $user_click4;
            $return['user_click5']  = $user_click5;
            $return['login_show1']  = $login_show1;
            $return['login_click1'] = $login_click1;
            $return['info_show1']   = $info_show1;
            $return['history_show1'] = $history_show1;
            $return['history_click1'] = $history_click1;
            $return['history_click2'] = $history_click2;
            $return['collect_show1'] = $collect_show1;
            $return['collect_click1'] = $collect_click1;
            $return['collect_click2'] = $collect_click2;
            $return['collect_click3'] = $collect_click3;
            $return['about_show1'] = $about_show1;
    
            return json_encode($return);
        } else {
            return false;
        }
    }
}
