<?php

namespace Leo\WechatPush;

use Flarum\Database\AbstractModel;
use Flarum\Database\ScopeVisibilityTrait;
use Flarum\Foundation\EventGeneratorTrait;

class TackoutMerchant extends AbstractModel
{
    // See https://docs.flarum.org/extend/models.html#backend-models for more information.

    protected $table = 'tackout_merchant';

    protected $fillable = ['wxid', 'poi', 'xml',
        'title', 'des', 'status',
        'air_conditioning', 'price', 'distance',
        'chili', 'queue', 'taste', 'service_attitude'];
}
