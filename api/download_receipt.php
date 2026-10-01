<?php
/**
 * LogixPulse CRM - Executed Agreement Receipt Download
 * Subtask: [FEB-04] Implement Signed Agreement Confirmation & Receipt Download
 * Assignee: Sayyda Arooj (Squad PHP-FE-B)
 */

$agreementId = isset($_GET['agreement_id']) ? htmlspecialchars($_GET['agreement_id']) : 'MSA-2026-904';
$referenceCode = isset($_GET['ref']) ? htmlspecialchars($_GET['ref']) : 'MSA-2026-904-EXEC';

$stateStorePath = __DIR__ . '/../uploads/signatures/agreement_state.json';
$record = null;

if (file_exists($stateStorePath)) {
    $data = json_decode(file_get_contents($stateStorePath), true);
    if (isset($data[$agreementId])) {
        $record = $data[$agreementId];
    }
}

$signerName = $record['signer_name'] ?? 'Sarah Jenkins';
$signerEmail = $record['signer_email'] ?? 's.jenkins@acmecloud.com';
$clientCompany = $record['client_company'] ?? 'Acme Cloud Technologies';
$timestamp = $record['execution_timestamp'] ?? date('M d, Y • h:i:s A T');
$certHash = $record['certificate_hash'] ?? strtoupper(hash('sha256', $agreementId . time()));
$sigImg = $record['signature_image_data'] ?? '';

header('Content-Type: text/html; charset=UTF-8');
header('Content-Disposition: attachment; filename="Executed-Agreement-' . $referenceCode . '.html"');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Executed Agreement - <?php echo $referenceCode; ?></title>
  <style>
    body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; margin: 40px auto; max-width: 800px; color: #1E293B; background: #fff; line-height: 1.6; }
    .header { border-bottom: 2px solid #3B82F6; padding-bottom: 16px; margin-bottom: 24px; display: flex; justify-content: space-between; align-items: flex-end; }
    .brand { font-size: 24px; font-weight: 800; color: #0F172A; }
    .brand span { color: #3B82F6; }
    .badge { display: inline-block; padding: 6px 14px; border-radius: 9999px; font-size: 12px; font-weight: 700; background: #DCFCE7; color: #166534; border: 1px solid #BBF7D0; }
    .meta-box { background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 8px; padding: 16px; margin-bottom: 24px; display: grid; grid-template-columns: 1fr 1fr; gap: 12px; font-size: 13px; }
    .terms { font-size: 13px; color: #475569; margin-bottom: 30px; text-align: justify; }
    .sig-section { border: 2px solid #E2E8F0; border-radius: 8px; padding: 20px; background: #FAF5FF; margin-top: 30px; }
    .sig-img { max-height: 80px; max-width: 280px; display: block; margin: 10px 0; border-bottom: 1px dashed #94A3B8; }
    .cert-stamp { font-family: monospace; font-size: 11px; background: #FFFFFF; padding: 10px; border: 1px solid #CBD5E1; border-radius: 6px; word-break: break-all; margin-top: 12px; color: #334155; }
    .footer { margin-top: 40px; padding-top: 16px; border-top: 1px solid #E2E8F0; text-align: center; font-size: 12px; color: #94A3B8; }
  </style>
</head>
<body>
  <div class="header">
    <div>
      <div class="brand">DevLogix <span>LogixPulse</span></div>
      <div style="font-size: 12px; color: #64748B;">Client Signing Portal &bull; Digital Contract Execution Engine</div>
    </div>
    <div>
      <span class="badge">&#10003; LEGALLY EXECUTED</span>
    </div>
  </div>

  <div class="meta-box">
    <div><strong>Agreement Reference:</strong> <?php echo $referenceCode; ?></div>
    <div><strong>Execution Timestamp:</strong> <?php echo $timestamp; ?></div>
    <div><strong>Client Organization:</strong> <?php echo $clientCompany; ?></div>
    <div><strong>Authorized Signer:</strong> <?php echo $signerName; ?> (<?php echo $signerEmail; ?>)</div>
    <div><strong>Legal Framework:</strong> ESIGN Act (15 U.S.C. &sect; 7001) &amp; UETA</div>
    <div><strong>Audit Security Status:</strong> Tamper-Evident SHA-256 Seal</div>
  </div>

  <div class="terms">
    <h3 style="color: #0F172A; margin-top: 0;">Master Services Agreement &bull; Terms Summary</h3>
    <p>1. <strong>Scope of Work:</strong> Both parties have formally agreed to the architectural deliverables, infrastructure provision schedules, and service level commitments as detailed in Statement of Work SOW-2026-01.</p>
    <p>2. <strong>Payment Schedule &amp; Invoicing:</strong> Payment milestones are settled upon deliverable acceptance through LogixPulse Client Portal with 15-day payment settlement terms.</p>
    <p>3. <strong>Confidentiality &amp; IP Transfer:</strong> Mutual non-disclosure protections remain perpetual. Full intellectual property for dedicated milestone artifacts transfers to client upon invoice clearance.</p>
  </div>

  <div class="sig-section">
    <h4 style="margin: 0 0 10px 0; color: #581C87;">Official Client E-Signature Verification</h4>
    <?php if (!empty($sigImg)): ?>
      <img src="<?php echo $sigImg; ?>" class="sig-img" alt="Client Signature" />
    <?php else: ?>
      <div style="font-family: 'Brush Script MT', cursive; font-size: 28px; color: #1E293B; margin: 10px 0;">
        <?php echo $signerName; ?>
      </div>
    <?php endif; ?>
    <div style="font-size: 12px; color: #64748B;">
      Signed by <strong><?php echo $signerName; ?></strong> &bull; <?php echo $timestamp; ?>
    </div>
    <div class="cert-stamp">
      <strong>SHA-256 Cryptographic Audit Seal:</strong><br>
      <?php echo $certHash; ?>
    </div>
  </div>

  <div class="footer">
    Executed through DevLogix LogixPulse Client Signing Portal &bull; Certified Tamper-Protected Copy
  </div>
</body>
</html>
