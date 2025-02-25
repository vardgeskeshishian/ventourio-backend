<?php

namespace App\Services\Parsing;

use App\Jobs\ImportParsed\ImportParsedContinentJob;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

final class ImportService
{
    public function run(?string $filePath = null): void
    {
        if (empty($filePath)) {
            $filePath = storage_path('app/parse.json');
        }

        if ( ! file_exists($filePath)) {
            throw new FileNotFoundException();
        }

        $planetOfHotelsInstances = json_decode(file_get_contents($filePath), true);

        if (empty($planetOfHotelsInstances)) {
            throw new \Exception('ImportData File is empty');
        }

        foreach (array_keys($planetOfHotelsInstances) as $continentIndex) {
            ImportParsedContinentJob::dispatch($continentIndex, $filePath);
        }
    }
}
