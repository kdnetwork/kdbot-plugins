<?php
/*telegram*/

/*safe lock*/
if (!defined('SYSTEM_ROOT')) {
    header('HTTP/1.0 403 Forbidden');
    die("Illegal Access\n");
}
/*请求内部函数*/
require_once(PLUGINS_ROOT . '/o/kd_telegram/kd_telegram_post.php');
function kd_telegram_out($token = null,$to = null,$others = []) {
    $a = [];
    foreach ($others["message"] as $key => $value) {
        if($value != [] || $value != null){
            $a[] = kd_telegram_post_out([$key => $value],$token,$to,$key);//我是谁，我从哪来，我在干啥
        }
    }
    return $a;
}