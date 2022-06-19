<?php
namespace Leo\WechatPush\Util;

class ConstellationUtil
{
    protected static $constellations = array(
        "白羊座" => "aries",
        "金牛座" => "taurus",
        "双子座" => "gemini",
        "巨蟹座" => "cancer",
        "狮子座" => "leo",
        "处女座" => "virgo",
        "天秤座" => "libra",
        "天蝎座" => "scorpio",
        "射手座" => "sagittarius",
        "摩羯座" => "capricorn",
        "水瓶座" => "aquarius",
        "双鱼座" => "pisces",
    );

    public static function check($content)
    {
        return array_key_exists($content, self::$constellations);
    }
    public static function query($content)
    {
        $reply_content = "";
        $json = file_get_contents(base_path() .
            sprintf("/constellation/%s.json", self::$constellations[$content]));
        if($json){
            $arr = json_decode($json, true);
            $reply_content .= sprintf("💗%s💗\n\n", $content);
            foreach ($arr as $key => $val){
                $reply_content .= sprintf("%s: %s%s\n\n",
                    $key, $val['star'],
                    ($val['text'] ? "\n" . $val['text'] : ""));
            }
        }
        return $reply_content;
    }
}
