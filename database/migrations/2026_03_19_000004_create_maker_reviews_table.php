<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {

    public function up()
    {
        Schema::create('maker_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders');
            $table->foreignId('maker_id')->constrained('users');
            $table->foreignId('buyer_id')->constrained('users');
            $table->integer('rating');
            $table->text('comment');
            $table->timestampTz('created_at')->useCurrent();

            $table->unique(['order_id', 'buyer_id']);
            $table->index(['maker_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('maker_reviews');
    }
};
