<?php

use App\Models\Region;
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
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->json('title_l');
            $table->foreignIdFor(Region::class)
                ->nullable()
                ->references('id')->on('regions');
            $table->json('description_l')->nullable();
            $table->json('geography_l')->nullable();
            $table->json('article_l')->nullable();
            $table->string('external_code')->nullable()->index();
            $table->boolean('show_in_best_deals')->default(false);
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
        Schema::dropIfExists('cities');
    }
};
