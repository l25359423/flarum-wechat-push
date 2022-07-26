<?php

namespace Leo\WechatPush\Console;

use Flarum\Console\AbstractCommand;
use Leo\WechatPush\MeiTuanShop;
use Leo\WechatPush\Util\PushMsgUtil;
use Leo\WechatPush\Util\WeiBoHotUtil;
use QL\QueryList;

class CrawlCalendar extends AbstractCommand
{

    protected function configure()
    {
        $this
            ->setName('leo:crawlcalendar')
            ->setDescription('crawl-calendar');
    }

    protected function fire()
    {
        $ql = QueryList::get('https://wannianrili.bmcx.com/', null, [
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.0.0 Safari/537.36',
                'accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
            ]
        ]);
        $wnrl_k_you_node = sprintf("#wnrl_k_you_id_%s", date('d')-1);
        $wnrl_k_xia_node = sprintf("#wnrl_k_xia_id_%s", date('d')-1);
        $ylDate = $ql->find($wnrl_k_you_node.">.wnrl_k_you_id_wnrl_nongli")->text();
        $guanzhi = $ql->find($wnrl_k_you_node.">.wnrl_k_you_id_wnrl_nongli_ganzhi")->text();
        $yis = $ql->find($wnrl_k_you_node.">.wnrl_k_you_id_wnrl_yi>.wnrl_k_you_id_wnrl_yi_neirong>a")
            ->texts()
            ->toArray();
        $jis = $ql->find($wnrl_k_you_node.">.wnrl_k_you_id_wnrl_ji>.wnrl_k_you_id_wnrl_ji_neirong>a")
            ->texts()
            ->toArray();

        $details = [];
        for($i=0;$i<16;$i++){
            $xTitle = $ql->find($wnrl_k_xia_node.">.wnrl_k_xia>.wnrl_k_xia_nr>.wnrl_k_xia_nr_wnrl_beizhu:eq({$i})>.wnrl_k_xia_nr_wnrl_beizhu_biaoti")
                ->text();
            $xContent = $ql->find($wnrl_k_xia_node.">.wnrl_k_xia>.wnrl_k_xia_nr>.wnrl_k_xia_nr_wnrl_beizhu:eq({$i})>.wnrl_k_xia_nr_wnrl_beizhu_neirong")
                ->text();
            $details[$xTitle] = $xContent;
        }
        $f = fopen(base_path()."/crawl-data/calendar.json", "w");
        fwrite($f, json_encode([
            "ylDate" => $ylDate,
            "yis" => $yis,
            "guanzhi" => $guanzhi,
            "jis" => $jis,
            "details" => $details,
        ]));
        fclose($f);
    }
}
