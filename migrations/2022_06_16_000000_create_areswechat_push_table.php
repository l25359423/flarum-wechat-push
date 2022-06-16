<?php

use Illuminate\Database\Schema\Blueprint;

use Flarum\Database\Migration;
use Illuminate\Database\Schema\Builder;

return [
    'up' => function (Builder $schema) {
        if ($schema->hasTable('wechat_push')) {
            return;
        }

        $schema->create('wechat_push', function (Blueprint $table) {
            $table->increments('id');
            $table->text('content');
            $table->string('url', 1024);
            // created_at & updated_at
            $table->timestamps();
        });
    },
    'down' => function (Builder $schema) {
        $schema->dropIfExists('wechat_push');
    },
];

