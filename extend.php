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

return [
    (new Extend\Event())
        ->listen(\Flarum\Post\Event\Posted::class, Listener\PostListener::class),
    (new Extend\Routes('api'))
        ->get('/wechat-msg', 'wechat_msg', Controller\ListDailyMusicsController::class)
];
