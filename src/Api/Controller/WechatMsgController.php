<?php

namespace Leo\WechatPush\Api\Controller;

use Flarum\Api\Controller\AbstractListController;
use Leo\WechatPush\City;
use Leo\WechatPush\Util\PushMsgUtil;
use Leo\WechatPush\Util\QingYunUtil;
use Leo\WechatPush\Util\WeatherUtil;
use Leo\WechatPush\WeiboHot;
use Leo\WechatPush\Util\WeiBoHotUtil;
use Leo\WechatPush\Util\CoverToUpperUtil;
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
        $default_reply_content = "哎呀，你说的这个钢镚儿似乎还不太懂，你可以告诉舒克大大，让他来教教我~";

        if(mb_substr($msg, 0, 4) != "@钢镚儿") {
            die;
        }

        // 功能罗列
        $msgText = trim(explode("@钢镚儿", $msg)[1]);
        if($msgText=='功能'){
            $reply_content = "1. 热门微博(每小时更新一次)，示例：\n@钢镚儿 热门微博\n\n".
                "2. 查询天气，示例：\n@钢镚儿 北京天气怎么样\n\n".
                "3. 金额转大写，示例：\n@钢镚儿 金额转大写：1111.23\n\n不断努力开发中...";
            PushMsgUtil::push($room_wxid, $reply_content);
            die;
        }

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

        // 金钱转大写
        if(CoverToUpperUtil::check($msg)){
            $reply_content = CoverToUpperUtil::query($msg);
            PushMsgUtil::push($room_wxid, $reply_content);
            die;
        }

        // 青云智能回复
        if(QingYunUtil::check($msg)){
            $reply_content = QingYunUtil::query($msg);
            if($reply_content){
                PushMsgUtil::push($room_wxid, $reply_content);
                die;
            }
        }

        // 默认回复
        if(mb_substr($msg, 0, 4) == "@钢镚儿"){
            PushMsgUtil::push($room_wxid, $default_reply_content);
        }

        die;
        return array("success" => true);
    }
}
