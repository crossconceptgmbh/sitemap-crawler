<?php

namespace Crossconcept\SitemapCrawler;

use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\UriInterface;
use Spatie\Crawler\CrawlProfile;

class CrawlOnlyPages extends CrawlProfile
{
    protected $baseUrl;

    public function __construct($baseUrl)
    {
        if (! $baseUrl instanceof UriInterface) {
            $baseUrl = new Uri($baseUrl);
        }

        $this->baseUrl = $baseUrl;
    }

    public function shouldCrawl(UriInterface $url): bool
    {
        $pathinfo = pathinfo(str_replace($this->baseUrl->getHost(), '', $url));
        if($this->baseUrl->getScheme() == $url->getScheme() &&
            $this->baseUrl->getHost() === $url->getHost() &&
            (empty($pathinfo['extension']) || $pathinfo['extension'] === 'html') &&
            (!empty($pathinfo['filename']) && !strstr($pathinfo['filename'], '?'))) {
            return true;
        } else {
            return false;
        }
    }
}