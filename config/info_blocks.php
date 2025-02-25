<?php

return [
    'format' => [
        "title" => ["type" => "text"],
        "button" => [
            "type" => "array",
            "data" => [
                "url" => ["type" => "url"],
                "text" => ["type" => "text"]
            ]
        ],
        'link' => [
            'type' => 'array',
            'data' => [
                'url' => ['type' => 'url'],
                'text' => ['type' => 'text']
            ]
        ],
        "header" => ["type" => "text"],
        "paragraph" => ["type" => "long_text"],
    ]
];
