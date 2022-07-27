<?php
namespace Leo\WechatPush\Util;


class SongUtil
{
    public static function check($content)
    {
        return stristr($content, "点歌") !== false;
    }
    public static function query($content)
    {
        $songName = str_replace("点歌", "", $content);
        try {
            $songList = self::getSongListByName($songName);

            $song = $songList['result']['songs'][0];
            $songID = $song['id'];
            $songName = $song['name'];

            $singer = "";
            foreach ($song['artists'] as $item){
                $singer .= $singer != ""
                    ? "/" . $item['name']
                    : $item['name'];
            }
        } catch (\Exception $e){

        }
        if(!$songID){
            return false;
        }
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
//        echo $xml;die;
        return $xml;
    }

    public static function getSongListByName($songName)
    {
        $songName = urlencode($songName);
        $curl = curl_init();
        $url = "http://music.163.com/api/search/get/web?csrf_token=hlpretag=&hlposttag=&s={$songName}&type=1&offset=0&total=true&limit=1";
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.0.0 Safari/537.36',
                'Cookie: NMTID=00OoxeFv_2A1UAXW0bWqcTynVduvsMAAAGCPyTAZg'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response, true);
    }
}
