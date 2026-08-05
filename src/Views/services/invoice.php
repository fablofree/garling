<?php
$e         = $entry_data ?? [];
$entry     = $entry ?? null;
$cfg       = $_app ?? [];
$cur       = $currency_symbol ?? ($cfg['currency']['symbol'] ?? 'Rs');
$fmt       = fn(float $v) => $cur . ' ' . number_format($v, 2);
$company   = $company_name ?? ($cfg['name'] ?? 'Garage A. Lingiah');
$invNo     = $invoice_number ?? ('INV-' . str_pad((string)($e['id'] ?? ''), 6, '0', STR_PAD_LEFT));
$totalPaid = (float)($total_paid ?? 0);
$balance   = (float)($balance ?? 0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice <?= htmlspecialchars($invNo, ENT_QUOTES, 'UTF-8') ?></title>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { margin: 0; }
            .invoice-wrapper { box-shadow: none; margin: 0; }
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f0f0f0; color: #1a1a1a; font-size: 13px; }
        .invoice-wrapper { max-width: 800px; margin: 30px auto; background: #fff; padding: 40px; box-shadow: 0 4px 20px rgba(0,0,0,0.12); }
        .inv-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 32px; border-bottom: 3px solid #f97316; padding-bottom: 24px; }
        .inv-company h1 { font-size: 24px; color: #f97316; font-weight: 700; }
        .inv-company p { color: #555; font-size: 12px; margin-top: 4px; }
        .inv-meta { text-align: right; }
        .inv-number { font-size: 20px; font-weight: 700; color: #1e293b; }
        .inv-date { color: #555; font-size: 12px; margin-top: 4px; }
        .inv-type-badge { display: inline-block; background: #f97316; color: #fff; padding: 3px 10px; border-radius: 4px; font-size: 11px; font-weight: 600; margin-top: 6px; }

        .parties { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 28px; }
        .party h3 { font-size: 11px; color: #888; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; }
        .party p { line-height: 1.7; color: #333; }
        .party strong { color: #1a1a1a; }

        .vehicle-info { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 14px 18px; margin-bottom: 24px; display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; }
        .vehicle-info div { }
        .vehicle-info .vi-label { font-size: 10px; text-transform: uppercase; color: #888; letter-spacing: 0.5px; }
        .vehicle-info .vi-value { font-weight: 600; color: #1a1a1a; margin-top: 2px; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background: #1e293b; color: #fff; padding: 10px 12px; text-align: left; font-size: 12px; }
        td { padding: 9px 12px; border-bottom: 1px solid #f0f0f0; }
        tr:nth-child(even) td { background: #fafafa; }
        .section-heading { background: #f1f5f9; font-weight: 600; color: #475569; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }

        .totals-box { display: flex; justify-content: flex-end; margin-bottom: 24px; }
        .totals-inner { min-width: 280px; }
        .total-line { display: flex; justify-content: space-between; padding: 5px 0; border-bottom: 1px solid #f0f0f0; font-size: 13px; }
        .total-line.grand { border-top: 2px solid #1e293b; border-bottom: 2px solid #1e293b; font-weight: 700; font-size: 15px; color: #1e293b; padding: 8px 0; }
        .total-line span:first-child { color: #555; }

        .payments-section { margin-bottom: 24px; }
        .payments-section h3 { font-size: 12px; text-transform: uppercase; color: #888; letter-spacing: 1px; margin-bottom: 10px; }
        .balance-box { background: #fff7ed; border: 2px solid #f97316; border-radius: 8px; padding: 14px 18px; display: flex; justify-content: space-between; align-items: center; }
        .balance-box.paid { background: #f0fdf4; border-color: #22c55e; }
        .balance-label { color: #555; font-size: 13px; }
        .balance-amount { font-size: 20px; font-weight: 700; color: #f97316; }
        .balance-box.paid .balance-amount { color: #16a34a; }

        .remarks-section { margin-bottom: 24px; }
        .remarks-section h3 { font-size: 11px; text-transform: uppercase; color: #888; margin-bottom: 6px; }
        .remarks-section p { color: #555; line-height: 1.6; }

        .inv-footer { border-top: 1px solid #e2e8f0; padding-top: 16px; display: flex; justify-content: space-between; color: #999; font-size: 11px; }

        .no-print { text-align: center; padding: 16px; }
        .no-print a, .no-print button { display: inline-block; margin: 0 6px; padding: 10px 24px; border-radius: 6px; font-size: 14px; cursor: pointer; border: none; }
        .btn-print { background: #f97316; color: #fff; text-decoration: none; font-weight: 600; }
        .btn-back { background: #e2e8f0; color: #333; text-decoration: none; }
    </style>
</head>
<body>

<div class="no-print" style="background:#1e293b; padding:12px; text-align:center;">
    <a href="/services/<?= (int)($e['id'] ?? 0) ?>" class="btn-back" style="color:#94a3b8">← Back</a>
    <button onclick="window.print()" class="btn-print">🖨 Print</button>
</div>

<div class="invoice-wrapper">

    <!-- Header -->
    <div class="inv-header">
        <div class="inv-company">
            <h1><?= htmlspecialchars($company, ENT_QUOTES, 'UTF-8') ?></h1>
            <p>Professional Automotive Services</p>
        </div>
        <div class="inv-meta">
            <div class="inv-number"><?= htmlspecialchars($invNo, ENT_QUOTES, 'UTF-8') ?></div>
            <div class="inv-date">Date: <?= htmlspecialchars($e['entry_date'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
            <?php if (!empty($e['delivery_date'])): ?>
            <div class="inv-date">Delivery: <?= htmlspecialchars($e['delivery_date'], ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
            <div><span class="inv-type-badge"><?= htmlspecialchars($e['entry_type'] ?? 'INVOICE', ENT_QUOTES, 'UTF-8') ?></span></div>
        </div>
    </div>

    <!-- Customer -->
    <div class="parties">
        <div class="party">
            <h3>Bill To</h3>
            <p>
                <strong><?= htmlspecialchars($e['customer_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong>
            </p>
        </div>
        <div class="party">
            <h3>From</h3>
            <p><strong><?= htmlspecialchars($company, ENT_QUOTES, 'UTF-8') ?></strong></p>
        </div>
    </div>

    <!-- Vehicle info -->
    <div class="vehicle-info">
        <div>
            <div class="vi-label">Registration</div>
            <div class="vi-value"><?= htmlspecialchars($e['registration_no'] ?? '—', ENT_QUOTES, 'UTF-8') ?></div>
        </div>
        <div>
            <div class="vi-label">Make / Model</div>
            <div class="vi-value"><?= htmlspecialchars(trim(($e['vehicle_make'] ?? '') . ' ' . ($e['vehicle_model'] ?? '')), ENT_QUOTES, 'UTF-8') ?></div>
        </div>
        <div>
            <div class="vi-label">Colour</div>
            <div class="vi-value"><?= htmlspecialchars($e['vehicle_colour'] ?? '—', ENT_QUOTES, 'UTF-8') ?></div>
        </div>
        <div>
            <div class="vi-label">Odometer</div>
            <div class="vi-value"><?= $e['odometer'] ? number_format((int)$e['odometer']) . ' ' . ($e['distance_unit'] ?? 'km') : '—' ?></div>
        </div>
        <div>
            <div class="vi-label">Chassis No.</div>
            <div class="vi-value"><?= htmlspecialchars($e['vehicle_chassis_no'] ?? '—', ENT_QUOTES, 'UTF-8') ?></div>
        </div>
        <div>
            <div class="vi-label">Next Service</div>
            <div class="vi-value"><?= $e['next_servicing'] ? number_format((int)$e['next_servicing']) . ' ' . ($e['distance_unit'] ?? 'km') : '—' ?></div>
        </div>
    </div>

    <!-- Line items table -->
    <table>
        <thead>
            <tr>
                <th style="width:50%">Description</th>
                <th class="text-right" style="width:15%">Qty</th>
                <th class="text-right" style="width:20%">Unit Price</th>
                <th class="text-right" style="width:15%">Amount</th>
            </tr>
        </thead>
        <tbody>
        <?php if (!empty($spare_parts)): ?>
            <tr><td colspan="4" class="section-heading">Spare Parts</td></tr>
            <?php foreach ($spare_parts as $p): ?>
            <tr>
                <td><?= htmlspecialchars($p['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                <td class="text-right"><?= htmlspecialchars((string)($p['quantity'] ?? 1), ENT_QUOTES, 'UTF-8') ?></td>
                <td class="text-right"><?= $fmt((float)($p['unit_price'] ?? 0)) ?></td>
                <td class="text-right"><?= $fmt((float)($p['total_price'] ?? 0)) ?></td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        <?php if (!empty($repairs)): ?>
            <tr><td colspan="4" class="section-heading">Repairs / Labour</td></tr>
            <?php foreach ($repairs as $r): ?>
            <tr>
                <td><?= htmlspecialchars($r['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                <td class="text-right"><?= htmlspecialchars((string)($r['quantity'] ?? 1), ENT_QUOTES, 'UTF-8') ?></td>
                <td class="text-right"><?= $fmt((float)($r['unit_price'] ?? 0)) ?></td>
                <td class="text-right"><?= $fmt((float)($r['total_price'] ?? 0)) ?></td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        <?php if (empty($spare_parts) && empty($repairs)): ?>
            <tr><td colspan="4" class="text-center" style="color:#999;padding:20px">No items</td></tr>
        <?php endif; ?>
        </tbody>
    </table>

    <!-- Totals -->
    <div class="totals-box">
        <div class="totals-inner">
            <?php if ((float)($e['discount_amount'] ?? 0) > 0): ?>
            <div class="total-line"><span>Parts Total</span><span><?= $fmt((float)($e['total_parts'] ?? 0)) ?></span></div>
            <div class="total-line"><span>Labour Total</span><span><?= $fmt((float)($e['total_labour'] ?? 0)) ?></span></div>
            <div class="total-line"><span>Discount</span><span>- <?= $fmt((float)($e['discount_amount'] ?? 0)) ?></span></div>
            <?php endif; ?>
            <div class="total-line"><span>Subtotal</span><span><?= $fmt((float)($e['subtotal'] ?? 0)) ?></span></div>
            <?php if ((float)($e['vat_percent'] ?? 0) > 0): ?>
            <div class="total-line"><span>VAT (<?= (float)($e['vat_percent'] ?? 0) ?>%)</span><span><?= $fmt((float)($e['vat_amount'] ?? 0)) ?></span></div>
            <?php endif; ?>
            <div class="total-line grand"><span>TOTAL</span><span><?= $fmt((float)($e['total_cost'] ?? 0)) ?></span></div>
        </div>
    </div>

    <!-- Payments -->
    <?php if (!empty($payments)): ?>
    <div class="payments-section">
        <h3>Payment History</h3>
        <table>
            <thead><tr><th>Date</th><th>Method</th><th>Reference</th><th class="text-right">Amount</th></tr></thead>
            <tbody>
            <?php foreach ($payments as $p): ?>
            <tr>
                <td><?= htmlspecialchars($p['payment_date'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($p['payment_method'] ?? '', ENT_QUOTES, 'UTF-8') ?><?= !empty($p['cheque_number']) ? ' #' . htmlspecialchars($p['cheque_number'], ENT_QUOTES, 'UTF-8') : '' ?></td>
                <td><?= htmlspecialchars($p['reference'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                <td class="text-right"><?= $fmt((float)($p['amount'] ?? 0)) ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <!-- Balance -->
    <div class="balance-box <?= $balance <= 0 ? 'paid' : '' ?>">
        <div class="balance-label"><?= $balance <= 0 ? 'PAID IN FULL' : 'OUTSTANDING BALANCE' ?></div>
        <div class="balance-amount"><?= $fmt(abs($balance)) ?></div>
    </div>

    <!-- Remarks -->
    <?php if (!empty($e['remarks'])): ?>
    <div class="remarks-section" style="margin-top:20px">
        <h3>Remarks</h3>
        <p><?= nl2br(htmlspecialchars($e['remarks'], ENT_QUOTES, 'UTF-8')) ?></p>
    </div>
    <?php endif; ?>

    <div class="inv-footer">
        <span><?= htmlspecialchars($company, ENT_QUOTES, 'UTF-8') ?></span>
        <span>Thank you for your business</span>
    </div>
</div>

</body>
</html>
