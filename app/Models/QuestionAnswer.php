<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Spatie\Translatable\HasTranslations;

class QuestionAnswer extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'page_id',
        'answer_l',
        'question_l',
    ];

    public array $translatable = [
        'answer_l',
        'question_l',
    ];

    protected function answer(): Attribute
    {
        return new Attribute(
            get: fn($value) => $value ?? $this->getAttributeValue('answer_l')
        );
    }

    protected function question(): Attribute
    {
        return new Attribute(
            get: fn($value) => $value ?? $this->getAttributeValue('question_l')
        );
    }

    public function page(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

}
