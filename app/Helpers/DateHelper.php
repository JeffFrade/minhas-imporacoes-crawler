<?php

namespace App\Helpers;

use Carbon\Carbon;

class DateHelper
{
    /**
     * @param string $date
     * @return string|void
     */
    public static function parse(string $date)
    {
        if ($date != null) {
            return Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
        }
    }
}
