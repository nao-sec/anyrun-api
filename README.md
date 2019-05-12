# ANY.RUN API Wrapper for PHP
Official document is [https://any.run/api-documentation/](https://any.run/api-documentation/)

## Requirement
- PHP (>= 7.0)
  - PHP-cURL
- Composer

## Setup
```
$ composer require nao-sec/anyrun-api
```

## Usage
```php
require_once 'vendor/autoload.php';
```

```php
// API Key
$anyrun = new ANYRUN\Client('API Key');

// Basic Auth
$anyrun = new ANYRUN\Client('Username', 'Password');
```

### Get history
```php
print_r($anyrun->get_history());
```

### Get report
```php
print_r($anyrun->get_report('AAAAAAAA-BBBB-CCCC-DDDD-EEEEEEEEEEEE'));
```

### Run new analysis
```php
// // file
print_r($anyrun->post_analysis('aaa.bin', ['obj_type' => 'file']));

// // url
print_r($anyrun->post_analysis("https://example.com", ['obj_type' => 'url']));

// // download url
print_r($anyrun->post_analysis("http://127.0.0.1/bbb.exe", ['obj_type' => 'download']));
```

### Request aavairable environment
```php
print_r($anyrun->get_env());
```

### Request user's limits
```php
print_r($anyrun->get_limits());
```

## LICENSE
This library is open-sourced software licensed under the [MIT License](LICENSE)
