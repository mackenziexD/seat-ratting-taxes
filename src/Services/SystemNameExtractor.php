<?php 

namespace Helious\SeatRattingTaxes\Services;

class SystemNameExtractor
{
    public static function extract($description)
    {
        $pattern = '/\b[A-Z0-9-]+\b$/';
        if (preg_match($pattern, $description, $matches)) {
            return $matches[0];
        }

        return 'Unknown System';
    }
}
