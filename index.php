<?php

Kirby::plugin('mirthe/instagram-import', [
    'options' => [
        'token' => option('instagram.token')
    ],

    'routes' => [
        [
            'pattern' => 'insta/getlatest',
            'action'  => function () {
                include 'getlatest.php';
            }
        ],

        [
            'pattern' => 'insta/bulkimport',
            'action'  => function () {
  
              $exportdir = __DIR__ . '/temp/';
              $insta_json = file_get_contents(__DIR__ . '/posts_1.json');
              $decoded_json = json_decode($insta_json, true);
              
              include_once 'bulkimport.php';
            }
        ]
    ]
    
]);
