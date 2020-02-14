<?php

namespace Crossconcept\SitemapCrawler;

use GuzzleHttp\RequestOptions;
use Icamys\SitemapGenerator\SitemapGenerator;
use Spatie\Crawler\Crawler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SitemapCommand extends Command
{
    /**
     * Configure Command
     */
    protected function configure() {
        $this->setName('sitemap')
            ->setDescription('Crawl a Website and build a sitemap afterwards')
            ->addArgument(
                'url',
                InputArgument::REQUIRED,
                'The url to check'
            );

    }
    /**
     * Execute Command
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Crawl pages
        $baseUrl = $input->getArgument('url');
        $crawlProfile = new CrawlOnlyPages($baseUrl);

        $output->writeln("Start scanning {$baseUrl}");
        $output->writeln('');

        $crawlObserver = new CrawlObserver($output);

        $clientOptions = [
            RequestOptions::TIMEOUT => 5,
            RequestOptions::VERIFY => false,
            RequestOptions::ALLOW_REDIRECTS => true,
        ];

        $clientOptions[RequestOptions::HEADERS]['user-agent'] = 'Sitemap-Crawler';

        $crawler = Crawler::create($clientOptions)
            ->setConcurrency(3)
            ->setCrawlObserver($crawlObserver)
            ->setCrawlProfile($crawlProfile);

        $crawler->startCrawling($baseUrl);

        // Create Sitemap
        $output->writeln("Create Sitemap {$baseUrl}");
        $output->writeln('');

        $outputDir = getcwd();
        $generator = new SitemapGenerator('', $outputDir);
        $generator->toggleGZipFileCreation();
        $generator->setMaxURLsPerSitemap(50000);
        $generator->setSitemapFileName("sitemap.xml");
        foreach($crawlObserver->crawledUrls['200'] as $urlData) {
            $output->writeln("Add: {$urlData['url']}");

            $datetime = new \DateTime();
            if(!empty(lastModified)) {
                $datetime->setTimestamp(strtotime($urlData['lastModified']));
            }

            $generator->addURL($urlData['url'], $datetime, 'daily');
        }
        $generator->createSitemap();
        $generator->writeSitemap();

        return 0;
    }
}
