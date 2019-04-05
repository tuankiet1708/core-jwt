<?php

return [
    'jwt' => [
        /*
        |--------------------------------------------------------------------------
        | Application Name
        |--------------------------------------------------------------------------
        |
        */
        'app_name' => 'Application',

        /*
        |--------------------------------------------------------------------------
        | JWT App ID
        |--------------------------------------------------------------------------
        |
        | Configures the id (jti claim), replicating as a header item
        |
        */
        'app_id' => 'app_id',

        /*
        |--------------------------------------------------------------------------
        | JWT Authentication Secret
        |--------------------------------------------------------------------------
        |
        | Don't forget to set this in your .env file, as it will be used to sign
        | your tokens. A helper command is provided for this:
        | `php artisan jwt:secret -s`
        |
        */
        'secret' => 'secret_key',

        /*
        |--------------------------------------------------------------------------
        | JWT time to live (TTL)
        |--------------------------------------------------------------------------
        |
        | Specify the length of time (in seconds) that the token will be valid for.
        | Defaults to 5 minutes.
        |
        | You can also set this to null, to yield a never expiring token.
        | Some people may want this behaviour for e.g. a mobile app.
        | This is not particularly recommended, so make sure you have appropriate
        | systems in place to revoke the token if necessary.
        |
        */

        'ttl' => 300,

        /*
        |--------------------------------------------------------------------------
        | JWT Authentication Secrets of Parties
        |--------------------------------------------------------------------------
        |
        | Specify the secrets of parties for jwt token validation
        |
        */
        'parties' => [
            // app_id
            'app_id' => [
                // app_id
                "app_id",
                // app_url
                "http://localhost",
                // secret_key
                "secret_key",
                // TTL (seconds)
                300
            ]
        ],
    ]
];