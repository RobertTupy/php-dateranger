<?php

namespace RT\DateTime;

class DateTimeToken
{
    private $formatter;

    private $level;

    public function __construct($formatter, $level)
    {
        $this->formatter = $formatter;
        $this->level = $level;
    }

    public function getLevel()
    {
        return $this->level;
    }

    public function compare(\DateTime $a, \DateTime $b)
    {
        $af = $a->format($this->formatter);
        $bf = $b->format($this->formatter);

        return ($af == $bf ? 0 : ($af < $bf) ? -1 : 1);
    }
}