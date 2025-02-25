<?php

use App\Models\PaymentSystem;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_ways', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(PaymentSystem::class)
                ->references('id')->on('payment_systems')
                ->restrictOnDelete();

            $table->string('payment_system_way')->nullable();
            $table->boolean('enabled')->default(true);
            $table->json('settings')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_ways');
    }
};
