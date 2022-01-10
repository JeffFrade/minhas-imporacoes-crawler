<?php

namespace App\Services;

use App\Helpers\FileHelper;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\FileCookieJar;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

class HttpClient
{
    /**
     * @var Client
     */
    private $client;

    /**
     * HttpClient constructor.
     */
    public function __construct()
    {
        $this->client = new Client([
            'cookies' => $this->setCookie(),
            'connect_timeout' => 0,
            'timeout' => 0,
            'follow_location' => true
        ]);
    }

    /**
     * @return FileCookieJar
     */
    private function setCookie(): FileCookieJar
    {
        $filename = 'cookies/' . rand() . '_cookie.txt';
        FileHelper::writeArchive('', $filename);
        $cookie = base_path('/storage/app/public/' . $filename);

        return new FileCookieJar($cookie, true);
    }


    /**
     * @param string $method
     * @param string $url
     * @param array $options
     * @return ResponseInterface
     * @throws GuzzleException
     */
    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        return $this->client->request($method, $url, $options);
    }
}
