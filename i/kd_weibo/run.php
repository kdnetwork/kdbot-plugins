<?php
/*safe lock*/
if (!defined('SYSTEM_ROOT')) {
    header('HTTP/1.0 403 Forbidden');
    die("Illegal Access\n");
}
function kd_weibo_in($uid = null,$pid = null,$others = []) {
    /*check*/
    $api = file_get_contents('https://weibo.cn/'.$uid);
    preg_match_all('/<title>(.+?)的微博<\/title>/',$api,$kd1);
    //$kd1[1][0]
    preg_match_all('/<div class=\"c\" id=\"(.+?)\">(.+?)<span class=\"ct\">(.+?)<\/span><\/div>/',$api,$kd2);
    if (preg_match('/<span class=\"kt\">置顶<\/span>/',$kd2[0][0])) {
        $text = chop(html_entity_decode(strip_tags($kd2[0][1])));
        $tweetid = $kd2[1][1];
    } else {
        $text = chop(html_entity_decode(strip_tags($kd2[0][0])));
        $tweetid = $kd2[1][0];
    }
    if ($tweetid == $pid) {
        return message_array();
    } else {
        return message_array("in" , null ,$uid , $pid, "kd_weibo", true ,["text" => $text]);
    }
}