<?php
namespace Leo\WechatPush\Util;

class QingYunUtil
{
    public static function check($msg)
    {
        return mb_substr($msg, 0, 4) == "@钢镚儿";
    }

    public static function query($msg)
    {
        $reply_content = self::request($msg);
        $reply_content = str_replace("菲菲", "钢镚儿", $reply_content);
        $reply_content = str_replace("{br}", "\n", $reply_content);
        return $reply_content['content'] ? : "";
    }

    public static function request($msg)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://api.qingyunke.com/api.php?key=free&appid=0&msg='.urlencode($msg),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response, true);
    }
}
