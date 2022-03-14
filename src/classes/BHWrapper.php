<?php

namespace ArkdevukBusinessHours\classes;


class BHWrapper
{
    /**
     * @see https://www.php.net/manual/en/timezones.php
     * @var string
     */
    protected string $timezone = 'GMT';

    /**
     * @var array|BHRange[]
     */
    protected array $stack;

    /**
     * Create a new wrapper
     *
     * @param string|null $timezone
     */
    public function __construct(?string $timezone = null)
    {
        $this->stack = [];
        // override all previous timezone if it's specified i the constructor
        if (null !== $timezone) {
            $this->timezone = $timezone;
        }
    }

    public function _cmp(BHRange $a, BHRange $b)
    {
        return strcmp($a->getStart(), $b->getStart());
    }

    /**
     * Match days to valid range
     *
     * @return array|array[]
     */
    public function compileDayToDay(): array
    {
        $o = [
            0 => [],
            1 => [],
            2 => [],
            3 => [],
            4 => [],
            5 => [],
            6 => [],
        ];

        foreach ($this->stack as $item) {
            foreach (['sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat'] as $i => $dayID) {
                if ($item->has($dayID)) {
                    $o[$i][] = $item;
                }
            }
        }


        foreach ($o as $i => $ranges) {
            usort($o[$i], [$this, "_cmp"]);
        }

        return $o;
    }

    public function compute(): self
    {

        return $this;
    }

    // UTILS::TOSTRING
    public function toString(array $options = [], string $locale = 'en_EN', ?string $timezone = null): string
    {
        if (null === $timezone) {
            $timezone = $this->getTimezone();
        }

        switch (strtolower($locale)) {
            case 'fr_fr':
                $className = 'FrFr_BHToString';
                break;
            default:
                $className = 'EnEn_BHToString';
        }
        /**
         * @var $c
         */
        $c = new ('ArkdevukBusinessHours\classes\localized\\'.$className)($this, $options, $timezone);

        return $c->toString();
    }

    /**
     * @return string
     */
    public function getTimezone(): string
    {
        return $this->timezone;
    }

    /**
     * Add the given object to the stack
     *
     * @param BHRange $range
     * @return $this
     */
    public function addRange(BHRange $range): self
    {
        if (!in_array($range, $this->stack, true)) {
            $range
                ->setWrapper($this)
                ->setTimezone($this->timezone);
            $this->stack[] = $range;
        }

        return $this;
    }

    /**
     * Set/Replace the stack with a given array of BHRange
     *
     * @param array $ranges
     * @return $this
     */
    public function setRange(array $ranges): self
    {
        $this->stack = $ranges;

        foreach ($this->stack as $item) {
            if ($item instanceof BHRange) {
                $item
                    ->setWrapper($this)
                    ->setTimezone($this->timezone);

            }
        }

        return $this;
    }

    /**
     * remove the given object from the stack and then re-index the stack
     *
     * @param BHRange $range
     * @return $this
     */
    public function removeRange(BHRange $range): self
    {
        $as = array_search($range, $this->stack, true);
        if ($as !== false) {
            $range->setWrapper(null);
            unset($this->stack[$as]);
            $this->stack = array_values($this->stack);
        }

        return $this;
    }
}