<?php
/*课表传送计划*/

/*safe lock*/
if (!defined('SYSTEM_ROOT')) {
    header('HTTP/1.0 403 Forbidden');
    die("Illegal Access\n");
}
function kd_schedule_in($uid = null,$pid = null,$others = null) {
    $text = "今日课程\n";
    $cc = substr($pid , 0 , -1);
    $week = $cc%2 == 0 ? 0 : 1;
    $day = substr($pid , -1);
    if ((date("N",RUN_TIME) >= $day || (date("N",RUN_TIME) == 1 && $day == 7))) {
        $a = sql_load("schedule",["*"],[["day" , "=" , $day] , ["single" , "!=" , $week] , ["from" , "<=" , $cc] , ["to" , ">=" , $cc]]);
        foreach ($a as $key) {
            $text .= "* {$key["time"]}的{$key["name"]} ，由{$key["teacher"]}在{$key["locate"]}上课\n\n";
        }
        if($text == "今日课程\n"){
            $text = "今日无课\n";
        }
        if ($day == 7) {
            $day = 1;
            $cc++;
        } else {
            $day++;
        }
        return message_array("in" , null ,$uid , ($cc*10)+$day, "kd_schedule", true ,["text" => $text]);
    } else {
        return message_array();
    }
}
function kd_schedule_date_week($d = null) {
    if ($d == null) {
        $d = date("N",time());
    }
    switch ($d) {
        case 1:
            $r = "星期一";
            break;
        case 2:
            $r = "星期二";
            break;
        case 3:
            $r = "星期三";
            break;
        case 4:
            $r = "星期四";
            break;
        case 5:
            $r = "星期五";
            break;
        case 6:
            $r = "星期六";
            break;
        case 7:
            $r = "星期日";
            break;
    }
    return $r;
}