<?php

namespace Database\Seeders;

use App\Enums\PageType;
use App\Models\Page;
use Illuminate\Database\Seeder;

class CommonPagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * @return void
     */
    public function run(): void
    {
        $commonPages = $this->getData();

        foreach ($commonPages as $pageData) {

            $page = Page::create([
                'slug' => $pageData['slug'],
                'type' => $pageData['type'],
                'heading_title_l' => $pageData['heading_title_l'] ?? null,
                'meta_title_l' => $pageData['meta_title_l'] ?? null,
                'meta_description_l' => $pageData['meta_description_l'] ?? null,
                'content_l' => $pageData['content_l'] ?? null
            ]);

            $infoBlocks = $pageData['info_blocks'] ?? null;
            if (empty($infoBlocks)) {
                continue;
            }

            foreach ($infoBlocks as $infoBlock) {
                $page->infoBlocks()->create([
                    'alias' => $infoBlock['alias'],
                    'content_l' => $infoBlock['content_l']
                ]);
            }
        }
    }

    private function getData(): array
    {
        return [
            [
                'slug' => 'main',
                'type' => PageType::HTML,
                'heading_title_l' => [
                    'en' => 'Ventourio'
                ],
                'meta_title_l' => [
                    'en' => 'Ventourio'
                ],
                'meta_description_l' => [
                    'en' => 'Ventourio description'
                ],
                'content_l' => [
                    'en' => "<h1>Особенности отдыха в Восточной Азии</h1>
<p>Восточная Азия является крупнейшей частью огромного континента, на ее территории располагается множество интереснейших стран (Япония, Южная и Северная Кореи, Китай, Тайвань и многие другие). Эта часть континента располагается в умеренных, субтропических и тропических климатических зонах, поэтому туристам обеспечен самый разнообразный отдых.</p>
<p>Для любителей городского отдыха открывают свои двери отели Пекина, Шанхая, Гонконга и других бурно развивающихся и растущих городов, которые предоставляют своим гостям возможность окунуться в незабываемый мир местной экзотики, ознакомиться с памятниками культуры, гармонично окруженными современнейшими небоскребами из стекла и металла.</p>
<p>Для любителей городского отдыха открывают свои двери отели Пекина, Шанхая, Гонконга и других бурно развивающихся и растущих городов, которые предоставляют своим гостям возможность окунуться в незабываемый мир местной экзотики, ознакомиться с памятниками культуры, гармонично окруженными современнейшими небоскребами из стекла и металла. Для любителей городского отдыха открывают свои двери отели Пекина, Шанхая, Гонконга и других бурно развивающихся и растущих городов, которые предоставляют своим гостям возможность окунуться в незабываемый мир местной экзотики, ознакомиться с памятниками культуры, гармонично окруженными современнейшими небоскребами из стекла и металла. Для любителей городского отдыха открывают свои двери отели Пекина, Шанхая, Гонконга и других бурно развивающихся и растущих городов, которые предоставляют своим гостям возможность окунуться в незабываемый мир местной экзотики, ознакомиться с памятниками культуры, гармонично окруженными современнейшими небоскребами из стекла и металла.</p>",
                    'ru' => "<h1>Особенности отдыха в Восточной Азии</h1>
<p>Восточная Азия является крупнейшей частью огромного континента, на ее территории располагается множество интереснейших стран (Япония, Южная и Северная Кореи, Китай, Тайвань и многие другие). Эта часть континента располагается в умеренных, субтропических и тропических климатических зонах, поэтому туристам обеспечен самый разнообразный отдых.</p>
<p>Для любителей городского отдыха открывают свои двери отели Пекина, Шанхая, Гонконга и других бурно развивающихся и растущих городов, которые предоставляют своим гостям возможность окунуться в незабываемый мир местной экзотики, ознакомиться с памятниками культуры, гармонично окруженными современнейшими небоскребами из стекла и металла.</p>
<p>Для любителей городского отдыха открывают свои двери отели Пекина, Шанхая, Гонконга и других бурно развивающихся и растущих городов, которые предоставляют своим гостям возможность окунуться в незабываемый мир местной экзотики, ознакомиться с памятниками культуры, гармонично окруженными современнейшими небоскребами из стекла и металла. Для любителей городского отдыха открывают свои двери отели Пекина, Шанхая, Гонконга и других бурно развивающихся и растущих городов, которые предоставляют своим гостям возможность окунуться в незабываемый мир местной экзотики, ознакомиться с памятниками культуры, гармонично окруженными современнейшими небоскребами из стекла и металла. Для любителей городского отдыха открывают свои двери отели Пекина, Шанхая, Гонконга и других бурно развивающихся и растущих городов, которые предоставляют своим гостям возможность окунуться в незабываемый мир местной экзотики, ознакомиться с памятниками культуры, гармонично окруженными современнейшими небоскребами из стекла и металла.</p>"
                ],
                'info_blocks' => [
                    [
                        'alias' => 'info_top',
                        'content_l' => [
                            'en' => [
                                'title' => 'Save 15% with Late Escape Deals',
                                'paragraph' => 'There’s still time to tick one more destination off your wishlist.',
                                'button' => [
                                    'text' => 'Explore deals',
                                    'url' => '#'
                                ],
                            ],
                            'ru' => [
                                'title' => 'Save 15% with Late Escape Deals',
                                'paragraph' => 'There’s still time to tick one more destination off your wishlist.',
                                'button' => [
                                    'text' => 'Explore deals',
                                    'url' => '#'
                                ],
                            ]
                        ]
                    ],
                    [
                        'alias' => 'info',
                        'content_l' => [
                            'en' => [
                                'title' => 'Covid info.',
                                'paragraph' => 'Rules you should khow before trip.',
                                'button' => [
                                    'text' => 'Learn more',
                                    'url' => '#'
                                ],
                            ],
                            'ru' => [
                                'title' => 'Covid info.',
                                'paragraph' => 'Rules you should khow before trip.',
                                'button' => [
                                    'text' => 'Learn more',
                                    'url' => '#'
                                ],
                            ]
                        ]
                    ],
                    [
                        'alias' => 'advantages_1',
                        'content_l' => [
                            'en' => [
                                'title' => 'Why Ventourio?',
                                'header_1' => 'Expertise and Experience',
                                'paragraph_1' => 'Ventourio OU is a licensed and experienced touristic operator that specializes in providing a wide range of touristic services, including individual hotel booking, tickets, visa services, transfers, educational tourism, medical tourism, and more. With their knowledge of the local market and expertise in the tourism industry, they can provide customized and high-quality services to their customers.',
                                'header_2' => 'Convenience and Transparency',
                                'paragraph_2' => 'Ventourio OU offers a convenient and transparent booking process for their customers, with a user-friendly online platform where customers can easily make prepayments and order services in their account. Their prices are transparent, and they hold customers funds until the touristic services are provided, ensuring peace of mind for customers.',
                                'header_3' => 'Customer Support and Satisfaction',
                                'paragraph_3' => ' Ventourio OU values customer satisfaction and provides excellent customer support throughout the booking and touristic service process. They have a dedicated support team that is available to assist customers with any questions or concerns, and they strive to ensure that each customer has a positive and memorable experience with their touristic services.',
                            ],
                            'ru' => [
                                'title' => 'Why Ventourio?',
                                'header_1' => 'Visa',
                                'paragraph_1' => 'Over 400 pages of practical digital marketing advice with downloadable worksheets and video guides.',
                                'header_2' => 'Visa',
                                'paragraph_2' => 'Over 400 pages of practical digital marketing advice with downloadable worksheets and video guides.',
                                'header_3' => 'Visa',
                                'paragraph_3' => 'Over 400 pages of practical digital marketing advice with downloadable worksheets and video guides.',
                            ]
                        ]
                    ],
                    [
                        'alias' => 'advantages_2',
                        'content_l' => [
                            'en' => [
                                'header_1' => '11 000 trips',
                                'paragraph_1' => 'Over 400 pages of practical digital marketing advice.',
                                'header_2' => '200 000 users',
                                'paragraph_2' => 'Over 400 pages of practical digital marketing advice.',
                            ],
                            'ru' => [
                                'header_1' => '11 000 trips',
                                'paragraph_1' => 'Over 400 pages of practical digital marketing advice.',
                                'header_2' => '200 000 users',
                                'paragraph_2' => 'Over 400 pages of practical digital marketing advice.',
                            ]
                        ]
                    ],
                    [
                        'alias' => 'guideline',
                        'content_l' => [
                            'en' => [
                                'header' => 'Guideline',
                                'paragraph' => 'Rules you should know before trip.',
                                'button' => [
                                    'text' => 'Explore',
                                    'url' => '#'
                                ]
                            ],
                            'ru' => [
                                'header' => 'Guideline',
                                'paragraph' => 'Rules you should know before trip.',
                                'button' => [
                                    'text' => 'Explore',
                                    'url' => '#'
                                ]
                            ]
                        ]
                    ],
                    [
                        'alias' => 'promo',
                        'content_l' => [
                            'en' => [
                                'title' => 'Certificates and deals',
                                'paragraph' => 'Info about best deals',
                                'header_1' => 'Certificates',
                                'paragraph_1' => 'There’s always time for one more trip. Discover deals on stays between',
                                'button_1' => [
                                    'text' => 'Explore',
                                    'url' => '/profile/certificates'
                                ],
                                'header_2' => 'Save 15%',
                                'paragraph_2' => 'There’s still time to tick one more destination off your wishlist.',
                                'button_2' => [
                                    'text' => 'Explore deals',
                                    'url' => '/book'
                                ],
                                'header_3' => 'Hot deal',
                                'paragraph_3' => 'There’s always time for one more trip. Discover deals on stays between',
                                'button_3' => [
                                    'text' => 'Explore deals',
                                    'url' => '/discount'
                                ]
                            ],
                            'ru' => [
                                'title' => 'Certificates and deals',
                                'paragraph' => 'Info about best deals',
                                'header_1' => 'Certificates',
                                'paragraph_1' => 'There’s always time for one more trip. Discover deals on stays between',
                                'button_1' => [
                                    'text' => 'Explore',
                                    'url' => '/profile/certificates'
                                ],
                                'header_2' => 'Certificates',
                                'paragraph_2' => 'There’s always time for one more trip. Discover deals on stays between',
                                'button_2' => [
                                    'text' => 'Explore deals',
                                    'url' => '/profile/certificates'
                                ],
                                'header_3' => 'Hot deal',
                                'paragraph_3' => 'There’s always time for one more trip. Discover deals on stays between',
                                'button_3' => [
                                    'text' => 'Explore deals',
                                    'url' => '/discount'
                                ]
                            ]
                        ]
                    ],
                    [
                        'alias' => 'banner',
                        'content_l' => [
                            'en' => [
                                'title' => 'Ventourio offers best trip around the world',
                                'paragraph' => 'Over 400 pages of practical digital marketing advice',
                                'button_1' => [
                                    'text' => 'Sign up',
                                    'url' => '#'
                                ]
                            ],
                            'ru' => [
                                'title' => 'Ventourio offers best trip around the world',
                                'paragraph' => 'Over 400 pages of practical digital marketing advice',
                                'button_1' => [
                                    'text' => 'Sign up',
                                    'url' => '#'
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            [
                'slug' => 'contacts',
                'type' => PageType::HTML,
                'heading_title_l' => [
                    'en' => 'Ventourio contacts'
                ],
                'meta_title_l' => [
                    'en' => 'Ventourio contacts'
                ],
                'meta_description_l' => [
                    'en' => 'Ventourio description'
                ],
                'info_blocks' => [
                    [
                        'alias' => 'phones',
                        'content_l' => [
                            'en' => [
                                [
                                    'title' => 'International',
                                    'url' => 'tel:123123',
                                    'text' => '+372 5604 3515',
                                ],
                            ]
                        ]
                    ],
                    [
                        'alias' => 'address',
                        'content_l' => [
                            'en' => [
                                'header' => 'Address',
                                'paragraph_1' => "ScanTrip Inc.",
                                'paragraph_2' => "10880 Wilshire Blvd., Suite 1101 Los Angeles, California, 90024 USA",
                            ]
                        ]
                    ],
                ]
            ],
            [
                'slug' => 'about',
                'type' => PageType::HTML,
                'heading_title_l' => [
                    'en' => 'About Ventourio'
                ],
                'meta_title_l' => [
                    'en' => 'About Ventourio'
                ],
                'meta_description_l' => [
                    'en' => 'Ventourio description'
                ],
                'content_l' => [
                    'en' => "<p>Touristic operator <strong>VENTOURIO OU</strong>- is a customer oriented and driven organisation, consistently striving to exceed its customers&rsquo; expectations and provide the highest level of services. We have a team of highly qualified and experienced professionals that are up to date with the latest trends in the industry. Our company also have an extensive and competitive portfolio of vacation packages from a wide range of destinations, taking into account the needs and preferences of different types of travellers. Our team able to create customized holiday packages depending on the customers budget, preferences and the time they have at their disposal.</p>\n\n<p>We provide transparent pricing policy, communicating all the details and specifications clearly and upfront, with no hidden fees or costs. Ventourio offer assistance and advice throughout the whole process, from the booking to arrival at the destination.We provide innovative booking system and real time customer support.</p>\n\n<p>Our professionals working in the agency have first-hand knowledge and experience of the destinations they are offering and providing and detailed information about the packages.</p>\n\n<p>We able to help customers and provide valuable insight on the local culture and attractions.</p>\n\n<p>Our organization have an established network of trusted partners in order to offer the best quality services at a competitive price.</p>",
                ]
            ],
            [
                'slug' => 'privacy_policy',
                'type' => PageType::HTML,
                'heading_title_l' => [
                    'en' => 'Privacy Policy'
                ],
                'meta_title_l' => [
                    'en' => 'Privacy Policy'
                ],
                'meta_description_l' => [
                    'en' => 'Privacy Policy'
                ],
                'content_l' => [
                    'en' => "<h1>Privacy Policy</h1>
<p>Founded in 1996 in Amsterdam, Ventourio.com has grown from a small Dutch startup to one of the world’s leading digital travel companies. Part of Booking Holdings Inc. (NASDAQ: BKNG), Ventourio.com’s mission is to make it easier for everyone to experience the world.</p>
<p>By investing in the technology that helps take the friction out of travel, Ventourio.com seamlessly connects millions of travellers with memorable experiences, a range of transport options and incredible places to stay - from homes to hotels and much more. As one of the world’s largest travel marketplaces for both established brands and entrepreneurs of all sizes, Ventourio.com enables properties all over the world to reach a global audience and grow their businesses.</p>
<p>Ventourio.com is available in 43 languages and offers more than 28 million total reported accommodation listings, including over 6.6 million listings alone of homes, apartments and other unique places to stay. No matter where you want to go or what you want to do, Vetourio.com makes it easy and backs it all up with 24/7 customer support.</p>
<h2>More info</h2>
<p>Founded in 1996 in Amsterdam, Ventourio.com has grown from a small Dutch startup to one of the world’s leading digital travel companies. Part of Booking Holdings Inc. (NASDAQ: BKNG), Ventourio.com’s mission is to make it easier for everyone to experience the world.</p>",
                ]
            ]
        ];
    }
}
