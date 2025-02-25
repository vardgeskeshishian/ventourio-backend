@component('mail::message')
    <h1>New Subscription!!</h1>
    <p>Thank you for subscription:</p>
    <p>Please verify your email:</p>

    @component('mail::panel')
        <a href="{{ route( 'subscriber.verify', ['token' => $verify_token] ) }}">Verify Email</a>
    @endcomponent

    <p>The allowed duration of the code is one hour from the time the message was sent</p>
@endcomponent
