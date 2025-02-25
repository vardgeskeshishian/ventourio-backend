<?php

namespace App\Observers;

use App\Models\Article;
use App\Models\CompanyService;
use App\Models\Page;

class ArticleObserver
{
    /**
     * Handle the CompanyServiceService "created" event.
     *
     * @param CompanyService $companyService
     * @return void
     */
    public function created(Article $article): void
    {
        if (empty($article->page) && ! $article->page()->exists()) {
            Page::create([
                'instance_id' => $article->id,
                'instance_type' => $article->getMorphClass(),
                'meta_title_l' => [
                    'en' => $article->title
                ],
                'heading_title_l' => [
                    'en' => $article->title
                ],
                'meta_description_l' => [
                    'en' => $article->title
                ],
                'content_l' => [],
                'slug' => str($article->title)->slug()
            ]);
        }
    }

    /**
     * Handle the CompanyServiceService "deleting" event.
     *
     * @param CompanyService $companyService
     * @return void
     */
    public function deleting(Article $article): void
    {
        $article->page()->delete();
    }
}
