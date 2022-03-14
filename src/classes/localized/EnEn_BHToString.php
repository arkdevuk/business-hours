<?php

namespace ArkdevukBusinessHours\classes\localized;

use ArkdevukBusinessHours\classes\BHRange;
use ArkdevukBusinessHours\classes\BHWrapper;

class EnEn_BHToString extends BHTLocalized
{
    public static int $firstDay = 0;

    public static string $closedLabel = 'closed';

    public static string $timeSeparator = ' - ';

    public static string $timeRangeSeparator = ', ';

    public function __construct(BHWrapper $wrapper, array $options = [], string $timezone = 'GMT')
    {
        parent::__construct($wrapper, $options, $timezone);
        $this->options['locale'] = 'en_EN';
    }

    /**
     * Return a day name in string from a day int identifier
     *
     * @param int $id
     * @param bool $short
     * @return string
     */
    public function getDayName(int $id, bool $short = false): string
    {
        if ($short) {
            return match ($id) {
                1 => 'Mon.',
                2 => 'Tue.',
                3 => 'Wed.',
                4 => 'Thu.',
                5 => 'Fri.',
                6 => 'Sat.',
                0 => 'Sun.',
                default => '',
            };
        }

        return match ($id) {
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
            0 => 'Sunday',
            default => '',
        };
    }


}