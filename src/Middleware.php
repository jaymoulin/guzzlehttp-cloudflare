<?php

namespace GuzzleCloudflare;

use CloudflareBypass\RequestMethod\Stream;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use \CloudflareBypass\CFBypasser;

class Middleware
{
    /** @var string USER_AGENT */
    const USER_AGENT = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_1) AppleWebKit/537.36 ' .
    '(KHTML, like Gecko) Chrome/70.0.3538.110 Safari/537.36';


    /** @var callable $cNextHandler */
    private $cNextHandler;

    /**
     * Middleware constructor.
     * @param callable $cNextHandler
     */
    public function __construct(callable $cNextHandler)
    {
        $this->cNextHandler = $cNextHandler;
    }


    public static function create(): \Closure
    {
        return function ($cHandler) {
            return new static($cHandler);
        };
    }

    /**
     * @param \Psr\Http\Message\RequestInterface $oRequest
     * @param array $aOptions
     *
     *
     * @return \Psr\Http\Message\RequestInterface
     */
    public function __invoke(RequestInterface $oRequest, array $aOptions = [])
    {
        $cNext = $this->cNextHandler;

        return $cNext($oRequest, $aOptions)
            ->then(
                function (ResponseInterface $oResponse) use ($oRequest, $aOptions) {
                    return $this->checkResponse($oRequest, $oResponse, $aOptions);
                }
            );
    }

    /**
     * @param \Psr\Http\Message\RequestInterface $oRequest
     * @param \Psr\Http\Message\ResponseInterface $oResponse
     * @param array $aOptions
     *
     * @return \Psr\Http\Message\RequestInterface|\Psr\Http\Message\ResponseInterface
     * @throws \Exception
     */
    protected function checkResponse(RequestInterface $oRequest, ResponseInterface $oResponse, array $aOptions = [])
    {
        return !$this->shouldHack($oResponse) ? $oResponse : $this($this->hackRequest($oRequest), $aOptions);
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $oResponse
     *
     * @return bool
     */
    protected function shouldHack(ResponseInterface $oResponse)
    {
        return $oResponse->getStatusCode() === 503 &&
            strpos($oResponse->getHeaderLine('Server'), 'cloudflare') !== false;
    }

    /**
     * @param \Psr\Http\Message\RequestInterface $oRequest
     *
     * @return \Psr\Http\Message\RequestInterface
     * @throws \Exception
     */
    protected function hackRequest(RequestInterface $oRequest): RequestInterface
    {
        $sUrl = $oRequest->getUri();
        $aInfo = parse_url($sUrl);
        $sDomain = $aInfo['host'];
        $aOpts = [
            'http' => [
                'method' => $oRequest->getMethod(),
                'header' => [
                    'accept: */*', // required
                    'host: ' . $sDomain, // required
                    'user-agent: ' . ($oRequest->getHeader('User-Agent')[0] ?? self::USER_AGENT),
                ]
            ]
        ];
        $oGuzzleBypass = new Stream($sUrl, stream_context_create($aOpts));
        // nobody should use static functions, this is madness
        (new CFBypasser)->exec($oGuzzleBypass, 'CFStreamContext', ['max_retries' => 5, 'cache' => false]);
        return $oRequest->withAddedHeader('Cookie', $oGuzzleBypass->getHeader("cookie"));
    }
}
