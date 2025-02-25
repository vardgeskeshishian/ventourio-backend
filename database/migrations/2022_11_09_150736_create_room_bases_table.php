<?php

use App\Models\Discount;
use App\Models\RoomType;
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
        Schema::create('room_bases', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(RoomType::class)
                ->references('id')->on('room_types')
                ->cascadeOnDelete();

            $table->json('title_l');

            $table->unsignedSmallInteger('booking_max_term')->default(365);
            $table->unsignedSmallInteger('booking_range')->default(365);
            $table->unsignedSmallInteger('cancel_range')->default(7);
            $table->unsignedTinyInteger('basis')->nullable();
            $table->boolean('refundable')->default(false);
            $table->json('remark_l')->nullable();
            $table->tinyInteger('adults_count');
            $table->tinyInteger('children_count')->nullable();
            $table->unsignedFloat('price');
            $table->unsignedFloat('base_price');

            $table->foreignIdFor(Discount::class)->nullable()
                ->references('id')->on('discounts')
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
        Schema::dropIfExists('room_bases');
    }
};
