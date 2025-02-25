<?php

namespace App\Models;

use App\Models\System\HasSubscribers;
use App\Models\System\CanNotifySubscribers;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

class Article extends Model implements HasMedia, HasSubscribers
{
    use HasFactory, HasTranslations, InteractsWithMedia, CanNotifySubscribers;

    protected string $mailTemplate = 'emails.subscriptions.article';
    protected string $mailSubject = 'New Article on Ventourio';

    protected $fillable = [
        'title_l',
        'article_category_id',
        'content_l',
        'parsing_source',
        'quote_l',
        'author_l',
        'created_at',
    ];

    public array $translatable = [
        'title_l',
        'quote_l',
        'author_l',
    ];

    protected $casts = [
        'content_l' => 'array'
    ];

    protected function title(): Attribute
    {
        return new Attribute(
            get: fn($value) => $value ?? $this->getAttributeValue('title_l')
        );
    }

    protected function author(): Attribute
    {
        return new Attribute(
            get: fn($value) => $value ?? $this->getAttributeValue('author_l')
        );
    }

    protected function quote(): Attribute
    {
        return new Attribute(
            get: fn($value) => $value ?? $this->getAttributeValue('quote_l')
        );
    }

    public function page(): MorphOne
    {
        return $this->morphOne(Page::class, 'instance');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ArticleCategory::class, 'article_category_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($model) {

            $model->page()->delete();

        });
    }

    public function getMailData(): array
    {
        if ( ! $this->relationLoaded('page')) {
            $this->load('page');
        }

        return [
            'href' => config('front.web_url') . '/articles/' . $this->page->slug
        ];
    }


    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('original')->format('webp')->nonQueued();
    }

}
