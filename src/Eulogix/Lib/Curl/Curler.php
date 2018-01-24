<?php
/*
 * This file is part of the Eulogix\Lib package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eulogix\Lib\Curl;

use Eulogix\Lib\Cache\CacherInterface;
use Eulogix\Lib\HTTP\HTTPUtils;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class Curler
{
    /**
     * @var string
     */
    protected $userAgent, $socks5proxy, $cookieContent, $cookieFile;

    /**
     * @var array
     */
    protected $requestHeaders;

    /**
     * @var CacherInterface
     */
    protected $cacher;

    /**
     * @var int
     */
    protected $fetchRetries = 10, $fetchDelaySecs = 2;

    /**
     * @var resource
     */
    protected $curl;

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        if(!extension_loaded('http'))
            throw new \Exception("Extension pecl_http is needed for Curler to work!");

        $this->setRequestHeaders([
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Language: it,en;q=0.8,en-us;q=0.6,zh;q=0.4,vi;q=0.2',
            'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7',
            'Keep-Alive: 300',
            'Connection: keep-alive',
            'Cache-Control: max-age=0'
        ]);
    }

    /**
     * @return CacherInterface
     */
    public function getCacher()
    {
        return $this->cacher;
    }

    /**
     * @param CacherInterface $cacher
     * @return $this
     */
    public function setCacher($cacher)
    {
        $this->cacher = $cacher;
        return $this;
    }

    /**
     * @return array
     */
    public function getRequestHeaders()
    {
        return $this->requestHeaders;
    }

    /**
     * @param array $requestHeaders
     * @return $this
     */
    public function setRequestHeaders($requestHeaders)
    {
        $this->requestHeaders = $requestHeaders;
        return $this;
    }

    /**
     * @return string
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * @param string $userAgent
     * @return $this
     */
    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;

        return $this;
    }

    /**
     * @return string
     */
    public function getSocks5proxy()
    {
        return $this->socks5proxy;
    }

    /**
     * @param string $socks5proxy
     * @return $this
     */
    public function setSocks5proxy($socks5proxy)
    {
        $this->socks5proxy = $socks5proxy;
        return $this;
    }

    /**
     * @return string
     */
    public function getCookieContent()
    {
        return $this->cookieContent;
    }

    /**
     * @param string $cookieContent
     * @return $this
     */
    public function setCookieContent($cookieContent)
    {
        $this->cookieContent = $cookieContent;
        return $this;
    }

    /**
     * @return string
     */
    public function getCookieFile()
    {
        return $this->cookieFile;
    }

    /**
     * @param string $cookieFile
     * @return $this
     */
    public function setCookieFile($cookieFile)
    {
        $this->cookieFile = $cookieFile;
        return $this;
    }

    /**
     * @return int
     */
    public function getFetchRetries()
    {
        return $this->fetchRetries;
    }

    /**
     * @param int $fetchRetries
     * @return $this
     */
    public function setFetchRetries($fetchRetries)
    {
        $this->fetchRetries = $fetchRetries;
        return $this;
    }

    /**
     * @return int
     */
    public function getFetchDelaySecs()
    {
        return $this->fetchDelaySecs;
    }

    /**
     * @param int $fetchDelaySecs
     * @return $this
     */
    public function setFetchDelaySecs($fetchDelaySecs)
    {
        $this->fetchDelaySecs = $fetchDelaySecs;
        return $this;
    }

    /**
     * @return resource
     */
    protected function getNewCurl()
    {
        $ch = curl_init();

        if($this->getCookieContent())
            curl_setopt($ch, CURLOPT_COOKIE, $this->getCookieContent());

        curl_setopt ($ch, CURLOPT_USERAGENT, $this->getUserAgent());
        curl_setopt ($ch, CURLOPT_HTTPHEADER, $this->getRequestHeaders());

        if($this->getSocks5proxy()) {
            curl_setopt($ch, CURLOPT_PROXY, $this->getSocks5proxy());
            curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
        }

        //curl_setopt ($ch, CURLOPT_ENCODING, 'gzip,deflate');
        curl_setopt ($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_TIMEOUT_MS, 30000);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS , 10000);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT , 10);

        if($this->getCookieFile()) {
            curl_setopt($ch, CURLOPT_COOKIEJAR, $this->getCookieFile()); //stores cookies from server
            curl_setopt($ch, CURLOPT_COOKIEFILE,  $this->getCookieFile()); //sends then back in requests
        }

        return $ch;
    }

    public function renewCurl() {
        if($this->curl)
            curl_close($this->curl);
        $this->curl = $this->getNewCurl();
    }

    /**
     * @return resource
     */
    public function getCurl() {
        if(!$this->curl)
            $this->renewCurl();
        return $this->curl;
    }

    public function clearCookies() {
        unlink($this->getCookieFile());
        $this->renewCurl();
    }

    /**
     * @param string $url
     * @param string|array $postData
     * @return bool|\httpMessage
     */
    function fetchPage($url, $postData = null) {

        $curl = $this->getCurl();

        $realPostData = is_array($postData) ? HTTPUtils::getPostDataFromAssociativeArrays($postData) : $postData;

        curl_setopt ($curl, CURLOPT_URL,$url);

        curl_setopt ($curl, CURLINFO_HEADER_OUT ,1);
        curl_setopt ($curl, CURLOPT_VERBOSE, 1);
        curl_setopt ($curl, CURLOPT_HEADER ,1);

        if($realPostData) {
            curl_setopt ($curl, CURLOPT_POST, 1);
            curl_setopt ($curl, CURLOPT_POSTFIELDS, $realPostData);
        } else {
            curl_setopt ($curl, CURLOPT_POSTFIELDS, null);
            curl_setopt ($curl, CURLOPT_POST, 0);
        }

        for($i = 1; $i < $this->getFetchRetries(); $i++) {

            $response = curl_exec ($this->getCurl());

            if($response !== false) {
                try {
                    return new \httpMessage($response);
                } catch(\HttpEncodingException $e) {
                    $response = str_replace('Transfer-Encoding: chunked','', $response);
                    return new \httpMessage($response);
                }
            }

            sleep($this->getFetchDelaySecs());
        }

        return false;
    }

}