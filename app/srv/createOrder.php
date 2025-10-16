<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['error' => 'Use POST']); exit;
}

const VAT = 0.21;
const DB_PATH = __DIR__ . '/../../onlineOrders/onlineOrders.db';

function db_load(): array {
  if (!file_exists(DB_PATH)) { db_save([]); return []; }
  $data = @file_get_contents(DB_PATH);
  return $data ? unserialize($data) : [];
}

function db_save(array $data): bool {
  return file_put_contents(DB_PATH, serialize($data), LOCK_EX) !== false;
}

function calc_total_with_vat(array $items): float {
  $subtotal = 0;
  foreach ($items as $i) {
    $price = floatval($i['price'] ?? 0);
    $qty   = intval($i['qty'] ?? 0);
    if ($price > 0 && $qty > 0) $subtotal += $price * $qty;
  }
  return round($subtotal * (1 + VAT), 2);
}

$orderId  = trim($_POST['orderId']  ?? '');
$fullName = trim($_POST['fullName'] ?? '');
$address  = trim($_POST['address']  ?? '');
$email    = trim($_POST['email']    ?? '');
$phone    = trim($_POST['phone']    ?? '');
$names  = $_POST['productName'] ?? [];
$prices = $_POST['price'] ?? [];
$qtys   = $_POST['qty'] ?? [];

$items = [];
for ($i = 0; $i < count($names); $i++) {
  $n = trim($names[$i] ?? '');
  $p = floatval($prices[$i] ?? 0);
  $q = intval($qtys[$i] ?? 0);
  if ($n !== '' && $p > 0 && $q > 0) {
    $items[] = ['name'=>$n, 'price'=>$p, 'qty'=>$q];
  }
}

if ($orderId === '' || $fullName === '' || empty($items)) {
  http_response_code(400);
  echo json_encode(['error'=>'Missing orderId, fullName or items']); exit;
}

$total = calc_total_with_vat($items);
$db = db_load();
$db[$orderId] = [
  'orderId'=>$orderId,
  'customer'=>[
    'fullName'=>$fullName,
    'address'=>$address,
    'email'=>$email,
    'phone'=>$phone
  ],
  'items'=>$items,
  'vat'=>VAT,
  'total'=>$total,
  'createdAt'=>date('c')
];
db_save($db);

echo json_encode(['orderId'=>$orderId,'totalWithVAT'=>$total]);
