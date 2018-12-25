![logo](logo.png)

Guzzle Cloudflare Bypass
========================

Bypass Cloudflare DDoS protection - Please use it carefully

This package is based on [KyranRana's cloudflare-bypass](https://github.com/KyranRana/cloudflare-bypass).

Installation
------------
Using `composer`

```bash 
composer require jaymoulin/guzzlehttp-cloudflare
```

Usage
-----

```php
$sUrl = 'https://thebot.net/';
$oClient = new \GuzzleHttp\Client([
    'cookies' => new \GuzzleHttp\Cookie\FileCookieJar(tempnam('/tmp', __CLASS__)),
    'headers' => ['Referer' => $sUrl],
]); // 1. Create Guzzle instance
/** @var \GuzzleHttp\HandlerStack $oHandler */
$oHandler = $oClient->getConfig('handler');
$oHandler->push(\GuzzleCloudflare\Middleware::create()); //2. ???

echo (string)$oClient->request('GET', $sUrl)->getBody(); //3. Profit!!
```
