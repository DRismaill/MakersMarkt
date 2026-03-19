<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {

    public function up()
    {
        Schema::create('product_types', function (Blueprint $table) {
            $table->integer('id')->nullable()->primary();
            $table->string('name')->unique();
            $table->text('description')->nullable();

        });
    }

    public function down()
    {
        Schema::dropIfExists('product_types');
    }
};
