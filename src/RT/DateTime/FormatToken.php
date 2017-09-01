<?php

namespace RT\DateTime;


class FormatToken
{
    private $format;
    private $position;
    private $required;
    private $dateTimeToken;

    public function __construct(string $format,int $position = 0, $required = true, DateTimeToken $dateTimeToken = null)
    {
        $this->format = $format;
        $this->position = $position;
        $this->required = $required;
        $this->dateTimeToken = $dateTimeToken;
    }

    /**
     * @return string
     */
    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->required;
    }

    /**
     * @return DateTimeToken
     */
    public function getDateTimeToken()
    {
        return $this->dateTimeToken;
    }

    /**
     * @return bool
     */
    public function isDateTimeToken(): bool
    {
        return !is_null($this->dateTimeToken);
    }

    public function __toString()
    {
        return (string) $this->getFormat();
    }
}