<?php

namespace App\Helpers;

final class Parser
{
    public static function csv(string $filePath, string $separator = '|'): array
    {
        $file_to_read = fopen($filePath, 'r');

        while (!feof($file_to_read) ) {
            $lines[] = fgetcsv($file_to_read, 1000, $separator);
        }

        fclose($file_to_read);

        return $lines ?? [];
    }
}
