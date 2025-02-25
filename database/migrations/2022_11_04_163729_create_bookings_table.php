<?php

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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Hotel::class)
                ->nullable()
                ->references('id')->on('hotels')
                ->nullOnDelete();

            $table->foreignIdFor(User::class)
                ->references('id')->on('users');

            $table->json('lead_person');
            $table->unsignedFloat('price');
            $table->string('search_code');
            $table->string('external_code')->nullable();
            $table->string('provider');
            $table->unsignedTinyInteger('status');
            $table->timestamp('arrival_date');
            $table->timestamp('departure_date');
            $table->timestamp('cancel_deadline')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->json('extra')->nullable();

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
        Schema::dropIfExists('bookings');
    }
};
