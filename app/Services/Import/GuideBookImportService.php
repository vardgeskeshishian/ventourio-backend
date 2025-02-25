<?php

namespace App\Services\Import;
use App\Helpers\UX;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Tag;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Storage;

set_time_limit(9000);


final class GuideBookImportService
{

    public string $domain = 'https://planetofhotels.com';
    public function run(): void
    {

        $this->articles = Storage::disk('public')->allFiles('planet-hotel-guide-book-data');
        $lastJsonFile = Storage::disk('public')->path($this->articles[count($this->articles) - 1]);

        if ( ! file_exists($lastJsonFile)) {
            throw new FileNotFoundException();
        }

        $fileToJson = json_decode(file_get_contents($lastJsonFile), true);

        if (empty($fileToJson)) {
            throw new \Exception('ImportData File is empty');
        }

        foreach ($fileToJson as $data) {
            $article = Article::updateOrCreate([
                'parsing_source' => $data['parsing_source']
                ],
                [
                    'parsing_source' => $data['parsing_source'],
                    'title_l' => $data['title_l'],
                    'content_l' => $data['content_l'],
                    'quote_l' => $data['quote_l'],
                    'author_l' => $data['author_l'],
                    'created_at' => $data['created_at']
                ]);
            $article->clearMediaCollection('default');
            $article->addMediaFromUrl($this->domain . $data['image'])
                ->toMediaCollection('default');

            $articleCategory = ArticleCategory::updateOrCreate([
                'parsing_source' => $data['category']['parsing_source']
                ],
                [
                    'title_l' => $data['category']['title_l'],
                ]);

            if(empty($articleCategory->color_hex)){
                $articleCategory->update([
                    'color_hex' => UX::generateHexColor(),
                ]);
            }

            $article->update([
                'article_category_id' => $articleCategory->id,
            ]);

            $tags = $this->updateOrCreateTags($data['tags']);

            $article->tags()->sync($tags ?? []);
        }
    }

    private function updateOrCreateTags(array $tags): array
    {
        $tagIds = [];
        foreach($tags as $tag){
            $tag = Tag::updateOrCreate([
                'parsing_source' => $tag['parsing_source']
                ],
                [
                    'title_l' => $tag['title_l'],
                ]);

            $tagIds[] = $tag->id;
        }

        return $tagIds;
    }

}
