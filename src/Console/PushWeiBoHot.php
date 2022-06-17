<?php

namespace Leo\WechatPush\Console;

use Flarum\Console\AbstractCommand;
use Leo\WechatPush\Util\PushMsgUtil;
use Leo\WechatPush\Util\WeiBoHotUtil;

class PushWeiBoHot extends AbstractCommand
{
    protected $room_wxids = array(
        "91217048@chatroom", // family
        "24006113632@chatroom", // Battle-Ares
        "23935830943@chatroom", // Share Baby
    );
    protected function configure()
    {
        $this
            ->setName('leo:pushwbhot')
            ->setDescription('push wbhot');
    }

    protected function fire()
    {
        $reply_content = WeiBoHotUtil::query();
        $reply_content = sprintf("早上好[太阳]，为你送上今日热点：\n\n%s", $reply_content);
        foreach ($this->room_wxids as $room_wxid){
            PushMsgUtil::push($room_wxid, $reply_content);
        }
        // See https://docs.flarum.org/extend/console.html#console and
        // https://symfony.com/doc/current/console.html#configuring-the-command for more information.
    }
}
