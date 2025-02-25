<?php

namespace App\Services\Web;

use App\Exceptions\BusinessException;
use App\Models\City;
use App\Models\District;
use App\Models\Page;
use App\Models\QuestionAnswer;
use App\Models\Region;
use App\Services\Web\City\QueryHelper as CityQueryHelper;
use App\Services\Web\District\QueryHelper as DistrictQueryHelper;
use App\Services\Web\Region\QueryHelper as RegionQueryHelper;

final class PageService extends WebService
{
    public function get(string $slug): Page
    {
        $page = Page::where('slug', $slug)
            ->select([
                'id',
                'slug',
                'content_l->' . $this->locale . ' as content',
                'meta_title_l->' . $this->locale . ' as meta_title',
                'heading_title_l->' . $this->locale . ' as heading_title',
                'meta_description_l->' . $this->locale . ' as meta_description',
            ])
            ->with([
                'infoBlocks' => function ($query) {
                    $query->select([
                        'info_blocks.id',
                        'info_blocks.alias',
                        'info_blocks.page_id',
                        'info_blocks.content_l->' . $this->locale . ' as content',
                    ]);
                }
            ])
            ->first();

        if ( ! $page) {
            throw new BusinessException(__('errors.app.page.not_found'));
        }

        $this->formatInfoBlocks($page);

        return $page;
    }

    public function getForHotelSearch(?string $citySlug = null, null|string|array $districtSlug = null, string $regionSlug = null): array
    {
        if (empty($citySlug) && empty($districtSlug) && empty($regionSlug)) {
            return [];
        }

        if ( ! empty($citySlug)) {
            $where = [
                ['instance_type', City::class],
                ['slug', $citySlug]
            ];

            $instanceQuery = CityQueryHelper::relationForBreadcrumbs($this->locale);

        } else if ( ! empty($regionSlug)) {
            $where = [
                ['instance_type', Region::class],
                ['slug', $regionSlug]
            ];

            $instanceQuery = RegionQueryHelper::relationForBreadcrumbs($this->locale);

        } else {

            if (is_array($districtSlug)) {
                $districtSlug = $districtSlug[0];
            }

            $where = [
                ['instance_type', District::class],
                ['slug', $districtSlug]
            ];

            $instanceQuery = DistrictQueryHelper::relationForBreadcrumbs($this->locale);
        }

        $page = Page::where($where)
            ->select([
                'id',
                'instance_id',
                'instance_type',
                'meta_title_l->' . $this->locale . ' as meta_title',
                'heading_title_l->' . $this->locale . ' as heading_title',
                'meta_description_l->' . $this->locale . ' as meta_description',
                'content_l->' . $this->locale . ' as content',
                'view_count'
            ])
            ->with([
                'instance' => $instanceQuery,
                'qa' => function ($query) {
                    $query->select([
                        'question_answers.id',
                        'question_answers.page_id',
                        'question_answers.answer_l->' . $this->locale . ' as answer',
                        'question_answers.question_l->' . $this->locale . ' as question',
                    ]);
                },
            ])
            ->first();

        if ( ! $page) {
            throw new BusinessException(__('errors.app.page.not_found'));
        }

        if (empty($page->qa) || $page->qa->isEmpty()) {

            $commonQa = QuestionAnswer::whereNull('page_id')->select([
                'question_answers.id',
                'question_answers.page_id',
                'question_answers.question_l->' . $this->locale . ' as question',
                'question_answers.answer_l->' . $this->locale . ' as answer',
            ])->get();

            $page->setRelation('qa', $commonQa);
        }

        $page->instance->append('breadcrumbs');
        $page->setAttribute('breadcrumbs', $page->instance->breadcrumbs);
        $page->makeHidden('instance');

        self::incrementViewCount($page->id);

        return $page->toArray();
    }

    public static function incrementViewCount(int $page)
    {
        // todo make check to not increment if person just refreshing the page
        Page::where('id', $page)->increment('view_count');
    }

    private function formatInfoBlocks(Page $page)
    {
        $infoBlocks = $page->infoBlocks;
        if ($infoBlocks->isEmpty()) {
            return;
        }

        $result = [];
        foreach ($infoBlocks as $infoBlock) {

            $result[$infoBlock->alias] = $infoBlock->content;
        }

        $page->setAttribute('infoBlocks', $result);
        $page->unsetRelation('infoBlocks');
    }
}
