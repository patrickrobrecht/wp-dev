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

    public function getLocaleCode(): string
    {
        return $this->localeCode;
    }

    public function getEnglishName(): string
    {
        return $this->englishName;
    }

    public function getNativeName(): string
    {
        return $this->nativeName;
    }
}
