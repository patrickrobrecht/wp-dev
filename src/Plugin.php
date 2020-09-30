<?php

namespace WordPressPluginDashboard;

class Plugin
{
    private $slug;
    private $infoJson;
    private $statsJson;
    private $translationsJson;

    public function __construct(string $slug, bool $force, WordPressApi $wordPressApi)
    {
        $this->slug = $slug;

        $infoJson = $wordPressApi->getPluginInfo($slug, $force);
        $statsJson = $wordPressApi->getPluginStats($slug, $force);
        $translationsJson = $wordPressApi->getPluginTranslations($slug, $force);

        $this->infoJson = json_decode($infoJson, true);
        $this->statsJson = json_decode($statsJson, true);
        $this->translationsJson = json_decode($translationsJson, true);
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
        return strtotime($this->infoJson['last_updated']);
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
