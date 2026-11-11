<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('instagram_feeds', function (Blueprint $table) {
            $table->id();
            $table->string('media_id')->unique();
            $table->string('media_url');
            $table->string('permalink');
            $table->text('caption')->nullable();
            $table->integer('like_count')->default(0);
            $table->datetime('timestamp');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('instagram_feeds');
    }
};
