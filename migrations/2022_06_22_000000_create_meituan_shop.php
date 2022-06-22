<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

return [
    'up' => function (Builder $schema) {
        if ($schema->hasTable('meituan_shop')) {
            return;
        }

        $schema->create('meituan_shop', function (Blueprint $table) {
            $table->increments('id');
            $table->string('poi_id', 32);
            $table->string('name', 225)->nullable(true);
            $table->string('score', 225)->nullable(true);
            $table->string('monty_sales_tip', 225)->nullable(true);
            $table->string('pic_url', 1024)->nullable(true);
            $table->string('delivery_time_tip', 225)->nullable(true);
            $table->string('min_price_tip', 225)->nullable(true);
            $table->string('shipping_fee_tip', 225)->nullable(true);
            $table->string('distance', 225)->nullable(true);
            $table->string('average_price_tip', 225)->nullable(true);
        });
    },
    'down' => function (Builder $schema) {
        $schema->dropIfExists('meituan_shop');
    },
];
