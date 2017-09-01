<?php

namespace RT\DateTime;


use RT\DateTime\FormatToken;

class DateTimeRanger
{
    /**
     * @var \DateTime
     */
    private $start;

    /**
     * @var \DateTime
     */
    private $stop;

    /**
     * @var string DateTime format for output date range
     */
    private $format;

    /**
     * @var string Delimiter/clue for interval representation
     */
    private $delimiter;

    public function __construct(\DateTime $start, \DateTime $stop, string $format = null, string $delimiter = null)
    {
        if ($stop < $start) {
            throw new \InvalidArgumentException('Stop date cannot be earlier then start');
        }
        $this->start = $start;
        $this->stop = $stop;
        $this->format = $format;
        $this->delimiter = $delimiter;
    }

    /**
     * @return string
     */
    public function getDelimiter()
    {
        if (is_null($this->delimiter)) {
            $this->delimiter = ' - ';
        }
        return $this->delimiter;
    }

    /**
     * @return string
     */
    public function getFormat()
    {
        if (is_null($this->format)) {
            $this->format = 'j.n. Y';
        }
        return $this->format;
    }

    public function getStart()
    {
        return $this->start;
    }

    public function getStop()
    {
        return $this->stop;
    }

    /**
     * @param string $format
     * @param string $delimiter
     * @return string
     */
    public function format(string $format = null, string $delimiter = null)
    {
        if (is_null($format)) {
            $format = $this->getFormat();
        }
        if (is_null($delimiter)) {
            $delimiter = $this->getDelimiter();
        }

        $start = $this->getStart();
        $stop = $this->getStop();

        $tokenizer = new FormatTokenizer($format);

        $formatTokens = $tokenizer->getTokens();

        $dateTimesTokensInFormat = array_filter(
            $formatTokens,
            function(FormatToken $token) {
                return $token->isDateTimeToken();
            }
        );

        if (empty($dateTimesTokensInFormat)) {
            return $this->start->format($format) . $delimiter . $this->stop->format($format);
        }

        uasort($dateTimesTokensInFormat, function(FormatToken $a, FormatToken $b) {
            $al = $a->getDateTimeToken()->getLevel();
            $bl = $b->getDateTimeToken()->getLevel();
            return $al == $bl ? 0 : ($al < $bl) ? -1 : 1;
        });

        $levels = [];
        foreach ($formatTokens as $token) {
            if ($token->isDateTimeToken()) {
                $levels[] = $token->getDateTimeToken()->getLevel();
            }
        }

        $groups = [];
        $groupIndex = 0;
        foreach ($levels as $i => $level) {
            $previousLevel = null;
            if (isset($levels[$i-1])) {
                $previousLevel = $levels[$i-1];
            }
            if ($previousLevel) {
                if ($previousLevel > $level) {
                    $groupIndex++;
                }
            }
            $groups[$groupIndex][] = $level;
        }

        $levelGroup = [];
        foreach ($groups as $group) {
            $a = $group;
            foreach ($group as $member) {
                $levelGroup[$member] = $a;
                array_shift($a);
            }
        }


        $diffLevel = 0;
        foreach ($dateTimesTokensInFormat as $token) {
            $comparison = $token->getDateTimeToken()->compare($start, $stop);
            if ($comparison !== 1) {
                $diffLevel = $token->getDateTimeToken()->getLevel();
                break;
            }
        }

        var_dump($groups, $levelGroup, $diffLevel); exit;

        $breakReached = false;
        $groupToFill = $levelGroup[$diffLevel];
        $firstTokenOfFill = array_pop(array_reverse($groupToFill));;
        $lastTokenOfFill = array_pop($groupToFill);

        var_dump($groupToFill, $firstTokenOfFill, $lastTokenOfFill);
        $groupFill = false;
        foreach ($formatTokens as $token) {
            if ($token->isDateTimeToken()) {
                if (!$breakReached && $level === $firstTokenOfFill) {
                    var_dump($token->getPosition());
                    $groupFill = true;
                }
            }
            if (!$breakReached || $groupFill) {
                $startTokens[] = $token;
            }
            if ($token->isDateTimeToken()) {
                $dateToken = $token->getDateTimeToken();
                $level = $dateToken->getLevel();
                if (!$breakReached
                    && $level === $diffLevel
                ) {
                    $breakReached = true;
                }
                if ($groupFill && $breakReached) {
                    if(!in_array($level, $groupToFill)
                        || $level === $lastTokenOfFill
                    ) {
                        $groupFill = false;
                    }
                }
            }
            if ($breakReached || $groupFill) {
                $stopTokens[] = $token;
            }
        }
        exit;

        return $start->format(implode('', $startTokens)) . $delimiter . $stop->format(implode('', $stopTokens));
    }

    public function __toString()
    {
        return (string) $this->format($this->getFormat(), $this->getDelimiter());
    }
}