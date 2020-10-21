<?php

namespace WordPressPluginDashboard;

class WordPressPluginDashboard
{
    private $api;
    private $authorSlugs = [];
    private $plugins = [];
    private $pluginSlugs = [];
    private $languages = [];
    private $latestWordPressVersion;
    private $messages = [];
    private $updatePluginSlug;

    public function __construct()
    {
        $this->api = new WordPressApi();

        $this->latestWordPressVersion = $this->loadLatestWordPressVersion();
        $this->checkForm();
    }

    private function loadLatestWordPressVersion()
    {
        $wordPressVersions = json_decode($this->api->getWordPressCoreVersions(), true);
        foreach ($wordPressVersions as $version => $info) {
            if ($info === 'latest') {
                return $version;
            }
        }
        return null;
    }

    private function checkForm(): void
    {
        $regex = '@([a-z]|[0-9]|-|,)+@s';

        $pluginSlugsFromAuthors = [];
        if (isset($_GET['authors'])) {
            $authorSlugsFromRequest = strtolower($_GET['authors']);
            if (preg_match($regex, $authorSlugsFromRequest)) {
                $authorSlugsFromRequest = explode(',', $authorSlugsFromRequest);

                $pluginsByAuthors = [];
                foreach ($authorSlugsFromRequest as $authorSlugFromRequest) {
                    $pluginsFromAuthorJson = json_decode($this->api->getAuthor($authorSlugFromRequest), true);
                    if (isset($pluginsFromAuthorJson['plugins'])) {
                        $this->authorSlugs[] = $authorSlugFromRequest;
                        $pluginsByAuthors[] = array_map(
                            static function ($i) {
                                return $i['slug'];
                            },
                            $pluginsFromAuthorJson['plugins']
                        );
                    }
                }
                $pluginSlugsFromAuthors = array_merge(...$pluginsByAuthors);
            }
        }

        $pluginSlugs = [];
        if (isset($_GET['plugins'])) {
            $pluginSlugsFromRequest = strtolower($_GET['plugins']);
            if (preg_match($regex, $pluginSlugsFromRequest)) {
                $pluginSlugs = explode(',', $pluginSlugsFromRequest);
            }
        }

        if (isset($_POST['update']) && in_array($_POST['update'], array_merge($pluginSlugsFromAuthors, $pluginSlugs), true)) {
            $this->updatePluginSlug = $_POST['update'];
        } else {
            $this->updatePluginSlug = '';
        }

        $this->loadData($pluginSlugsFromAuthors, true);
        $this->loadData($pluginSlugs, false);
        asort($this->plugins);
        asort($this->languages);
    }

    private function loadData(array $pluginSlugs, $fromAuthor): void
    {
        foreach ($pluginSlugs as $pluginSlug) {
            $plugin = new Plugin($pluginSlug, $pluginSlug === $this->updatePluginSlug, $this->api);
            if ($plugin->hasErrors()) {
                $this->messages[] = new Message('error', sprintf('Plugin %s not found', $pluginSlug));
            } else {
                if (!$fromAuthor) {
                    $this->pluginSlugs[] = $pluginSlug;
                }
                $this->plugins[$plugin->getName()] = $plugin;
                foreach ($plugin->getTranslations() as $translation) {
                    $this->languages[$translation['english_name']] = new Language(
                        $translation['language'],
                        $translation['english_name'],
                        $translation['native_name']
                    );
                }
            }
        }
    }

    public function getAuthorSlugs(): array
    {
        return $this->authorSlugs;
    }

    /**
     * @return Message[]
     */
    public function getMessages(): array
    {
        return array_merge($this->api->getMessages(), $this->messages);
    }

    /**
     * @return Language[]
     */
    public function getLanguages(): array
    {
        return $this->languages;
    }

    public function getLatestWordPressVersion(): ?string
    {
        return $this->latestWordPressVersion;
    }

    /**
     * @return Plugin[]
     */
    public function getPlugins(): array
    {
        return $this->plugins;
    }

    public function getPluginSlugs(): array
    {
        return $this->pluginSlugs;
    }
}
