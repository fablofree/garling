<?php
$e          = $entry ?? [];
$cfg        = $_app ?? [];
$cur        = $cfg['currency']['symbol'] ?? 'Rs';
$fmt        = fn(float $v) => $cur . ' ' . number_format($v, 2);
$company    = $cfg['name'] ?? 'Garage A. Lingiah';
$invNo      = $invoice_number ?? ($invoice['invoice_number'] ?? str_pad((string)($e['id'] ?? ''), 6, '0', STR_PAD_LEFT));
$totalPaid  = (float)($total_paid ?? 0);
$balance    = (float)($balance ?? 0);
$logoUrl    = htmlspecialchars($cfg['logo_url'] ?? '/assets/images/logo.svg', ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice <?= htmlspecialchars($invNo, ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="icon" type="image/svg+xml" href="<?= $logoUrl ?>">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { margin: 0; background: #fff; }
            .invoice-wrapper { box-shadow: none; margin: 0; max-width: 100%; }
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f0f0f0; color: #1a1a1a; font-size: 13px; }
        .invoice-wrapper { max-width: 850px; margin: 30px auto; background: #fff; padding: 15px 40px; box-shadow: 0 4px 20px rgba(0,0,0,0.12); }

        /* Header */
        .inv-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 18px; padding-bottom: 20px; border-bottom: 3px solid #1e293b; }
        .inv-logo-block { display: flex; align-items: center; gap: 14px; }
        .inv-logo-block img { max-height: 60px; max-width: 120px; object-fit: contain; }
        .inv-company-name { font-size: 20px; font-weight: 700; color: #1e293b; }
        .inv-title-center { text-align: center; }
        .inv-title-center h1 { font-size: 26px; font-weight: 800; color: #1e293b; letter-spacing: 2px; }
        .inv-meta-right { text-align: right; }
        .inv-number { font-size: 16px; font-weight: 700; color: #1e293b; }
        .inv-meta-right p { color: #555; font-size: 12px; margin-top: 4px; }

        /* Parties */
        .parties { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 8px; }
        .party { padding: 14px 16px; border: 1px solid #e2e8f0; border-radius: 6px; background: #f8fafc; }
        .party h3 { font-size: 10px; color: #888; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; }
        .party p { line-height: 1.8; color: #333; font-size: 13px; }
        .party strong { color: #1a1a1a; font-size: 14px; }

        /* Vehicle info bar */
        .vehicle-info { background: #1e293b; color: #fff; border-radius: 6px; padding: 12px 18px; margin-bottom: 12px; display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; }
        .vi-label { font-size: 9px; text-transform: uppercase; color: #94a3b8; letter-spacing: 0.5px; }
        .vi-value { font-weight: 600; color: #fff; margin-top: 2px; font-size: 13px; }

        /* Two-column line items */
        .line-cols { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
        .line-col h4 { font-size: 11px; text-transform: uppercase; color: #475569; font-weight: 700; letter-spacing: 0.5px; margin-bottom: 8px; padding-bottom: 6px; border-bottom: 2px solid #1e293b; }

        table { width: 100%; border-collapse: collapse; }
        th { background: #f1f5f9; color: #475569; padding: 8px 10px; text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: 0.3px; }
        td { padding: 7px 10px; border-bottom: 1px solid #f0f0f0; font-size: 13px; }
        tr:last-child td { border-bottom: none; }
        .text-right { text-align: right; }
        .col-subtotal { background: #f8fafc; font-weight: 600; padding: 7px 10px; border-top: 2px solid #e2e8f0; text-align: right; font-size: 13px; }

        /* Totals box */
        .totals-box { display: flex; justify-content: flex-end; margin-bottom: 24px; }
        .totals-inner { min-width: 300px; border: 1px solid #e2e8f0; border-radius: 6px; overflow: hidden; }
        .total-line { display: flex; justify-content: space-between; padding: 7px 14px; border-bottom: 1px solid #f0f0f0; font-size: 13px; }
        .total-line span:first-child { color: #555; }
        .total-line.grand { background: #1e293b; color: #fff; font-weight: 700; font-size: 15px; padding: 10px 14px; }
        .total-line.grand span { color: #fff; }

        /* Payments */
        .payments-section { margin-bottom: 24px; }
        .payments-section h3 { font-size: 11px; text-transform: uppercase; color: #888; letter-spacing: 1px; margin-bottom: 8px; }

        /* Balance box */
        .balance-box { border-radius: 8px; padding: 14px 18px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; background: #fff7ed; border: 2px solid #f97316; }
        .balance-box.paid { background: #f0fdf4; border-color: #22c55e; }
        .balance-label { color: #555; font-size: 13px; font-weight: 600; }
        .balance-amount { font-size: 22px; font-weight: 700; color: #f97316; }
        .balance-box.paid .balance-amount { color: #16a34a; }

        /* Next service */
        .next-service { text-align: center; margin-bottom: 20px; padding: 10px; background: #f1f5f9; border-radius: 6px; font-size: 13px; color: #475569; }
        .next-service strong { color: #1e293b; }

        /* Signatures */
        .signatures { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-top: 15px; padding-top: 0px; border-top: 1px solid #e2e8f0; }
        .sig-block { text-align: center; }
        .sig-line { border-top: 1px solid #1e293b; margin: 25px 20px 8px; }
        .sig-label { font-size: 12px; color: #555; }

        /* Print button bar */
        .no-print { background: #1e293b; padding: 12px; text-align: center; }
        .no-print a, .no-print button { display: inline-block; margin: 0 6px; padding: 8px 20px; border-radius: 6px; font-size: 13px; cursor: pointer; border: none; text-decoration: none; font-weight: 600; }
        .btn-print { background: #f97316; color: #fff; }
        .btn-back  { background: #475569; color: #fff; }
    </style>
</head>
<body>

<div class="no-print">
    <a href="/services/<?= (int)($e['id'] ?? 0) ?>" class="btn-back">← Back</a>
    <button onclick="window.print()" class="btn-print">Print Invoice</button>
</div>

<div class="invoice-wrapper">

    <!-- Header: logo left | title center | number right -->
    <div class="inv-header">
        <div class="inv-logo-block">
            <img src="<?= $logoUrl ?>" alt="Logo">
            <div class="inv-company-name"><?= htmlspecialchars($company, ENT_QUOTES, 'UTF-8') ?></div>
        </div>
        <div class="inv-title-center">
            <h1>VAT INVOICE</h1>
        </div>
        <div class="inv-meta-right">
            <div class="inv-number">No: <?= htmlspecialchars($invNo, ENT_QUOTES, 'UTF-8') ?></div>
            <p>Date: <?= htmlspecialchars($e['entry_date'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
            <?php if (!empty($e['delivery_date'])): ?>
            <p>Delivery: <?= htmlspecialchars($e['delivery_date'], ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Parties: Garage info left | Customer right -->
    <div class="parties">
        <div class="party">
            <h3>From</h3>
            <p>
                <strong><?= htmlspecialchars($company, ENT_QUOTES, 'UTF-8') ?></strong><br>
                <?php if (!empty($cfg['brn'])): ?>BRN: <?= htmlspecialchars($cfg['brn'], ENT_QUOTES, 'UTF-8') ?><br><?php endif; ?>
                <?php if (!empty($cfg['vat_reg'])): ?>VAT Reg: <?= htmlspecialchars($cfg['vat_reg'], ENT_QUOTES, 'UTF-8') ?><br><?php endif; ?>
                <?php if (!empty($cfg['address'])): ?><?= nl2br(htmlspecialchars($cfg['address'], ENT_QUOTES, 'UTF-8')) ?><br><?php endif; ?>
                <?php if (!empty($cfg['tel'])): ?>Tel: <?= htmlspecialchars($cfg['tel'], ENT_QUOTES, 'UTF-8') ?><br><?php endif; ?>
                <?php if (!empty($cfg['email'])): ?><?= htmlspecialchars($cfg['email'], ENT_QUOTES, 'UTF-8') ?><?php endif; ?>
            </p>
        </div>
        <div class="party">
            <h3>Bill To</h3>
            <p>
                <strong><?= htmlspecialchars($e['customer_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong><br>
                <?php if (!empty($e['customer_brn'])): ?>BRN: <?= htmlspecialchars($e['customer_brn'], ENT_QUOTES, 'UTF-8') ?><br><?php endif; ?>
                <?php if (!empty($e['customer_address'])): ?>Address: <?= htmlspecialchars($e['customer_address'], ENT_QUOTES, 'UTF-8') ?><br><?php endif; ?>
                <?php if (!empty($e['customer_vat_number'])): ?>VAT No: <?= htmlspecialchars($e['customer_vat_number'], ENT_QUOTES, 'UTF-8') ?><br><?php endif; ?>
                <?php if (!empty($e['registration_no'])): ?>Vehicule: <?= htmlspecialchars($e['registration_no'], ENT_QUOTES, 'UTF-8') ?><br><?php endif; ?>
                <?php if (!empty($e['vehicle_make']) || !empty($e['vehicle_make'])): ?>Make: <?= htmlspecialchars(trim(($e['vehicle_make'] ?? '') ), ENT_QUOTES, 'UTF-8') ?><br><?php endif; ?>
                <?php if (!empty($e['odometer_reading'])): ?>Odometer: <?= htmlspecialchars($e['odometer_reading'], ENT_QUOTES, 'UTF-8') ?><br><?php endif; ?>
                <?php if (!empty($e['next_servicing'])): ?>Next Servicing: <?= htmlspecialchars($e['next_servicing'], ENT_QUOTES, 'UTF-8') ?><?php endif; ?>
            </p>
        </div>
    </div>

    <!-- Vehicle info bar -->
    <!-- <div class="vehicle-info">
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
            <div class="vi-label">Chassis No.</div>
            <div class="vi-value"><?= htmlspecialchars($e['vehicle_chassis_no'] ?? '—', ENT_QUOTES, 'UTF-8') ?></div>
        </div>
        <div>
            <div class="vi-label">Odometer</div>
            <div class="vi-value"><?= $e['odometer'] ? number_format((int)$e['odometer']) . ' ' . ($e['distance_unit'] ?? 'km') : '—' ?></div>
        </div>
        <div>
            <div class="vi-label">Next Servicing At.</div>
            <div class="vi-value"><?= $e['odometer'] ? number_format((int)$e['next_servicing']) . ' ' . ($e['distance_unit'] ?? 'km') : '—' ?></div>
        </div>
    </div> -->

    <!-- Two-column: Spare Parts | Repairs -->
    <div class="line-cols">
        <div class="line-col">
            <h4>Spare Parts</h4>
            <table>
                <thead><tr><th>Description</th><th class="text-right">Amount</th></tr></thead>
                <tbody>
                <?php if (!empty($spare_parts)): ?>
                    <?php foreach ($spare_parts as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="text-right"><?= $fmt((float)($p['amount'] ?? 0)) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="2" style="color:#999;text-align:center;padding:10px">—</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
            <div class="col-subtotal"><?= $fmt((float)($e['total_parts'] ?? 0)) ?></div>
        </div>
        <div class="line-col">
            <h4>Repairs / Labour</h4>
            <table>
                <thead><tr><th>Description</th><th class="text-right">Amount</th></tr></thead>
                <tbody>
                <?php if (!empty($repairs)): ?>
                    <?php foreach ($repairs as $r): ?>
                    <tr>
                        <td><?= htmlspecialchars($r['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="text-right"><?= $fmt((float)($r['amount'] ?? 0)) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="2" style="color:#999;text-align:center;padding:10px">—</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
            <div class="col-subtotal"><?= $fmt((float)($e['total_labour'] ?? 0)) ?></div>
        </div>
    </div>

    <!-- Totals box -->
    <div class="totals-box">
        <div class="totals-inner">
            <div class="total-line"><span>Parts Total</span><span><?= $fmt((float)($e['total_parts'] ?? 0)) ?></span></div>
            <div class="total-line"><span>Labour Total</span><span><?= $fmt((float)($e['total_labour'] ?? 0)) ?></span></div>
            <?php if ((float)($e['discount_amount'] ?? 0) > 0): ?>
            <div class="total-line"><span>Discount</span><span>- <?= $fmt((float)($e['discount_amount'] ?? 0)) ?></span></div>
            <?php endif; ?>
            <div class="total-line"><span>Subtotal</span><span><?= $fmt((float)($e['subtotal'] ?? 0)) ?></span></div>
            <?php if ((float)($e['vat_percent'] ?? 0) > 0): ?>
            <div class="total-line"><span>VAT (<?= (float)($e['vat_percent'] ?? 0) ?>%)</span><span><?= $fmt((float)($e['vat_amount'] ?? 0)) ?></span></div>
            <?php endif; ?>
            <div class="total-line grand"><span>TOTAL</span><span><?= $fmt((float)($e['total_cost'] ?? 0)) ?></span></div>
        </div>
    </div>

    <!-- Payment history -->
    <?php if (!empty($payments)): ?>
    <!-- <div class="payments-section">
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
    </div> -->
    <?php endif; ?>

    <!-- Balance box -->
    <!-- <div class="balance-box <?= $balance <= 0 ? 'paid' : '' ?>">
        <div class="balance-label"><?= $balance <= 0 ? 'PAID IN FULL' : 'OUTSTANDING BALANCE' ?></div>
        <div class="balance-amount"><?= $fmt(abs($balance)) ?></div>
    </div> -->

    <!-- Next service -->
    <?php if (!empty($e['next_servicing'])): ?>
    <!-- <div class="next-service">
        Next Service at: <strong><?= number_format((int)$e['next_servicing']) . ' ' . ($e['distance_unit'] ?? 'km') ?></strong>
    </div> -->
    <?php endif; ?>

    <!-- Remarks -->
    <?php if (!empty($e['remarks'])): ?>
    <div style="margin-bottom:20px;">
        <p style="font-size:11px;text-transform:uppercase;color:#888;margin-bottom:6px;">Remarks</p>
        <p style="color:#555;line-height:1.6"><?= nl2br(htmlspecialchars($e['remarks'], ENT_QUOTES, 'UTF-8')) ?></p>
    </div>
    <?php endif; ?>

    <!-- Signatures -->
    <div class="signatures">
        <div class="sig-block">
            <div class="sig-line"></div>
            <div class="sig-label">Authorised Signature</div>
        </div>
        <div class="sig-block">
            <div class="sig-line"></div>
            <div class="sig-label">Customer Signature</div>
        </div>
    </div>

</div>
</body>
</html>
