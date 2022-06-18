<?php
namespace Leo\WechatPush\Util;

class HongBaoUtil
{
    public static function check($content)
    {
        return stristr($content, '红包') !== false
            || stristr($content, '外卖') !== false
            || stristr($content, '美团') !== false
            || stristr($content, '饿了么') !== false;
    }

    public static function query($content)
    {
        $config = app('flarum.config');
        $root_url = (string)$config->url();
        $waimai_url = $root_url . "/waimai";
        return sprintf("点击下方链接领取美团饿了么外卖红包，最高可领66元，快去领取红包开启干饭模式吧[坏笑]~\n\n%s", $waimai_url);
    }
}
