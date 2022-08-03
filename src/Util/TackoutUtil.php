<?php
namespace Leo\WechatPush\Util;


use Leo\WechatPush\TackoutAddStep;
use Leo\WechatPush\TackoutMerchant;
use Leo\WechatPush\TackoutMerchantTag;

class TackoutUtil
{
    public static function check($content)
    {
        return $content == "外卖商家添加"
            || stristr($content, "外卖商家步骤删除 ") !== false
            || stristr($content, "外卖商家删除 ") !== false
            || stristr($content, "外卖商家列表") !== false
            || stristr($content, "外卖Tag删除") !== false
            || stristr($content, "外卖Tag标记") !== false
            || mb_substr($content, 0, 7) === "外卖Tag标记"
            || mb_substr($content, 0, 6) === "外卖属性标记"
            || mb_substr($content, 0, 5) === "今天吃什么";
    }
    public static function query($content, $wxid)
    {
        $attrArr = array(
            "空调"=>"air_conditioning",
            "价格"=>"price",
            "距离"=>"distance",
            "辣度"=>"chili",
            "排队"=>"queue",
            "口感"=>"taste",
            "服务"=>"service_attitude",
        );
        if($content == "外卖商家添加") {
            $query = TackoutAddStep::query();
            $obj = $query->where("wxid", $wxid)
                ->where("step", "<>", "finish")
                ->first();
            if($obj !== null){
                $reply_content = sprintf("您有还未完成的商家添加\n当前步骤：%s\n\n如需重新添加商家，请回复：\n外卖商家步骤删除ID %s\n\n删除成功后重新操作即可",
                    $obj['step'], $obj['id']);
            } else {
                $tackoutAddStep = new TackoutAddStep([
                    "wxid" => $wxid,
                    "step" => "init",
                ]);
                $tackoutAddStep->save();
                $reply_content = "请将需要添加的商家分享至本群";
            }
        } else if($content == "外卖商家列表"){
            $reply_content = "";
            $query = TackoutMerchant::query();
            $tmTQuery = TackoutMerchantTag::query();
            $list = $query->where("wxid", $wxid)->get();

            foreach ($list as $index => $item){
                $reply_content .= sprintf("%s. %s\n",
                    $item->id, $item->title);

                $tags = $tmTQuery->where("merchant_id", $item->id)->get();
                $tags = $tags->toArray();
                if($tags){
                    $reply_content .= "标签: ";
                    foreach ($tags as $tag){
                        $reply_content .= sprintf("%s:%s ",
                            $tag['id'], $tag['tag']);
                    }
                    $reply_content .= "\n";
                } else {
                    $reply_content .= "标签: 无\n";
                }

                $reply_content .= sprintf("空调：%s\n", implode("",
                    array_fill(0, $item->air_conditioning, "⭐️")));
                $reply_content .= sprintf("价格：%s\n", implode("",
                    array_fill(0, $item->price, "⭐️")));
                $reply_content .= sprintf("距离：%s\n", implode("",
                    array_fill(0, $item->distance, "⭐️")));
                $reply_content .= sprintf("辣度：%s\n", implode("",
                    array_fill(0, $item->chili, "⭐️")));
                $reply_content .= sprintf("排队：%s\n", implode("",
                    array_fill(0, $item->queue, "⭐️")));
                $reply_content .= sprintf("口感：%s\n", implode("",
                    array_fill(0, $item->taste, "⭐️")));
                $reply_content .= sprintf("服务：%s\n", implode("",
                    array_fill(0, $item->service_attitude, "⭐️")));

            }
            $reply_content .= sprintf("\n共%s个商家\n\n", count($list));
            $reply_content .= "属性标记：外卖属性标记-{ID}-空调:1|价格:2|距离:3|辣度:4|排队:5|口感:5|服务:5\n";
            $reply_content .= "删除商家：外卖商家删除 {ID}\n";
            $reply_content .= "删除TAG：外卖Tag删除 {ID}\n";
            $reply_content .= "标记TAG：外卖商家标记Tag-{ID}-面食 米饭 特辣（多个tag之间用空格隔开）\n";
            return $reply_content;
        } else if(stristr($content, "外卖商家删除") !== false) {
            $id = trim(str_replace("外卖商家删除", "", $content));
            if(!is_numeric($id)){
                $reply_content = "删除失败，格式错误，请按照以下格式进行回复：\n外卖商家删除 {ID}";
                return $reply_content;
            }
            $query = TackoutMerchant::query();
            $query->where("id", $id)->delete();
            $reply_content = "删除成功！";
        } else if(stristr($content, "外卖商家步骤删除") !== false) {
            $id = trim(str_replace("外卖商家步骤删除", "", $content));
            if(!is_numeric($id)){
                $reply_content = "删除失败，格式错误，请按照以下格式进行回复：\n外卖商家步骤删除 {ID}";
                return $reply_content;
            }
            $query = TackoutAddStep::query();
            $obj = $query->where("id", $id)->first();

            $query = TackoutMerchant::query();
            $query->where("poi", $obj->poi)
                ->where("wxid", $wxid)
                ->delete();
            $obj->delete();
            $reply_content = "删除成功！";
        } else if(stristr($content, "外卖Tag标记") !== false) {
            $arr = explode("-", $content);
            $id = $arr[1];
            $tags = explode(" ", $arr[2]);
            if(!is_numeric($id) || !$tags){
                $reply_content = "格式错误，请按照以下格式进行标记tag：\n\n";
                $reply_content .= "外卖Tag标记-{ID}-面食 米饭 特辣（多个tag之间用空格隔开）";
                return $reply_content;
            }
            $query = TackoutMerchant::query();
            $obj = $query->where("id", $id)->first();
            if($obj===null){
                return "商家不存在！";
            }
            foreach ($tags as $tag){
                $tackoutMerchantTag = new TackoutMerchantTag([
                    "merchant_id" => $id,
                    "tag" => trim($tag)
                ]);
                $tackoutMerchantTag->save();
            }
            return "标记成功！";
        } else if(stristr($content, "外卖Tag删除") !== false) {
            $id = trim(str_replace("外卖Tag删除", "", $content));
            if(!is_numeric($id)){
                $reply_content = "删除失败，格式错误，请按照以下格式进行回复：\n外卖Tag删除 {ID}";
                return $reply_content;
            }

            $query = TackoutMerchantTag::query();
            $query->where("id", $id)
                ->delete();
            $reply_content = "删除成功！";
        } else if (mb_substr($content, 0, 6) === "外卖属性标记"){
            $content = str_replace("：", ":", $content);
            $arr = explode("-", $content);
            if(count($arr) != 3){
                return "格式错误";
            }
            $id = $arr[1];
            if(!is_numeric($id)){
                return "格式错误";
            }
            $query = TackoutMerchant::query();
            $obj = $query->where("id", $id)->first();
            if($obj === null){
                return "商家不存在！";
            }
            $attrs = explode("|", $arr[2]);
            if(!$attrs){
                return "格式错误";
            }
            $updateData = [];
            foreach ($attrs as $attr){
                $item = explode(":", $attr);
                $key = trim($item[0]);
                $value = trim($item[1]);
                $column = $attrArr[$key];
                if(!$column){
                    return sprintf("%s column不存在！", $key);
                }
                if(!is_numeric($value) || ($value<0 || $value > 5)){
                    return "值的范围必须是0-5之间！";
                }
                $updateData[$column] = $value;

                $query->where("id", $id)->update($updateData);
            }
            return "属性标记成功！";
        } else if (mb_substr($content, 0, 5) === "今天吃什么"){
            $content = str_replace("：", ":", $content);
            $query = TackoutMerchant::query();
            $query->where("wxid", $wxid);
            if(stristr($content, "-") !== false){
                $arr = explode("-", $content);
                $parameters = $arr[1];

                if($parameters){
                    $parameters = explode("|", $parameters);
                    foreach ($parameters as $parameter){
                        $item = explode(":", $parameter);
                        $key = trim($item[0]);
                        $value = trim($item[1]);
                        $column = $attrArr[$key];
                        $query->where($column, ">=", $value);
                    }
                }
            }

            $obj = $query->orderByRaw("rand()")
                ->first();
            if($obj === null){
                return "要求太高了，调整参数让范围扩大一点吧，要不今天大家都饿肚子~";
            }
            return array("xml", $obj->xml);
        }

        return $reply_content;
    }

