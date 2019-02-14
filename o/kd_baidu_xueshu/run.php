<?php
//百度学术
/*safe lock*/
if (!defined('SYSTEM_ROOT')) {
    header('HTTP/1.0 403 Forbidden');
    die("Illegal Access\n");
}
function kd_baidu_xueshu_out($token = null,$to = null,$others = []){
    $r_msg = [];
    $tr = true;
    //急求最后两项的关键词，咸鱼实在写不出论文去提交啊
    foreach (["firstLookSub" => 1, "firstLogin" => 1, "firstLoginHelp" => 1, "firstSimpleSearch" => 1, "firstAdvancedSearch" => 1, "firstLookFavorite" => 1, "recommendKeyword" => 1, "newLable" => 3, "favoritePaper" => 2, "addSub" => 2] as $key => $value){
        for($x = 0; $x < $value; $x++){
            $r_msg = array_merge($r_msg, [($key . $x) => json_decode(new sscurl("https://xueshu.baidu.com/usercenter/show/userinfo?cmd=update_task&task_type=" . $key, "get", ["Cookie: BDUSS={$token}"]), true)["status"]]);
        }
    }
    foreach ($r_msg as $key => $value){
        if($value != 200){
            $tr = false;
            break;
        }
    }
    $r_text = message_array("out" , null , null , null , null , $tr , ["text" => json_encode($r_msg)]);
    return $r_text;
}