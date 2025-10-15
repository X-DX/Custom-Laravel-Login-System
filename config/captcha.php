<?php

return [
    'disable' => env('CAPTCHA_DISABLE', false),
    'default' => [
        'length' => 6,  // 6 chars = still secure but readable
        'width' => 200, // more horizontal space
        'height' => 50, // taller for better visibility
        'quality' => 100, // high image quality
        'math' => false,
        'characters'=> '2346789ABCDEFGHJKLMNPQRTUXYZ', // exclude confusing chars like 0, O, I, l
        'sensitive' => true,
        'expire' => 120,    // give user more time
        'encrypt' => false,
        'bgColor' => '#ffffff',       // pure white background
        'fontColors' => ['#000000'],  // solid black text
        'lines' => 2,                 // minimal lines for clarity
        'contrast' => 0,              // neutral contrast
        'bgImage' => false,           // no background noise
    ],
    'math' => [
        'length' => 9,
        'width' => 120,
        'height' => 36,
        'quality' => 90,
        'math' => true,
    ],

    'flat' => [
        'length' => 6,
        'width' => 160,
        'height' => 46,
        'quality' => 90,
        'lines' => 6,
        'bgImage' => false,
        'bgColor' => '#ecf2f4',
        'fontColors' => ['#2c3e50', '#c0392b', '#16a085', '#c0392b', '#8e44ad', '#303f9f', '#f57c00', '#795548'],
        'contrast' => -1,
    ],
    'mini' => [
        'length' => 3,
        'width' => 60,
        'height' => 32,
    ],
    'inverse' => [
        'length' => 5,
        'width' => 120,
        'height' => 36,
        'quality' => 90,
        'sensitive' => true,
        'angle' => 12,
        'sharpen' => 10,
        'blur' => 2,
        'invert' => true,
        'contrast' => -5,
    ]
];
