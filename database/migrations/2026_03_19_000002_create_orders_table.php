<?php

use App\Enums\OrderStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            // id serial [pk]
            $table->id();

            // buyer_id int [not null] // users.role = buyer
            $table->foreignId('buyer_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            // product_id int [not null]
            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            // maker_id int [not null] // users.role = maker
            $table->foreignId('maker_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            // status order_status [not null, default: 'pending_payment']
            $table->string('status', 50)->default(OrderStatus::PendingPayment->value);
            $table->text('status_note')->nullable();

            // price_credit numeric(10,2) [not null]
            $table->decimal('price_credit', 10, 2);

            $table->timestampsTz();

            // indexes
            $table->index('buyer_id');
            $table->index('maker_id');
            $table->index('product_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
