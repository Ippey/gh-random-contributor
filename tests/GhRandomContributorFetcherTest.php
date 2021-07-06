<?php

namespace Ippey\GhRandomContributor\Tests;

use Ippey\GhRandomContributor\GhContributor;
use Ippey\GhRandomContributor\GhRandomContributorFetcher;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GhRandomContributorFetcherTest extends TestCase
{
    public function testParseLink()
    {
        $link = '<https://api.github.com/repositories/370640005/contributors?per_page=1&page=2>; rel="next", <https://api.github.com/repositories/370640005/contributors?per_page=1&page=4>; rel="last"';
        $httpClient = $this->createMock(HttpClientInterface::class);
        $fetcher = new GhRandomContributorFetcher($httpClient);
        $links = $fetcher->parseLink($link);
        $this->assertEquals([
            'next' => [
                'url' => 'https://api.github.com/repositories/370640005/contributors?per_page=1&page=2',
                'page' => 2,
            ],
            'last' => [
                'url' => 'https://api.github.com/repositories/370640005/contributors?per_page=1&page=4',
                'page' => 4,
            ],
        ], $links);
    }

    /**
     * @throws \Ippey\GhRandomContributor\GhRandomContributorFetchException
     */
    public function testGet()
    {
        $httpClient = new MockHttpClient([
            new MockResponse('', ['http_code' => 200, 'response_headers' => ['link' => ['<https://api.github.com/repositories/370640005/contributors?per_page=1&page=2>; rel="next", <https://api.github.com/repositories/370640005/contributors?per_page=1&page=2>; rel="last"']]]),
            new MockResponse('[
  {
    "login": "Ippey",
    "id": 471948,
    "node_id": "MDQ6VXNlcjQ3MTk0OA==",
    "avatar_url": "https://avatars.githubusercontent.com/u/471948?v=4",
    "gravatar_id": "",
    "url": "https://api.github.com/users/Ippey",
    "html_url": "https://github.com/Ippey",
    "followers_url": "https://api.github.com/users/Ippey/followers",
    "following_url": "https://api.github.com/users/Ippey/following{/other_user}",
    "gists_url": "https://api.github.com/users/Ippey/gists{/gist_id}",
    "starred_url": "https://api.github.com/users/Ippey/starred{/owner}{/repo}",
    "subscriptions_url": "https://api.github.com/users/Ippey/subscriptions",
    "organizations_url": "https://api.github.com/users/Ippey/orgs",
    "repos_url": "https://api.github.com/users/Ippey/repos",
    "events_url": "https://api.github.com/users/Ippey/events{/privacy}",
    "received_events_url": "https://api.github.com/users/Ippey/received_events",
    "type": "User",
    "site_admin": false,
    "contributions": 15
  }
]', ['http_code' => 200])
        ]);
        $fetcher = new GhRandomContributorFetcher($httpClient);
        $contributor = $fetcher->get('ippey', 'rr-symfony');
        $expected = new GhContributor();
        $expected
            ->setId(471948)
            ->setUsername('Ippey')
            ->setAvatarUrl('https://avatars.githubusercontent.com/u/471948?v=4')
            ->setThumbnailUrl('https://avatars.githubusercontent.com/u/471948?v=4&s=4')
            ->setUrl('https://github.com/Ippey')
            ->setContributions(15)
            ;
        $this->assertInstanceOf(GhContributor::class, $contributor);
        $this->assertEquals($expected->getId(), $contributor->getId());
        $this->assertEquals($expected->getUsername(), $contributor->getUsername());
    }
}
