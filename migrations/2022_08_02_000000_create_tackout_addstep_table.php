<?php

use Illuminate\Database\Schema\Blueprint;

use Flarum\Database\Migration;

return Migration::createTable(
    'tackout_addstep',
    function (Blueprint $table) {
        $table->increments('id');
        $table->string("wxid", 225);
        $table->string("poi", 225)->nullable(true);
        $table->enum("step", ["init", "share_app", "add_tag", "finish"]);
        // created_at & updated_at
        $table->timestamps();
    }
);

