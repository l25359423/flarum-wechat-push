<?php

namespace Leo\WechatPush;

use Flarum\Database\AbstractModel;
use Flarum\Database\ScopeVisibilityTrait;
use Flarum\Foundation\EventGeneratorTrait;

class WeiboHot extends AbstractModel
{
    // See https://docs.flarum.org/extend/models.html#backend-models for more information.

    protected $table = 'weibo_hot';

    protected $fillable = ['title_md5', 'url'];
}
