<?php

namespace WordPressPluginDashboard;

class Message
{
    private string $class;
    private string $text;

    public function __construct(string $class, string $text)
    {
        $this->class = $class;
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }
}
