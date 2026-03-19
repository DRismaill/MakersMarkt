<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('credit_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_user_id')->constrained('users')->nullable();
            $table->foreignId('to_user_id')->constrained('users')->nullable();
            $table->string('amount');
            $table->string('reason_type', 20);
            $table->foreignId('order_id')->constrained('orders')->nullable();
            $table->foreignId('created_by_admin_id')->constrained('users')->nullable();
            $table->timestampTz('created_at')->useCurrent();

            $table->index(['from_user_id']);
            $table->index(['to_user_id']);
            $table->index(['order_id']);
            $table->index(['reason_type']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('credit_transactions');
    }
};
