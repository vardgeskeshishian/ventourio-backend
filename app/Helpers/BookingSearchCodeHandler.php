<?php

namespace App\Helpers;

use App\Enums\Provider;
use Exception;
use JetBrains\PhpStorm\ArrayShape;

final class BookingSearchCodeHandler
{
    /**
     * @throws Exception
     */
    public static function create(array $data, Provider $provider = Provider::DB): string
    {
        return match ($provider) {
            Provider::DB => self::createDB($data),
            Provider::GOGLOBAL => self::createGoGlobal($data),
        };
    }

    /**
     * @throws Exception
     */
    public static function parse(string $searchCode, Provider $provider = Provider::DB): array
    {
        return match ($provider) {
            Provider::DB => self::parseDB($searchCode),
            Provider::GOGLOBAL => self::parseGoGlobal($searchCode),
        };
    }

    /**
     * @throws Exception
     */
    private static function createDB(array $data): string
    {
        $hotelId = $data['hotel_id'] ?? null;
        $rooms   = $data['rooms'] ?? null;

        if (empty($hotelId) || empty($rooms)) {
            throw new Exception('Empty required params');
        }

        $searchCode = $hotelId . '|';
        $searchCode .= implode('/', $rooms);

        return $searchCode;
    }

    public static function createGoGlobal(array $data): string
    {
        $searchCode = $data['search_code'] ?? null;
        $searchSession = $data['search_session'] ?? null;

        if (empty($searchCode) || empty($searchSession)) {
            throw new Exception('Empty required params');
        }

        return $searchCode . '|' . $searchSession;
    }

    #[ArrayShape(['hotel_id' => "string", 'rooms' => "string[]"])]
    private static function parseDB(string $searchCode): array
    {
        $array = explode('|', $searchCode);

        if (count($array) !== 2) {
            throw new Exception('search_code is not correct');
        }

        $hotelId = $array[0];
        $rooms = explode('/', $array[1]);

        return [
            'hotel_id' => $hotelId,
            'rooms' => array_values($rooms),
        ];
    }

    #[ArrayShape(['external_code' => "string", 'search_session' => "string"])]
    private static function parseGoGlobal(string $searchCode): array
    {
        $array = explode('|', $searchCode);

        if (count($array) !== 2) {
            throw new Exception('search_code is not correct');
        }

        $external_code = $array[0];
        $searchSession = $array[1];

        return [
            'external_code' => $external_code,
            'search_session' => $searchSession
        ];
    }
}
