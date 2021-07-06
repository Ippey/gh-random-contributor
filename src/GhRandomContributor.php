<?php

namespace Ippey\GhRandomContributor;

use Symfony\Component\HttpClient\HttpClient;

class GhRandomContributor
{
    /**
     * @return GhRandomContributorFetcher
     */
    public static function createFetcher(): GhRandomContributorFetcher
    {
        $httpClient = HttpClient::create();
        return new GhRandomContributorFetcher($httpClient);
    }
}
