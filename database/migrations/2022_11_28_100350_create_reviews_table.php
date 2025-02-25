<?php

use App\Models\Booking;
use App\Models\Hotel;
use App\Models\User;
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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Booking::class)
                ->references('id')->on('bookings')
                ->cascadeOnDelete();

            $table->longText('body');
            $table->json('rating');
            $table->unsignedFloat('rating_avg');

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
        Schema::dropIfExists('reviews');
    }
};
