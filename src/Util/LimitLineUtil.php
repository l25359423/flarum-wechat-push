<?php
namespace Leo\WechatPush\Util;

use Overtrue\Pinyin\Pinyin;

class LimitLineUtil
{
    public static function check($content)
    {
        return stristr($content, "限行") !== false;
    }
    public static function query($msg)
    {
        $keywords = array(
            '昨天' => date('Y-m-d', strtotime('-1 day')),
            '今天' => date('Y-m-d'),
            '明天' => date('Y-m-d', strtotime('+1 day')),
            '后天' => date('Y-m-d', strtotime('+2 day')),
        );

        $city = str_replace("限行", "", $msg);

        $date = date('Y-m-d');

        foreach ($keywords as $keyword => $dateStr){
            if(stristr($msg, $keyword) !== false){
                $date = $dateStr;
                $city = str_replace($keyword, "", $city);
            }
        }
        $cityPinyin = Pinyin::permalink($city, '');

        $res = self::getXX($cityPinyin, $dateStr);

        if($res['status']===0){
            $reply_content = sprintf("限行尾号：%s\n限行时间：%s\n%s",
                $res['result']['number'], implode(", ", $res['result']['time']), $res['result']['numberrule']);
        } else {
            $reply_content = $res['msg'];
        }
        return $reply_content;
    }

    public static function getXX($city, $dateStr)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://jisuclwhxx.market.alicloudapi.com/vehiclelimit/query?city={$city}&date=".$dateStr,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: APPCODE 0c755cba710544bca42cddf9f53264fb'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response, true);
    }
}
