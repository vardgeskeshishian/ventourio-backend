<?php

use App\Models\ArticleCategory;
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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ArticleCategory::class)
                ->nullable()
                ->references('id')->on('article_categories')
                ->nullOnDelete();
            $table->json('title_l');
            $table->json('content_l');
            $table->json('quote_l')->nullable();
            $table->json('author_l')->nullable();
            $table->string('parsing_source')
                ->unique()
                ->nullable();
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
        Schema::dropIfExists('articles');
    }
};
