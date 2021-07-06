<?php

namespace Ippey\GhRandomContributor;

use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GhRandomContributorFetcher
{
    private $url = 'https://api.github.com/repos/%s/%s/contributors?per_page=1';

    /**
     * @var HttpClientInterface
     */
    private $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function get(string $organization, string $repository)
    {
        if (empty($organization) || empty($repository)) {
            throw new GhRandomContributorFetchException();
        }
        $url = sprintf($this->url, $organization, $repository);
        try {
            $response = $this->httpClient->request('GET', $url, []);
            if ($response->getStatusCode() !== 200) {
                throw new TransportException('Can not get contributor.');
            }
            $headers = $response->getHeaders();
            $links = $this->parseLink($headers['link'][0] ?? '');

            $currentPage = 1;
            if (isset($links['last'])) {
                $currentPage = rand(1, $links['last']['page']);
            }

            $response = $this->httpClient->request('GET', $url . '&page=' . $currentPage, []);

            $rows = json_decode((string) $response->getContent(), true);
            if (empty($rows)) {
                return null;
            }

            return GhContributorFactory::createWithArray($rows[0]);
        } catch (ExceptionInterface $e) {
            throw new GhRandomContributorFetchException($e->getMessage(), $e->getCode());
        }
    }

    public function parseLink($link): array
    {
        $results = [];
        preg_match_all('/<([^?]+\?per_page=1&[a-z]+=([\d]+))>;[\s]*rel="([a-z]+)"/', $link, $matches);
        for ($i = 0; $i < count($matches[0]); $i ++) {
            $results[$matches[3][$i]] = [
                'url' => $matches[1][$i],
                'page' => $matches[2][$i],
            ];
        }

        return $results;
    }
}
