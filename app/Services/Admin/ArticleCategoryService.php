<?php

namespace App\Services\Admin;

use App\Http\Resources\Admin\ArticleCategoryResource;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Page;

class ArticleCategoryService
{
    public function getData($request)
    {
        $articleCategories = ArticleCategory::orderBy('id', "desc");

        $page  = $request->input('page') ? : 1;
        $take  = $request->input('count') ? : 8;
        $count = $articleCategories->count();

        if ($page) {
            $skip = $take * ($page - 1);
            $articleCategories = $articleCategories->take($take)->skip($skip);
        } else {
            $articleCategories = $articleCategories->take($take)->skip(0);
        }

        return [
            'data' => ArticleCategoryResource::collection($articleCategories->get()),
            'pagination'=>[
                'count_pages' => ceil($count / $take),
                'count' => $count
            ]
        ];
    }

    public function store(array $data)
    {

        $articleCategory = ArticleCategory::create([
            'title_l' => $data['title_l'],
            'color_hex' => $data['color_hex']
        ]);


        $this->updateOrCreatePage($data, $articleCategory);

        return $articleCategory;
    }



    public function update(array $data, $articleCategory)
    {
        $this->updateOrCreatePage($data, $articleCategory);
        $articleCategory->update($data);

        return $articleCategory;
    }

    /**
     * @throws Exception
     */
    private function updateOrCreatePage(array $data, ArticleCategory $articleCategory): void
    {
        $pageData = $data['page'] ?? [];
        if (empty($pageData['slug'])) {
            $pageData['slug'] = $articleCategory->title;
        }

        $pageData['slug'] = str($pageData['slug'])->slug();

        if (Page::where('instance_type', $articleCategory->getMorphClass())->whereNot('instance_id', $articleCategory->id)->where('slug', $pageData['slug'])->exists()) {
            throw new Exception("slug '{$pageData['slug']}' already used");
        }

        Page::updateOrCreate([
            'instance_id' => $articleCategory->id,
            'instance_type' => $articleCategory->getMorphClass()
        ],
            [
                'slug' => $pageData['slug'],
                'heading_title_l' => $pageData['heading_title_l'] ?? null,
                'meta_title_l' => $pageData['meta_title_l'] ?? null,
                'meta_description_l' => $pageData['meta_description_l'] ?? null,
                'content_l' => $pageData['content_l'] ?? null
            ]);
    }
}
