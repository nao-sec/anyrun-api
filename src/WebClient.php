<?php

namespace ANYRUN;

use GuzzleHttp\Client;

class WebClient
{
    /**
     * @access public
     *
     * @param string $url
     * @param string $authorization
     *
     * @return string $response
     */
    public static function get(string $url, string $authorization = null): string
    {
        $client = new Client(['verify' => false]);
        $response = $client->request(
            'GET',
            $url,
            [
                'headers' => [
                    'User-Agent' => 'ANY.RUN API Client',
                    'Authorization' => $authorization,
                ],
            ]
        );

        if ($response->getStatusCode() >= 400) {
            throw new \Exception('WebRequest Error');
        }

        $response = $response->getBody() . '';
        return $response;
    }

    /**
     * @access public
     *
     * @param string $url
     * @param array $data
     * @param string $authorization
     *
     * @return string $response
     */
    public static function post(string $url, array $data = null, string $authorization = null): string
    {
        $client = new Client(['verify' => false]);
        $response = $client->request(
            'POST',
            $url,
            [
                'headers' => [
                    'User-Agent' => 'ANY.RUN API Client',
                    'Authorization' => $authorization,
                ],
                'form_params' => $data,
            ]
        );

        if ($response->getStatusCode() >= 400) {
            throw new \Exception('WebRequest Error');
        }

        $response = $response->getBody() . '';
        return $response;
    }

    /**
     * @access public
     *
     * @param string $url
     * @param string $file_path
     * @param array $options
     * @param string $authorization
     *
     * @return string $response
     */
    public static function post_file(string $url, string $file_path, array $options, string $authorization): string
    {
        $client = new Client(['verify' => false]);
        $response = $client->request(
            'POST',
            $url,
            [
                'headers' => [
                    'User-Agent' => 'ANY.RUN API Client',
                    'Authorization' => $authorization,
                ],
                'multipart' => [
                    [
                        'name' => 'file',
                        'contents' => fopen($file_path, 'r'),
                    ],
                ],
                'query' => $options,
            ]
        );

        if ($response->getStatusCode() >= 400) {
            throw new \Exception('WebRequest Error');
        }

        $response = $response->getBody() . '';
        return $response;
    }
}