    public static function checkStep($wxid, $step)
    {
        $query = TackoutAddStep::query();
        $obj = $query->where("wxid", $wxid)
            ->where("step", $step)
            ->first();
        return $obj !== null;
    }

    public static function add($rawArr, $wxid, $xml)
    {
        $url = $rawArr['appmsg']['url'];
        $title = $rawArr['appmsg']['title'];
        $des = $rawArr['appmsg']['des'];
        $urlParse = parse_url($url);

        $poi = explode("/", $urlParse['path']);
        $poi = end($poi);

        $query = TackoutMerchant::query();


        $obj = $query->where("poi", $poi)
            ->first();

        if($obj !== null){
            $reply_content = "商家已存在！";
            return $reply_content;
        }
        $xml = preg_replace("/<fromusername>.*<\/fromusername>/", "<fromusername>yuzhe5</fromusername>", $xml);
        $ackoutMerchant = new TackoutMerchant([
            "title" => $title,
            "des" => $des,
            "poi" => $poi,
            "wxid" => $wxid,
            "xml" => $xml,
        ]);
        $ackoutMerchant->save();

        $tackoutAddStepQuery = TackoutAddStep::query();
        $tackoutAddStepQuery->where("wxid", $wxid)
            ->update([
               "step" => "finish",
               "poi" => $poi,
            ]);
        $reply_content = sprintf("商家添加成功,ID: %s, 请按照以下格式标记商家属性，取值范围0-5：\n\n", $ackoutMerchant->id);
        $reply_content .= "外卖属性标记-{ID}-空调:1|价格:2|距离:3|辣度:4|排队:5|口感:5|服务:5\n";
        return $reply_content;
    }
}
