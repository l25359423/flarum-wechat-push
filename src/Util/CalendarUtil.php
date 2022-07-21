<?php
namespace Leo\WechatPush\Util;


class CalendarUtil
{
    public static function check($content)
    {
        return stristr($content, "日历") !== false
                || stristr($content, "黄历") !== false
                || stristr($content, "日期") !== false
                || stristr($content, "运势") !== false;
    }
    public static function query()
    {
        $weeks = array('日', '一', '二', '三', '四', '五', '六');
        $reply_content = "";
        $json = file_get_contents(base_path() . "/crawl-data/calendar.json");
        $arr = json_decode($json, true);
        if($arr){
            $reply_content .= sprintf("%s(%s) 星期%s\n",
                date('Y年 m月 d日'),
                $arr['ylDate'],
                $weeks[date("w")]);
            $reply_content .= $arr['guanzhi'] . "\n\n";
            $reply_content .= sprintf("宜：%s\n\n",
                implode(" ", $arr['yis']));
            $reply_content .= sprintf("忌：%s\n\n",
                implode(" ", $arr['jis']));
            $reply_content .= sprintf("生肖：%s\n", $arr['details']['生肖']);
            $reply_content .= sprintf("星座：%s\n", $arr['details']['星座']);
            $reply_content .= sprintf("节气：%s\n", $arr['details']['节气']);

        }
        return $reply_content;
    }
}
