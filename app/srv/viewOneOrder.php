<?php

const DB_PATH = __DIR__ . '/../../onlineOrders/onlineOrders.db';

function db_load(): array {
  if (!file_exists(DB_PATH)) return [];
  $raw = @file_get_contents(DB_PATH);
  $arr = @unserialize($raw);
  return is_array($arr) ? $arr : [];
}
function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

$orderId = trim($_GET['orderId'] ?? '');
$db = db_load();
$found = null;

if ($orderId !== '') {
  if (isset($db[$orderId])) {
    $found = $db[$orderId];
  } else {
    foreach ($db as $o) {
      if (($o['orderId'] ?? '') === $orderId) { $found = $o; break; }
    }
  }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>View One Order – The Tree of Books</title>
  <link rel="stylesheet" href="../cli/css/style.css" />
</head>
<body>
  <main class="container">
    <section class="card" style="max-width: 900px; text-align:left;">
      <h1 class="app-title">Order details</h1>
      <p class="tagline">Search result for ID: <strong><?= $orderId ? e($orderId) : '—' ?></strong></p>

      <?php if ($orderId === ''): ?>
        <div class="card" style="margin-top:1rem;">
          <p>Please provide an <strong>orderId</strong> parameter (e.g., <code>?orderId=ORD001</code>).</p>
        </div>

      <?php elseif (!$found): ?>
        <div class="card" style="margin-top:1rem;">
          <p>Order <strong><?= e($orderId) ?></strong> was not found.</p>
        </div>

      <?php else: ?>
        <div class="card" style="margin-top:1rem;">
          <h2>Customer</h2>
          <p><strong>Name:</strong> <?= e($found['customer']['fullName'] ?? '') ?></p>
          <p><strong>Address:</strong> <?= e($found['customer']['address'] ?? '') ?></p>
          <p><strong>Email:</strong> <?= e($found['customer']['email'] ?? '') ?></p>
          <p><strong>Phone:</strong> <?= e($found['customer']['phone'] ?? '') ?></p>
          <p><strong>Date:</strong> <?= e($found['createdAt'] ?? '') ?></p>
        </div>

        <div class="card" style="margin-top:1rem;">
          <h2>Items</h2>
          <ul style="margin-left:1.25rem;">
            <?php foreach (($found['items'] ?? []) as $it): 
              $p = (float)($it['price'] ?? 0);
              $q = (int)($it['qty'] ?? 0);
              $lineTotal = $p * $q;
            ?>
              <li>
                <?= e($it['name'] ?? '') ?>
                — €<?= number_format($p, 2) ?>
                × <?= $q ?>
                = €<?= number_format($lineTotal, 2) ?>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>

        <div class="card" style="margin-top:1rem;">
          <h2>Totals</h2>
          <p><strong>VAT:</strong> <?= isset($found['vat']) ? (float)$found['vat']*100 : 21 ?>%</p>
          <p><strong>Total (with VAT):</strong> €<?= number_format((float)($found['total'] ?? 0), 2) ?></p>
        </div>
      <?php endif; ?>
    </section>

    <nav class="actions" style="margin-top:1.5rem;">
      <button class="btn" onclick="location.href='../cli/viewOneOrder.html'">🔎 Search again</button>
      <button class="btn" onclick="location.href='../cli/menu.html'">⬅ Back to Menu</button>
      <button class="btn" onclick="location.href='../cli/index.html'">Home</button>
    </nav>

    <footer class="footer">
      <small>© 2025 The Tree of Books Inc. • Phase 4 – Activity 7</small>
    </footer>
  </main>
</body>
</html>
