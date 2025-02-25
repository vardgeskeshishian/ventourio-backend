<?php

namespace App\Services\Admin;

use App\Enums\Gender;
use App\Http\Resources\Admin\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

final class EditorService
{

    private string $editorGalleryPath = '/public/editor_gallery/';

    public function uploadAndReturnPath(array $data): string
    {

        $media = $data['upload'];

        $imageName = time().'.'.$media->extension();

        $media->storeAs("{$this->editorGalleryPath}",$imageName);

        return asset('storage/editor_gallery/' . ($imageName));
    }
}
