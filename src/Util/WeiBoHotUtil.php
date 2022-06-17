<?php
namespace Leo\WechatPush\Util;


use Leo\WechatPush\WeiboHot;

class WeiBoHotUtil
{
    private static $weatherURL = 'https://devapi.qweather.com/v7/weather/now';
    private static $getLocationURL = 'https://geoapi.qweather.com/v2/city/lookup';
    private static $key = '0a1acf9d2c7b458dbbdb41e8d9a2fef6';
    public static function check($content)
    {
        return mb_substr($content, 0, 4) == "@钢镚儿" && (stristr($content, "微博热搜") !== false
                || stristr($content, "微博热门") !== false
                || stristr($content, "热门微博") !== false
                || stristr($content, "微博") !== false
                || stristr($content, "吃瓜") !== false
                || stristr($content, "热门") !== false
                || stristr(strtolower($content), "weibo") !== false);
    }
    public static function query()
    {
        $reply_content = "";
        $weibo_top = file_get_contents(base_path() . "/weibo-hot/hot.json");
        if($weibo_top){
            $weibo_top = json_decode($weibo_top, true);
            $weibo_top10 = array_slice($weibo_top, 0, 10);
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

                $reply_content .= sprintf("%d. %s \n链接: %s\n\n",
                    $index, $title, $weibo_url);
                $index++;
            }
        }
        return $reply_content;
    }
}
