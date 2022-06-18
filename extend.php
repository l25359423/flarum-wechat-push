<?php

/*
 * This file is part of leo/wechat-push.
 *
 * Copyright (c) 2022 leo.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Leo\WechatPush;

use Flarum\Extend;
use Leo\WechatPush\Api\Controller;
use Leo\WechatPush\Console;
use Leo\WechatPush\Forum\Controller as ForumController;

return [
    (new Extend\Csrf())
        ->exemptRoute('wechat_msg'),
    (new Extend\Event())
        ->listen(\Flarum\Post\Event\Posted::class, Listener\PostListener::class),
    (new Extend\Routes('api'))
        ->post('/wechat-msg', 'wechat_msg', Controller\WechatMsgController::class),
    (new Extend\Frontend('forum'))
        ->route('/weibo/{title_md5}', 'weibo_hot', Controller\WeiboHotRedirectController::class),
    (new Extend\Console())
        ->command(Console\PushWeiBoHot::class),
    (new Extend\Routes('forum'))
        ->get('/waimai', 'waimai', ForumController\WaiMaiController::class),
    (new Extend\View())
        ->namespace('leo.waimai', __DIR__.'/resources/views'),
];
