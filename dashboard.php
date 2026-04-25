<?php
session_start();
if(!isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }
require_once 'config/db.php';

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$xp = $user['xp'];
$streak = $user['streak'];
$username = $user['username'];

// Level thresholds
$levels = [
  ['name'=>'Beginner','icon'=>'🐣','xp_needed'=>0,'color'=>'#58CC02','bg'=>'#F0FFF0'],
  ['name'=>'Explorer','icon'=>'🔍','xp_needed'=>50,'color'=>'#1CB0F6','bg'=>'#E3F8FF'],
  ['name'=>'Defender','icon'=>'🛡️','xp_needed'=>120,'color'=>'#CE82FF','bg'=>'#F7EEFF'],
  ['name'=>'Guardian','icon'=>'⚔️','xp_needed'=>220,'color'=>'#FF9600','bg'=>'#FFF7E6'],
  ['name'=>'Hacker Slayer','icon'=>'💀','xp_needed'=>350,'color'=>'#FF4B4B','bg'=>'#FFF0F0'],
];

$current_level = 0;
foreach($levels as $i => $lv) {
  if($xp >= $lv['xp_needed']) $current_level = $i;
}
$next_level_xp = isset($levels[$current_level+1]) ? $levels[$current_level+1]['xp_needed'] : $xp;
$curr_xp_start = $levels[$current_level]['xp_needed'];
$progress_pct = $next_level_xp > $curr_xp_start
  ? min(100, round(($xp - $curr_xp_start) / ($next_level_xp - $curr_xp_start) * 100))
  : 100;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>CyberQuest — Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Fredoka+One&display=swap" rel="stylesheet">
