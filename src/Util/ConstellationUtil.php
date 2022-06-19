<?php
namespace Leo\WechatPush\Util;

class ConstellationUtil
{
    protected static $constellations = array(
        "白羊座" => array(
            "file" => "aries",
            "symbol" => "♈"
        ),
        "金牛座" => array(
            "file" => "taurus",
            "symbol" => "♉"
        ),
        "双子座" => array(
            "file" => "gemini",
            "symbol" => "♊"
        ),
        "巨蟹座" => array(
            "file" => "cancer",
            "symbol" => "♋"
        ),
        "狮子座" => array(
            "file" => "leo",
            "symbol" => "♌"
        ),
        "处女座" => array(
            "file" => "virgo",
            "symbol" => "♍"
        ),
        "天秤座" => array(
            "file" => "libra",
            "symbol" => "♎"
        ),
        "天蝎座" => array(
            "file" => "scorpio",
            "symbol" => "♏"
        ),
        "射手座" => array(
            "file" => "sagittarius",
            "symbol" => "♐"
        ),
        "摩羯座" => array(
            "file" => "capricorn",
            "symbol" => "♑"
        ),
        "水瓶座" => array(
            "file" => "aquarius",
            "symbol" => "♒"
        ),
        "双鱼座" => array(
            "file" => "pisces",
            "symbol" => "♓"
        ),
    );

    public static function check($content)
    {
        return array_key_exists($content, self::$constellations);
    }
    public static function query($content)
    {
        $reply_content = "";
        $json = file_get_contents(base_path() .
            sprintf("/constellation/%s.json", self::$constellations[$content]['file']));
        if($json){
            $arr = json_decode($json, true);
            $reply_content .= sprintf("💗%s💗\n\n", $content);
            foreach ($arr as $key => $val){
                $reply_content .= sprintf("%s%s: %s%s\n\n",
                    self::$constellations[$content]['symbol'], $key, $val['star'],
                    ($val['text'] ? "\n" . $val['text'] : ""));
            }
        }
        return $reply_content;
    }
}
