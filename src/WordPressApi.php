<?php

namespace WordPressPluginDashboard;

class WordPressApi
{
    private string $dataDirectory;
    private bool $outputMessages;

    public function __construct(bool $outputMessages)
    {
        $this->outputMessages = $outputMessages;

        global $dataDirectory;
        $this->dataDirectory = $dataDirectory;
    }

    public function getAuthor(string $author, $forceUpdate = false): string
    {
        $filePath = $this->dataDirectory . '/authors/' . $author . '.json';
        if ($forceUpdate || self::fileMissingOrOld($filePath)) {
            $apiUrl = sprintf('https://api.wordpress.org/plugins/info/1.2/?action=query_plugins&request[author]=%s', $author);
            $copied = copy($apiUrl, $filePath);
            $this->echoUpdateMessage('author', $author, $copied);
        }
        return @file_get_contents($filePath);
    }

    public function getPluginInfo(string $pluginSlug, $forceUpdate = false): string
    {
        $filePath = $this->dataDirectory . '/plugins/' . $pluginSlug . '.json';
        if ($forceUpdate || self::fileMissingOrOld($filePath)) {
            $apiUrl = sprintf('https://api.wordpress.org/plugins/info/1.0/%s.json?fields=active_installs', $pluginSlug);
            $copied = @copy($apiUrl, $filePath);
            $this->echoUpdateMessage('plugin', $pluginSlug, $copied);
        }
        return @file_get_contents($filePath);
    }

    public function getPluginStats(string $pluginSlug, $forceUpdate = false): string
    {
        $filePath = $this->dataDirectory . '/plugins-stats/' . $pluginSlug . '.json';
        if ($forceUpdate || self::fileMissingOrOld($filePath)) {
            $apiUrl = sprintf('https://api.wordpress.org/stats/plugin/1.0/%s', $pluginSlug);
            $copied = copy($apiUrl, $filePath);
            $this->echoUpdateMessage('plugin stats', $pluginSlug, $copied);
        }
        return @file_get_contents($filePath);
    }


    public function getPluginTranslations(string $pluginSlug, $forceUpdate = false): string
    {
        $filePath = $this->dataDirectory . '/plugins-translations/' . $pluginSlug . '.json';
        if ($forceUpdate || self::fileMissingOrOld($filePath)) {
            $apiUrl = sprintf('https://api.wordpress.org/translations/plugins/1.0/?slug=%s', $pluginSlug);
            $copied = copy($apiUrl, $filePath);
            $this->echoUpdateMessage('plugin translations', $pluginSlug, $copied);
        }
        return @file_get_contents($filePath);
    }

    private static function fileMissingOrOld(string $filePath): bool
    {
        if (!file_exists($filePath)) {
            return true;
        }

        $lastChanged = filemtime($filePath);
        return (date('U') - $lastChanged) > ( 24 * 60 * 60 );
    }

    private function echoUpdateMessage(string $type, string $plugin, bool $copied): void
    {
        if (!$this->outputMessages) {
            return;
        }

        if ($copied) {
            $message = '<li><span class="success">Updated</span>: %s file for %s</li>';
        } else {
            $message = '<li><span class="error">Error</span>: Could not update %s file for %s</li>';
        }
        echo sprintf($message, $type, $plugin);
    }
}
