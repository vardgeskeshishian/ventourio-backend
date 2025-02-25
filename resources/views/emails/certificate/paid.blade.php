@php
    /** @var \App\Models\Certificate $certificate */
@endphp

@component('mail::message')
    <h1>Hello, {{ $certificate->boughtByUser->first_name }}.</h1>
    <p>You have successfully purchased a gift certificate ({{ $certificate->baseCertificate->title }}).</p>
    <p>Have a good day!</p>
    <p>
        <i>*This email is auto-generated and does not need to be answered*</i>
    </p>
@endcomponent
