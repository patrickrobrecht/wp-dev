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

    public function getClass(): string
    {
        return $this->class;
    }

    public function getText(): string
    {
        return $this->text;
    }
}
