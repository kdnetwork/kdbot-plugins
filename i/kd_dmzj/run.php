<?php
/*动漫之家*/

/*safe lock*/
if (!defined('SYSTEM_ROOT')) {
    header('HTTP/1.0 403 Forbidden');
    die("Illegal Access\n");
}
function kd_dmzj_in($uid = null,$pid = null,$others = "{\"single\":true}") {
    if(json_decode($others, true)["single"]) {
        //单独漫画
        $rss_xml = new sscurl('https://manhua.dmzj.com/' . substr($uid, 0, 1) . '/' . $uid . '/rss.xml');
        $info = simplexml_load_string($rss_xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        if (strtotime($info["channel"]["item"][0]["pubDate"]) > $pid) {
            //preg_match("/src=\'(.+?)\'/",$info["channel"]["item"][0]["description"],$a);//这里是图片，暂时没有解决反防盗链问题
            return message_array("in", null, $uid, strtotime($info["channel"]["item"][0]["pubDate"]), "kd_dmzj", true, ["text" => htmlentities(str_replace('~~动漫之家漫画网', '', $info["channel"]["item"][0]["title"]) . ' 更新了 ' . str_replace('?from=rssReader', '', strip_tags($info["channel"]["item"][0]["description"] . ' ' . $info["channel"]["item"][0]["link"])))]);
        } else {
            return message_array();
        }
    }else{
        //独立分区
        $get_tags = json_decode(new sscurl("http://v3api.dmzj.com/classify/filter.json"), true);//获取tags
        $default_tag = [];
        foreach ($get_tags as $get_tagss) {
            foreach($get_tagss["items"] as $get_tag){
                $default_tag = array_merge($default_tag, [$get_tag["tag_name"] => $get_tag["tag_id"]]);
            }
        }
        $manga = json_decode(substr(new sscurl("https://sacg.dmzj.com/mh/index.php?c=category&m=doSearch&status=0&reader_group=0&zone=0&initial=all&type={$default_tag[$uid]}&_order=t&p=1&callback=s"), 2, -2), true);
        $text = null;
        $get_manga_id = [];
        $compare_manga_id = ($pid != null) ? json_decode($pid, true) : [];
        foreach($manga["result"] as $key){
            $get_manga_id[] = $key["comic_url"];
        }
        $compare_manga_id = array_diff($get_manga_id, array_intersect($get_manga_id, $compare_manga_id));
        for($x = 0; $x < count($compare_manga_id); $x++){
            $text .= "漫画 {$manga["result"][$x]["name"]} 已更新 {$manga["result"][$x]["last_chapter"]} \nhttps://manhua.dmzj.com{$manga["result"][$x]["comic_url"]}\n";
            if($manga["result"][$x]["status"] == "[完</span>]"){
                $text .= "（已完结）\n";
            }
            $text .= "------\n";
        }
        if($text != null){
            return message_array("in", null, $uid, json_encode($get_manga_id), "kd_dmzj", true, ["text" => $text]);
        }else{
            return message_array();
        }
    }
}
