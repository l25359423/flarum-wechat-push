<?php
namespace Leo\WechatPush\Util;

class CoverToUpperUtil
{
    private static $weatherURL = 'https://devapi.qweather.com/v7/weather/now';
    private static $getLocationURL = 'https://geoapi.qweather.com/v2/city/lookup';
    private static $key = '0a1acf9d2c7b458dbbdb41e8d9a2fef6';
    public static function check($content)
    {
        return mb_substr($content, 0, 4) == "@钢镚儿"
            && (stristr($content, '金额转大写') !== false);
    }

    public static function query($content)
    {
        $arr = explode(":", $content);
        if(count($arr) < 2){
            $arr = explode("：", $content);
        }
        $price = $arr[1];
        $upperText = self::num_to_rmb($price);
        $reply_content = $upperText ?
            : "金钱格式有误，请重新输入，示例：\n@钢镚儿 金额转大写：1111.23";
        return $reply_content;
    }

    public static function num_to_rmb($num) {
        $c1 = "零壹贰叁肆伍陆柒捌玖";
        $c2 = "分角元拾佰仟万拾佰仟亿";
        //精确到分后面就不要了，所以只留两个小数位
        $num = round($num, 2);
        //将数字转化为整数
        $num = $num * 100;

        if (strlen($num) > 10) {
            return "金额太大，请检查";
        }

        $i = 0;
        $c = "";

        while (1) {
            if ($i == 0) {
                //获取最后一位数字
                $n = substr($num, strlen($num) - 1, 1);
            } else {
                $n = $num % 10;
            }    //每次将最后一位数字转化为中文

            $p1 = substr($c1, 3 * $n, 3);
            $p2 = substr($c2, 3 * $i, 3);

            if ($n != '0' || ($n == '0' && ($p2 == '亿' || $p2 == '万' || $p2 == '元'))) {
                $c = $p1 . $p2 . $c;
            } else {
                $c = $p1 . $c;
            }

            $i = $i + 1;

            //去掉数字最后一位了
            $num = $num / 10;
            $num = (int) $num;

            //结束循环
            if ($num == 0) {
                break;
            }
        }

        $j = 0;
        $slen = strlen($c);

        while ($j < $slen) {
            //utf8一个汉字相当3个字符
            $m = substr($c, $j, 6);
            //处理数字中很多0的情况,每次循环去掉一个汉字“零”
            if ($m == '零元' || $m == '零万' || $m == '零亿' || $m == '零零') {
                $left = substr($c, 0, $j);
                $right = substr($c, $j + 3);
                $c = $left . $right;
                $j = $j - 3;
                $slen = $slen - 3;
            }

            $j = $j + 3;
        }

        //这个是为了去掉类似23.0中最后一个“零”字
        if (substr($c, strlen($c) - 3, 3) == '零') {
            $c = substr($c, 0, strlen($c) - 3);
        }

        //将处理的汉字加上“整”
        if (empty($c)) {
            return "零元整";
        } else {
            return $c . "整";
        }
    }
}
