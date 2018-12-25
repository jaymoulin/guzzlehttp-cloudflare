<?php
namespace GuzzleCloudflareTests;

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
        $this->assertContains('This site uses cookies', (string)$oResponse->getBody());
    }
}
