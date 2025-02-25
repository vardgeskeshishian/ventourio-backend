<?php

namespace App\Services\Admin;


use App\Models\Language;

class LanguageService
{

    public function __construct()
    {

    }

    public function store(array $data): Language
    {
        $data['localization_json'] = json_decode($data['localization_json']);




//        $imag = $data['flag'] ?? null;
//        if ($imag) {
//            $fileName = uniqid() . '.'. $imag->getClientOriginalExtension();
//            $imag->storeAs('public/flag', $fileName);
//            $data['flag'] = $fileName;
//        }

        $language = Language::create([
            'title_l' => $data['title_l'],
            'code' => $data['code'],
            'type' => $data['type'],
            'flag' => $data['flag'] ?? null,
            'is_rtl' => $data['is_rtl'] ?? 0,
            'is_active' => $data['is_active'] ?? 1,
            'is_default' => $data['is_default'] ?? 0,
            'localization_json' => $data['localization_json'] ?? null
        ]);

        if ( ! empty($data['flag'])) {
            $language->addMediaFromRequest('flag')
                ->toMediaCollection('flag');
        }

        $language->countries()->sync($data['countries'] ?? []);

        return $language;
    }

    public function updateOrCreateLanguage(array $data, int $id): Language
    {
        $data['localization_json'] = json_decode($data['localization_json']);

//        if ( ! empty($data['flag']) && !is_string($data['flag'])) {
//            $imag = $data['flag'];
//            if ($imag) {
//                $fileName = uniqid() . '.'. $imag->getClientOriginalExtension();
//                $imag->storeAs('public/flag', $fileName);
//
//                $flag = Language::where('id', $id)->pluck('flag')->first();
//                if ($flag) {
//                    unlink(storage_path('app/public/flag/'.$flag));
//                }
//
//                $data['flag'] = $fileName;
//            }
//        }

        $language = Language::updateOrCreate(
            ['id' => $id],
            [
                'title_l' => $data['title_l'],
                'code' => $data['code'],
                'type' => $data['type'],
                'flag' => $data['flag'] ?? null,
                'is_rtl' => $data['is_rtl'] ?? 0,
                'is_active' => $data['is_active'] ?? 1,
                'is_default' => $data['is_default'] ?? 0,
                'localization_json' => $data['localization_json'] ?? null
            ]
        );

        if ( ! empty($data['flag'])) {
            $language->clearMediaCollection('flag');
            $language->addMediaFromRequest('flag')
                ->toMediaCollection('flag');
        }

        $language->countries()->sync($data['countries'] ?? []);

        return $language;
    }

    public function destroy ($id)
    {
        $flag = Language::where('id', $id)->pluck('flag')->first();
        if ($flag) {
            unlink(storage_path('app/public/flag/'.$flag));
        }

        $result = Language::where('id', $id)->delete() ;

        return $result;

    }

}
