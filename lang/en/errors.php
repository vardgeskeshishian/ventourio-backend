<?php

$appName = config('app.name');
$supportUrl =  config('front.support_url');
$passwordResetUrl = config('front.password_reset_url');

return [
    'api' => [
        'improper_format' => 'Improper response format',
        'internal_error' => 'Internal error. Contact with support',
        'person_limit_exceeded' => 'The maximum amount of persons (adults + children), per room, is 8',
        'children_limit_exceeded' => 'The limit of children, per room, is 4',
        'rooms_limit_exceeded' => 'The maximum amount of rooms, in a single search, is 8',
        'booking_not_found' => 'Booking not found',
        'no_adults' => 'ALL rooms in a request must have Adults'
    ],
    'system' => [
        'contact_support' => 'Some error. Contact the support!',
        'reload_page' => 'Internal error. Reload page.',
        'empty_required_params' => 'Empty required params',
        'rsa_encryption_failed' => 'RSA Encryption failed',
        'captcha' => 'Captcha failed',
        'currency' => [
            'not_exists' => 'Currency :value is not exists',
            'empty_rate' => 'Currency :value has empty rate'
        ]
    ],
    'payment' => [
        'not_created' => 'Payment creation error. Contact the support'
    ],
    'subscription' => [
        'stored' => 'Subscription Error!'
    ],
    'app' => [
        'common' => [
            'no_ip' => 'Can not get IP',
        ],
        'country' => [
            'can_not_define' => 'Can not define country',
            'search' => [
                'not_found' => 'No countries were found!'
            ]
        ],
        'city' => [
            'not_found' => 'City not found',
            'search' => [
                'not_found' => 'No Cities Were Found!'
            ]
        ],
        'region' => [
            'not_found' => 'Region not found',
        ],
        'certificate' => [
            'is_used' => 'Certificate already used!',
            'is_paid' => 'Certificate already paid!',
            'not_paid' => 'Certificate is not paid!',
        ],
        'page' => [
            'not_found' => 'Page not found'
        ],
        'hotel' => [
            'not_found' => 'Hotel not found',
            'search' => [
                'not_found' => 'No Hotels Were Found Matching Searched Criteria!'
            ]
        ],
        'booking' => [
            'not_created' => 'Booking creation error. Contact the support',
            'cancel' => [
                'already_cancelled' => 'Booking already cancelled.',
                'already_requested' => 'Booking cancellation already requested.',
                'can_not' => 'Booking can not be cancelled.',
            ],
            'different_price' => 'Price has changed! New price is :value',
            'already_booked' => 'Some of the rooms already booked. Please refresh the page',
            'external' => [
                'not_external' => 'This booking is not from External Service',
                'already_created' => 'External booking already created.',
                'not_created' => 'External booking is not created.',
            ],
        ],
        'user' => [
            'no_balance' => 'Not enough money. You need to replenish the balance by :value',
            'have_already_social_account' => 'You have already :provider account',
            'provider_user_already_exists' => "Подключаемый вами аккаунт :provider уже привязан к другой учетной записи. Отключить ваш аккаунт  :provider от прошлой учетной записи. Если утратили доступ к учетной записи <a href='{$supportUrl}'> {$appName} - свяжитесь садминистратором</a> или <a href='{$passwordResetUrl}'> восстановите свою учетную запись</а>.",
            'social_auth' => [
                'incorrect_redirect_url' => 'Incorrect redirect url',
            ],
            'email_used' => 'Email already used'
        ],
        'district' => [
            'not_found' => 'District not found',
        ]
    ]
];
