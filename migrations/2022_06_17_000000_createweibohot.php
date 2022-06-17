<?php

use Illuminate\Database\Schema\Blueprint;

use Illuminate\Database\Schema\Builder;

// HINT: you might want to use a `Flarum\Database\Migration` helper method for simplicity!
// See https://docs.flarum.org/extend/models.html#migrations to learn more about migrations.
return [
    'up' => function (Builder $schema) {
        if ($schema->hasTable('weibo_hot')) {
            return;
        }

        $schema->create('weibo_hot', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title_md5', 16);
            $table->string('url', 1024);
            // created_at & updated_at
            $table->timestamps();
        });
    },
    'down' => function (Builder $schema) {
        $schema->dropIfExists('weibo_hot');
    },
];
