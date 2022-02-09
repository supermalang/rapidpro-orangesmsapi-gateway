<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

/** Service utility that has some useful functions */
class OrangeTokenHelper
{
    public function __construct(HttpClientInterface $client)
    {
        $this->httpclient = $client;
    }

    // get Basic details

    //get Token
    public function getNewToken(string $clientId, string $clientSecret, string $baseUrl): ?iterable
    {
        $base64Credentials = base64_encode(sprintf('%s:%s', $clientId, $clientSecret));
        $authheader = sprintf('Authorization: Basic %s', $base64Credentials);

        $headers = ['Content-Type: application/x-www-form-urlencoded', $authheader];

        $body = ['grant_type' => 'client_credentials'];

        $response = $this->httpclient->request('POST', $baseUrl, ['headers' => $headers, 'body' => $body]);

        if (200 == $response->getStatusCode()) {
            return $response->toArray();
        }

        return null;
        // TODO: Raise exception if no token is received
    }

    // Update Token into DB
}
