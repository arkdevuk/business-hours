<?php

namespace ArkdevukBusinessHours\classes\localized;

use ArkdevukBusinessHours\classes\BHWrapper;

class FrFr_BHToString extends BHTLocalized
{
    public static int $firstDay = 1;

    public static string $closedLabel = 'fermé';

    public static string $timeSeparator = ' - ';

    public static string $timeRangeSeparator = ', ';

    public function __construct(BHWrapper $wrapper, array $options = [], string $timezone = 'GMT')
    {
        parent::__construct($wrapper, $options, $timezone);
        $this->options['locale'] = 'fr_FR';
    }

    public function getDayName(int $id, bool $short = false): string
    {
        if ($short) {
            return match ($id) {
                1 => 'Lun.',
                2 => 'Mar.',
                3 => 'Mer.',
                4 => 'Jeu.',
                5 => 'Ven.',
                6 => 'Sam.',
                0 => 'Dim.',
                default => '',
            };
        }

        return match ($id) {
            1 => 'Lundi',
            2 => 'Mardi',
            3 => 'Mercredi',
            4 => 'Jeudi',
            5 => 'Vendredi',
            6 => 'Samedi',
            0 => 'Dimanche',
            default => '',
        };
    }
}