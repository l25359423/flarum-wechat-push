<?php

use Illuminate\Database\Schema\Blueprint;

use Illuminate\Database\Schema\Builder;

// HINT: you might want to use a `Flarum\Database\Migration` helper method for simplicity!
// See https://docs.flarum.org/extend/models.html#migrations to learn more about migrations.
return [
    'up' => function (Builder $schema) {
        // up migration
        $schema->table('wechat_push', function (Blueprint $table) {
            $table->boolean('pushed')->default(0);
        });
    },
    'down' => function (Builder $schema) {
        // down migration
    }
];
