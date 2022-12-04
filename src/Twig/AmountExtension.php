<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AmountExtension extends AbstractExtension {

    public function getFilters()
    {
        return [
            new TwigFilter('amount', [$this, 'amount'])
        ];
    }
    public function amount($value, string $symbol = '€', string $sepaDec = ',', string $sepaThousand = ' ') {
        $finalValue = $value * 1;
        $finalValue = number_format($finalValue, 2, $sepaDec, $sepaThousand);
        return $finalValue . '' . $symbol;
    }
}