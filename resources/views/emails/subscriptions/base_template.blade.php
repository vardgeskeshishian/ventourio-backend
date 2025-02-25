@php
    /** @var \App\Models\System\HasSubscribers $model */
@endphp

@component('mail::message')
    <h1>{{ $model->getMailSubject() }}</h1>
@endcomponent
