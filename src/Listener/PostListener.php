<?php

namespace Leo\WechatPush\Listener;

use Flarum\Post\Event\Posted;
use Leo\WechatPush\WechatPush;

class PostListener
{
    public function handle(Posted $event)
    {
        $data = json_decode(json_encode($event), true);

        $user_name = $data['post']['user']['nickname']
            ? : $data['post']['user']['username'];
        $discussion_id = $data['post']['discussion']['id'];
        $discussion_title = $data['post']['discussion']['title'];
        $discussion_slug = $data['post']['discussion']['slug'];
        $discussion_tag = $data['post']['discussion']['tags'][0]['name'];
        $type = $data['post']['discussion']['first_post_id'] ? 'comment' : 'post';
        $last_post_number = $data['post']['discussion']['last_post_number'];
        $discussion_content = $data['post']['content'];

        $config = app('flarum.config');
        $url = (string)$config->url();

        if ($type == 'post')
        {
            $d_url = sprintf("%s/d/%d-%s", $url, $discussion_id, $discussion_slug);
            $content = sprintf("%s在《%s》板块发布了帖子：\n%s\n详情请点击下面的链接：\n%s",
                $user_name, $discussion_tag, $discussion_title, $d_url);
        } else {
            $d_url = sprintf("%s/d/%d-%s/%d", $url, $discussion_id, $discussion_slug, $last_post_number);
            $content = sprintf("%s在《%s》回复了帖子说：\n%s\n详情请点击下面的链接：\n%s",
                $user_name, $discussion_title, strip_tags($discussion_content), $d_url);
        }

        $wechat_push = new WechatPush([
            "content" => $content,
            "url"  => $d_url
        ]);
        $wechat_push->save();

        $this->pushmsg("24006113632@chatroom", $content);
        $this->pushmsg("23935830943@chatroom", $content);
    }

    private function pushmsg($wxid, $msg)
    {
        $request_data = array(
            "client_id" => 1,
            "type" => "MT_SEND_TEXTMSG",
            "data" => array(
                "to_wxid" => $wxid,
                "content" => $msg
            )
        );

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://39.96.193.226:12580/',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>json_encode($request_data),
            CURLOPT_HTTPHEADER => array(
                'token: a9f6da01edb9727258bb3c411ae0a9bf',
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
    }
}
