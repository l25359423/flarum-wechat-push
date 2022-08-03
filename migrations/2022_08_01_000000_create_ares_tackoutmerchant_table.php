<?php

use Illuminate\Database\Schema\Blueprint;

use Flarum\Database\Migration;

return Migration::createTable(
    'tackout_merchant',
    function (Blueprint $table) {
        $table->increments('id');
        $table->text('xml')->nullable(true);
        $table->string('poi', 225)->nullable(true);
        $table->string('title', 500)->nullable(true);
        $table->string('des', 1000)->nullable(true);
        $table->string('wxid', 225)->nullable(true);
        $table->boolean('status')->default(false);
        $table->integer('air_conditioning')->default(0);
        $table->integer('price')->default(0);
        $table->integer('distance')->default(0);
        $table->integer('chili')->default(0);
        $table->integer('queue')->default(0);
        $table->integer('taste')->default(0);
        $table->integer('service_attitude')->default(0);

        // created_at & updated_at
        $table->timestamps();
    }
);

