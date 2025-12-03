<?php

namespace WordPressPluginDashboard;

use DateInterval;
use DateTime;
use JsonException;
use SimpleXMLElement;

class Plugin
{
    private bool $errors = false;
    private string $slug;
    private $infoJson;
    private $statsJson;
    private $translationsJson;
    private $reviewsXml;

    public function __construct(string $slug, bool $force, WordPressApi $wordPressApi)
    {
        $this->slug = $slug;

        $infoJson = $wordPressApi->getPluginInfo($slug, $force);
        $reviewXml = $wordPressApi->getPluginReviews($slug, $force);
        $statsJson = $wordPressApi->getPluginStats($slug, $force);
        $translationsJson = $wordPressApi->getPluginTranslations($slug, $force);

        if (!$infoJson || !$reviewXml || !$statsJson || !$translationsJson) {
            $this->errors = true;
        }

        try {
            $this->infoJson = json_decode($infoJson, true, 512, JSON_THROW_ON_ERROR);
            $this->statsJson = json_decode($statsJson, true, 512, JSON_THROW_ON_ERROR);
            $this->translationsJson = json_decode($translationsJson, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            $this->errors = true;
        }

        $this->reviewsXml = @simplexml_load_string($reviewXml);
        if (!$this->reviewsXml) {
            $this->errors = true;
        }
    }

    public function hasErrors(): bool
    {
        return $this->errors;
    }

    public function getActiveInstallsCount(): int
    {
        return (int) $this->infoJson['active_installs'];
    }

    public function getAuthor(): string
    {
        return $this->infoJson['author'];
    }

    public function getDownloadCount(): int
    {
        return (int) $this->infoJson['downloaded'];
    }

    public function getDownloadUrl(): string
    {
        return $this->infoJson['download_link'];
    }

    public function getMinWordPressVersion()
    {
        return $this->infoJson['requires'];
    }

    public function getMaxWordPressVersion()
    {
        return $this->infoJson['tested'];
    }

    public function getMinPhpVersion()
    {
        return $this->infoJson['requires_php'];
    }

    public function getLastUpdated()
    {
        return date_create($this->infoJson['last_updated']);
    }

    public function getName(): string
    {
        return $this->infoJson['name'];
    }

    public function getPluginUrl(): string
    {
        return sprintf('https://wordpress.org/plugins/%s', $this->slug);
    }

    public function getRatingAverage(): float
    {
        if ($this->getRatingCount() <= 0) {
            return 0;
        }
        $sum = 0;
        foreach (range(1, 5) as $stars) {
            $sum += $this->getRatings($stars) * $stars;
        }
        return $sum / $this->getRatingCount();
    }

    public function getRatingCount(): int
    {
        return (int) $this->infoJson['num_ratings'];
    }

    public function getRatings(int $stars): int
    {
        return (int) $this->infoJson['ratings'][$stars];
    }

    public function getBadReviewsNotAnswered(): int
    {
        $reviewsCount = 0;
        $sixMonthsAgo = (new DateTime())->sub(new DateInterval('P6M'));

        foreach ($this->reviewsXml->xpath('channel/item') as $item) {
            $date = self::getFirstElement($item, 'pubDate');
            $description = self::getFirstElement($item, 'description');
            if ($date && $description) {
                $date = DateTime::createFromFormat(DateTime::RSS, $date[0]);

                if ($date < $sixMonthsAgo) {
                    continue;
                }

                $descriptionHtml = @simplexml_load_string('<html lang="en">' . $description->__toString() . '</html>');
                $paragraphs = $descriptionHtml->xpath('p');
                foreach ($paragraphs as $paragraph) {
                    if (strpos($paragraph, 'Replies') !== false) {
                        $replies = (int) trim(str_replace('Replies:', '', $paragraph->__toString()));
                    } elseif (strpos($paragraph, 'Rating') !== false) {
                        $rating = (int) trim(str_replace(['Rating:', 'stars'], '', $paragraph->__toString()));
                    }
                }

                if (isset($replies, $rating) && $rating <= 2 && $replies === 0) {
                    $reviewsCount++;
                }
            }
        }

        return $reviewsCount;
    }

    private static function getFirstElement(SimpleXMLElement $xmlElement, string $path): ?SimpleXMLElement
    {
        $results = $xmlElement->xpath($path);
        if (count($results) >= 0) {
            return $results[0];
        }
        return null;
    }

    public function getSupportThreadCount(): int
    {
        return (int) $this->infoJson['support_threads'];
    }

    public function getSupportThreadCountResolved(): int
    {
        return (int) $this->infoJson['support_threads_resolved'];
    }

    public function getSupportThreadCountUnresolved(): int
    {
        return $this->getSupportThreadCount() - $this->getSupportThreadCountResolved();
    }

    public function getSupportForumUrl(): string
    {
        return sprintf('https://wordpress.org/support/plugin/%s', $this->slug);
    }

    public function getSupportFeedUrl(): string
    {
        return sprintf('https://wordpress.org/support/plugin/%s/feed/', $this->slug);
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getSvnUrl(): string
    {
        return sprintf('https://plugins.svn.wordpress.org/%s/', $this->slug);
    }

    public function getTracFeedUrl(): string
    {
        return sprintf('https://plugins.trac.wordpress.org/log/%s?limit=100&mode=stop_on_copy&format=rss', $this->slug);
    }

    public function getTracUrl(): string
    {
        return sprintf('https://plugins.trac.wordpress.org/browser/%s/', $this->slug);
    }

    public function getTranslateUrl(): string
    {
        return sprintf('https://translate.wordpress.org/projects/wp-plugins/%s', $this->slug);
    }

    public function getTranslations(): array
    {
        if (!isset($this->translationsJson['translations'])) {
            return [];
        }

        $translations = [];
        foreach ($this->translationsJson['translations'] as $translation) {
            $translations[$translation['language']] = $translation;
        }
        return $translations;
    }

    public function getTranslationsCount()
    {
        return count($this->getTranslations());
    }

    public function getVersion(): string
    {
        return $this->infoJson['version'];
    }

    public function getVersionStats(): array
    {
        $versionToCount = array_filter(
            $this->statsJson,
            static function ($count, $version) {
                return $version !== 'other';
            },
            ARRAY_FILTER_USE_BOTH
        );
        krsort($versionToCount);
        return $versionToCount;
    }
}
