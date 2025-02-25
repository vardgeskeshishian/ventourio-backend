@php
    /**
    * @var string $email
    * @var string $password
    */
@endphp

@component('mail::message')
    <h1>Congratulations!</h1>
    <p>You are registered on Ventourio!</p>
    <p>Information for entering the Ventourio website:</p>
    <p>Email: {{ $email }}</p>
    <p>Password: {{ $password }}</p>
    <p>We wish you a pleasant use of Ventourio!</p>
@endcomponent
