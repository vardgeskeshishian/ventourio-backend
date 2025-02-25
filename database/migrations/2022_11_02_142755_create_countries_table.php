<?php

use App\Models\Continent;
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
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Continent::class)
                ->nullable()
                ->references('id')->on('continents');
            $table->json('title_l');
            $table->json('description_l')->nullable();
            $table->json('geography_l')->nullable();
            $table->json('article_l')->nullable();
            $table->json('nationality_l')->nullable();
            $table->string('iso_code')->nullable();
            $table->string('external_code')->nullable();
            $table->json('geo')->nullable();
            $table->string('parsing_source')->nullable();
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
        Schema::dropIfExists('countries');
    }
};
