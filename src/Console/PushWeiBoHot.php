<?php

namespace Leo\DailyMusic\Console;

use Flarum\Console\AbstractCommand;
use Maicol07\Flarum\Api\Client;
use Leo\DailyMusic\Model\DailyMusic;

class PostMusic extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('leo:postmusic')
            ->setDescription('post music');
    }

    protected function fire()
    {
        $config = app('flarum.config');
        $url = (string)$config->url();
        $query = DailyMusic::query();
        $music = $query->where("released", 0)->orderBy("id", "asc")->limit(1)->get();

        if($music->isEmpty()){
            return false;
        }
        $music = $music[0];

        $api = new Client($url, ['token' => 'zhewvzlxzfgxhjnzyhfujicmyvsngmxc; userId=1']);

        $curl = curl_init();

        $request_data = [
            'attributes' => [
                'title'   => $music->title,
                'content' => sprintf("%s\n%s", $music->title, $music->url),
            ],
            'relationships' => [
                'tags' => [
                    'data' => [
                        [
                            'type' => 'tags',
                            'id'   => '5'
                        ]
                    ]
                ]
            ],
            'type' => 'discussions'
        ];

        try {
            $response = $api->discussions()->post($request_data)->request();
            $music->released = 1;
            $music->save();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

        // See https://docs.flarum.org/extend/console.html#console and
        // https://symfony.com/doc/current/console.html#configuring-the-command for more information.
    }
}
