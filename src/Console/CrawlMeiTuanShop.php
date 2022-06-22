<?php

namespace Leo\WechatPush\Console;

use Flarum\Console\AbstractCommand;
use Leo\WechatPush\MeiTuanShop;
use Leo\WechatPush\Util\PushMsgUtil;
use Leo\WechatPush\Util\WeiBoHotUtil;

class CrawlMeiTuanShop extends AbstractCommand
{
    protected $limit = 10;

    protected $jm = array(
        "&#xe758;" => 0,
        "&#xf44a;" => 1,
        "&#xe429;" => 2,
        "&#xe8ef;" => 3,
        "&#xeeef;" => 4,
        "&#xf0de;" => 5,
        "&#xf866;" => 6,
        "&#xe803;" => 7,
        "&#xf4ba;" => 8,
        "&#xe201;" => 9,
    );

    protected function configure()
    {
        $this
            ->setName('leo:crawl-meituan')
            ->setDescription('crawl-meituan');
    }

    protected function fire()
    {
        for($i=0; $i<$this->limit; $i++){
            $data = $this->request($i);
            $shops = $data['data']['shopList'];
            foreach ($shops as $shop) {
                $shopData = [
                    'poi_id' => $shop['mtWmPoiId'],
                    'name' => $shop['shopName'],
                    'score' => $shop['wmPoiScore'],
                    'monty_sales_tip' => $this->format($shop, 'monthSalesTip'),
                    'pic_url' => $shop['picUrl'],
                    'delivery_time_tip' => $this->format($shop, 'deliveryTimeTip'),
                    'min_price_tip' => $this->format($shop, 'minPriceTip') ? : 0,
                    'shipping_fee_tip' => $this->format($shop, 'shippingFeeTip') ? : 0,
                    'distance' => $this->format($shop, 'distance') ? : 0,
                    'average_price_tip' => $this->format($shop, 'averagePriceTip') ? : 0,
                ];
                $query = MeiTuanShop::query();
                $meituan_shop_obj = $query->where("poi_id", $shopData['poi_id'])
                    ->first();
                if($meituan_shop_obj === null){
                    $meituan_shop = new MeiTuanShop($shopData);
                    $meituan_shop->save();
                } else {
                    $meituan_shop_obj->name = $shop['shopName'];
                    $meituan_shop_obj->score = $shop['score'];
                    $meituan_shop_obj->monty_sales_tip = $shop['monty_sales_tip'];
                    $meituan_shop_obj->pic_url = $shop['pic_url'];
                    $meituan_shop_obj->delivery_time_tip = $shop['delivery_time_tip'];
                    $meituan_shop_obj->min_price_tip = $shop['min_price_tip'];
                    $meituan_shop_obj->shipping_fee_tip = $shop['shipping_fee_tip'];
                    $meituan_shop_obj->distance = $shop['distance'];
                    $meituan_shop_obj->average_price_tip = $shop['average_price_tip'];
                    $meituan_shop_obj->save();
                }
            }
            sleep(20);
        }
    }

    protected function format($shop, $type)
    {
        $dw = "m";
        if($type=='monthSalesTip'){
            $text = $shop[$type];
            $text = str_replace("月售", "", $text);
        } else if ($type=='deliveryTimeTip'){
            $text = $shop[$type];
            $text = str_replace("分钟", "", $text);
        } else if ($type=='minPriceTip'){
            $text = $shop[$type];
            $text = str_replace("起送 ¥", "", $text);
        } else if ($type=='shippingFeeTip'){
            $text = $shop[$type];
            if($text=='免配送费'){
                $text = 0;
            } else {
                $text = str_replace("配送 ¥", "", $text);
            }
        } else if ($type=='distance'){
            $text = $shop[$type];
            if(substr($text,-2) == 'km') {
                $dw = "km";
                $text = substr($text, 0, -2);
            } else {
                $text = substr($text, 0, -1);
            }
        } else if ($type=='shippingFeeTip'){
            $text = $shop[$type];
            $text = str_replace("配送 ¥", "", $text);
        }

        $re = '/\&\#([a-z0-9]+){5}\;/m';

        preg_match_all($re, $text, $matches, PREG_SET_ORDER, 0);

        foreach ($matches as $matche){
            $text = str_replace($matche[0], $this->jm[$matche[0]], $text);
        }
        if($dw == 'km' && $text) {
            $text *= 1000;
        }
        return $text;
    }

