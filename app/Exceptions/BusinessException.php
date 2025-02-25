<?php

namespace App\Exceptions;

use RuntimeException;

class BusinessException extends RuntimeException
{
    private string $userMessage;
    private $filterParams;

    public function __construct(string $userMessage, $filterParams = null)
    {
        $this->userMessage = $userMessage;
        $this->filterParams = $filterParams;


        parent::__construct('Business exception');
    }

    public function getUserMessage(): string
    {
        return $this->userMessage;
    }

    /**
     * @return null
     */
    public function getFilterParams()
    {
        return $this->filterParams;
    }
}
