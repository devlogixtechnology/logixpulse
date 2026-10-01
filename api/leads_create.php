<?php
require __DIR__ . '/../db.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') out(['ok'=>false,'error'=>'POST only'], 405);
$in = body();
$name = trim($in['name'] ?? '');
$email = trim($in['email'] ?? '');
$phone = trim($in['phone'] ?? '');
$source = $in['source'] ?? '';
if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/^[+()\d\s-]{7,20}$/', $phone) || !in_array($source, ['Website','Referral','Campaign','Direct'], true)) {
  out(['ok'=>false,'error'=>'Please check the form fields'], 422);
}
try {
  $pdo->prepare('INSERT INTO leads(title,contact_name,email,phone,source,stage) VALUES(?,?,?,?,?,?)')
      ->execute([$name, $name, $email, $phone, $source, 'New Lead']);
  $id = (int)$pdo->lastInsertId();
  $pdo->prepare("INSERT INTO lead_activities(lead_id,type,body) VALUES(?, 'created', 'Lead created')")->execute([$id]);
  out(['ok'=>true,'lead'=>['id'=>$id,'name'=>$name,'email'=>$email,'phone'=>$phone,'source'=>$source,'stage'=>'New Lead']], 201);
} catch (Throwable $e) { out(['ok'=>false,'error'=>'Server error'], 500); }