<?php

const DB_PATH = __DIR__ . '/../../onlineOrders/onlineOrders.db';

function db_load(): array {
  if (!file_exists(DB_PATH)) return [];
  $raw = @file_get_contents(DB_PATH);
  $arr = @unserialize($raw);
  return is_array($arr) ? $arr : [];
}
function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

$orders = db_load();

usort($orders, fn($a,$b) => strcmp($a['createdAt'] ?? '', $b['createdAt'] ?? ''));
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>View All Orders – The Tree of Books</title>
  <link rel="stylesheet" href="../cli/css/style.css" />
</head>
<body>
  <main class="container">
    <section class="card" style="max-width: 1000px; text-align:left;">
      <h1 class="app-title">All Orders</h1>
      <p class="tagline">One line per order, fields separated by “:”</p>

      <?php if (empty($orders)): ?>
        <div class="card" style="margin-top:1rem;">
          <p>No orders found.</p>
        </div>
      <?php else: ?>
        <div class="card" style="margin-top:1rem; line-height:1.6;">
          <?php foreach ($orders as $o): 
            $id   = $o['orderId'] ?? '';
            $name = $o['customer']['fullName'] ?? '';
            $tot  = $o['total'] ?? 0;
            $dt   = $o['createdAt'] ?? '';
          ?>
            <div><?= e($id) ?> : <?= e($name) ?> : €<?= number_format((float)$tot, 2) ?> : <?= e($dt) ?></div>
          <?php endforeach; ?>
        </div>

        <details class="card" style="margin-top:1rem;">
          <summary><strong>Show items per order</strong></summary>
          <div style="margin-top:.75rem;">
            <?php foreach ($orders as $o): ?>
              <div class="card" style="margin:.5rem 0; padding:1rem;">
                <div><strong>Order:</strong> <?= e($o['orderId'] ?? '') ?></div>
                <div><strong>Customer:</strong> <?= e($o['customer']['fullName'] ?? '') ?></div>
                <div><strong>Total:</strong> €<?= number_format((float)($o['total'] ?? 0), 2) ?></div>
                <div><strong>Date:</strong> <?= e($o['createdAt'] ?? '') ?></div>
                <div style="margin-top:.5rem;">
                  <strong>Items:</strong>
                  <ul style="margin-left:1.25rem;">
                    <?php foreach (($o['items'] ?? []) as $it): ?>
                      <li>
                        <?= e($it['name'] ?? '') ?>
                        — €<?= number_format((float)($it['price'] ?? 0), 2) ?>
                        × <?= (int)($it['qty'] ?? 0) ?>
                      </li>
                    <?php endforeach; ?>
                  </ul>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </details>
      <?php endif; ?>
    </section>

    <nav class="actions" style="margin-top:1.5rem;">
      <button class="btn" onclick="location.href='../cli/menu.html'">⬅ Back to Menu</button>
      <button class="btn" onclick="location.href='../cli/index.html'">Home</button>
    </nav>

    <footer class="footer">
      <small>© 2025 The Tree of Books Inc.</small>
    </footer>
  </main>
</body>
</html>