    protected function request($startIndex)
    {
        $curl = curl_init();

        $query = "startIndex={$startIndex}&sortId=5&navigateType=910&firstCategoryId=910&secondCategoryId=910&multiFilterIds=&sliderSelectCode=&sliderSelectMin=&sliderSelectMax=&actualLat=39.987001&actualLng=116.480211&initialLat=40.021598&initialLng=116.465616&geoType=2&rankTraceId=&uuid=3515D27379D4DA583144337412893700588A330FEE410ED664AAFB890566C1AC&platform=3&partner=4&originUrl=https%3A%2F%2Fh5.waimai.meituan.com%2Fwaimai%2Fmindex%2Fkingkong%3FnavigateType%3D910%26firstCategoryId%3D910%26secondCategoryId%3D910%26title%3D%25E7%25BE%258E%25E9%25A3%259F%26index%3D1&riskLevel=71&optimusCode=10&wm_latitude=40021598&wm_longitude=116465616&wm_actual_latitude=39987001&wm_actual_longitude=116480211&wmUuidDeregistration=0&wmUserIdDeregistration=0&openh5_uuid=3515D27379D4DA583144337412893700588A330FEE410ED664AAFB890566C1AC&_token=eJxdkGuPokoQhv8LifsFIzT3nmRyIgiKyggCCmz2AwI2yHBvUNzsfz84J5vdnKSSqn7eStdb9ZNo9Zh4AzQQaTAnhqQl3giwoBcCMSdwNykCz0sSZHlBYIU5Ef3NBAAYdk5c2tOKePvOAW4uQeHHCxyn9x%2Fwp2LpKV4d%2BtRApBjX3RtFpfziHmZFmC2KJMN9WC6iqqD%2BQ1SRlXHyoPKsRHlVon%2FKcMhQiBNnrJN3COhv16ztsDIRVLWjHn%2BxLomqMv4fxBn%2BTN5nqjiT1ZmkzlQ4W7IzqH37mvAOpo1%2FOyoWlzCL%2By8fxOS3cF5%2BGYmdA5p%2FgfwFphz%2BLcz3%2BkvDvzVjuuX0aZehcqqS7YjzHAzcuLTSAe633oHWfRmfcr39YMPc%2FmAd3Ec6E1uB7BtaL1PUtSqc9hLgW4lhQIZBS8UH6GlHPtc023V7AdU31AjhxnbTIOKbQVFV0zfMKOyzTQ0o9g5tVX%2BsxaN4DUaaVZSH2m%2BW%2FvPTkDXqvCHl2JXqnRtqtZHKjN%2BYgFTkbQCt7Y1rhNyA3geTirx%2F%2BkT51nLYgmvOVXHGFjPszmuDrpqjY4NqXV%2B82DVqtnRjXUzbkdfBMxFIrc48PvBp5m7vDQWkwt3HT%2BZu6hCVF9J6PG6ltNOr2xNIJ2fZjrZrhgwZwmnM%2FqQ%2BkEyBc2kohy1z3ZQQFoN3PlUbXKYjzNJ1Rj%2F3VdOT5Nh3nZA7Lq0%2Bezqul%2BozqjeNCPJwNa106yjbb6SuiTct28Q7I10dVmylMSA%2FnjlWsfy16MG2onruMjy2ZOglA0pRTsqSc3CjNEAFs6Mc3zxSt0ESTN97foIBQwulSepNVzuaKHZQWo5cciAtp1aS3AkRV7S7K7OvVT5GEfZOpV0KbrS%2FAw%2BW0snsw04RTbDiJEl9J379C1f1GxM%3D";

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://i.waimai.meituan.com/openh5/channel/kingkongshoplist?_=1655889361125',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $query,
            CURLOPT_HTTPHEADER => array(
                'Cookie: WEBDFPID=03u8450y4u6057yz17zw56z1x1zz82078181019y43u97958vx2ux31x-1655955722644-1655869322008OOOMGQE868c0ee73ab28e1d0b03bc83148500061024; _lxsdk_s=1818980b487-600-55c-b5d%7C%7C27; au_trace_key_net=default; cssVersion=6e26c3a9; isIframe=false; openh5_uuid=209D528E64F9C66F537F7F0670AD16384F949666347BA874DE6A5A2E6E8B4A31; request_source=openh5; uuid=209D528E64F9C66F537F7F0670AD16384F949666347BA874DE6A5A2E6E8B4A31; openh5_uuid=209D528E64F9C66F537F7F0670AD16384F949666347BA874DE6A5A2E6E8B4A31; terminal=i; w_token=mg9m9vU_7EwFjR-M8-_tf51HSjoAAAAAhxIAAAI9NNvuQrfXOeJwt2OljpWZGWTUfqmSi4jqL10NNJEMUwgYlkGOiTbThJM3cgAWkA; w_utmz="utm_campaign=(direct)&utm_source=5000&utm_medium=(none)&utm_content=(none)&utm_term=(none)"; w_visitid=b89dd32b-5d4b-4ef4-9925-b2044934a6ad; mt_c_token=mg9m9vU_7EwFjR-M8-_tf51HSjoAAAAAhxIAAAI9NNvuQrfXOeJwt2OljpWZGWTUfqmSi4jqL10NNJEMUwgYlkGOiTbThJM3cgAWkA; oops=mg9m9vU_7EwFjR-M8-_tf51HSjoAAAAAhxIAAAI9NNvuQrfXOeJwt2OljpWZGWTUfqmSi4jqL10NNJEMUwgYlkGOiTbThJM3cgAWkA; token=mg9m9vU_7EwFjR-M8-_tf51HSjoAAAAAhxIAAAI9NNvuQrfXOeJwt2OljpWZGWTUfqmSi4jqL10NNJEMUwgYlkGOiTbThJM3cgAWkA; userId=145333205; iuuid=209D528E64F9C66F537F7F0670AD16384F949666347BA874DE6A5A2E6E8B4A31; _lx_utm=utm_source%3D60066; wm_order_channel=default; _hc.v=4a776572-311d-941e-33cd-5d4b99aa26e7.1655698682; _lxsdk=209D528E64F9C66F537F7F0670AD16384F949666347BA874DE6A5A2E6E8B4A31; cityname=%E5%8C%97%E4%BA%AC; _lxsdk_cuid=1817f54ce75c8-07c4006715570e-3c404320-505c8-1817f54ce75c8; openh5_uuid=3515D27379D4DA583144337412893700588A330FEE410ED664AAFB890566C1AC; terminal=i; w_token=mg9m9vU_7EwFjR-M8-_tf51HSjoAAAAAhxIAAAI9NNvuQrfXOeJwt2OljpWZGWTUfqmSi4jqL10NNJEMUwgYlkGOiTbThJM3cgAWkA; w_utmz="utm_campaign=(direct)&utm_source=5000&utm_medium=(none)&utm_content=(none)&utm_term=(none)"',
                'User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 15_4 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/102.0.5005.87 Mobile/15E148 Safari/604.1',
                'Referer: https://h5.waimai.meituan.com/',
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response, true);
    }
}
