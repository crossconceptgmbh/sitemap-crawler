<?php

namespace Crossconcept\SitemapCrawler;

use Symfony\Component\Console\Application;

class ConsoleApplication extends Application
{
    public function __construct()
    {
        error_reporting(-1);

        parent::__construct('Sitemap Crawler', '1.0.0');

        $this->add(new SitemapCommand());
    }

    public function getLongVersion()
    {
        return parent::getLongVersion().' by <comment>crossconcept</comment>';
    }
}
