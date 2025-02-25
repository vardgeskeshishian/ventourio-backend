@php
    /**
    * @var \App\Models\Booking $booking
    * @var \App\Models\PaymentRequisite $payment_requisite
    * @var string $support_href
    */
@endphp

@component('mail::message')
    <p>You can pay for the booking using these details:</p>
    <p>{{ $payment_requisite->data }}</p>
    <p>Have questions about payment? Contact <a href="{{ $support_href }}">support</a></p>
@endcomponent
