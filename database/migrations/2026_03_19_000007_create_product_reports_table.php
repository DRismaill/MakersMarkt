<?php

use App\Enums\ReportStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('product_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('reported_by_user_id')->constrained('users');
            $table->text('reason')->nullable();
            $table->string('status', 20)->default(ReportStatus::Open->value);
            $table->timestampTz('created_at')->useCurrent();

            $table->index(['product_id']);
            $table->index(['status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_reports');
    }
};
