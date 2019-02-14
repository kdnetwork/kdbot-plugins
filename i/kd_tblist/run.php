<?php
/*贴吧目录监视*/
/*safe lock*/
if (!defined('SYSTEM_ROOT')) {
    header('HTTP/1.0 403 Forbidden');
    die("Illegal Access\n");
}
function kd_tblist_in($uid = null,$pid = null,$others = null) {
    $others = json_decode($others, true);
    $b = json_decode(new sscurl("http://tieba.baidu.com/bawu2/platform/getAllDir?word={$uid}&ie=utf-8", 'get', ["Cookie: BDUSS={$others["token"]}"]), true)["all_dir"];
    $a1 = json_decode($pid, true);
    $b1 = [];
    //合并first dir, second dir为ky对
    foreach($b as $key => $values){
        foreach($values["level_2_name"] as $value){
            $b1[] = $values["level_1_name"] . "->" . $value;
        }
    }
    //一层目录交集
    $intersect = array_intersect($b1, $a1);
    $add = array_diff($b1, $intersect);
    $del = array_diff($a1, $intersect);
    if($add == [] && $del == []){
        return message_array();
    }else{
        $text = "贴吧目录监视\n------\n";
        if($add != []){
            $text .= "新增目录: \n";
            foreach ($add as $key){
                $text .= '  ' . html_entity_decode($key) . "\n";
            }
            $text .= "------\n";
        }
        if($del != []){
            $text .= "删除目录: \n";
            foreach ($del as $key){
                $text .= '  ' . html_entity_decode($key) . "\n";
            }
            $text .= "------\n";
        }
        return message_array("in", null, $uid, json_encode($b1, JSON_UNESCAPED_UNICODE), "kd_tblist", true, ["text" => $text]);
    }
}