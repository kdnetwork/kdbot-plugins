<?php
/*safe lock*/
if(!defined('SYSTEM_ROOT')){
    header('HTTP/1.0 403 Forbidden');
    die("Illegal Access\n");
}
/*自带telegrambot post函数，快速迁移*/
function kd_telegram_post_out ($input,$token,$master_chat_id,$type = "forward",$others = []) {
    //这是所有消息通过bot向外发送的地方 | 一般的开发请不要调用post()函数，请分别调用下面的已封装好的函数 | 迟早会重构
    switch ($type) {
        case 'text':
            $out = scurl('https://api.telegram.org/bot' .$token .'/sendMessage','post',["chat_id" => $master_chat_id,"text" => $input["text"]],null,null,"KDboT",10);
            break;
        case 'audio':
            $out = scurl('https://api.telegram.org/bot' .$token .'/sendAudio','post',["chat_id" => $master_chat_id,"audio" => $input["audio"]],null,null,"KDboT",10);
            break;
        case 'document':
            $out = scurl('https://api.telegram.org/bot' .$token .'/sendDocument','post',["chat_id" => $master_chat_id,"document" => $input["document"]],null,null,"KDboT",10);
            break;
        case 'animation':
            $out = scurl('https://api.telegram.org/bot' .$token .'/sendAnimation','post',["chat_id" => $master_chat_id,"animation" => $input["animation"]],null,null,"KDboT",10);
            break;
        case 'photo':
            $out = scurl('https://api.telegram.org/bot' .$token .'/sendphoto','post',["chat_id" => $master_chat_id,"photo" => $input["photo"]],null,null,"KDboT",10);
            break;
        case 'sticker':
            $out = scurl('https://api.telegram.org/bot' .$token .'/sendMessage','post',["chat_id" => $master_chat_id,"sticker" => $input["sticker"]],null,null,"KDboT",10);
            break;
        case 'video':
            $out = scurl('https://api.telegram.org/bot' .$token .'/sendVideo','post',["chat_id" => $master_chat_id,"video" => $input["video"]],null,null,"KDboT",10);
            break;
        case 'voice':
            $out = scurl('https://api.telegram.org/bot' .$token .'/sendVoice','post',["chat_id" => $master_chat_id,"voice" => $input["voice"]],null,null,"KDboT",10);
            break;
        case 'video_note':
            $out = scurl('https://api.telegram.org/bot' .$token .'/sendVideoNote','post',["chat_id" => $master_chat_id,"video_note" => $input["video_note"]],null,null,"KDboT",10);
            break;
        //case 'contact':
        //    $out = scurl('https://api.telegram.org/bot' .$token .'/sendMessage','post',["chat_id" => $master_chat_id,"text" => $input["text"]],null,null,"KDboT",10);
        //    break;
        //case 'location':
        //    $out = scurl('https://api.telegram.org/bot' .$token .'/sendMessage','post',["chat_id" => $master_chat_id,"text" => $input["text"]],null,null,"KDboT",10);
        //    break;
        //case 'venue':
        //    $out = scurl('https://api.telegram.org/bot' .$token .'/sendMessage','post',["chat_id" => $master_chat_id,"text" => $input["text"]],null,null,"KDboT",10);
        //    break;
        case 'forward':
            $out = scurl('https://api.telegram.org/bot' .$token .'/forwardMessage',"post",["from_chat_id" => $input["message"]["chat"]["id"],"chat_id" => $master_chat_id, "message_id" => $input["message"]["message_id"]],null,null,"KDboT",10);
            break;
    }
    return json_decode($out , true);
}
//wait soon...
//function text($input){
//    $a = [post($input["media"],$GLOBALS["token"],$GLOBALS["master_chat_id"],"text")];
//    return $a;
//}
//function mix($input){
//    $a = [];
//    foreach (array_keys($input["media"]) as $key) {
//        if($input["media"][$key] != null){
//            $a[] = post($input["media"],$GLOBALS["token"],$GLOBALS["master_chat_id"],$key);
//        }
//    }
//    return $a;
//}