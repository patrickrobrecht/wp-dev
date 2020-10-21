<?php

namespace WordPressPluginDashboard;

class Language
{
    private string $localeCode;
    private string $englishName;
    private string $nativeName;

    public function __construct(string $localCode, string $englishName, string $nativeName)
    {
        $this->localeCode = $localCode;
        $this->englishName = $englishName;
        $this->nativeName = $nativeName;
    }

    /**
     * @return string
     */
    public function getLocaleCode(): string
    {
        return $this->localeCode;
    }

    /**
     * @return string
     */
    public function getEnglishName(): string
    {
        return $this->englishName;
    }

    /**
     * @return string
     */
    public function getNativeName(): string
    {
        return $this->nativeName;
    }
}
