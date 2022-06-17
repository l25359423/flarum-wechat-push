<?php

namespace Leo\WechatPush\Api\Controller;

use Flarum\Api\Controller\AbstractListController;
use Leo\WechatPush\City;
use Leo\WechatPush\Util\PushMsgUtil;
use Leo\WechatPush\Util\WeatherUtil;
use Leo\WechatPush\WeiboHot;
use Leo\WechatPush\Util\WeiBoHotUtil;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;
use Illuminate\Contracts\Bus\Dispatcher;

class WechatMsgController extends AbstractListController
{
    /**
     * {@inheritdoc}
     */
    protected function data(ServerRequestInterface $request, Document $document)
    {
        $data = json_decode(file_get_contents("php://input"), true);
        $msg = $data['data']['msg'];
        $room_wxid = $data['data']['room_wxid'];
        $default_reply_content = "哎呀，你说的这个钢镚儿似乎还不太懂，你可以告诉舒克大大，让它来教教我~";


        // 热门微博
        if(WeiBoHotUtil::check($msg)) {
            $reply_content = WeiBoHotUtil::query();
            PushMsgUtil::push($room_wxid, $reply_content);
            die;
        }

        // 查询天气
        if(WeatherUtil::check($msg)){
            $reply_content = WeatherUtil::query($msg);
            PushMsgUtil::push($room_wxid, $reply_content);
            die;
        }

        // 默认回复
        if(stristr($msg, "@钢镚儿") !== false){
            PushMsgUtil::push($room_wxid, $default_reply_content);
        }

        die;
        return array("success" => true);
    }
}
