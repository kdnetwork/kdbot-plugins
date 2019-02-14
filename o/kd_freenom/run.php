<?php
/*freenom*/

/*safe lock*/
if (!defined('SYSTEM_ROOT')) {
    header('HTTP/1.0 403 Forbidden');
    die("Illegal Access\n");
}
/*请求内部函数*/
function kd_freenom_out($token = null,$to = null,$others = ["months" => 1,"token"=>null]) {
    $account = json_decode($token, true);
    $username = $account["username"];
    //email
    $password = $account["password"];
    //password
    $months = $others["months"];
    //months (1-12)
    if ($months < 1 || $months > 12) {
        $months = 1;
    }
    preg_match_all('/Set-Cookie: (.+);/U',scurl('https://my.freenom.com/clientarea.php','get',null,null,null,3,10,true,false,false,true),$b);
    $c = '';
    foreach ($b[1] as $key) {
        $c .= $key.';';
    }
    preg_match('/name="token" value="(.+)"/',scurl('https://my.freenom.com/clientarea.php','get',null,$c),$b);
    $token = $b[1];
    preg_match_all('/Set-Cookie: (.+);/U',scurl('https://my.freenom.com/dologin.php','post',["token" => $token , "username" => $username , "password" => $password , "rememberme" => true],$c,'https://my.freenom.com/clientarea.php',3,10,true,false,10),$b);
    foreach ($b[1] as $key) {
        $c .= $key.';';
    }
    $a = scurl('https://my.freenom.com/domains.php?a=renewals','get',null,$c);
    preg_match_all('/<tr><td>(.*)<\/td><td>Active<\/td><td>/iU',$a,$b);
    preg_match_all('/>([0-9]*) Days<\/span>/iU',$a,$e);
    preg_match_all('/href="domains.php\?a=renewdomain&domain=([0-9]*)"/iU',$a,$d);
    $x = 0;
    $bd = [];
    foreach ($e[1] as $day) {
        if ($day <= 14) {
            $bd[] = array($b[1][$x],$d[1][$x]);
        }
        $x++;
    }
    if ($bd != []) {
        $message = [];
        foreach ($bd as $key) {
            preg_match('/name="token" value="(.+)"/',scurl('https://my.freenom.com/domains.php?a=renewdomain&domain=' . $key[1],'get',null,$c),$b);
            $token = $b[1];
            if (preg_match('/Location: cart.php\?a=complete/' , scurl('https://my.freenom.com/domains.php?submitrenewals=true','post',["token" => $token , "renewalid" => $key[1],"renewalperiod[" . $key[1] . "]" => $months . 'M', "paymentmethod" => "credit"],$c,null,3,10,true,false,10))) {
                $message = [$key[0] => "续期成功"];
            } else {
                $message = [$key[0] => "续期失败"];
            }
        }
        return message_array("out" , null , null , null , null , true , ["text" => "已续期".count($message)."个域名" , "others" => $message]);
    }else{
        return message_array("out" , null , null , null , null , false , ["text" => "无需续期域名"]);
    }
}