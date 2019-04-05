JSON Web Token Builder / Validator
======================

## About

It's for generating and validating Json Web Token ([JWT](https://jwt.io/)) communicating with parties together. Features:

- Simple
- Easy to use
- Dynamic configuration
- Lightweight

## Usage

First of all, you need install some dependencies, please run this command before using.

```bash
composer install
```

Then you can use it now. See an example in a folder named __examples/__.

1. Build a token
```php
(new Leo\JWT\JWT())->buildToken();
```

2. Validate a token
```php
(new Leo\JWT\JWT())->checkValid("<token_string>");
```

## Contributing

You can contribute to this package by discovering bugs and opening issues. 

## Security Vulnerabilities

If you discover a security vulnerability within this project, please send an e-mail to Kiet Tran via [tuankiet1708@gmail.com](mailto:tuankiet1708@gmail.com). All security vulnerabilities will be promptly addressed.

## License

This is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).