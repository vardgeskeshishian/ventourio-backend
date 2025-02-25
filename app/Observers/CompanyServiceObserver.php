<?php

namespace App\Observers;

use App\Models\CompanyService;
use App\Models\Page;

class CompanyServiceObserver
{
    /**
     * Handle the CompanyServiceService "created" event.
     *
     * @param CompanyService $companyService
     * @return void
     */
    public function created(CompanyService $companyService): void
    {
        if (empty($companyService->page) && ! $companyService->page()->exists()) {
            Page::create([
                'instance_id' => $companyService->id,
                'instance_type' => $companyService->getMorphClass(),
                'heading_title_l' => [
                    'en' => $companyService->title
                ],
                'meta_title_l' => [
                    'en' => $companyService->title
                ],
                'meta_description_l' => [
                    'en' => $companyService->title
                ],
                'content_l' => [
                    'en' => [
                        'header' => 'Title 1',
                        'paragraph' => 'By investing in the technology that helps take the friction out of travel, Ventourio.com seamlessly connects millions of travellers with memorable experiences, a range of transport options and incredible places to stay - from homes to hotels and much more. As one of the world’s largest travel marketplaces for both established brands and entrepreneurs of all sizes, Ventourio.com enables properties all over the world to reach a global audience and grow their businesses.'
                    ]
                ],
                'slug' => str($companyService->title)->slug()
            ]);
        }
    }

    /**
     * Handle the CompanyServiceService "deleting" event.
     *
     * @param CompanyService $companyService
     * @return void
     */
    public function deleting(CompanyService $companyService): void
    {
        $companyService->page()->delete();
    }
}
