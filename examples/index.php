<?php

require_once __DIR__ . '/../vendor/autoload.php';

echo ">>>>>> This is [Application] <<<<<< \n";
$myOwnedConfig = [
    "jwt" => [
        "app_name" => "Application",
        "app_id" => "my_app",
        "secret" => "secret_key_of_my_app",
        "ttl" => 300,
        "parties" => [
            "my_app" => [
                "my_app",
                "http://my-app.local.vn/",
                "secret_key_of_my_app",
                300,
            ],
            "log_app" => [
                "log_app",
                "http://log.local.vn/",
                "secret_key_of_log_app",
                300,
            ],
        ],
    ]
];

$jwt = new Leo\JWT\JWT($myOwnedConfig);

echo "Show the JWT config of application: \n";
var_dump($jwt->info());

echo "\n";

echo "Generate a JWT Token: \n";
// Use this token to communicate with parties
var_dump($token = $jwt->buildToken()); 

echo "\n";

###################

echo ">>>>>> This is [Log App] <<<<<< \n";
$myOwnedConfig = [
    "jwt" => [
        "app_name" => "Log App",
        "app_id" => "log_app",
        "secret" => "secret_key_of_log_app",
        "ttl" => 300,
        "parties" => [
            "my_app" => [
                "my_app",
                "http://my-app.local.vn/",
                "secret_key_of_my_app",
                300,
            ],
            "log_app" => [
                "log_app",
                "http://log.local.vn/",
                "secret_key_of_log_app",
                300,
            ],
        ],
    ]
];

$jwt = new Leo\JWT\JWT($myOwnedConfig);

echo "Show the JWT config of application: \n";
var_dump($jwt->info());

echo "Validate the token from my_app: \n";
// When the application receives a request from other party, you can validate that token is valid or not.
var_dump($jwt->checkValid($token));

echo "\n";