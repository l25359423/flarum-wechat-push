<?php
namespace Leo\WechatPush\Util;

class HongBaoUtil
{
    public static function check($content)
    {
        return stristr($content, '外卖红包') !== false;
    }

    public static function query($content)
    {
        $config = app('flarum.config');
        $root_url = (string)$config->url();
        $waimai_url = $root_url . "/waimai";
        return sprintf("先领红包再干饭，既省钱来又划算，点击下方链接领取美团饿了么外卖红包，最高可领66元[红包][红包][红包]~\n\n%s", $waimai_url);
    }
}
