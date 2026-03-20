<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('type', 30);
            $table->text('message');
            $table->foreignId('related_order_id')->nullable()->constrained('orders');
            $table->foreignId('related_product_id')->nullable()->constrained('products');
            $table->boolean('is_read')->default(false);
            $table->timestampTz('created_at')->useCurrent();

            $table->index(['user_id', 'is_read']);
            $table->index(['related_order_id']);
            $table->index(['related_product_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('notifications');
    }
};