<style>
:root{--green:#58CC02;--green-dark:#46a302;--blue:#1CB0F6;--purple:#CE82FF;--orange:#FF9600;--red:#FF4B4B;--yellow:#FFD900;--bg:#F7F9FC;--text:#1F2937;--text-light:#6B7280;}
*{margin:0;padding:0;box-sizing:border-box;}
body{font-family:'Nunito',sans-serif;background:var(--bg);min-height:100vh;}

/* NAV */
nav{background:white;border-bottom:3px solid #E5E7EB;padding:0 2rem;display:flex;align-items:center;justify-content:space-between;height:68px;position:sticky;top:0;z-index:100;box-shadow:0 2px 12px rgba(0,0,0,0.06);}
.nav-logo{font-family:'Fredoka One',cursive;font-size:1.6rem;background:linear-gradient(135deg,var(--green),var(--blue));-webkit-background-clip:text;-webkit-text-fill-color:transparent;}
.nav-right{display:flex;align-items:center;gap:1.5rem;}
.nav-stat{display:flex;align-items:center;gap:0.4rem;font-weight:800;font-size:1rem;}
.nav-stat.streak{color:#FF9600;}
.nav-stat.xp{color:var(--green);}
.nav-avatar{width:40px;height:40px;background:linear-gradient(135deg,var(--purple),var(--blue));border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.2rem;font-weight:900;color:white;}
.btn-logout{padding:0.4rem 1rem;background:none;border:2.5px solid #E5E7EB;border-radius:10px;font-family:'Nunito',sans-serif;font-weight:800;color:var(--text-light);cursor:pointer;font-size:0.85rem;transition:all 0.2s;}
.btn-logout:hover{border-color:var(--red);color:var(--red);}

/* MAIN */
.main{max-width:900px;margin:0 auto;padding:2rem 1.5rem;}

/* HERO WELCOME */
.welcome-card{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);border-radius:24px;padding:2rem;color:white;display:flex;align-items:center;justify-content:space-between;margin-bottom:2rem;box-shadow:0 8px 32px rgba(102,126,234,0.35);animation:fadeUp 0.6s ease both;}
@keyframes fadeUp{from{opacity:0;transform:translateY(20px);}to{opacity:1;transform:translateY(0);}}
.welcome-text h2{font-family:'Fredoka One',cursive;font-size:2rem;margin-bottom:0.25rem;}
.welcome-text p{opacity:0.85;font-size:1rem;font-weight:600;}
.mascot{font-size:5rem;animation:bounce 2s ease-in-out infinite;}
@keyframes bounce{0%,100%{transform:translateY(0);}50%{transform:translateY(-12px);}}

/* STATS ROW */
.stats-row{display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:2rem;}
.stat-card{background:white;border-radius:20px;padding:1.25rem;text-align:center;box-shadow:0 2px 12px rgba(0,0,0,0.07);animation:fadeUp 0.6s ease both;border-bottom:4px solid;}
.stat-card:nth-child(1){border-color:var(--orange);animation-delay:0.1s;}
.stat-card:nth-child(2){border-color:var(--green);animation-delay:0.2s;}
.stat-card:nth-child(3){border-color:var(--blue);animation-delay:0.3s;}
.stat-icon{font-size:2rem;margin-bottom:0.4rem;}
.stat-num{font-family:'Fredoka One',cursive;font-size:2rem;color:var(--text);}
.stat-label{font-size:0.85rem;font-weight:700;color:var(--text-light);text-transform:uppercase;letter-spacing:0.05em;}

/* LEVEL PROGRESS */
.section-title{font-family:'Fredoka One',cursive;font-size:1.4rem;color:var(--text);margin-bottom:1rem;}
.level-card{background:white;border-radius:20px;padding:1.5rem;margin-bottom:2rem;box-shadow:0 2px 12px rgba(0,0,0,0.07);animation:fadeUp 0.6s ease 0.35s both;}
.level-info{display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;}
.level-badge{display:flex;align-items:center;gap:0.75rem;}
.level-badge-icon{font-size:2.2rem;}
.level-badge-text strong{font-family:'Fredoka One',cursive;font-size:1.3rem;color:var(--text);display:block;}
.level-badge-text span{font-size:0.9rem;color:var(--text-light);font-weight:700;}
.xp-label{font-weight:800;color:var(--green);font-size:1rem;}
.progress-track{background:#F3F4F6;border-radius:99px;height:18px;overflow:hidden;position:relative;}
.progress-fill{height:100%;border-radius:99px;background:linear-gradient(90deg,var(--green),#8EE000);transition:width 1.5s cubic-bezier(0.34,1.2,0.64,1);position:relative;}
.progress-fill::after{content:'';position:absolute;top:3px;left:8px;right:8px;height:5px;background:rgba(255,255,255,0.4);border-radius:99px;}

/* LEVELS GRID */
.levels-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:1rem;margin-bottom:2rem;}
.level-item{border-radius:20px;padding:1.25rem;text-align:center;cursor:pointer;transition:all 0.25s ease;position:relative;overflow:hidden;animation:popIn 0.5s cubic-bezier(0.34,1.56,0.64,1) both;border:3px solid transparent;}
@keyframes popIn{from{opacity:0;transform:scale(0.7);}to{opacity:1;transform:scale(1);}}
.level-item.unlocked{box-shadow:0 4px 16px rgba(0,0,0,0.1);}
.level-item.unlocked:hover{transform:translateY(-5px) scale(1.03);box-shadow:0 12px 28px rgba(0,0,0,0.15);}
.level-item.locked{background:#F3F4F6;border:3px dashed #D1D5DB;cursor:not-allowed;opacity:0.6;}
.level-item.current{border:3px solid var(--green);box-shadow:0 0 0 4px rgba(88,204,2,0.2),0 8px 24px rgba(88,204,2,0.2);}
.level-number{font-family:'Fredoka One',cursive;font-size:0.8rem;opacity:0.7;margin-bottom:0.3rem;text-transform:uppercase;letter-spacing:0.08em;}
.level-emoji{font-size:2.5rem;margin-bottom:0.4rem;display:block;}
.level-name{font-weight:800;font-size:0.9rem;color:var(--text);}
.lock-icon{font-size:1.2rem;margin-bottom:0.25rem;display:block;}
.current-badge{position:absolute;top:8px;right:8px;background:var(--green);color:white;font-size:0.65rem;font-weight:900;padding:2px 6px;border-radius:99px;text-transform:uppercase;}

/* START BUTTON */
.start-btn{display:block;width:100%;max-width:360px;margin:0 auto 2rem;padding:1.2rem;background:linear-gradient(135deg,var(--green),#46CC0A);border:none;border-radius:16px;color:white;font-family:'Nunito',sans-serif;font-size:1.15rem;font-weight:900;cursor:pointer;text-align:center;text-decoration:none;box-shadow:0 4px 0 var(--green-dark),0 8px 24px rgba(88,204,2,0.35);transition:all 0.25s ease;letter-spacing:0.02em;}
.start-btn:hover{transform:translateY(-3px);box-shadow:0 7px 0 var(--green-dark),0 14px 28px rgba(88,204,2,0.4);}
.start-btn:active{transform:translateY(2px);box-shadow:0 2px 0 var(--green-dark);}

@media(max-width:600px){.stats-row{grid-template-columns:1fr 1fr;}.welcome-card{flex-direction:column;text-align:center;}.mascot{font-size:3.5rem;}.levels-grid{grid-template-columns:1fr 1fr;}}
</style>
</head>
<body>

<nav>
  <div class="nav-logo">🛡️ CyberQuest</div>
  <div class="nav-right">
    <div class="nav-stat streak">🔥 <?= $streak ?></div>
    <div class="nav-stat xp">⭐ <?= $xp ?> XP</div>
    <div class="nav-avatar"><?= strtoupper(substr($username,0,1)) ?></div>
    <a href="logout.php"><button class="btn-logout">Logout</button></a>
  </div>
</nav>

<div class="main">

  <!-- Welcome -->
  <div class="welcome-card">
    <div class="welcome-text">
      <h2>Hey, <?= htmlspecialchars($username) ?>! 👋</h2>
      <p>Ready to level up your cybersecurity skills today?</p>
      <?php if($streak > 1): ?>
      <p style="margin-top:0.5rem;font-size:0.9rem;">🔥 You're on a <strong><?= $streak ?>-day streak!</strong> Keep it up!</p>
      <?php endif; ?>
    </div>
    <div class="mascot">🤖</div>
  </div>

  <!-- Stats -->
  <div class="stats-row">
    <div class="stat-card">
      <div class="stat-icon">🔥</div>
      <div class="stat-num"><?= $streak ?></div>
      <div class="stat-label">Day Streak</div>
    </div>
    <div class="stat-card">
      <div class="stat-icon">⭐</div>
      <div class="stat-num"><?= $xp ?></div>
      <div class="stat-label">Total XP</div>
    </div>
    <div class="stat-card">
      <div class="stat-icon"><?= $levels[$current_level]['icon'] ?></div>
      <div class="stat-num" style="font-size:1.3rem;"><?= $levels[$current_level]['name'] ?></div>
      <div class="stat-label">Your Level</div>
    </div>
  </div>

  <!-- XP Progress -->
  <p class="section-title">⚡ Your Progress</p>
  <div class="level-card">
    <div class="level-info">
      <div class="level-badge">
        <div class="level-badge-icon"><?= $levels[$current_level]['icon'] ?></div>
        <div class="level-badge-text">
          <strong><?= $levels[$current_level]['name'] ?></strong>
          <span>Current Level</span>
        </div>
      </div>
      <div class="xp-label">⭐ <?= $xp ?> / <?= $next_level_xp ?> XP</div>
    </div>
    <div class="progress-track">
      <div class="progress-fill" id="progressBar" style="width:0%"></div>
    </div>
    <?php if(isset($levels[$current_level+1])): ?>
    <p style="margin-top:0.75rem;font-size:0.9rem;color:var(--text-light);font-weight:700;">
      Next: <?= $levels[$current_level+1]['icon'] ?> <?= $levels[$current_level+1]['name'] ?> at <?= $levels[$current_level+1]['xp_needed'] ?> XP
    </p>
    <?php else: ?>
    <p style="margin-top:0.75rem;font-size:0.9rem;color:var(--green);font-weight:800;">🏆 MAX LEVEL REACHED!</p>
    <?php endif; ?>
  </div>

  <!-- Levels -->
  <p class="section-title">🗺️ Learning Path</p>
  <div class="levels-grid">
    <?php foreach($levels as $i => $lv):
      $unlocked = $xp >= $lv['xp_needed'];
      $is_current = ($i == $current_level);
      $cls = $unlocked ? 'unlocked' : 'locked';
      if($is_current) $cls .= ' current';
    ?>
    <div class="level-item <?= $cls ?>"
         style="<?= $unlocked ? "background:{$lv['bg']};border-color:{$lv['color']};" : '' ?>"
         <?= $unlocked ? "onclick=\"location.href='quiz.php?level=".($i+1)."'\"" : '' ?>>
      <?php if($is_current): ?><span class="current-badge">NOW</span><?php endif; ?>
      <div class="level-number">Level <?= $i+1 ?></div>
      <?php if($unlocked): ?>
        <span class="level-emoji"><?= $lv['icon'] ?></span>
      <?php else: ?>
        <span class="lock-icon">🔒</span>
      <?php endif; ?>
      <div class="level-name"><?= $lv['name'] ?></div>
      <?php if(!$unlocked): ?>
        <div style="font-size:0.75rem;color:var(--text-light);font-weight:700;margin-top:0.3rem;"><?= $lv['xp_needed'] ?> XP needed</div>
      <?php endif; ?>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- Start Quiz Button -->
  <a href="quiz.php?level=<?= $current_level+1 ?>" class="start-btn">
    🚀 Start Today's Quiz — Level <?= $current_level+1 ?>
  </a>

</div>

<script>
  // Animate progress bar on load
  window.addEventListener('load', () => {
    setTimeout(() => {
      document.getElementById('progressBar').style.width = '<?= $progress_pct ?>%';
    }, 300);
  });
</script>
</body>
</html>
