<?php

use \App\Enums\RoomBasis;
use \App\Enums\BookingStatus;
use \App\Enums\HotelStars;

return [
    'auth' => [
        'testing' => true,
        'dev' => [
            'agency' => 134596,
            'user' => 'PERSONALXMLTEST',
            'password' => 'XF0NGH6NE12',
            'pub_key' => storage_path('goglobal/public.key')
        ],
        'prod' => [
            'agency' => env('GOGLOBAL_AGENCY'),
            'user' => env('GOGLOBAL_USER'),
            'password' => env('GOGLOBAL_PASSWORD'),
        ],
    ],
    'request_types' => [
        'hotel_search' => [
            'operation' => 'HOTEL_SEARCH_REQUEST',
            'code' => 11,
            'version' => 2.3
        ],
        'booking_valuation' => [
            'operation' => 'BOOKING_VALUATION_REQUEST',
            'code' => 9,
            'version' => 2.0
        ],
        'booking_insert' => [
            'operation' => 'BOOKING_INSERT_REQUEST',
            'code' => 2,
            'version' => 2.3
        ],
        'booking_status' => [
            'operation' => 'BOOKING_STATUS_REQUEST',
            'code' => 5,
            'version' => null
        ],
        'booking_search' => [
            'operation' => 'BOOKING_SEARCH_REQUEST',
            'code' => 4,
            'version' => 2.2
        ],
        'adv_booking_search' => [
            'operation' => 'ADV_BOOKING_SEARCH_REQUEST',
            'code' => 10,
            'version' => 2.2
        ],
        'booking_cancel' => [
            'operation' => 'BOOKING_CANCEL_REQUEST',
            'code' => 3,
            'version' => null,
        ],
        'voucher_details' => [
            'operation' => 'VOUCHER_DETAILS_REQUEST',
            'code' => 8,
            'version' => 2.3
        ],
        'booking_info_for_amendment' => [
            'operation' => 'BOOKING_INFO_FOR_AMENDMENT_REQUEST',
            'code' => 15,
            'version' => null
        ],
        'booking_amendment' => [
            'operation' => 'BOOKING_AMENDMENT_REQUEST',
            'code' => 16,
            'version' => null
        ],
        'hotel_info' => [
            'operation' => 'HOTEL_INFO_REQUEST',
            'code' => 61, // 6 - without GEO, 61 - with GEO
            'version' => 2.2
        ],
        'price_breakdown' => [
            'operation' => 'PRICE_BREAKDOWN_REQUEST',
            'code' => 14,
            'version' => 2.0
        ],
    ],
    'booking' => [
        'room_basis' => [
            [
                'code' => 'BB',
                'description' => 'BED AND BREAKFAST',
                'matched' => RoomBasis::BED_AND_BREAKFAST
            ],
            [
                'code' => 'CB',
                'description' => 'CONTINENTAL BREAKFAST',
                'matched' => RoomBasis::CONTINENTAL_BREAKFAST
            ],
            [
                'code' => 'AI',
                'description' => 'ALL-INCLUSIVE',
                'matched' => RoomBasis::ALL_INCLUSIVE
            ],
            [
                'code' => 'FB',
                'description' => 'FULL-BOARD',
                'matched' => RoomBasis::FULL_BOARD
            ],
            [
                'code' => 'HB',
                'description' => 'HALF-BOARD',
                'matched' => RoomBasis::HALF_BOARD
            ],
            [
                'code' => 'RO',
                'description' => 'ROOM ONLY',
                'matched' => RoomBasis::ROOM_ONLY
            ],
            [
                'code' => 'BD',
                'description' => 'BED AND DINNER',
                'matched' => RoomBasis::BED_AND_DINNER
            ],
        ],
        'statuses' => [
            [
                'code' => 'RQ',
                'title' => 'Requested',
                'description' => 'Request for Booking was received - status not final, pending confirmation (C) or RJ',
                'matched' => BookingStatus::REQUESTED->value
            ],
            [
                'code' => 'C',
                'title' => 'Confirmed',
                'description' => 'Booking is finalized and active',
                'matched' => BookingStatus::CONFIRMED->value
            ],
            [
                'code' => 'RX',
                'title' => 'Req. Cancellation',
                'description' => 'Cancellation Request was received - status not final, expect either X, C, VI or XF',
                'matched' => BookingStatus::CANCELLATION_REQUESTED->value
            ],
            [
                'code' => 'X',
                'title' => 'Cancelled',
                'description' => 'Booking canceled FOC',
                'matched' => BookingStatus::CANCELLED->value
            ],
            [
                'code' => 'XF',
                'title' => 'Cancelled with Fees',
                'description' => 'Booking has penalty fees - Booking Price is the fee amount',
                'matched' => BookingStatus::CANCELLED_WITH_FEES->value
            ],
            [
                'code' => 'RJ',
                'title' => 'Rejected',
                'description' => 'Booking was rejected',
                'matched' => BookingStatus::REJECTED->value
            ],
            [
                'code' => 'VCH',
                'title' => 'Voucher Issued',
                'description' => 'Booking is finalized, confirmed and the voucher was issued',
                'matched' => BookingStatus::VOUCHER_ISSUED->value
            ],
            [
                'code' => 'VRQ',
                'title' => 'Voucher Req.',
                'description' => 'Booking is finalized, confirmed and request for voucher was issued',
                'matched' => BookingStatus::VOUCHER_REQUESTED->value
            ],
        ],
        'facilities' => [
            "24-hour check-in",
            "24-hour reception",
            "Aerobics",
            "Air conditioning",
            "Air conditioning (centrally regulated)",
            "Air conditioning (individually regulated)",
            "Aqua aerobics",
            "Archery",
            "Badminton",
            "Balcony/Terrace",
            "Banana Boat",
            "Bar(s)",
            "Basketball",
            "Bathroom",
            "Bathtub",
            "Beach Volleyball",
            "Bicycle Cellar",
            "Bicycle Hire",
            "Bidet",
            "Billiards/Snooker",
            "Bocce",
            "Bowling",
            "Café",
            "Canoe",
            "Car Park",
            "Carpeting",
            "Casino",
            "Catamaran",
            "Central Heating",
            "Children's Entertainment",
            "Children's Pool",
            "Cloakroom",
            "Conference Room",
            "Currency Exchange",
            "Cycling/Mountain Biking",
            "Darts",
            "Direct dial telephone",
            "Diving",
            "Double Bed",
            "Entertainment Programme",
            "Final Cleaning",
            "Foyer",
            "Fridge",
            "Games room",
            "Garage",
            "Golf",
            "Gym",
            "Hairdresser",
            "Hairdryer",
            "Heated Pool",
            "Heating (individually regulated)",
            "Horse Riding",
            "Hotel Safe",
            "Indoor Pool",
            "Internet access",
            "Jacuzzi",
            "Jet Skiing",
            "Kids Club",
            "King-size Bed",
            "Kitchenette",
            "Laundry Facilities",
            "Laundry Service",
            "Lifts",
            "Lobby",
            "Lounge",
            "Massage",
            "Medical Assistance",
            "Microwave",
            "Minibar",
            "Minigolf",
            "Mobile Phone Network",
            "Newspaper kiosk",
            "Nightclub",
            "Outdoor Pool",
            "Oven",
            "Parasols",
            "Pedal Boating",
            "Playground",
            "Pool bar",
            "Pub(s)",
            "Radio",
            "Restaurant(s)",
            "Restaurant(s) with air conditioning",
            "Restaurant(s) with high-chairs",
            "Restaurant(s) with non-smoking area",
            "Restaurant(s) with smoking area",
            "Room Service",
            "Safe",
            "Sailing",
            "Saltwater Pool",
            "Satellite/cable TV",
            "Sauna",
            "Several Pools",
            "Shops",
            "Shower",
            "Small supermarket",
            "Special spa package",
            "Speed Boating",
            "Squash",
            "Steam bath",
            "Stereo",
            "Sun loungers",
            "Sun terrace",
            "Surfing",
            "Swimming Pool",
            "Table Tennis",
            "Tanning Studio/Solarium",
            "Tea/coffee maker",
            "Tennis",
            "Theatre",
            "Tiling",
            "TV",
            "TV Room",
            "Washing Machine",
            "Water Skiing",
            "Wheelchair access",
            "Windsurfing",
            "WLAN access",
        ]
    ],
    'languages' => [
        [
            'code' => 'us',
            'title' => 'English'
        ],
        [
            'code' => 'es',
            'title' => 'Spanish'
        ],
        [
            'code' => 'hr',
            'title' => 'Croatian'
        ],
        [
            'code' => 'sk',
            'title' => 'Slovak'
        ],
        [
            'code' => 'bg',
            'title' => 'Bulgarian'
        ],
        [
            'code' => 'pl',
            'title' => 'Polish'
        ],
        [
            'code' => 'ru',
            'title' => 'Russian'
        ],
        [
            'code' => 'ua',
            'title' => 'Ukrainian'
        ],
        [
            'code' => 'fr',
            'title' => 'French'
        ],
        [
            'code' => 'it',
            'title' => 'Italian'
        ],
        [
            'code' => 'lv',
            'title' => 'Latvian'
        ],
        [
            'code' => 'ro',
            'title' => 'Romanian'
        ],
        [
            'code' => 'cz',
            'title' => 'Czech'
        ],
        [
            'code' => 'hu',
            'title' => 'Hungarian'
        ],
        [
            'code' => 'de',
            'title' => 'German'
        ],
        [
            'code' => 'si',
            'title' => 'Slovenian'
        ],
        [
            'code' => 'tr',
            'title' => 'Turkish'
        ],
        [
            'code' => 'rs',
            'title' => 'Serbian'
        ],
        [
            'code' => 'il',
            'title' => 'Hebrew'
        ],
        [
            'code' => 'pt',
            'title' => 'Portuguese'
        ],
        [
            'code' => 'zh',
            'title' => 'Chinese'
        ],
        [
            'code' => 'kr',
            'title' => 'Korean'
        ],
        [
            'code' => 'br',
            'title' => 'Portuguese (Brazil)'
        ],
    ],
    'stars' => [
        [
            # 1 star
            'code' => 1,
            'matched' => HotelStars::ONE->value
        ],
        [
            # 1.5 stars
            'code' => 2,
            'matched' => null,
        ],
        [
            # 2 stars
            'code' => 3,
            'matched' => HotelStars::TWO->value
        ],
        [
            # 2.5 stars
            'code' => 4,
            'matched' => null,
        ],
        [
            # 3 stars
            'code' => 5,
            'matched' => HotelStars::THREE->value
        ],
        [
            # 3.5 stars
            'code' => 6,
            'matched' => null
        ],
        [
            # 4 stars
            'code' => 7,
            'matched' => HotelStars::FOUR->value
        ],
        [
            # 4.5 stars
            'code' => 8,
            'matched' => null
        ],
        [
            # 5 stars
            'code' => 9,
            'matched' => HotelStars::FIVE->value
        ],
        [
            # 5.5 stars
            'code' => 10,
            'matched' => null
        ],
//        [
//            # 6 stars
//            'code' => 11,
//            'matched' => HotelStars::SIX->value
//        ],
    ]
];
