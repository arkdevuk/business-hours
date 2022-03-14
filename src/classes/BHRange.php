<?php

namespace ArkdevukBusinessHours\classes;


use JsonSerializable;

class BHRange implements JsonSerializable
{
    /**
     * days state
     *
     * true = active
     * false = inactive
     *
     * @var bool
     */
    public bool $mon = false;
    public bool $tue = false;
    public bool $wed = false;
    public bool $thu = false;
    public bool $fri = false;
    public bool $sat = false;
    public bool $sun = false;
    /**
     * starting time of the current range
     * @var string
     */
    public string $start = '00:00';
    /**
     * Ending time of the current range
     * @var string
     */
    public string $end = '23:59';
    /**
     * If current range is stored in a wrapper, reference of the wrapper is stored here
     *
     * @var BHWrapper|null
     */
    protected ?BHWrapper $wrapper = null;
    /**
     * is set by the wrapper or manually
     * note : if you send this object to a BHWrapper it will override the manual value
     *
     * @see https://www.php.net/manual/en/timezones.php
     * @var string
     */
    protected string $timezone = 'GMT';

    /**
     * Create it empty or populate it from an array
     *
     * @param array|null $data
     */
    public function __construct(?array $data = null)
    {
        if (null !== $data) {
            // import array
            $this->_importFromArray_($data);
        }
    }

    /**
     * Import data from array
     *
     * @param array $data
     * @return void
     */
    protected function _importFromArray_(array $data): void
    {
        foreach (['sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat'] as $dayID) {
            $this->$dayID = $data[$dayID] ?? false;
        }
        if (isset($data['end'])) {
            $this->end = $data['end'];
        }
        if (isset($data['start'])) {
            $this->start = $data['start'];
        }
    }

    /**
     * Check if item has the given day active
     *
     * @param string $day
     * @return bool
     */
    public function has(string $day): bool
    {
        return $this->$day ?? false;
    }

    /**
     * object to json
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'daysOfWeek' => $this->getDays(),
            // day details
            'mon' => $this->mon,
            'tue' => $this->tue,
            'wed' => $this->wed,
            'thu' => $this->thu,
            'fri' => $this->fri,
            'sat' => $this->sat,
            'sun' => $this->sun,
            // time start & end
            'start' => $this->start,
            'end' => $this->end,
        ];
    }

    /**
     * get an array with the day active for this range
     *
     * sunday is 0, monday is 1
     *
     * @return array
     */
    public function getDays(): array
    {
        $o = [];

        if ($this->mon) {
            $o[] = 1;
        }
        if ($this->tue) {
            $o[] = 2;
        }
        if ($this->wed) {
            $o[] = 3;
        }
        if ($this->thu) {
            $o[] = 4;
        }
        if ($this->fri) {
            $o[] = 5;
        }
        if ($this->sat) {
            $o[] = 6;
        }
        if ($this->sun) {
            $o[] = 0;
        }

        return $o;
    }

    /**
     * @return string
     */
    public function getTimezone(): string
    {
        return $this->timezone;
    }

    /**
     * @param string $timezone
     * @return BHRange
     */
    public function setTimezone(string $timezone): BHRange
    {
        $this->timezone = $timezone;

        return $this;
    }

    /**
     * @return bool
     */
    public function isInWrapper(): bool
    {
        return $this->wrapper !== null;
    }

    /**
     * @return BHWrapper|null
     */
    public function getWrapper(): ?BHWrapper
    {
        return $this->wrapper;
    }

    /**
     * @param BHWrapper|null $wrapper
     * @return $this
     */
    public function setWrapper(?BHWrapper $wrapper): BHRange
    {
        $this->wrapper = $wrapper;

        return $this;
    }

    /**
     * @return string
     */
    public function getEnd(): string
    {
        return $this->end;
    }

    /**
     * @return string
     */
    public function getStart(): string
    {
        return $this->start;
    }

}