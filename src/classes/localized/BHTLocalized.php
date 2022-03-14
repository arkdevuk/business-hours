<?php

namespace ArkdevukBusinessHours\classes\localized;

use ArkdevukBusinessHours\classes\BHRange;
use ArkdevukBusinessHours\classes\BHWrapper;
use ArkdevukBusinessHours\interfaces\BHToStringInterface;
use IntlDateFormatter;

abstract class BHTLocalized implements BHToStringInterface
{
    /**
     * Represent the first day of the week
     * 0 is sunday
     * 1 is monday
     * and so on'
     * @var int
     */
    public static int $firstDay = 0;

    /**
     * String used if there is no hours registered for a given day
     *
     * @var string
     */
    public static string $closedLabel = 'closed';

    /**
     * Separator used between start & end
     *
     * @var string
     */
    public static string $timeSeparator = ' - ';

    /**
     * Separator used between BHRange
     *
     * @var string
     */
    public static string $timeRangeSeparator = ', ';

    /**
     * is set by the wrapper or manually
     * note : if you send this object to a BHWrapper it will override the manual value
     *
     * @see https://www.php.net/manual/en/timezones.php
     * @var string
     */
    protected string $timezone = 'GMT';

    /**
     * Options of the current query
     *
     * @var array
     */
    protected array $options;

    /**
     * Wrapper that store all the needed ranges
     *
     * @var BHWrapper
     */
    protected BHWrapper $wrapper;

    /**
     * @param BHWrapper $wrapper
     * @param array $options
     * @param string $timezone
     */
    public function __construct(BHWrapper $wrapper, array $options = [], string $timezone = 'GMT')
    {
        $this->wrapper = $wrapper;

        $this->timezone = $timezone;
        // default otions
        $this->options = [
            'locale' => 'en_EN',
            'mode' => 'normal', // short, normal, details
            'name' => 'long', // short, long
        ];
        $this->options = array_merge_recursive($this->options, $options);
    }

    public function toString(): string
    {
        $days = $this->toArray();

        /*
        if($this->options['mode'] === 'short'){
            // todo short mode
            return '';
        }

        if($this->options['mode'] === 'normal'){
            // todo normal mode

            return '';
        }//*/

        $output = '<ul class="business-hours ul-reset">'.PHP_EOL;

        foreach ($days as $iD => $d) {
            $todayClass = '';
            if (isset($d['src']['today']) && $d['src']['today'] === true) {
                $todayClass = ' is-today ';
            }
            $line = '<li class="day-'.$iD.$todayClass.'">';
            $line .= '<span class="day-label">'.$d['label'].'</span>';
            $line .= '<span class="day-hours">';
            if (is_array($d['hours'])) {
                $line .= implode($this::$timeRangeSeparator, $d['hours']);
            } else {
                $line .= $d['hours'];
            }
            $line .= '</span>';

            $output .= $line.'</li>'.PHP_EOL;
        }

        return $output.'</ul>';
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function toArray(): array
    {
        $days = $this->computeData();
        $dateFormater = $this->getDateFormat($this->options['locale']);
        $timeFormater = $this->getTimeFormat($this->options['locale']);

        /*
        if($this->options['mode'] === 'short'){
            // todo short mode
            return [];
        }

        if($this->options['mode'] === 'normal'){
            // todo normal mode

            return [];
        }//*/

        $o = [];
        // todo details mode

        foreach ($this->getDayIDs() as $dayID) {
            $day = $days[$dayID];
            $current = [
                'src' => $day,
                'label' => $day['day'],
                'hours' => [],
            ];
            if ($day['active'] === false) {
                $current['hours'] = $this::$closedLabel;
                $o[] = $current;
                continue;
            }
            foreach ($day['hours'] as $h) {
                $start = $h['start'];
                $end = $h['end'];
                $current['hours'][] = $timeFormater->format($start).$this::$timeSeparator.$timeFormater->format($end);
            }

            $o[] = $current;
        }

        return $o;
    }

    /**
     * @return array
     * @throws \Exception
     */
    protected function computeData(): array
    {
        $w = $this->getWrapper();
        $days = $w->compileDayToDay();
        $today = (int)date('w');

        $shortName = $this->options['name'] === 'short';

        $o = [];
        for ($i = 0; $i <= 6; $i++) {
            $previousKey = '';
            $key = '';
            $hoursArray = [];
            $d = $days[$i];
            $dayName = $this->getDayName($i, $shortName);

            foreach ($d as $item) {
                if ($item instanceof BHRange) {
                    $key .= $item->getStart().'-'.$item->getEnd();

                    [$startHour, $startMinutes] = explode(':', $item->getStart());
                    $startHour = (int)$startHour;
                    $startMinutes = (int)$startMinutes;
                    //*
                    $date = new \DateTime('now', new \DateTimeZone($this->getWrapper()->getTimezone()));
                    $date->setTime($startHour, $startMinutes, 0);
                    $date->setTimezone(new \DateTimeZone($this->getTimezone()));

                    [$endHour, $endMinutes] = explode(':', $item->getEnd());
                    $endHour = (int)$endHour;
                    $endMinutes = (int)$endMinutes;
                    //*
                    $date2 = new \DateTime('now', new \DateTimeZone($this->getWrapper()->getTimezone()));
                    $date2->setTime($endHour, $endMinutes, 0);
                    $date2->setTimezone(new \DateTimeZone($this->getTimezone()));
                    //var_dump($date->format("H:i:s"));//*/
                    $hoursArray[] = [
                        'start' => $date,
                        'end' => $date2,
                    ];
                }
            }
            if (isset($o[$i - 1])) {
                $previousKey = $o[$i - 1]['key'];
            }

            $o[$i] = [
                'key' => $key,
                'same_as_previous' => $key === $previousKey,
                'day' => $dayName,
                'today' => $today === $i,
                'active' => count($hoursArray) > 0,
                'hours' => $hoursArray,
            ];
        }

        $o[0]['same_as_previous'] = $o[0]['key'] === $o[6]['key'];

        return $o;
    }

    /**
     * @return BHWrapper
     */
    public function getWrapper(): BHWrapper
    {
        return $this->wrapper;
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
     * @return BHTLocalized
     */
    public function setTimezone(string $timezone): BHTLocalized
    {
        $this->timezone = $timezone;

        return $this;
    }

    public function getDateFormat($locale)
    {
        $locale = str_replace('_', '-', $locale);
        $formatter = new IntlDateFormatter(
            $locale, IntlDateFormatter::SHORT,
            IntlDateFormatter::NONE,
            $this->getTimezone(),
        );

        //return $formatter->getPattern();
        return $formatter;
    }

    public function getTimeFormat($locale)
    {
        $locale = str_replace('_', '-', $locale);
        $formatter = new IntlDateFormatter(
            $locale, IntlDateFormatter::NONE,
            IntlDateFormatter::SHORT,
            $this->getTimezone(),
        );

        //return $formatter->getPattern();
        return $formatter;
    }

    /**
     * return days order
     *
     * @return int[]
     */
    public function getDayIDs(): array
    {
        if ($this::$firstDay === 0) {
            return [0, 1, 2, 3, 4, 5, 6];
        }

        return [1, 2, 3, 4, 5, 6, 0];
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }
}