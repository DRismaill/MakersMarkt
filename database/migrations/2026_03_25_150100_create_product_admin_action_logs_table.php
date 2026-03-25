<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_admin_action_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignId('admin_user_id')
                ->nullable()
                ->constrained('users')
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->string('product_name');
            $table->string('action_type', 30);
            $table->timestampTz('created_at')->useCurrent();

            $table->index(['product_id']);
            $table->index(['admin_user_id']);
            $table->index(['action_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_admin_action_logs');
    }
};
