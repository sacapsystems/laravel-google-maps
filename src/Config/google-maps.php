<?php

return [
    'base_url' => [
        'search' => env('GOOGLE_MAPS_SEARCH_URL', 'https://maps.googleapis.com/maps/api/place/textsearch/json'),
        'details' => env('GOOGLE_MAPS_DETAILS_URL', 'https://maps.googleapis.com/maps/api/place/details/json'),
    ],
    'api_key' => env('GOOGLE_MAPS_API_KEY'),
];
