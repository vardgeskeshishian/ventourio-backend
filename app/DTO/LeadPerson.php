<?php

namespace App\DTO;

final class LeadPerson
{
    private function __construct(
        private readonly string $firstName,
        private readonly string $lastName,
        private readonly string $email
    ) {}

    public static function create(string $firstName, string $lastName, string $email): LeadPerson
    {
        return new LeadPerson($firstName, $lastName, $email);
    }

    public function value(): array
    {
        return [
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'email' => $this->email,
        ];
    }

    public function firstName(): string
    {
        return $this->firstName;
    }

    public function lastName(): string
    {
        return $this->lastName;
    }

    public function email(): string
    {
        return $this->email;
    }
}
