<?php

namespace App\Service;

use IntlDateFormatter;

class DateFormaterService
{
    private IntlDateFormatter $fmt;

    public function __construct(?string $pattern = "EEEE d MMMM Y à HH:mm")
    {
        $this->fmt = new IntlDateFormatter("fr_FR",IntlDateFormatter::FULL,IntlDateFormatter::FULL,null,null,$pattern);

        return $this;
    }

    public function format(int|string|\DateTimeInterface $datetime): string
    {
        return $this->fmt->format($datetime);
    }
}