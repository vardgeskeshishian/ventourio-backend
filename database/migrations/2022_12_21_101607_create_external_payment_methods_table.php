<?php

use App\Models\Booking;
use App\Models\CreditCard;
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
        Schema::create('external_payment_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Booking::class)
                ->references('id')->on('bookings')
                ->cascadeOnDelete();
            $table->tinyInteger('type');
            $table->foreignIdFor(CreditCard::class)
                ->nullable()
                ->references('id')->on('credit_cards')
                ->nullOnDelete();
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
        Schema::dropIfExists('external_payment_methods');
    }
};
