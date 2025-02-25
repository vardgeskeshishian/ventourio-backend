<?php

namespace App\Observers;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\CompanyService;
use App\Models\Page;

class ArticleCategoryObserver
{
    /**
     * Handle the CompanyServiceService "created" event.
     *
     * @param CompanyService $companyService
     * @return void
     */
    public function created(ArticleCategory $articleCategory): void
    {
        if (empty($articleCategory->page) && ! $articleCategory->page()->exists()) {
            Page::create([
                'instance_id' => $articleCategory->id,
                'instance_type' => $articleCategory->getMorphClass(),
                'heading_title_l' => [
                    'en' => $articleCategory->title
                ],
                'meta_title_l' => [
                    'en' => $articleCategory->title
                ],
                'meta_description_l' => [
                    'en' => $articleCategory->title
                ],
                'content_l' => [],
                'slug' => str($articleCategory->title)->slug()
            ]);
        }
    }

    /**
     * Handle the CompanyServiceService "deleting" event.
     *
     * @param CompanyService $companyService
     * @return void
     */
    public function deleting(ArticleCategory $articleCategory): void
    {
        $articleCategory->page()->delete();
    }
}
