<?php

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
        Schema::create('languages', function (Blueprint $table) {
            $table->id();
            $table->json('title_l');
            $table->string('code');
            $table->string('type');
            $table->string('flag')->nullable();
            $table->boolean('is_rtl')->default(0);
            $table->boolean('is_active')->default(1);
            $table->boolean('is_default')->default(0);
            $table->longText('localization_json')->nullable();
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
        Schema::dropIfExists('languages');
    }
};
