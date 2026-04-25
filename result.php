<?php
session_start();
if(!isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }
require_once 'config/db.php';

$user_id = $_SESSION['user_id'];
$score   = isset($_POST['score'])  ? (int)$_POST['score']  : 0;
$total   = isset($_POST['total'])  ? (int)$_POST['total']  : 100;
$level   = isset($_POST['level'])  ? (int)$_POST['level']  : 1;
$pct     = $total > 0 ? round($score / $total * 100) : 0;

// Award XP
$stmt = $conn->prepare("UPDATE users SET xp = xp + ? WHERE id = ?");
$stmt->bind_param("ii", $score, $user_id);
$stmt->execute();

// Fetch updated user
$u = $conn->prepare("SELECT * FROM users WHERE id = ?");
$u->bind_param("i", $user_id);
$u->execute();
$user = $u->get_result()->fetch_assoc();

$passed = $pct >= 60;
$badge = '';
if($pct == 100) $badge = '🏆 Perfect Score!';
elseif($pct >= 80) $badge = '⭐ Excellent!';
elseif($pct >= 60) $badge = '✅ Passed!';
else $badge = '😅 Keep Practicing!';

$cert_unlocked = ($user['xp'] >= 50 && $pct >= 60);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>CyberQuest — Results</title>
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Fredoka+One&display=swap" rel="stylesheet">
<style>
:root{--green:#58CC02;--green-dark:#46a302;--blue:#1CB0F6;--purple:#CE82FF;--orange:#FF9600;--red:#FF4B4B;--yellow:#FFD900;--bg:#F7F9FC;--text:#1F2937;--text-light:#6B7280;}
*{margin:0;padding:0;box-sizing:border-box;}
body{font-family:'Nunito',sans-serif;background:var(--bg);min-height:100vh;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:2rem 1rem;}

.result-card{background:white;border-radius:28px;padding:2.5rem;width:100%;max-width:520px;box-shadow:0 24px 64px rgba(0,0,0,0.1);text-align:center;position:relative;overflow:hidden;animation:popIn 0.6s cubic-bezier(0.34,1.56,0.64,1) both;}
@keyframes popIn{from{opacity:0;transform:scale(0.7);}to{opacity:1;transform:scale(1);}}
.result-card::before{content:'';position:absolute;top:0;left:0;right:0;height:6px;background:<?= $passed ? 'linear-gradient(90deg,var(--green),#8EE000)' : 'linear-gradient(90deg,var(--orange),var(--red))' ?>;}

.result-emoji{font-size:5rem;display:block;margin-bottom:0.75rem;animation:bounce 1s ease-in-out infinite;}
@keyframes bounce{0%,100%{transform:translateY(0);}50%{transform:translateY(-12px);}}
.result-badge{display:inline-block;background:<?= $passed ? '#F0FFF0' : '#FFF7E6' ?>;color:<?= $passed ? 'var(--green-dark)' : 'var(--orange)' ?>;padding:0.4rem 1.2rem;border-radius:99px;font-weight:900;font-size:1rem;margin-bottom:1rem;}
.result-title{font-family:'Fredoka One',cursive;font-size:2.2rem;color:var(--text);margin-bottom:0.5rem;}
.result-sub{color:var(--text-light);font-weight:700;margin-bottom:2rem;}

/* Score circle */
.score-circle{width:140px;height:140px;border-radius:50%;background:conic-gradient(<?= $passed ? 'var(--green)' : 'var(--orange)' ?> <?= $pct*3.6 ?>deg, #E5E7EB 0deg);display:flex;align-items:center;justify-content:center;margin:0 auto 2rem;position:relative;}
.score-inner{width:108px;height:108px;background:white;border-radius:50%;display:flex;flex-direction:column;align-items:center;justify-content:center;}
.score-num{font-family:'Fredoka One',cursive;font-size:2.2rem;color:var(--text);}
.score-label{font-size:0.75rem;font-weight:800;color:var(--text-light);text-transform:uppercase;letter-spacing:0.05em;}

/* Stats row */
.res-stats{display:flex;justify-content:center;gap:1.5rem;margin-bottom:2rem;}
.res-stat{text-align:center;}
.res-stat-num{font-family:'Fredoka One',cursive;font-size:1.6rem;color:var(--text);}
.res-stat-label{font-size:0.8rem;font-weight:700;color:var(--text-light);text-transform:uppercase;}

/* XP earned */
.xp-earned{background:linear-gradient(135deg,#FFF9E6,#FFF3CC);border:2.5px solid var(--yellow);border-radius:16px;padding:1rem;margin-bottom:1.5rem;display:flex;align-items:center;justify-content:center;gap:0.75rem;}
.xp-earned span{font-family:'Fredoka One',cursive;font-size:1.3rem;color:#B8860B;}

/* Certificate banner */
.cert-banner{background:linear-gradient(135deg,#667eea,#764ba2);border-radius:16px;padding:1.25rem;margin-bottom:1.5rem;color:white;text-align:center;}
.cert-banner strong{font-family:'Fredoka One',cursive;font-size:1.1rem;display:block;margin-bottom:0.25rem;}
.cert-banner p{font-size:0.85rem;opacity:0.85;font-weight:700;}

/* Buttons */
.btn-row{display:flex;flex-direction:column;gap:0.75rem;}
.btn-green{padding:1rem;background:linear-gradient(135deg,var(--green),#46CC0A);border:none;border-radius:14px;color:white;font-family:'Nunito',sans-serif;font-size:1rem;font-weight:900;cursor:pointer;text-decoration:none;display:block;box-shadow:0 4px 0 var(--green-dark);transition:all 0.2s;letter-spacing:0.02em;}
.btn-green:hover{transform:translateY(-2px);box-shadow:0 6px 0 var(--green-dark);}
.btn-outline{padding:1rem;background:white;border:2.5px solid #E5E7EB;border-radius:14px;color:var(--text-light);font-family:'Nunito',sans-serif;font-size:1rem;font-weight:800;cursor:pointer;text-decoration:none;display:block;transition:all 0.2s;}
.btn-outline:hover{border-color:var(--blue);color:var(--blue);}
.btn-cert{padding:1rem;background:linear-gradient(135deg,#667eea,#764ba2);border:none;border-radius:14px;color:white;font-family:'Nunito',sans-serif;font-size:1rem;font-weight:900;cursor:pointer;text-decoration:none;display:block;box-shadow:0 4px 0 #4a5cc7;transition:all 0.2s;}
.btn-cert:hover{transform:translateY(-2px);}

/* Confetti */
.confetti-piece{position:fixed;width:10px;height:10px;border-radius:2px;animation:confettiFall linear forwards;}
@keyframes confettiFall{0%{transform:translateY(-10px) rotate(0deg);opacity:1;}100%{transform:translateY(100vh) rotate(720deg);opacity:0;}}
</style>
</head>
<body>

<div class="result-card">
  <span class="result-emoji"><?= $passed ? '🎉' : '😅' ?></span>
  <div class="result-badge"><?= $badge ?></div>
  <h2 class="result-title"><?= $passed ? 'Quiz Complete!' : 'Almost There!' ?></h2>
  <p class="result-sub">Level <?= $level ?> — Cybersecurity Quiz</p>

  <div class="score-circle">
    <div class="score-inner">
      <div class="score-num"><?= $pct ?>%</div>
      <div class="score-label">Score</div>
    </div>
  </div>

  <div class="res-stats">
    <div class="res-stat">
      <div class="res-stat-num"><?= $score/10 ?>/<?= $total/10 ?></div>
      <div class="res-stat-label">Correct</div>
    </div>
    <div class="res-stat">
      <div class="res-stat-num">🔥 <?= $user['streak'] ?></div>
      <div class="res-stat-label">Streak</div>
    </div>
    <div class="res-stat">
      <div class="res-stat-num">⭐ <?= $user['xp'] ?></div>
      <div class="res-stat-label">Total XP</div>
    </div>
  </div>

  <div class="xp-earned">
    <span>🌟 +<?= $score ?> XP Earned this Quiz!</span>
  </div>

  <?php if($cert_unlocked): ?>
  <div class="cert-banner">
    <strong>🏆 Certificate Unlocked!</strong>
    <p>You've earned a Certificate of Completion!</p>
  </div>
  <?php endif; ?>

  <div class="btn-row">
    <?php if($cert_unlocked): ?>
    <a href="certificate.php" class="btn-cert">📜 Download My Certificate</a>
    <?php endif; ?>
    <a href="quiz.php?level=<?= $level ?>" class="btn-green">🔄 Try Again</a>
    <a href="dashboard.php" class="btn-outline">🏠 Back to Dashboard</a>
  </div>
</div>

<script>
<?php if($passed): ?>
// Launch confetti!
const colors = ['#58CC02','#1CB0F6','#CE82FF','#FFD900','#FF9600'];
for(let i=0;i<60;i++){
  const c = document.createElement('div');
  c.className = 'confetti-piece';
  c.style.cssText = `
    left:${Math.random()*100}vw;
    background:${colors[Math.floor(Math.random()*colors.length)]};
    animation-duration:${1.5+Math.random()*2}s;
    animation-delay:${Math.random()*1.5}s;
    width:${6+Math.random()*10}px;
    height:${6+Math.random()*10}px;
    border-radius:${Math.random()>0.5?'50%':'2px'};
  `;
  document.body.appendChild(c);
  setTimeout(()=>c.remove(), 4000);
}
<?php endif; ?>
</script>
</body>
</html>
