<?php

declare(strict_types=1);

namespace App\Entity\Student;

enum MethodePaiement: string
{
    case Especes = 'especes';
    case Cheque = 'cheque';
    case Virement = 'virement';
    case MobileMoney = 'mobile_money';
}
