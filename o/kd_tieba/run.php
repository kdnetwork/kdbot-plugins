<?php
/*MIX2TieBa 计划(原twtotb计划)*/

/*safe lock*/
if (!defined('SYSTEM_ROOT')) {
    header('HTTP/1.0 403 Forbidden');
    die("Illegal Access\n");
}
function kd_tieba_out($token = null,$to = null,$others = []) {
    $text = $others["message"]["text"];
    $o_replace = [["https://t.co/[0-9a-zA-Z]{10}", ""], ["@", "@ "]];
    foreach ($o_replace as $newkd2) {
        $text = preg_replace('~' . $newkd2[0] . '~', $newkd2[1], $text);
    }
    $tbinfo=json_decode(scurl('http://tieba.baidu.com/photo/bw/catch/threadInfo?tid='.$to),1);
    $fid=$tbinfo["data"]["fid"];
    $tbn=$tbinfo["data"]["fname"];
    $tbpic = null;
    $cookie = "BDUSS={$token};";
    $gettbs = json_decode(scurl('http://tieba.baidu.com/dc/common/tbs', '', '', $cookie, '', 1), true);
    $post_type = isset($others["post_type"]) ? $others["post_type"] : null;
    if ($gettbs["is_login"]) {
        $tbs = $gettbs["tbs"];
        switch ($post_type) {
            case "ajax":
                $fid = json_decode(file_get_contents('http://tieba.baidu.com/f/commit/share/fnameShareApi?ie=utf-8&fname=' . $tbn), 1)["data"]["fid"];
                $data = array("co" => $text, "_t" => time(), "tag" => 11, "upload_img_info" => $tbpic, "fid" => $fid, "src" => 1, "word" => $tbn, "tbs" => $tbs, "z" => $to);
                $a = json_decode(scurl('https://tieba.baidu.com/mo/q/apubpost', 'post', $data, $cookie, 'http://tieba.baidu.com/p/' . $to . '?pn=0&', 1), 1);
                /*我也忘记下面这个拿来干嘛了*/
                //18.12.9 看起来没什么用，先注释掉下一行
                //scurl('https://tieba.baidu.com/mo/q/m?kz=' . $to . '&last=1&has_url_param=0&is_ajax=1&post_type=normal&_t=' . time() . '000', 0, '', $cookie, 'http://tieba.baidu.com/p/' . $to . '?pn=0&', 1);
                //if ($a['no'] != "0") {
                //    echo $others["name"] . "：错误代码#" . $a['no'] . ',错误原因：' . $a['error'] . "\n";
                //} else {
                //    echo $others["name"] . '：发送成功' . "\n";
                //}
                $r = array(time(), json_encode($a), $to, $text);
                $r = message_array("out" , null,null,'pid'.$a["data"]["pid"],"kd_tieba",true,["text" => json_encode($a)]);
                break;
            default:
                $t = scurl('http://tieba.baidu.com/mo/m?kz=' . $to, 0, '', $cookie, '', 'kdcloud automatic bot');
                preg_match('/<form action=\"(.+?)\" method=\"post\">/', $t, $formurl);
                preg_match('/<input type=\"hidden\" name=\"ti\" value=\"(.*?)\"\/>/', $t, $ti);//标题
                preg_match('/<input type=\"hidden\" name=\"src\" value=\"(.*?)\"\/>/', $t, $src);//未知
                preg_match('/<input type=\"hidden\" name=\"floor\" value=\"(.*?)\"\/>/', $t, $floor);//楼层
                $data = ["co" => $text, "ti" => $ti[1], "src" => $src[1], "word" => $tbn, "tbs" => $tbs, "ifpost" => 1, "ifposta" => 0, "post_info" => 0, "tn" => "baiduWiseSubmit", "fid" => $fid, "verify" => "", "verify_2" => "", "pinf" => "1_2_0", "pic_info" => "", "z" => $to, "last" => 0, "pn" => 0, "r" => 0, "see_lz" => 0, "no_post_pic" => 0, "floor" => $floor[1], "sub1" => "回帖"];
                $a = scurl('http://tieba.baidu.com' . $formurl[1], 'post', $data, $cookie, 'http://tieba.baidu.com/mo/m?kz=' . $to, 'kdcloud automatic bot');
                if (!preg_match('/<span class=\"light\">回贴成功<\/span>/', $a)) {
                    preg_match('/<div class=\"d\">(.+?)<\/div>/', $a, $whyerror);
                    $result = $whyerror[1];
                } else {
                    $result = '发送成功';
                }
                //$r = array(time(), $result, $to, $text);
                $r = message_array("out",null,null,'time'.time(),"kd_tieba",true,$result);
        }
        return $r;
    }else{
        return message_array("out",null,null,'time'.time(),"kd_tieba",false);
    }
}
/*贴吧发图接口*/
function kd_tieba_pic_out($tbs, $path, $tbn, $mode, $cookie)
{
    if ($path == '') {
        return '';
    } else {
        $data = ['filetype' => 'base64', 'file' => base64_encode(file_get_contents($path))];
        $a = json_decode(scurl("https://uploadphotos.baidu.com/upload/pic?tbs={$tbs}&fid=&save_yun_album=0", 1, $data, $cookie, 'http://tieba.baidu.com/f?kw=' . $tbn, 3), 1);
        if ($a["err_no"] != 0) {
            return '';
        }
        //preg_match('/http:\/\/imgsrc.baidu.com\/tieba\/pic\/item\/(.+?).jpg/',$a["info"]["pic_water"],$b);
        switch ($mode) {
            case "ajax":
                if (preg_match('/sign=/', $a["info"]["pic_water"])) {
                    $bbbb = substr($a["info"]["pic_water"], -44, -5);
                } else {
                    $bbbb = $a["info"]["pic_id_encode"];
                }
                return $bbbb . ',' . $a["info"]["fullpic_width"] . ',' . $a["info"]["fullpic_height"] . ',false';
                break;
            default:
                return '#(pic,' . $a["info"]["pic_id"] . ',' . $a["info"]["fullpic_width"] . ',' . $a["info"]["fullpic_height"] . ')';
                break;
        }
    }
}
