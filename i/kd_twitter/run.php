<?php
/*safe lock*/
if (!defined('SYSTEM_ROOT')) {
    header('HTTP/1.0 403 Forbidden');
    die("Illegal Access\n");
}
function kd_twitter_in($uid = null,$pid = null,$others = []) {
    /*check*/
    if ($uid != null) {
        $get_update = json_decode(file_get_contents('https://twitter.com/i/profiles/show/' . $uid . '/timeline/tweets?composed_count=0&include_available_features=0&include_entities=0&include_new_items_bar=true&interval=30000&latent_count=0&min_position=' . $pid), true);
        $check = $get_update["new_latent_count"];
        if ($check > 0) {
            $pid = $get_update["max_position"];
            //$char_array = [0,1,2,3,4,5,6,7,8,9,'a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','-'];
            //$yy = null;
            //for($x = 0;$x < 22;$x++){
            //    $yy .= $char_array[rand(0,21)];
            //}
            $ch = curl_init();
            curl_setopt($ch,CURLOPT_URL,'https://mobile.twitter.com/');
            curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Linux; Android 5.1.1; Nexus 6 Build/LYZ28E) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Mobile Safari/537.36');
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
            $content = curl_exec($ch);
            curl_close($ch);
            preg_match('/gt=([0-9]*);/',$content,$a);
            
            $user_info = json_decode(scurl('https://api.twitter.com/graphql/GpQevDzQ0VLTV4-68vrePA','post',json_encode(["variables" => json_encode(["screen_name" => $uid , "withHighlightedLabel" => true]), "queryId" => "GpQevDzQ0VLTV4-68vrePA"]),null,'https://mobile.twitter.com/lovelive_staff',1,10,false,true,10,false,null,array("authorization: Bearer AAAAAAAAAAAAAAAAAAAAANRILgAAAAAAnNwIzUejRCOuH5E6I8xnZz4puTs%3D1Zv7ttfk8LF81IUq16cHjhLTvJu4FA33AGWWjCpTnA","content-type: application/json","x-guest-token: " . $a[1])) , true);
            $tweets = json_decode(scurl('https://api.twitter.com/2/timeline/profile/' . $user_info["data"]["user"]["rest_id"] . '.json?include_profile_interstitial_type=1&include_blocking=1&include_blocked_by=1&include_followed_by=1&include_want_retweets=1&include_mute_edge=1&include_can_dm=1&include_can_media_tag=1&skip_status=1&cards_platform=Web-12&include_cards=1&include_ext_alt_text=true&include_reply_count=1&tweet_mode=extended&include_entities=true&include_user_entities=true&include_ext_media_color=true&send_error_codes=true&include_tweet_replies=false&userId=' . $user_info["data"]["user"]["rest_id"] . '&count=' . $check . '&ext=mediaStats%2ChighlightedLabel','get',null,null,null,null,10,false,false,false,false,null,["authorization: Bearer AAAAAAAAAAAAAAAAAAAAANRILgAAAAAAnNwIzUejRCOuH5E6I8xnZz4puTs%3D1Zv7ttfk8LF81IUq16cHjhLTvJu4FA33AGWWjCpTnA","x-guest-token: " . $a[1]]) , true);
            
            /*以后再填*/
            //$o = ["text" => $user_info["data"]["user"]["legacy"]["name"] . " : \n",
            //        "photo" => [],
            //        "audio" => [],
            //        "video" => [],
            //        "file" => [],
            //        "others" => []
            //    ];
            $links = [];
            foreach(array_keys($tweets["globalObjects"]["tweets"]) as $s_tweetid){
                $links[] = "https://twitter.com/{$uid}/status/{$s_tweetid}";
            }
            $o = $user_info["data"]["user"]["legacy"]["name"] . "|@ {$uid} : \n";
            foreach ($tweets["globalObjects"]["tweets"] as $key => $value) {
                $b = $value;
                $o .= $b["full_text"] ."\n";//未完待续
                //$o["others"] =["language" => $b["lang"],"conversation_id_str" => $b["conversation_id_str"]];
            }
            return message_array("in" , null , $uid , $pid , "kd_twitter" , true , ["text" => trim($o),"others" => ["link" => $links]]);
        } else {
            return message_array();
        }
    }
}
