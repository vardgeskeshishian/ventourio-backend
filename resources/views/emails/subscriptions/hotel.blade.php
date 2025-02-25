@php
    /**
    * @var string $href
    * @var \App\Models\Hotel $model
    */
@endphp

@component('mail::message')
    <p>Hello!</p>
    <p>A new <a href="{{ $href }}">hotel - "{{ $model->title }}"</a> has been registered on our Ventourio website.</p>
    <p>Check it out, maybe this is exactly what you were looking for!</p>
    <p>Thank you for choosing Ventourio!</p>
@endcomponent
