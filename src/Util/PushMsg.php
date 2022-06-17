<?php
namespace Leo\WechatPush\Util;

class PushMsg
{
    public static function push($wxid, $msg)
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
