<?php

namespace Leo\WechatPush\Api\Controller;

use Flarum\Api\Controller\AbstractListController;
use Leo\WechatPush\City;
use Leo\WechatPush\Util\CalendarUtil;
use Leo\WechatPush\Util\PushMsgUtil;
use Leo\WechatPush\Util\QingYunUtil;
use Leo\WechatPush\Util\WeatherUtil;
use Leo\WechatPush\WeiboHot;
use Leo\WechatPush\Util\WeiBoHotUtil;
use Leo\WechatPush\Util\CoverToUpperUtil;
use Leo\WechatPush\Util\HongBaoUtil;
use Leo\WechatPush\Util\ConstellationUtil;
use Leo\WechatPush\Util\EatWhatUtil;
use Leo\WechatPush\Util\LimitLineUtil;
use Leo\WechatPush\Util\ShareMusicUtil;
use Leo\WechatPush\Util\SongUtil;
use Leo\WechatPush\Util\SearchDiscussionUtil;
use Leo\WechatPush\Util\TackoutUtil;
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
        $msg = isset($data['data']['msg']) ? $data['data']['msg'] : "";
        $raw_msg = isset($data['data']['raw_msg']) ? $data['data']['raw_msg'] : "";
        $room_wxid = isset($data['data']['room_wxid']) ? $data['data']['room_wxid'] : "";
        $from_wxid = isset($data['data']['from_wxid']) ? $data['data']['from_wxid'] : "";
        $wxid = $room_wxid ? : $from_wxid;
        $type = $data['type'];

        if($from_wxid == 'yuzhe5') {
            die;
        }

        $default_reply_content = "哎呀，你说的这个钢镚儿似乎还不太懂，你可以告诉舒克大大，让他来教教我~";

        if($type=="MT_RECV_OTHER_APP_MSG"){
            $this->processAppMsg($msg, $raw_msg, $wxid, $type);

        } else if($type=="MT_RECV_TEXT_MSG") {
            $this->processContentMsg($msg, $raw_msg, $wxid, $type);
        }

        die;
        return array("success" => true);
    }

    public function processAppMsg($msg, $raw_msg, $wxid, $type)
    {
        $obj = simplexml_load_string($raw_msg, 'SimpleXMLElement', LIBXML_NOCDATA);
        $json = json_encode($obj);
        $rawArr = json_decode($json, true);
        if(!isset($rawArr['appinfo']) || !isset($rawArr['appinfo']['appname'])){
            die;
        }

        if($rawArr['appinfo']['appname']=="美团"){
            if(TackoutUtil::checkStep($wxid, "init")){
                $reply_content = TackoutUtil::add($rawArr, $wxid, $raw_msg);
                PushMsgUtil::push($wxid, $reply_content);
            }
        }
    }

    public function processContentMsg($msg, $raw_msg, $wxid, $type)
    {
        if(!$msg && $msg !== 0 && $msg !== "0") {
            die;
        }
//        if(mb_substr($msg, 0, 4) != "@钢镚儿") {
//            die;
//        }
        if(mb_substr($msg, 0, 4) == "@钢镚儿") {
            $msg = str_replace(" ", "", trim(explode("@钢镚儿", $msg)[1]));
        }
        // 功能罗列
        if($msg=='功能'){
            $reply_content = "💥. 外卖红包领取，示例：\n@钢镚儿 外卖红包\n@钢镚儿 美团\n@钢镚儿 饿了么\n\n".
                "💥. 资源搜索，示例：\n@钢镚儿 资源搜索梦华录 | 资源搜索：梦华录\n\n".
                "💥. 热门微博(每十分钟更新一次)，示例：\n@钢镚儿 热门微博\n\n".
                "💥. 星座运势，示例：\n@钢镚儿 金牛座\n\n".
                "💥. 今日推荐歌曲，示例：\n@钢镚儿 推荐歌曲 | 今日推荐歌曲\n\n".
                "💥. 点歌，示例：\n@钢镚儿 点歌义勇军进行曲\n\n".
                "💥. 查询天气，示例：\n@钢镚儿 北京天气怎么样\n\n".
                "💥. 查询日历，示例：\n@钢镚儿 日历\n\n".
                "💥. 查询限行，示例：\n@钢镚儿 北京限行 | 北京明天限行\n\n".
                "💥. 金额转大写，示例：\n@钢镚儿 金额转大写：1111.23\n\n舒克大大没日没夜的开发中...";
            PushMsgUtil::push($wxid, $reply_content);
            die;
        }

        // 网址
        if(stristr("网址", $msg) !== false
            || stristr("网址", $msg) !== false
            || stristr("share", strtolower($msg)) !== false
            || stristr("sharebaby", strtolower($msg)) !== false){
            PushMsgUtil::push($wxid, "https://www.sharebaby.cn");
            die;
        }

        // 外卖红包
        if(HongBaoUtil::check($msg)){
            $reply_content = HongBaoUtil::query($msg);
            PushMsgUtil::push($wxid, $reply_content);
            die;
        }

        // 热门微博
        if(WeiBoHotUtil::check($msg)){
            $reply_content = WeiBoHotUtil::query();
            PushMsgUtil::push($wxid, $reply_content);
            die;
        }

        // 查询天气
        if(WeatherUtil::check($msg)){
            $reply_content = WeatherUtil::query($msg);
            PushMsgUtil::push($wxid, $reply_content);
            die;
        }

        // 金钱转大写
        if(CoverToUpperUtil::check($msg)){
            $reply_content = CoverToUpperUtil::query($msg);
            PushMsgUtil::push($wxid, $reply_content);
            die;
        }

        // 星座运势
        if(ConstellationUtil::check($msg)){
            $reply_content = ConstellationUtil::query($msg);
            PushMsgUtil::push($wxid, $reply_content);
            die;
        }

        // 日历
        if(CalendarUtil::check($msg)){
            $reply_content = CalendarUtil::query($msg);
            PushMsgUtil::push($wxid, $reply_content);
            die;
        }

        // 限行
        if(LimitLineUtil::check($msg)){
            $reply_content = LimitLineUtil::query($msg);
            PushMsgUtil::push($wxid, $reply_content);
            die;
        }

        // 每日推荐歌曲
        if(ShareMusicUtil::check($msg)){
            $reply_content = ShareMusicUtil::query($msg);
            PushMsgUtil::push($wxid, $reply_content, "xml");
            die;
        }

        // 点歌
        if(SongUtil::check($msg)){
            $reply_content = SongUtil::query($msg);
            if($reply_content === false){
                $reply_content = "抱歉，没找到您想要的歌曲";
                PushMsgUtil::push($wxid, $reply_content);
                die;
            }
            PushMsgUtil::push($wxid, $reply_content, "xml");
            die;
        }

        // 资源搜索
        if(SearchDiscussionUtil::check($msg)){
            $reply_content = SearchDiscussionUtil::query($msg);
            PushMsgUtil::push($wxid, $reply_content);
            die;
        }

        //今天中午吃什么
//        if(EatWhatUtil::check($msg)){
//            $reply_content = EatWhatUtil::query($msg);
//            PushMsgUtil::push($wxid, $reply_content);
//            die;
//        }

        // 添加外卖餐厅
        if(TackoutUtil::check($msg)){
            $reply_content = TackoutUtil::query($msg, $wxid);
            if(is_array($reply_content)){
                PushMsgUtil::push($wxid, $reply_content[1], $reply_content[0]);
            } else {
                PushMsgUtil::push($wxid, $reply_content);
            }
            die;
        }

        // 青云智能回复
        if(QingYunUtil::check($msg)){
            $reply_content = QingYunUtil::query($msg);
            if($reply_content){
                PushMsgUtil::push($wxid, $reply_content);
                die;
            }
        }

        // 默认回复
        if(mb_substr($msg, 0, 4) == "@钢镚儿"){
            PushMsgUtil::push($wxid, $default_reply_content);
        }
    }
}
