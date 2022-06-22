<?php
namespace Leo\WechatPush\Util;


use Leo\WechatPush\MeiTuanShop;
class EatWhatUtil
{

    public static function check($content)
    {
        return stristr("吃什么", $content) !== false;
    }
    public static function query()
    {
        $query = MeiTuanShop::query();
        $shop = $query->where("distance", "<", 2000)
            ->orderByRaw("rand()")
            ->first();
        $distance = round($shop['distance']/1000, 1);
        $distance = $distance > 1 ? $distance . "km" : $shop['distance'] . "m";

        $reply_content = sprintf("%s\n评分：%s\n距离：%s\n人均消费：%s\n月售：%s\n配送费：%s",
            $shop['name'],
            round($shop['score']/10, 1),
            $distance,
            $shop['average_price_tip'],
            $shop['monty_sales_tip'],
            $shop['shipping_fee_tip']);
        return $reply_content;
    }
}
