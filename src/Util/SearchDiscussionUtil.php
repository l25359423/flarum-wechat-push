<?php
namespace Leo\WechatPush\Util;

use Maicol07\Flarum\Api\Client;
class SearchDiscussionUtil
{
    public static function check($content)
    {
        return mb_substr($content, 0, 4) == "资源搜索";
    }

    public static function query($content)
    {
        $zyName = preg_replace("/^资源搜索(:|：)?/", "", $content);

        $reply_content = "";
        $config = app('flarum.config');
        $url = (string)$config->url();
        $api = new Client($url, ['token' => 'zhewvzlxzfgxhjnzyhfujicmyvsngmxc; userId=1']);

        $list = $api->discussions()->filter(['q'=>$zyName])->request();
        try {
            $list = $list->collect()->toArray();
        } catch (Exception $e){
            $reply_content = "抱歉，没有找到有关{$zyName}的资源，请提交至以下链接或@舒克进行报备，找到资源后，我们会第一时间通知你。\nhttps://www.sharebaby.cn/d/92-wzdzy";
            return $reply_content;
        }
        if(!$list){
            $reply_content = "抱歉，没有找到有关{$zyName}的资源，请提交至以下链接或@舒克进行报备，找到资源后，我们会第一时间通知你。\nhttps://www.sharebaby.cn/d/92-wzdzy";
            return $reply_content;
        }
        $reply_content = "下面是有关<{$zyName}>的资源：\n\n";
        $index = 0;
        foreach ($list as $item){
            $index+=1;

            $reply_content .= sprintf("%s. %s\n", $index, $item->title);
            $tags = "";
            foreach ($item->tags as $tag){
                $tags .= $tags == ""
                    ? $tag->attributes['name']
                    : " / " . $tag->attributes['name'];
            }
            $reply_content .= sprintf("所在板块：%s\n", $tags);
            $reply_content .= sprintf("资源链接：%s/d/%s\n\n",
                $url, $item->slug);

            if($index >= 9){
                break;
            }
        }
        $reply_content .= sprintf("查看更多资源请访问：\n");
        $reply_content .= sprintf("%s?q=%s", $url, urlencode($zyName));
        return $reply_content;
    }
}
