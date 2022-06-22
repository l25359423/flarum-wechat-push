<?php

namespace Leo\WechatPush;

use Flarum\Database\AbstractModel;
use Flarum\Database\ScopeVisibilityTrait;
use Flarum\Foundation\EventGeneratorTrait;

class MeiTuanShop extends AbstractModel
{
    // See https://docs.flarum.org/extend/models.html#backend-models for more information.

    protected $table = 'meituan_shop';

    protected $fillable = [
        'poi_id',
        'name',
        'score',
        'monty_sales_tip',
        'pic_url',
        'delivery_time_tip',
        'min_price_tip',
        'shipping_fee_tip',
        'distance',
        'average_price_tip',
    ];
}
