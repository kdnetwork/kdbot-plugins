<?php
/**
 * Author: BANKA2017
 * Version: 3.2
 */
/*safe lock*/
if (!defined('SYSTEM_ROOT')) {
    header('HTTP/1.0 403 Forbidden');
    die("Illegal Access\n");
}
function kd_acfun_out($token = null,$to = null,$others = ["token" => null]){
    $data = json_decode($token, true);
    //login (client)
    $get_token = json_decode(scurl('http://account.app.acfun.cn/api/account/signin/normal','post',["username" => $data["username"], "password" => $data["password"], "cid" => "ELSH6ruK0qva88DD"],null,null,null),true);
    $access_token = $get_token["vdata"]["token"];
    $cookie = "acPasstoken=" . $get_token["vdata"]["acPasstoken"];
    //检查登录状态及签到状态
    $signstatus = @json_decode(scurl('http://api.new-app.acfun.cn/rest/app/user/hasSignedIn','post',["access_token" => $access_token],$cookie,null,null,10,false,false,false,false,null,["content-encoding: gzip","acPlatform: ANDROID_PHONE"]),true);
    if($cookie !=null && !$signstatus["hasSignedIn"]){
        $sign  = @json_decode(scurl('http://api.new-app.acfun.cn/rest/app/user/signIn','post',["access_token" => $access_token],$cookie,null,null,10,false,false,false,false,null,["content-encoding: gzip","acPlatform: ANDROID_PHONE"]),true);
        $r_text = message_array("out" , null , null , null , null , true , ["text" => $sign["msg"]]);
    }else{
        $r_text = message_array("out" , null , null , null , null , false , ["text" => "今日已签到"]);
    }
    return $r_text;
}
