![logo](logo.png)

Guzzle Cloudflare Bypass
========================

[![latest release](https://img.shields.io/github/release/jaymoulin/guzzlehttp-cloudflare.svg "latest release")](http://github.com/jaymoulin/guzzlehttp-cloudflare/releases)
[![Bitcoin donation](https://github.com/jaymoulin/jaymoulin.github.io/raw/master/btc.png "Bitcoin donation")](https://m.freewallet.org/id/374ad82e/btc)
[![Litecoin donation](https://github.com/jaymoulin/jaymoulin.github.io/raw/master/ltc.png "Litecoin donation")](https://m.freewallet.org/id/374ad82e/ltc)
[![PayPal donation](https://github.com/jaymoulin/jaymoulin.github.io/raw/master/ppl.png "PayPal donation")](https://www.paypal.me/jaymoulin)
[![Buy me a coffee](https://www.buymeacoffee.com/assets/img/custom_images/orange_img.png "Buy me a coffee")](https://www.buymeacoffee.com/3Yu8ajd7W)


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
$aOptions = [
    'cache' => new \CloudflareBypass\Storage($sPathToYourCacheFolder),
]; // Example for cache, this is completely optional, with $sPathToYourCacheFolder a string to your cache folder
/** @var \GuzzleHttp\HandlerStack $oHandler */
$oHandler = $oClient->getConfig('handler');
$oHandler->push(\GuzzleCloudflare\Middleware::create($aOptions)); //2. ???

echo (string)$oClient->request('GET', $sUrl)->getBody(); //3. Profit!!
```
