<?php

use App\Models\Page;
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
        Schema::create('info_blocks', function (Blueprint $table) {
            $table->id();
            $table->string('alias');
            $table->foreignIdFor(Page::class)
                ->nullable()
                ->references('id')->on('pages')
                ->cascadeOnDelete();
            $table->json('content_l');
            $table->timestamps();

            $table->unique(['alias', 'page_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('info_blocks');
    }
};
