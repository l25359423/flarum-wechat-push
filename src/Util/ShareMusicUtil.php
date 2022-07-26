<?php
namespace Leo\WechatPush\Util;


use Leo\WechatPush\DailyMusic;

class ShareMusicUtil
{
    public static function check($content)
    {
        return stristr($content, "歌曲") !== false ||
            stristr($content, "音乐") !== false;
    }
    public static function query()
    {
        $query = DailyMusic::query();
        $music = $query->where("released", 1)
            ->orderBy("id", "desc")
            ->limit(1)->get();
        $music = $music[0];
        $title = $music->title;
        $url = $music->url;
        $songName = explode("-", $title)[0];
        $singer = explode("-", $title)[1];
        $url_components = parse_url($url);
        parse_str($url_components['query'], $params);
        $songID = $params['id'];
        $xml = "<msg>
            <appmsg appid=\"wx8dd6ecd81906fd84\" sdkver=\"0\">
            <title>%s</title>
            <des>%s</des>
            <action />
            <type>3</type>
            <showtype>0</showtype>
            <soundtype>0</soundtype>
            <mediatagname />
            <messageext />
            <messageaction />
            <content />
            <contentattr>0</contentattr>
            <url>https://y.music.163.com/m/song?id=%s</url>
            <lowurl />
            <dataurl>http://music.163.com/song/media/outer/url?id=%s</dataurl>
            <lowdataurl />

            <appattach>
                <totallen>0</totallen>
                <attachid />
                <emoticonmd5 />
                <fileext />
                <cdnthumburl>3057020100044b30490201000204630925e402032f4f5502044c72512a020462d910ed042439653032333165342d383830632d343065662d386665332d3135626333616632366438350204012400030201000405004c4c6d00</cdnthumburl>
                <cdnthumbmd5>cde2407e51ba286323cedd5300372487</cdnthumbmd5>
                <cdnthumblength>4504</cdnthumblength>
                <cdnthumbwidth>135</cdnthumbwidth>
                <cdnthumbheight>135</cdnthumbheight>
                <cdnthumbaeskey>6d3bfa9c5f19fc0b3ce3c2dbdf250830</cdnthumbaeskey>

                <encryver>0</encryver>

            </appattach>
            <extinfo />
            <sourceusername />
            <sourcedisplayname />
            <thumburl />
            <md5 />

            <directshare>0</directshare>
            <recorditem><![CDATA[(null)]]></recorditem>

            </appmsg>
            <fromusername>yuzhe5</fromusername>
            <scene>0</scene>
            <appinfo>
                <version>49</version>
                <appname>网易云音乐</appname>
            </appinfo>
            <commenturl />
        </msg>";
        $xml = sprintf($xml, $songName, $singer, $songID, $songID);
        return $xml;
    }
}
