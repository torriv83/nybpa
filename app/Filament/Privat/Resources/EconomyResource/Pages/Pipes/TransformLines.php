<?php

/**
 * Created by Tor J. Rivera.
 * Date: 13.04.2024
 * Time: 14:00
 * Company: Rivera Consulting
 */

namespace App\Filament\Privat\Resources\EconomyResource\Pages\Pipes;

use Closure;

class TransformLines
{
    /**
     * Transforms an array of transactions into a standardized format.
     *
     * @param  array  $transactions  The array of transactions to be transformed.
     * @param  Closure  $next  The next Closure in the middleware stack.
     * @return mixed The result of the next Closure in the middleware stack.
     */
    public function handle(array $transactions, Closure $next): mixed
    {
        $transformedTransactions = [];

        foreach ($transactions as $transaction) {
            if ($transaction[0] == 'Reservert') {
                $dato = today()->format('Y/m/d');
            } else {
                $dato = $transaction[0];
            }

            $transformedData = [
                'Date' => $dato,
                'Payee' => $transaction[5],
                'Memo' => '',
                'Amount' => str_replace(',', '.', $transaction[1]),
            ];

            $transformedTransactions[] = $transformedData;
        }

        return $next($transformedTransactions);
    }
}
