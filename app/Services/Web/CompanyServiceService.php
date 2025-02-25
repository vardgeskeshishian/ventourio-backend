<?php

namespace App\Services\Web;

use App\Http\Resources\Web\CompanyServiceResource;
use App\Models\CompanyService;
use App\Models\QuestionAnswer;

final class CompanyServiceService extends WebService
{
    public function getData(array $data): array
    {
        if (!isset($data['page'])) {
            $data['page'] = 1;
        }
        if (!isset($data['count'])) {
            $data['count'] = 8;
        }

        $companyServices = CompanyService::orderBy('id', "desc")
            ->select([
                'id',
                'title_l->' . $this->locale . ' as title',
                'description_l->' . $this->locale . ' as description',
            ])
            ->with([
                'page' => function ($query) {
                    $query->select([
                        'pages.id',
                        'pages.slug',
                        'pages.instance_id',
                        'pages.instance_type',
                    ]);
                },
                'media'
            ]);

        $count = $companyServices->count();

        $skip = $data['count'] * ($data['page'] - 1);
        $companyServices = $companyServices->take($data['count'])->skip($skip);

        return [
            'data' => CompanyServiceResource::collection($companyServices->get()),
            'pagination'=>[
                'count_pages' => ceil($count / $data['count']),
                'count' => $count
            ]
        ];
    }

    public function get(array $data): CompanyService
    {
        $slug = $data['slug'];
        $companyService = CompanyService::select([
                'id',
                'title_l->' . $this->locale . ' as title',
                'description_l->' . $this->locale . ' as description',
            ])
            ->whereHas('page', function ($query) use ($slug) {
                $query->where('slug', $slug);
            })
            ->with([
                'page' => function ($query) {
                    $query->select([
                        'pages.id',
                        'pages.slug',
                        'pages.type',
                        'pages.instance_id',
                        'pages.instance_type',
                        'pages.content_l->' . $this->locale . ' as content',
                        'pages.heading_title_l->' . $this->locale . ' as heading_title',
                        'pages.meta_title_l->' . $this->locale . ' as meta_title',
                        'pages.meta_description_l->' . $this->locale . ' as meta_description',
                    ])
                        ->with([
                            'qa' => function ($query) {
                                $query->select([
                                    'question_answers.id',
                                    'question_answers.page_id',
                                    'question_answers.question_l->' . $this->locale . ' as question',
                                    'question_answers.answer_l->' . $this->locale . ' as answer',
                                ]);
                            }
                        ]);
                },
                'media'
            ])
            ->first();

        if (empty($companyService->page->qa) || $companyService->page->qa->isEmpty()) {
            $commonQa = QuestionAnswer::whereNull('page_id')->select([
                'question_answers.id',
                'question_answers.page_id',
                'question_answers.question_l->' . $this->locale . ' as question',
                'question_answers.answer_l->' . $this->locale . ' as answer',
            ])->get();

            $companyService->page->setRelation('qa', $commonQa);
        }

        return $companyService;
    }
}
