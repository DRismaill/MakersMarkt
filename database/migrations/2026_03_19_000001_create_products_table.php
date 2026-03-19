<?php

use App\Enums\ComplexityLevel;
use App\Enums\DurabilityLevel;
use App\Enums\ProductApprovalStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            // id serial [pk]
            $table->id();

            // maker_id int [not null] // users.role = maker
            $table->foreignId('maker_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            // product_type_id int [not null]
            $table->foreignId('product_type_id')
                ->constrained('product_types')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->string('name');                // varchar(255)
            $table->string('slug')->nullable()->unique();
            $table->text('description');
            $table->string('material');
            $table->integer('production_time_days');

            // Gebruik string i.p.v. enum voor betere SQLite‑compatibiliteit
            $table->string('complexity', 20);      // ComplexityLevel enum
            $table->string('durability', 20);      // DurabilityLevel enum

            $table->text('unique_feature');

            // numeric(10,2)
            $table->decimal('price_credit', 10, 2);

            // approval_status enum -> string
            $table->string('approval_status', 20)->default(ProductApprovalStatus::Pending->value);

            // mag null zijn in DBML
            $table->foreignId('approved_by_admin_id')
                ->nullable()
                ->constrained('users')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->timestampTz('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();

            // booleans met echte booleans, geen strings
            $table->boolean('has_external_link')->default(false);
            $table->boolean('needs_moderation')->default(false);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_deleted')->default(false);

            // average_rating numeric(3,2)
            $table->decimal('average_rating', 3, 2)->nullable();
            $table->integer('review_count')->default(0);

            $table->timestampsTz();

            // Indexen
            $table->index('maker_id');
            $table->index('product_type_id');
            $table->index('approval_status');
            $table->index(['is_active', 'is_deleted']);
        });

        /**
         * Optioneel: CHECK constraints voor "enum"-velden.
         * SQLite ondersteunt CHECK, maar je moet het met raw SQL doen
         * als je het écht wilt afdwingen.
         *
         * Let op: dit werkt alleen bij nieuwe tabellen/migraties;
         * ALTER TABLE is beperkt in SQLite.
         */
        if (DB::getDriverName() === 'sqlite') {
            // In SQLite kun je niet eenvoudig achteraf een CHECK toevoegen
            // zonder de tabel te recreëren, dus vaak laat je dit weg
            // en valideer je in je app / FormRequest.
        } else {
            // Voor bv. PostgreSQL kun je hier raw CHECKs doen als je wilt
            // DB::statement("ALTER TABLE products ADD CONSTRAINT chk_products_complexity
            //     CHECK (complexity IN ('low', 'medium', 'high'));");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
