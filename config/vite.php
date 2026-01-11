<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Vite Build Directory
    |--------------------------------------------------------------------------
    |
    | Laravel uses this value to know where the Vite manifest.json file is
    | located. Since Vite 7 stores manifest.json inside:
    |
    |    public/build/.vite/manifest.json
    |
    | We must set the build path accordingly.
    |
    */

    'build_path' => 'build/.vite',

    /*
    |--------------------------------------------------------------------------
    | Development Server
    |--------------------------------------------------------------------------
    */

    'dev_server' => [
        'url' => env('VITE_DEV_SERVER_URL'),
    ],
];
