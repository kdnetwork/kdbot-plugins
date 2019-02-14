<?php
//贴吧名人堂
/*safe lock*/
if (!defined('SYSTEM_ROOT')) {
    header('HTTP/1.0 403 Forbidden');
    die("Illegal Access\n");
}
function kd_baidu_tieba_support_out($token = null,$to = null,$others = ["fname" => null]){
    $fname = $others["fname"];
    $tr = false;
    //get tbs&fid
    $tbs = json_decode(new sscurl("http://tieba.baidu.com/dc/common/tbs", "get", ["Cookie: BDUSS={$token}"]), true)["tbs"];
    $fid = json_decode(new sscurl("http://tieba.baidu.com/f/commit/share/fnameShareApi?ie=utf-8&fname={$fname}"), true)["data"]["fid"];
    //get npcid
    $npcid = json_decode(new sscurl("https://tieba.baidu.com/celebrity/submit/getForumSupport", "post", ["Cookie: BDUSS={$token}"], 3, ["tbs" => $tbs, "forum_id" => $fid]), true)["data"][0]["npc_info"]["npc_id"];
    //post support
    $r_msg = json_decode(new sscurl("https://tieba.baidu.com/celebrity/submit/support", "post", ["Cookie: BDUSS={$token}"], 3, ["tbs" => $tbs, "forum_id" => $fid, "npc_id" => $npcid]), true);
    if(!$r_msg["no"]){
        $tr = true;
        $r_msg = "{$fname}吧助攻成功,已连续助攻{$r_msg["data"]["user"]["my_continue_days"]}天";//助冲成功
    }elseif($r_msg["no"] == 2280006){
        $tr = false;
        $r_msg = "今日已在{$fname}吧助攻";//已助冲
    }else{
        $tr = false;
        $r_msg = "{$fname}吧助攻过程出现问题";//已助冲
    }
    $r_text = message_array("out" , null , null , null , null , $tr , ["text" => $r_msg]);
    return $r_text;
}