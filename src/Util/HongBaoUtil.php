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
        return sprintf("点击下方链接领取外卖红包，开启干饭模式~\n%s", $waimai_url);
    }
}
