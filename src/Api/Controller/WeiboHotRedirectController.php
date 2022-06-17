<?php
/*
 * This file is part of fof/user-directory.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leo\WechatPush\Api\Controller;

use Flarum\Api\Client;
use Flarum\Frontend\Document;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Facades\Redirect;
use Leo\WechatPush\WeiboHot;
use Psr\Http\Message\ServerRequestInterface as Request;
use Illuminate\Support\Arr;
class WeiboHotRedirectController
{
    /**
     * @var Client
     */
    protected $api;

    /**
     * @var Factory
     */
    private $view;

    public function __construct(Client $api, Factory $view)
    {
        $this->api = $api;
        $this->view = $view;
    }

    public function __invoke(Document $document, Request $request)
    {
        $title_md5 = Arr::get($request->getQueryParams(), 'title_md5');

        $query = WeiboHot::query();
        $weibo_hot = $query->where("title_md5", $title_md5)->get();
        if($weibo_hot->isEmpty()){
            $config = app('flarum.config');
            $root_url = (string)$config->url();
            echo sprintf("<script>alert('链接不存在！');
            window.location.href = '%s';</script>", $root_url);
            die;
        }
        $weibo_hot = $weibo_hot[0];
        header(sprintf("Location: %s", $weibo_hot->url));
        die;
        return $document;
    }
}
