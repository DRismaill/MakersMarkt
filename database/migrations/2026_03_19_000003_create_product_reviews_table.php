<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('product_reviews', function (Blueprint $table) {
            // id serial [pk]
            $table->id();

            // order_id int [not null]
            $table->foreignId('order_id')
                  ->constrained('orders')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();

            // product_id int [not null]
            $table->foreignId('product_id')
                  ->constrained('products')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();

            // buyer_id int [not null]
            $table->foreignId('buyer_id')
                  ->constrained('users')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();

            // rating int [not null] (1–5) -> validatie in app
            $table->integer('rating');

            // comment text [not null]
            $table->text('comment');

            // created_at timestamptz [not null, default now()]
            // Je kunt hier gewoon timestampsTz gebruiken; updated_at heb je niet echt nodig,
            // maar het is niet erg als hij er wel is.
            $table->timestampTz('created_at')->useCurrent();

            // Unieke combinatie: max 1 review per order per buyer
            $table->unique(['order_id', 'buyer_id']);

            // Index voor product_id
            $table->index('product_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_reviews');
    }
};
