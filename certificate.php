<?php
session_start();
if(!isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }
require_once 'config/db.php';

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$xp       = $user['xp'];
$username = $user['username'];

// ─── Level thresholds (must match dashboard.php) ───────────────
// Level 1: 0 XP  Level 2: 50  Level 3: 120  Level 4: 220  Level 5: 350
// To "complete" a level the user must have enough XP to reach the NEXT level
// i.e. completed all 5 levels means xp >= 350 (entered level 5) AND
// has at least passed level 5 quiz once (xp >= 350 + passing score of a quiz ≥ 60 = 60 pts)
// We'll gate on xp >= 450 as a simple all-5-levels-done threshold.
// (350 to unlock lvl5 + at least 60pts from lvl5 quiz with 60% pass = 60xp)

$CERT_XP_REQUIRED = 450;   // must have unlocked AND passed level 5
$levels_completed  = 0;
$thresholds = [0, 50, 120, 220, 350];
foreach ($thresholds as $t) {
    if ($xp >= $t) $levels_completed++;
}

$can_download = ($xp >= $CERT_XP_REQUIRED);
$date = date('F d, Y');

// ─── If not eligible, show locked page ───────────────────────
if (!$can_download) {
    $xp_needed = $CERT_XP_REQUIRED - $xp;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>CyberQuest — Certificate Locked</title>
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Fredoka+One&display=swap" rel="stylesheet">
<style>
:root{--green:#58CC02;--green-dark:#46a302;--blue:#1CB0F6;--purple:#CE82FF;--orange:#FF9600;--red:#FF4B4B;--yellow:#FFD900;--bg:#F7F9FC;--text:#1F2937;--text-light:#6B7280;}
*{margin:0;padding:0;box-sizing:border-box;}
body{font-family:'Nunito',sans-serif;background:var(--bg);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:2rem;}
.card{background:white;border-radius:28px;padding:2.5rem;max-width:520px;width:100%;box-shadow:0 24px 64px rgba(0,0,0,0.1);text-align:center;position:relative;overflow:hidden;}
.card::before{content:'';position:absolute;top:0;left:0;right:0;height:5px;background:linear-gradient(90deg,var(--purple),var(--blue),var(--green));}
.lock-emoji{font-size:4.5rem;display:block;margin-bottom:1rem;animation:pulse 2s ease-in-out infinite;}
@keyframes pulse{0%,100%{transform:scale(1);}50%{transform:scale(1.1);}}
h2{font-family:'Fredoka One',cursive;font-size:2rem;color:var(--text);margin-bottom:0.5rem;}
.subtitle{color:var(--text-light);font-weight:700;font-size:0.95rem;margin-bottom:2rem;}

/* Level progress */
.levels-row{display:flex;justify-content:center;gap:0.6rem;margin-bottom:2rem;flex-wrap:wrap;}
.lv{width:52px;height:52px;border-radius:14px;display:flex;flex-direction:column;align-items:center;justify-content:center;font-size:0.65rem;font-weight:900;text-transform:uppercase;letter-spacing:0.04em;gap:2px;}
.lv-done{background:#F0FFF0;border:2.5px solid var(--green);color:var(--green-dark);}
.lv-done .lv-icon::before{content:'✅';}
.lv-locked{background:#F3F4F6;border:2.5px dashed #D1D5DB;color:var(--text-light);}
.lv-locked .lv-icon::before{content:'🔒';}
.lv-icon{font-size:1.2rem;}

/* XP bar */
.xp-section{background:#F7F9FC;border-radius:16px;padding:1.25rem;margin-bottom:2rem;}
.xp-info{display:flex;justify-content:space-between;font-weight:800;font-size:0.9rem;color:var(--text-light);margin-bottom:0.6rem;}
.xp-info span:last-child{color:var(--purple);font-weight:900;}
.prog-track{background:#E5E7EB;border-radius:99px;height:14px;overflow:hidden;}
.prog-fill{height:100%;border-radius:99px;background:linear-gradient(90deg,var(--purple),var(--blue));transition:width 1.5s cubic-bezier(0.34,1.2,0.64,1);}
.xp-hint{font-size:0.82rem;color:var(--text-light);font-weight:700;margin-top:0.6rem;}

.btn-go{display:block;padding:1rem;background:linear-gradient(135deg,var(--green),#46CC0A);border:none;border-radius:14px;color:white;font-family:'Nunito',sans-serif;font-size:1rem;font-weight:900;cursor:pointer;text-decoration:none;box-shadow:0 4px 0 var(--green-dark);transition:all 0.2s;margin-bottom:0.75rem;}
.btn-go:hover{transform:translateY(-2px);box-shadow:0 6px 0 var(--green-dark);}
.btn-back{display:block;padding:0.9rem;background:white;border:2.5px solid #E5E7EB;border-radius:14px;color:var(--text-light);font-family:'Nunito',sans-serif;font-size:0.95rem;font-weight:800;cursor:pointer;text-decoration:none;transition:all 0.2s;}
.btn-back:hover{border-color:var(--blue);color:var(--blue);}
</style>
</head>
<body>
<div class="card">
  <span class="lock-emoji">🏆</span>
  <h2>Certificate Locked</h2>
  <p class="subtitle">Complete all 5 levels to unlock your official Certificate of Completion!</p>

  <div class="levels-row">
    <?php
    $lv_names = ['Beginner','Explorer','Defender','Guardian','H.Slayer'];
    for($i=1;$i<=5;$i++):
      $done = $xp >= $thresholds[$i-1];
    ?>
    <div class="lv <?= $done?'lv-done':'lv-locked' ?>">
      <span class="lv-icon"></span>
      <span>Lv<?=$i?></span>
      <span style="font-size:0.55rem;"><?=$lv_names[$i-1]?></span>
    </div>
    <?php endfor; ?>
  </div>

  <div class="xp-section">
    <div class="xp-info">
      <span>Your XP: ⭐ <?= $xp ?></span>
      <span>Goal: <?= $CERT_XP_REQUIRED ?> XP</span>
    </div>
    <div class="prog-track">
      <div class="prog-fill" id="certProg" style="width:0%"></div>
    </div>
    <div class="xp-hint">📍 You need <strong><?= $xp_needed ?> more XP</strong> — keep quizzing to unlock!</div>
  </div>

  <a href="dashboard.php" class="btn-go">🚀 Continue Learning</a>
  <a href="dashboard.php" class="btn-back">← Back to Dashboard</a>
</div>

<script>
setTimeout(()=>{
  document.getElementById('certProg').style.width='<?= min(100,round($xp/$CERT_XP_REQUIRED*100)) ?>%';
},300);
</script>
</body>
</html>
<?php
    exit();
}

// ─── ELIGIBLE — Generate the PDF ──────────────────────────────
$fpdf_path = __DIR__ . '/fpdf/fpdf.php';
if(!file_exists($fpdf_path)) {
    showFPDFInstructions($username, $xp, $date);
    exit();
}

require($fpdf_path);

class CyberQuestCertPDF extends FPDF {

    // Helper: draw a rounded rectangle
    function RoundedRect($x,$y,$w,$h,$r,$style='') {
        $k=$this->k; $hp=$this->h;
        if($style=='F') $op='f'; elseif($style=='FD'||$style=='DF') $op='B'; else $op='S';
        $MyArc = 4/3*(sqrt(2)-1);
        $this->_out(sprintf('%.2F %.2F m',($x+$r)*$k,($hp-$y)*$k));
        $xc=$x+$w-$r; $yc=$y+$r;
        $this->_out(sprintf('%.2F %.2F l',($x+$w-$r)*$k,($hp-$y)*$k));
        $this->_Arc($xc+$r*$MyArc,$yc-$r,$xc+$r,$yc-$r*$MyArc,$xc+$r,$yc);
        $xc=$x+$w-$r; $yc=$y+$h-$r;
        $this->_out(sprintf('%.2F %.2F l',($x+$w)*$k,($hp-($y+$h-$r))*$k));
        $this->_Arc($xc+$r,$yc+$r*$MyArc,$xc+$r*$MyArc,$yc+$r,$xc,$yc+$r);
        $xc=$x+$r; $yc=$y+$h-$r;
        $this->_out(sprintf('%.2F %.2F l',($x+$r)*$k,($hp-($y+$h))*$k));
        $this->_Arc($xc-$r*$MyArc,$yc+$r,$xc-$r,$yc+$r*$MyArc,$xc-$r,$yc);
        $xc=$x+$r; $yc=$y+$r;
        $this->_out(sprintf('%.2F %.2F l',($x)*$k,($hp-($y+$r))*$k));
        $this->_Arc($xc-$r,$yc-$r*$MyArc,$xc-$r*$MyArc,$yc-$r,$xc,$yc-$r);
        $this->_out($op);
    }

    function _Arc($x1,$y1,$x2,$y2,$x3,$y3) {
        $h=$this->h;
        $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c ',
            $x1*$this->k,($h-$y1)*$this->k,
            $x2*$this->k,($h-$y2)*$this->k,
            $x3*$this->k,($h-$y3)*$this->k));
    }

    function Header() {}
    function Footer() {}

    // Draw a filled rectangle with RGB
    function ColorRect($x,$y,$w,$h,$r,$g,$b) {
        $this->SetFillColor($r,$g,$b);
        $this->Rect($x,$y,$w,$h,'F');
    }
}

$pdf = new CyberQuestCertPDF('L','mm','A4');
$pdf->AddPage();
$W = 297; $H = 210;

// ── BACKGROUND ──────────────────────────────────────────────
// Deep navy gradient simulation (layered rects)
for($i=0;$i<20;$i++){
    $r = (int)(8 + $i*0.8); $g = (int)(8 + $i*0.6); $b = (int)(35 + $i*1.2);
    $pdf->SetFillColor($r,$g,$b);
    $pdf->Rect(0,$i*($H/20),$W,$H/20,'F');
}

// ── DECORATIVE CORNER ORNAMENTS ─────────────────────────────
// Top-left triangle ornament
$pdf->SetFillColor(255,215,0);
$pdf->Rect(0,0,35,35,'F');
$pdf->SetFillColor(10,15,45);
$pdf->Rect(3,3,29,29,'F');
$pdf->SetFillColor(255,215,0);
$pdf->Rect(6,6,20,2,'F');
$pdf->Rect(6,6,2,20,'F');

// Top-right corner
$pdf->SetFillColor(255,215,0);
$pdf->Rect($W-35,0,35,35,'F');
$pdf->SetFillColor(10,15,45);
$pdf->Rect($W-32,3,29,29,'F');
$pdf->SetFillColor(255,215,0);
$pdf->Rect($W-26,6,20,2,'F');
$pdf->Rect($W-8,6,2,20,'F');

// Bottom-left corner
$pdf->SetFillColor(255,215,0);
$pdf->Rect(0,$H-35,35,35,'F');
$pdf->SetFillColor(10,15,45);
$pdf->Rect(3,$H-32,29,29,'F');
$pdf->SetFillColor(255,215,0);
$pdf->Rect(6,$H-26,20,2,'F');
$pdf->Rect(6,$H-8,2,20,'F');

// Bottom-right corner
$pdf->SetFillColor(255,215,0);
$pdf->Rect($W-35,$H-35,35,35,'F');
$pdf->SetFillColor(10,15,45);
$pdf->Rect($W-32,$H-32,29,29,'F');
$pdf->SetFillColor(255,215,0);
$pdf->Rect($W-26,$H-26,20,2,'F');
$pdf->Rect($W-8,$H-26,2,20,'F');

// ── OUTER GOLD BORDER ───────────────────────────────────────
$pdf->SetDrawColor(255,215,0);
$pdf->SetLineWidth(2.5);
$pdf->Rect(10,10,$W-20,$H-20);

// ── INNER THIN BORDER ───────────────────────────────────────
$pdf->SetDrawColor(100,120,200);
$pdf->SetLineWidth(0.5);
$pdf->Rect(14,14,$W-28,$H-28);

// ── TOP COLOUR BAR ───────────────────────────────────────────
// Green-to-blue gradient bar
$seg = ($W-28)/6;
$colors = [[88,204,2],[50,190,80],[28,176,246],[100,130,255],[206,130,255],[255,150,0]];
foreach($colors as $ci=>$c){
    $pdf->SetFillColor($c[0],$c[1],$c[2]);
    $pdf->Rect(14+$ci*$seg,14,$seg,6,'F');
}

// ── BOTTOM COLOUR BAR ────────────────────────────────────────
foreach(array_reverse($colors) as $ci=>$c){
    $pdf->SetFillColor($c[0],$c[1],$c[2]);
    $pdf->Rect(14+$ci*$seg,$H-20,$seg,6,'F');
}

// ── SHIELD WATERMARK (background) ────────────────────────────
$pdf->SetFont('Helvetica','B',110);
$pdf->SetTextColor(20,25,60);
$pdf->SetXY(80,45);
$pdf->Cell($W-160,110,'#',0,0,'C');

// ── ACADEMY NAME ─────────────────────────────────────────────
$pdf->SetFont('Helvetica','B',10);
$pdf->SetTextColor(88,204,2);
$pdf->SetXY(0,22);
$pdf->Cell($W,8,'C Y B E R Q U E S T   A C A D E M Y',0,1,'C');

// ── TITLE ────────────────────────────────────────────────────
$pdf->SetFont('Helvetica','B',34);
$pdf->SetTextColor(255,255,255);
$pdf->SetXY(0,35);
$pdf->Cell($W,14,'Certificate of Completion',0,1,'C');

// ── SUBTITLE ─────────────────────────────────────────────────
$pdf->SetFont('Helvetica','',12);
$pdf->SetTextColor(180,190,255);
$pdf->SetXY(0,53);
$pdf->Cell($W,8,'This is to proudly certify that the following student',0,1,'C');

// ── RECIPIENT NAME ───────────────────────────────────────────
$pdf->SetFont('Helvetica','B',42);
$pdf->SetTextColor(255,215,0);
$pdf->SetXY(0,64);
$pdf->Cell($W,18,$username,0,1,'C');

// Name underline
$name_w = $pdf->GetStringWidth($username) + 24;
$pdf->SetDrawColor(255,215,0);
$pdf->SetLineWidth(1.2);
$pdf->Line(($W-$name_w)/2, 83, ($W+$name_w)/2, 83);

// ── ACHIEVEMENT TEXT ─────────────────────────────────────────
$pdf->SetFont('Helvetica','',11);
$pdf->SetTextColor(200,215,255);
$pdf->SetXY(40,87);
$pdf->MultiCell($W-80,7,
    'has successfully completed all five levels of the CyberQuest Cybersecurity'."\n".
    'Learning Program, mastering digital safety, threat awareness, and advanced cybersecurity principles.',
    0,'C');

// ── LEVEL BADGES ROW ─────────────────────────────────────────
$badge_data = [
    ['L1','Beginner',    88,204,2],
    ['L2','Explorer',   28,176,246],
    ['L3','Defender',  206,130,255],
    ['L4','Guardian',  255,150,0],
    ['L5','H.Slayer',  255,75,75],
];
$bw=28; $bh=12; $gap=6;
$total_bw = count($badge_data)*($bw+$gap)-$gap;
$bx_start = ($W-$total_bw)/2;
$by = 108;
foreach($badge_data as $bi=>$b){
    $bx = $bx_start + $bi*($bw+$gap);
    $pdf->SetFillColor($b[2],$b[3],$b[4]);
    $pdf->Rect($bx,$by,$bw,$bh,'F');
    $pdf->SetTextColor(255,255,255);
    $pdf->SetFont('Helvetica','B',7);
    $pdf->SetXY($bx,$by+1);
    $pdf->Cell($bw,5,$b[0],0,0,'C');
    $pdf->SetFont('Helvetica','',5.5);
    $pdf->SetXY($bx,$by+6);
    $pdf->Cell($bw,5,$b[1],0,0,'C');
}

// ── XP BADGE ─────────────────────────────────────────────────
$pdf->SetFillColor(30,50,20);
$pdf->Rect(($W-48)/2,123,48,10,'F');
$pdf->SetDrawColor(88,204,2);
$pdf->SetLineWidth(0.5);
$pdf->Rect(($W-48)/2,123,48,10);
$pdf->SetFont('Helvetica','B',9);
$pdf->SetTextColor(88,204,2);
$pdf->SetXY(($W-48)/2,124);
$pdf->Cell(48,8,'TOTAL XP: '.$xp.' pts',0,0,'C');

// ── HORIZONTAL RULE ──────────────────────────────────────────
$pdf->SetDrawColor(60,70,120);
$pdf->SetLineWidth(0.4);
$pdf->Line(30,137,$W-30,137);

// ── DATE + AUTHORISED BY ─────────────────────────────────────
$col_w = ($W-60)/2;
// Date column
$pdf->SetFont('Helvetica','',8);
$pdf->SetTextColor(140,150,200);
$pdf->SetXY(30,140);
$pdf->Cell($col_w,6,'Date of Completion',0,0,'C');
$pdf->SetFont('Helvetica','B',11);
$pdf->SetTextColor(255,255,255);
$pdf->SetXY(30,147);
$pdf->Cell($col_w,6,$date,0,0,'C');
$pdf->SetDrawColor(150,160,220);
$pdf->SetLineWidth(0.4);
$pdf->Line(50,155,$col_w+10,155);

// Cert ID
$cert_id = 'CQ-'.strtoupper(substr(md5($username.$xp),0,8));
$pdf->SetFont('Helvetica','',8);
$pdf->SetTextColor(100,110,160);
$pdf->SetXY(30,158);
$pdf->Cell($col_w,5,'Certificate ID: '.$cert_id,0,0,'C');

// Auth column
$pdf->SetFont('Helvetica','',8);
$pdf->SetTextColor(140,150,200);
$pdf->SetXY(30+$col_w,140);
$pdf->Cell($col_w,6,'Authorised By',0,0,'C');
$pdf->SetFont('Helvetica','B',11);
$pdf->SetTextColor(255,215,0);
$pdf->SetXY(30+$col_w,147);
$pdf->Cell($col_w,6,'CyberQuest Academy',0,0,'C');
$pdf->SetDrawColor(150,160,220);
$pdf->Line(30+$col_w+20,155,$W-30,155);

$pdf->SetFont('Helvetica','I',7.5);
$pdf->SetTextColor(80,90,140);
$pdf->SetXY(30+$col_w,158);
$pdf->Cell($col_w,5,'Director of Cybersecurity Education',0,0,'C');

// ── TAGLINE ──────────────────────────────────────────────────
$pdf->SetFont('Helvetica','I',8.5);
$pdf->SetTextColor(80,90,150);
$pdf->SetXY(0,$H-16);
$pdf->Cell($W,5,'"Securing the digital world — one learner at a time."',0,0,'C');

// ── OUTPUT ───────────────────────────────────────────────────
$safe_name = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $username);
$pdf->Output('D','CyberQuest_Certificate_'.$safe_name.'.pdf');
exit();

// ─── Fallback if FPDF not installed ──────────────────────────
function showFPDFInstructions($username, $xp, $date) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>CyberQuest — Install FPDF</title>
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@700;800;900&family=Fredoka+One&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{font-family:'Nunito',sans-serif;background:#F7F9FC;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:2rem;}
.card{background:white;border-radius:24px;padding:2.5rem;max-width:580px;width:100%;box-shadow:0 8px 32px rgba(0,0,0,0.1);}
.card::before{content:'';display:block;height:5px;background:linear-gradient(90deg,#667eea,#764ba2,#58CC02);border-radius:99px;margin-bottom:2rem;}
h2{font-family:'Fredoka One',cursive;font-size:1.8rem;color:#1F2937;margin-bottom:0.4rem;}
p{color:#6B7280;font-weight:700;margin-bottom:1.5rem;line-height:1.6;}
.step{background:#F3F4F6;border-radius:14px;padding:0.9rem 1.1rem;margin-bottom:0.65rem;font-size:0.93rem;color:#374151;font-weight:700;display:flex;align-items:center;gap:0.75rem;}
.step-num{min-width:28px;height:28px;background:#667eea;color:white;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:0.85rem;font-weight:900;}
code{background:#1F2937;color:#58CC02;padding:0.15rem 0.5rem;border-radius:6px;font-size:0.88rem;}
.btn-row{display:flex;gap:0.75rem;margin-top:1.5rem;flex-wrap:wrap;}
a.btn-p{display:inline-block;padding:0.8rem 1.5rem;background:linear-gradient(135deg,#667eea,#764ba2);color:white;text-decoration:none;border-radius:12px;font-weight:900;font-size:0.95rem;}
a.btn-b{display:inline-block;padding:0.8rem 1.5rem;background:white;border:2.5px solid #E5E7EB;color:#6B7280;text-decoration:none;border-radius:12px;font-weight:800;font-size:0.95rem;}
</style>
</head>
<body>
<div class="card">
  <h2>🎉 You qualify for a Certificate!</h2>
  <p>Amazing work, <strong><?= htmlspecialchars($username) ?></strong>! You have <?= $xp ?> XP and completed all 5 levels.<br>
  You just need to install the free <strong>FPDF library</strong> once to generate your PDF:</p>

  <div class="step"><span class="step-num">1</span> Go to <strong>http://www.fpdf.org</strong> → click Download</div>
  <div class="step"><span class="step-num">2</span> Extract the downloaded zip file</div>
  <div class="step"><span class="step-num">3</span> Rename the extracted folder to exactly <code>fpdf</code></div>
  <div class="step"><span class="step-num">4</span> Copy it into your <code>CyberQuest/</code> folder so the path is <code>CyberQuest/fpdf/fpdf.php</code></div>
  <div class="step"><span class="step-num">5</span> Come back here and click the certificate button again 🏆</div>

  <div class="btn-row">
    <a href="certificate.php" class="btn-p">🔄 Try Again</a>
    <a href="dashboard.php" class="btn-b">← Dashboard</a>
  </div>
</div>
</body>
</html>
<?php
}
?>