<?php

namespace WordPressPluginDashboard;

class WordPressApi
{
    private string $dataDirectory;
    private array $messages = [];

    public function __construct()
    {
        global $dataDirectory;
        $this->dataDirectory = $dataDirectory;
    }

    public function getAuthor(string $author, $forceUpdate = false): string
    {
        $filePath = $this->dataDirectory . '/authors/' . $author . '.json';
        if ($forceUpdate || self::fileMissingOrOld($filePath)) {
            $apiUrl = sprintf('https://api.wordpress.org/plugins/info/1.2/?action=query_plugins&request[author]=%s', $author);
            $copied = copy($apiUrl, $filePath);
            $this->addMessage('author', $author, $copied);
        }
        return @file_get_contents($filePath);
    }

    public function getPluginInfo(string $pluginSlug, $forceUpdate = false): string
    {
        $filePath = $this->dataDirectory . '/plugins/' . $pluginSlug . '.json';
        if ($forceUpdate || self::fileMissingOrOld($filePath)) {
            $apiUrl = sprintf('https://api.wordpress.org/plugins/info/1.0/%s.json?fields=active_installs', $pluginSlug);
            $copied = @copy($apiUrl, $filePath);
            $this->addMessage('plugin', $pluginSlug, $copied);
        }
        return @file_get_contents($filePath);
    }

    public function getPluginReviews(string $pluginSlug, $forceUpdate = false): string
    {
        $filePath = $this->dataDirectory . '/reviews/' . $pluginSlug . '.xml';
        if ($forceUpdate || self::fileMissingOrOld($filePath)) {
            $apiUrl = sprintf('https://wordpress.org/support/plugin/%s/reviews/feed/', $pluginSlug);
            $copied = copy($apiUrl, $filePath);
            $this->addMessage('plugin reviews', $pluginSlug, $copied);
        }
        return @file_get_contents($filePath);
    }

    public function getPluginStats(string $pluginSlug, $forceUpdate = false): string
    {
        $filePath = $this->dataDirectory . '/stats/' . $pluginSlug . '.json';
        if ($forceUpdate || self::fileMissingOrOld($filePath)) {
            $apiUrl = sprintf('https://api.wordpress.org/stats/plugin/1.0/%s', $pluginSlug);
            $copied = copy($apiUrl, $filePath);
            $this->addMessage('plugin stats', $pluginSlug, $copied);
        }
        return @file_get_contents($filePath);
    }

    public function getPluginTranslations(string $pluginSlug, $forceUpdate = false): string
    {
        $filePath = $this->dataDirectory . '/translations/' . $pluginSlug . '.json';
        if ($forceUpdate || self::fileMissingOrOld($filePath)) {
            $apiUrl = sprintf('https://api.wordpress.org/translations/plugins/1.0/?slug=%s', $pluginSlug);
            $copied = copy($apiUrl, $filePath);
            $this->addMessage('plugin translations', $pluginSlug, $copied);
        }
        return @file_get_contents($filePath);
    }

    private static function fileMissingOrOld(string $filePath): bool
    {
        if (!file_exists($filePath)) {
            return true;
        }

        $lastChanged = filemtime($filePath);
        return (date('U') - $lastChanged) > (24 * 60 * 60);
    }

    private function addMessage(string $type, string $plugin, bool $copied): void
    {
        if ($copied) {
            $this->messages[] = new Message('success', sprintf('Updated: %s file for %s', $type, $plugin));
        } else {
            $this->messages[] = new Message('error', sprintf('Error: Could not update %s file for %s', $type, $plugin));
        }
    }

    public function getMessages(): array
    {
        return $this->messages;
    }

    public function getWordPressCoreVersions()
    {
        $filePath = $this->dataDirectory . '/wordpress-core-versions.json';
        if (self::fileMissingOrOld($filePath)) {
            $apiUrl = 'https://api.wordpress.org/core/stable-check/1.0/';
            copy($apiUrl, $filePath);
        }
        return @file_get_contents($filePath);
    }
}
