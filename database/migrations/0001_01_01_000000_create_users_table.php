<?php

use App\Enums\UserRole;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // id serial [pk]

            // role user_role [not null, default: 'buyer']
            $table->string('role', 20)->default(UserRole::Buyer->value);

            // username varchar(100) [not null, unique]
            $table->string('username', 100)->unique();

            // email varchar(255) [not null, unique]
            $table->string('email', 255)->unique();

            // password_hash varchar(255) [not null]
            $table->string('password_hash', 255);

            // credit_balance numeric(10,2) [not null, default: 0]
            $table->decimal('credit_balance', 10, 2)->default(0);

            // is_blocked boolean [not null, default: false]
            $table->boolean('is_blocked')->default(false);

            // is_deleted boolean [not null, default: false]
            $table->boolean('is_deleted')->default(false);

            // Optioneel: nog steeds handig voor auth
            $table->rememberToken();

            // created_at / updated_at als timestamptz
            $table->timestampsTz();
        });

        // Als je password_reset_tokens en sessions wilt behouden kun je deze laten staan;
        // zo niet, kun je dit deel verwijderen.

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
