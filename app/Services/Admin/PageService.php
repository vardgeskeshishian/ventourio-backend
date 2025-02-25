<?php

namespace App\Services\Admin;

use App\Enums\PageType;
use App\Exceptions\BusinessException;
use App\Http\Resources\Admin\PageResource;
use App\Models\InfoBlock;
use App\Models\Page;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PageService
{
    public function getData(array $data): array
    {
        $pages = Page::query();

        $page = $data['page'] ?? 1;
        $take = $data['count'] ?? 8;
        $count = $pages->count();

        if ($page) {
            $skip = $take * ($page - 1);
            $pages = $pages->take($take)->skip($skip);
        } else {
            $pages = $pages->take($take)->skip(0);
        }

        return [
            'success' => true,
            'data' => PageResource::collection($pages->with('infoBlocks')->get()),
            'pagination'=>[
                'count_pages' => ceil($count / $take),
                'count' => $count
            ]
        ];
    }

    public function store(array $data): Page
    {
        DB::beginTransaction();
        try {
            $page = Page::create([
                'content_l' => $data['content_l'] ?? null,
                'heading_title_l' => $data['heading_title_l'] ?? null,
                'meta_title_l' => $data['meta_title_l'] ?? null,
                'meta_description_l' => $data['meta_description_l'] ?? null,
                'slug' => $data['slug'],
                'type' => PageType::from($data['type'])
            ]);

            if ( ! empty($data['info_blocks'])) {
                $blocks = [];

                foreach($data['info_blocks'] as $infoBlock){
                    $blocks[] = new InfoBlock($infoBlock);
                }

                $page->infoBlocks()->saveMany($blocks);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            throw new BusinessException($e->getMessage());
        }

        return $page;
    }

    public function update(array $data, Page $page): void
    {
        DB::beginTransaction();
        try {

            $page->update([
                'content_l' => $data['content_l'] ?? null,
                'heading_title_l' => $data['heading_title_l'] ?? null,
                'meta_title_l' => $data['meta_title_l'] ?? null,
                'meta_description_l' => $data['meta_description_l'] ?? null,
                'slug' => $data['slug'],
                'type' => PageType::from($data['type']),
            ]);

            $page->infoBlocks()->delete();

            if ( ! empty($data['info_blocks'])) {
                $blocks = [];

                foreach($data['info_blocks'] as $infoBlock){
                    $blocks[] = new InfoBlock($infoBlock);
                }

                $page->infoBlocks()->saveMany($blocks);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            throw new BusinessException($e->getMessage());
        }
    }
}
