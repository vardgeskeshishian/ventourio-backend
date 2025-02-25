<?php

use App\Models\Discount;
use App\Models\District;
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
        Schema::create('hotels', function (Blueprint $table) {
            $table->id();

            $table->string('external_code')->nullable()->index();
            $table->foreignIdFor(District::class)
                ->references('id')
                ->on('districts')
                ->cascadeOnDelete();
            $table->json('title_l');
            $table->json('description_l')->nullable();
            $table->string('address');
            $table->json('house_rules')->nullable();
            $table->string('phone')->nullable();
            $table->string('fax')->nullable();
            $table->float('stars')->nullable();
            $table->json('geo')->nullable();
            $table->boolean('is_apartment')->nullable();
            $table->string('giata_code')->nullable();
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
        Schema::dropIfExists('hotels');
    }
};
