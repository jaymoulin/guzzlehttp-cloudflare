<?php

namespace GuzzleCloudflareTests;

use CloudflareBypass\CFBypasser;
use CloudflareBypass\RequestMethod\Stream;

class Middleware extends \PHPUnit\Framework\TestCase
{
    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testWorks()
    {
        $sUrl = 'https://thebot.net/';
        $oClient = new \GuzzleHttp\Client(
            [
                'cookies' => new \GuzzleHttp\Cookie\FileCookieJar(tempnam('/tmp', __CLASS__)),
                'headers' => ['Referer' => $sUrl],
            ]
        );
        /**
         * @var \GuzzleHttp\HandlerStack $oHandler
         */
        $oHandler = $oClient->getConfig('handler');
        $oHandler->push(\GuzzleCloudflare\Middleware::create());

        $oResponse = $oClient->request('GET', $sUrl);

        $this->assertSame(200, $oResponse->getStatusCode());
        $this->assertStringContainsString('This site uses cookies', (string)$oResponse->getBody());
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testWorksLogin()
    {
        $sUrl = 'https://thebot.net';
        $oClient = new \GuzzleHttp\Client(
            [
                'cookies' => new \GuzzleHttp\Cookie\FileCookieJar(tempnam('/tmp', __CLASS__)),
                'headers' => ['Referer' => $sUrl],
            ]
        );
        /**
         * @var \GuzzleHttp\HandlerStack $oHandler
         */
        $oHandler = $oClient->getConfig('handler');
        $oHandler->push(\GuzzleCloudflare\Middleware::create());

        $oResponse = $oClient->request('GET', $sUrl . '/login');

        $this->assertSame(200, $oResponse->getStatusCode());
        $this->assertStringContainsString('This site uses cookies', (string)$oResponse->getBody());
        if (!preg_match('~name="_xfToken" value="([^"]+)" />~', (string)$oResponse->getBody(), $aMatches)) {
            $this->assertTrue(false, "Unable to get xfToken");
        }
        $aParams = [
            'login' => 'jaymoulin',
            'password' => '123456',
            'remember' => '1',
            '_xfRedirect' => 'https://thebot.net/',
            '_xfToken' => $aMatches[1],
        ];
        $oResponse = $oClient->request('POST', $sUrl . '/login/login', ['form_params' => $aParams]);
        $this->assertStringContainsString('jaymoulin', (string)$oResponse->getBody());
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testThirdVersionCloudflare()
    {
        $sUrl = 'http://www.mangago.me/';
        $oClient = new \GuzzleHttp\Client(
            [
                'cookies' => new \GuzzleHttp\Cookie\FileCookieJar(tempnam('/tmp', __CLASS__)),
                'headers' => ['Referer' => $sUrl],
            ]
        );
        /**
         * @var \GuzzleHttp\HandlerStack $oHandler
         */
        $oHandler = $oClient->getConfig('handler');
        $oHandler->push(\GuzzleCloudflare\Middleware::create());

        $oResponse = $oClient->request('GET', $sUrl);

        $this->assertSame(200, $oResponse->getStatusCode());
        $this->assertStringContainsString('Read Manga Online For Free', (string)$oResponse->getBody());
    }
}
