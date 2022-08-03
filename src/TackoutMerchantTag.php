<?php

namespace Leo\WechatPush;

use Flarum\Database\AbstractModel;
use Flarum\Database\ScopeVisibilityTrait;
use Flarum\Foundation\EventGeneratorTrait;

class TackoutMerchantTag extends AbstractModel
{
    // See https://docs.flarum.org/extend/models.html#backend-models for more information.

    protected $table = 'tackout_merchanttag';

    protected $fillable = ['merchant_id', 'tag'];
}
