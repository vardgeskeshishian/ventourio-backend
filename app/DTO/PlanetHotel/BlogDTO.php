<?php

namespace App\DTO\PlanetHotel;

use App\DTO\DTO;
use Symfony\Component\DomCrawler\Crawler;

final class BlogDTO extends DTO
{
    public function __construct(public  Crawler $page
    ) {}

    /**
     * @return Crawler
     */
    public function getInfoBlock(): Crawler
    {
        return $this->page->filter('.c-tiny')->filter('.l-row');
    }


    public function getTitle(): string
    {
        return $this->getInfoBlock()->eq(0)->filter('h1')->text();
    }

    public function getPublishDate(): string
    {
        return $this->getInfoBlock()->filter('.c-tiny__info--date')->text();
    }

    public function getAuthor(): string
    {
        return $this->getInfoBlock()->filter('.c-tiny__info--author-value')->text();
    }

    public function getImage(): string
    {
        return $this->getInfoBlock()->filter('img')->attr('data-src');
    }

    public function getTags(): array
    {
        $tags = [];

        $this->getInfoBlock()->filter('.c-tag--inline a')->each(function($tag) use(&$tags){
            $tags[] = [
                'title_l' => $tag->text(),
                'parsing_source' => $tag->attr('href'),
            ];
        });

        return $tags;
    }

    public function getDescription(): string|null
    {
        $description = array_unique($this->getInfoBlock()->filter('.c-description__text p')->extract(['_text']));

        return $description ? implode(',', $description) : null;
    }

    public function getQuote(): string|null
    {
        return $this->textExistsOrNull($this->getInfoBlock()->filter('.blog-post--plain-text blockquote'));
    }

    public function getCategoryName(): string|null
    {
        return $this->page->filter('.c-crumb--standart')
            ->filter('ol>li')
            ->eq(6)
            ->text();
    }

    public function getCategorySourceId(): string|null
    {
        $category =  $this->page->filter('.c-crumb--standart')
            ->filter('ol>li')
            ->eq(6);

        return $category->filter('a')->attr('href');
    }

    private function textExistsOrNull(Crawler $node): string|null
    {
        return $node->count() > 0 ? $node->text() : null;
    }

}
