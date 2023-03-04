<?php

namespace App\Service;

use IntlDateFormatter;

class DateFormaterService
{
    private IntlDateFormatter $fmt;

    public function __construct()
    {
        $this->fmt = new IntlDateFormatter("fr_FR",IntlDateFormatter::FULL,IntlDateFormatter::FULL,null,null,"E d MMM Y à HH:mm");

        return $this;
    }

    public function format(int|string|\DateTimeInterface $datetime): string
    {
        return $this->fmt->format($datetime);
    }
}