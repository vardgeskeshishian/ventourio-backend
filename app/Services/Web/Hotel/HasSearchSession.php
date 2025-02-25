<?php

namespace App\Services\Web\Hotel;

trait HasSearchSession
{
    private function addSearchSession(array &$data): void
    {
        $dataForHash = $data;
        $dataForHash['currency'] = $this->currency;
        $dataForHash['locale'] = $this->locale;
        $dataForHash['url'] = url()->current();

        $data['search_session'] = hash('md5', json_encode($dataForHash));
    }
}
