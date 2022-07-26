<?php
namespace Leo\WechatPush\Util;

use Leo\WechatPush\City;

class WeatherUtil
{
    private static $weatherURL = 'https://devapi.qweather.com/v7/weather/now';
    private static $getLocationURL = 'https://geoapi.qweather.com/v2/city/lookup';
    private static $key = '0a1acf9d2c7b458dbbdb41e8d9a2fef6';
    public static function check($content)
    {
        return stristr($content, '天气') !== false
            || stristr($content, '气候') !== false;
    }

    public static function query($content)
    {
        $reply_msg = "";
        $cities = City::all();

        foreach ($cities as $city){
            $city_name = $city['city'];

            $city_suffix = mb_substr($city_name, mb_strlen($city_name)-1, mb_strlen($city_name), "UTF-8");
            $short_city_name = $city_name;
            if(in_array($city_suffix, ['区', '县', '市', '省'])) {
                $short_city_name = mb_substr($city_name, 0, mb_strlen($city_name)-1);
            }

            if(stristr($content, $city_name) !== false
                || stristr($content, $short_city_name) !== false){
                $getLocationIDURL = sprintf("%s?key=%s&location=%s",
                    self::$getLocationURL, self::$key, urlencode($city_name));

                $locationData = self::request($getLocationIDURL);
                $locationID = $locationData['location'][0]['id'];

                $getWeatherURL = sprintf("%s?key=%s&location=%s",
                    self::$weatherURL, self::$key, $locationID);
                $weatherData = self::request($getWeatherURL);
                $text = $weatherData['now']['text'];
                $winDir = $weatherData['now']['windDir'];
                $windScale = $weatherData['now']['windScale'];
                $temp = $weatherData['now']['temp'];
                if ($text){
                    $reply_msg = sprintf("%s天气%s， %s%s级，当前温度：%s°C",
                        $city_name, $text, $winDir, $windScale, $temp);
                    break;
                }
            }
        }
        if(!$reply_msg){
            $reply_msg = "想问天气的话，可以这样问，例如：\n 北京市天气怎么样？";
        }
        return $reply_msg;
    }

    public static function request($url)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return json_decode($response, true);
    }
}
