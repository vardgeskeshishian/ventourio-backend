<?php

use App\Models\Facility;
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
        Schema::create('facility_room_type', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(RoomType::class)
                ->references('id')->on('room_types')
                ->cascadeOnDelete();
            $table->foreignIdFor(Facility::class)
                ->references('id')->on('facilities')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('facility_room_type');
    }
};
