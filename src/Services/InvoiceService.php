<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ServiceEntry;
use App\Repositories\ServiceEntryRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\VehicleRepository;

class InvoiceService
{
    public function __construct(
        private readonly ServiceEntryRepository $serviceRepo,
        private readonly PaymentRepository $paymentRepo,
        private readonly CustomerRepository $customerRepo,
        private readonly VehicleRepository $vehicleRepo,
    ) {}

    public function buildInvoiceData(int $serviceEntryId): ?array
    {
        $entryData = $this->serviceRepo->findById($serviceEntryId);
        if ($entryData === null) {
            return null;
        }

        $entry    = new ServiceEntry($entryData);
        $parts    = $this->serviceRepo->getSpareParts($serviceEntryId);
        $repairs  = $this->serviceRepo->getRepairs($serviceEntryId);
        $payments = $this->paymentRepo->findByServiceEntryId($serviceEntryId);

        $totalPaid  = array_sum(array_column($payments, 'amount'));
        $balance    = $entry->getTotalCost() - $totalPaid;

        $config = require ROOT_PATH . '/config/app.php';

        return [
            'entry'           => $entry,
            'entry_data'      => $entryData,
            'spare_parts'     => $parts,
            'repairs'         => $repairs,
            'payments'        => $payments,
            'total_paid'      => $totalPaid,
            'balance'         => $balance,
            'currency_symbol' => $config['currency']['symbol'],
            'company_name'    => $config['name'],
            'invoice_number'  => 'INV-' . str_pad((string)$serviceEntryId, 6, '0', STR_PAD_LEFT),
        ];
    }

    public function calculateTotals(
        array $spareParts,
        array $repairs,
        float $vatPercent,
        float $discountAmount
    ): array {
        $totalParts  = 0.0;
        $totalLabour = 0.0;

        foreach ($spareParts as $part) {
            $qty   = (float)($part['quantity'] ?? 1);
            $price = (float)($part['unit_price'] ?? 0);
            $totalParts += $qty * $price;
        }

        foreach ($repairs as $repair) {
            $qty   = (float)($repair['quantity'] ?? 1);
            $price = (float)($repair['unit_price'] ?? 0);
            $totalLabour += $qty * $price;
        }

        $subtotal  = $totalParts + $totalLabour - $discountAmount;
        $subtotal  = max(0, $subtotal);
        $vatAmount = round($subtotal * ($vatPercent / 100), 2);
        $total     = $subtotal + $vatAmount;

        return [
            'total_parts'  => round($totalParts, 2),
            'total_labour' => round($totalLabour, 2),
            'subtotal'     => round($subtotal, 2),
            'vat_amount'   => $vatAmount,
            'total_cost'   => round($total, 2),
        ];
    }
}
