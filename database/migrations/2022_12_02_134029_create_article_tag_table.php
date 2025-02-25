<?php

    use App\Models\Article;
    use App\Models\Tag;
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
        Schema::create('article_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Article::class)
                ->references('id')->on('articles')
                ->cascadeOnDelete();
            $table->foreignIdFor(Tag::class)
                ->references('id')->on('tags')
                ->cascadeOnDelete();
            $table->unique(['article_id', 'tag_id']);
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
        Schema::dropIfExists('article_tag');
    }
};
