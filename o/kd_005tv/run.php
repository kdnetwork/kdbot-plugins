<?php
/*safe lock*/
if (!defined('SYSTEM_ROOT')) {
    header('HTTP/1.0 403 Forbidden');
    die("Illegal Access\n");
}
function kd_005tv_out($token = null,$to = null,$others = ["token"=>null]){
    $account = json_decode($token, true);
    $cookie = null;
    /*登录*/
    $login = scurl("http://bbs.005.tv/member.php?mod=logging&action=login&loginsubmit=yes&infloat=yes&lssubmit=yes","post",["quickforward" => "yes", "handlekey" => "ls", "username" => $account["username"], "password" => md5($account["password"]), "cookietime" => 2592000], null, null, 3, 10, true);
    preg_match_all('/Set-Cookie:(.*;)/iU', $login, $str);
    foreach ($str[1] as $key) {
        $cookie .= $key;
    }
    preg_match('/<script type="text\/javascript" src="http:\/\/usr.005.tv\/api\/uc.php\?(.+?)" reload="1"><\/script>/', $login, $str);
    $get_cookie = scurl("http://usr.005.tv/api/uc.php?" . $str[1], "get", null, null, null, 3, 10, true);
    $cookie = null;
    preg_match_all('/Set-Cookie:(.*;)/iU', $get_cookie, $str);
    foreach ($str[1] as $key) {
        $cookie .= $key;
    }
    $sign = json_decode(scurl("http://usr.005.tv/Index/sign_in.html?type=0", "post", null, $cookie, "http://usr.005.tv/", 3), true);
    $tr_sign = $sign["data"]["state"] == 2 ? true : false;
    $r_text = message_array("out" , null , null , null , null , $tr_sign , ["text" => $sign["msg"]]);
    return $r_text;
}