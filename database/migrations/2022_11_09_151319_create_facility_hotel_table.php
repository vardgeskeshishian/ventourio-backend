<?php

use App\Models\Facility;
use App\Models\Hotel;
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
        Schema::create('facility_hotel', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Hotel::class)
                ->references('id')->on('hotels')
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
        Schema::dropIfExists('facility_hotel');
    }
};
