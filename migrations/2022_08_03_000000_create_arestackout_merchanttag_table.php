<?php

use Illuminate\Database\Schema\Blueprint;

use Flarum\Database\Migration;

return Migration::createTable(
    'tackout_merchanttag',
    function (Blueprint $table) {
        $table->increments('id');
        $table->unsignedInteger("merchant_id");
        $table->string("tag", 225);

        // created_at & updated_at
        $table->timestamps();
        $table->foreign("merchant_id")->references('id')
            ->on('tackout_merchant')
            ->onDelete('cascade');
    }
);

