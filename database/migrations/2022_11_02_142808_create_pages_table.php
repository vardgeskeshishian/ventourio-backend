<?php

use App\Enums\PageType;
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
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->nullableMorphs('instance');
            $table->string('slug')->index();
            $table->string('type')->default(PageType::JSON->value);
            $table->json('content_l')->nullable();
            $table->json('heading_title_l')->nullable();
            $table->json('meta_title_l')->nullable();
            $table->json('meta_description_l')->nullable();
            $table->unsignedInteger('view_count')->default(0);
            $table->timestamps();

            $table->unique(['instance_type', 'slug'], 'page_instance_slug_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pages');
    }
};
