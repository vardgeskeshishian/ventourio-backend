<?php

use App\Models\Country;
use App\Models\Language;
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
        Schema::create('country_language', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Country::class)
                ->references('id')->on('countries')
                ->cascadeOnDelete();

            $table->foreignIdFor(Language::class)
                ->references('id')->on('languages')
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
        Schema::dropIfExists('country_language');
    }
};
