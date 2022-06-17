<?php

use Illuminate\Database\Schema\Blueprint;

use Flarum\Database\Migration;
use Illuminate\Database\Schema\Builder;

return [
    'up' => function (Builder $schema) {
        if ($schema->hasTable('city')) {
            return;
        }

        $schema->create('city', function (Blueprint $table) {
            $table->increments('id');
            $table->string('city', 32);
            $table->integer('level');
        });
    },
    'down' => function (Builder $schema) {
        $schema->dropIfExists('city');
    },
];

