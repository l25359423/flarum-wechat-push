<?php

namespace Leo\WechatPush;

use Flarum\Database\AbstractModel;
use Flarum\Database\ScopeVisibilityTrait;
use Flarum\Foundation\EventGeneratorTrait;

class DailyMusic extends AbstractModel
{
    // See https://docs.flarum.org/extend/models.html#backend-models for more information.

    protected $table = 'daily_music';

    public static function build($title, $url)
    {
        $page = new static();

        $page->title = $title;
        $page->url = $url;

        return $page;
    }
}
