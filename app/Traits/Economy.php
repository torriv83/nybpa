<?php
/**
 * Created by Tor J. Rivera.
 * Date: 22.11.2023
 * Time: 14.17
 * Company: Rivera Consulting
 */

namespace App\Traits;

trait Economy
{

    /**
     * Divide the given value by 1000.
     *
     * @param  mixed  $value
     * @return float|int
     */
    public function divideYnab(mixed $value): float|int
    {
        return $value / 1000;
    }


    /**
     * Format the given amount as a currency string.
     *
     * @param  float  $amount  The amount to format.
     * @param  bool  $month  Whether to format as monthly currency.
     * @return string The formatted currency string.
     */
    public static function formatCurrency(float $amount, bool $month = false): string
    {
        $string = ' kr';

        if ($month)
        {
            $amount = $amount / 12;
            $string = ' kr i måneden';
        }

        return number_format($amount, 0, ',', '.').$string;
    }

}