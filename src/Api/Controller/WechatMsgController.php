<?php

namespace Leo\WechatPush\Api\Controller;

use Flarum\Api\Controller\AbstractListController;
use Leo\WechatPush\Util\PushMsg;
use Leo\WechatPush\WeiboHot;
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
        $file = fopen("/tmp/test.txt", "w");
        $data = json_decode(file_get_contents("php://input"), true);
        $msg = $data['data']['msg'];
        $room_wxid = $data['data']['room_wxid'];


        if(stristr($msg, "微博热搜") !== false
            || stristr($msg, "微博热门") !== false
            || stristr($msg, "微博") !== false) {
            $weibo_top = file_get_contents(base_path() . "/weibo-hot/hot.json");
            if($weibo_top){
                $weibo_top = json_decode($weibo_top, true);
                $weibo_top10 = array_slice($weibo_top, 0, 10);
                $content = "";
                $index = 1;
                foreach ($weibo_top10 as $title => $top) {
                    $title_md5 = substr(md5($title), 0, 16);

                    $query = WeiboHot::query();
                    $weibo_hot_obj = $query->where("title_md5", $title_md5)->get();

                    if($weibo_hot_obj->isEmpty()){
                        $weibo_hot = new WeiboHot([
                            "title_md5" => $title_md5,
                            "url"  => $top['href']
                        ]);
                        $weibo_hot->save();
                    }
                    $config = app('flarum.config');
                    $root_url = (string)$config->url();
                    $weibo_url = sprintf("%s/weibo/%s",
                        $root_url, $title_md5);

                    $content .= sprintf("%d. %s \n链接: %s\n\n",
                        $index, $title, $weibo_url);
                    $index++;
                }
                PushMsg::push($room_wxid, $content);
            }
        }
        fclose($file);
        die;
        return array("success" => true);
    }
}
