<?php
session_start();
if(!isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }
require_once 'config/db.php';

$user_id = $_SESSION['user_id'];
$level = isset($_GET['level']) ? (int)$_GET['level'] : 1;
$level = max(1, min(5, $level));

// 5 questions per level
$stmt = $conn->prepare("SELECT * FROM questions WHERE level = ? ORDER BY RAND() LIMIT 5");
$stmt->bind_param("i", $level);
$stmt->execute();
$questions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

if(empty($questions)) {
    echo "<div style='font-family:Nunito,sans-serif;padding:3rem;text-align:center;'>
      <p style='font-size:1.2rem;font-weight:800;'>No questions for Level $level yet!</p>
      <a href='dashboard.php' style='color:#1CB0F6;font-weight:800;'>← Go back</a></div>";
    exit();
}

$_SESSION['quiz_questions'] = $questions;
$_SESSION['quiz_level'] = $level;

$themes = [
  1=>['color'=>'#58CC02','dark'=>'#46a302','bg'=>'#F0FFF0','name'=>'Beginner','icon'=>'🐣'],
  2=>['color'=>'#1CB0F6','dark'=>'#0e8fc7','bg'=>'#E3F8FF','name'=>'Explorer','icon'=>'🔍'],
  3=>['color'=>'#CE82FF','dark'=>'#a055e0','bg'=>'#F7EEFF','name'=>'Defender','icon'=>'🛡️'],
  4=>['color'=>'#FF9600','dark'=>'#cc7800','bg'=>'#FFF7E6','name'=>'Guardian','icon'=>'⚔️'],
  5=>['color'=>'#FF4B4B','dark'=>'#cc2424','bg'=>'#FFF0F0','name'=>'Hacker Slayer','icon'=>'💀'],
];
$t = $themes[$level];

function lighten($hex, $amt=40){
  $hex=ltrim($hex,'#');
  return '#'.sprintf('%02x%02x%02x',
    min(255,hexdec(substr($hex,0,2))+$amt),
    min(255,hexdec(substr($hex,2,2))+$amt),
    min(255,hexdec(substr($hex,4,2))+$amt));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>CyberQuest — Level <?=$level?> Quiz</title>
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Fredoka+One&display=swap" rel="stylesheet">
<style>
:root{
  --green:#58CC02;--green-dark:#46a302;--blue:#1CB0F6;
  --purple:#CE82FF;--orange:#FF9600;--red:#FF4B4B;--yellow:#FFD900;
  --bg:#F7F9FC;--text:#1F2937;--text-light:#6B7280;
  --lvl:<?=$t['color']?>;--lvl-dark:<?=$t['dark']?>;--lvl-bg:<?=$t['bg']?>;
  --lvl-light:<?=lighten($t['color'])?>;
}
*{margin:0;padding:0;box-sizing:border-box;}
body{
  font-family:'Nunito',sans-serif;background:var(--bg);
  min-height:100vh;display:flex;flex-direction:column;
  align-items:center;padding:1.5rem 1rem 3rem;
  position:relative;overflow-x:hidden;
}

/* ===== PARTICLES ===== */
.particle{position:fixed;border-radius:50%;pointer-events:none;z-index:0;animation:particleFloat linear infinite;}
@keyframes particleFloat{
  0%{transform:translateY(100vh) rotate(0deg);opacity:0;}
  10%{opacity:0.5;}90%{opacity:0.5;}
  100%{transform:translateY(-10vh) rotate(360deg);opacity:0;}
}

/* ===== HEADER ===== */
.quiz-header{width:100%;max-width:700px;display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem;position:relative;z-index:2;}
.back-btn{background:none;border:2.5px solid #E5E7EB;border-radius:12px;padding:0.5rem 1rem;font-family:'Nunito',sans-serif;font-weight:800;color:var(--text-light);cursor:pointer;font-size:0.9rem;transition:all 0.2s;text-decoration:none;}
.back-btn:hover{border-color:var(--lvl);color:var(--lvl);}
.quiz-logo{font-family:'Fredoka One',cursive;font-size:1.4rem;background:linear-gradient(135deg,var(--lvl),var(--blue));-webkit-background-clip:text;-webkit-text-fill-color:transparent;}
.hearts-area{font-size:1.35rem;display:flex;gap:0.2rem;align-items:center;transition:all 0.3s;}

/* ===== LEVEL BADGE ===== */
.level-badge{
  display:flex;align-items:center;gap:0.6rem;
  background:var(--lvl-bg);border:2.5px solid var(--lvl);
  border-radius:99px;padding:0.4rem 1.1rem;
  font-weight:900;font-size:0.9rem;color:var(--lvl-dark);
  margin-bottom:1.25rem;position:relative;z-index:2;
  box-shadow:0 2px 12px rgba(0,0,0,0.08);width:fit-content;
}

/* ===== COMBO BANNER ===== */
.combo-banner{
  width:100%;max-width:700px;border-radius:14px;padding:0.65rem 1rem;
  font-weight:900;font-size:1rem;display:none;
  align-items:center;justify-content:center;gap:0.5rem;
  margin-bottom:1rem;z-index:2;position:relative;
  background:linear-gradient(135deg,#FFF9C4,#FFEF88);
  border:2.5px solid var(--yellow);color:#856404;
}
.combo-banner.show{display:flex;animation:comboPop 0.4s cubic-bezier(0.34,1.56,0.64,1) both;}
@keyframes comboPop{from{opacity:0;transform:scale(0.7) rotate(-2deg);}to{opacity:1;transform:scale(1) rotate(0);}}

/* ===== PROGRESS ===== */
.progress-area{width:100%;max-width:700px;margin-bottom:1.5rem;position:relative;z-index:2;}
.progress-info{display:flex;justify-content:space-between;margin-bottom:0.5rem;font-weight:800;font-size:0.9rem;color:var(--text-light);}
.prog-track{background:#E5E7EB;border-radius:99px;height:16px;overflow:hidden;}
.prog-fill{
  height:100%;border-radius:99px;
  background:linear-gradient(90deg,var(--lvl),var(--lvl-light));
  transition:width 0.6s cubic-bezier(0.34,1.2,0.64,1);position:relative;
}
.prog-fill::after{content:'';position:absolute;top:3px;left:6px;right:6px;height:5px;background:rgba(255,255,255,0.45);border-radius:99px;}
.step-dots{display:flex;justify-content:space-around;margin-top:0.6rem;padding:0 4px;}
.step-dot{
  width:26px;height:26px;border-radius:50%;border:2.5px solid #E5E7EB;
  background:white;display:flex;align-items:center;justify-content:center;
  font-size:0.7rem;font-weight:900;color:#D1D5DB;transition:all 0.4s ease;
}
.step-dot.done{background:var(--lvl);border-color:var(--lvl);color:white;}
.step-dot.active{border-color:var(--lvl);color:var(--lvl);background:var(--lvl-bg);transform:scale(1.25);box-shadow:0 0 0 3px rgba(0,0,0,0.06);}

/* ===== QUESTION CARD ===== */
.q-card{
  background:white;border-radius:24px;padding:2rem;
  width:100%;max-width:700px;
  box-shadow:0 8px 32px rgba(0,0,0,0.08);
  margin-bottom:1.25rem;position:relative;overflow:hidden;
  z-index:2;animation:slideIn 0.45s cubic-bezier(0.34,1.2,0.64,1) both;
}
.q-card::before{content:'';position:absolute;top:0;left:0;right:0;height:5px;background:linear-gradient(90deg,var(--lvl),var(--blue));}
@keyframes slideIn{from{opacity:0;transform:translateY(35px) scale(0.97);}to{opacity:1;transform:translateY(0) scale(1);}}
.q-label{font-size:0.78rem;font-weight:900;text-transform:uppercase;letter-spacing:0.12em;color:var(--lvl);margin-bottom:0.6rem;}
.q-text{font-family:'Fredoka One',cursive;font-size:1.45rem;color:var(--text);line-height:1.4;}
.hint-row{margin-top:0.9rem;display:flex;align-items:center;gap:0.6rem;flex-wrap:wrap;}
.hint-btn{background:none;border:2px solid #E5E7EB;border-radius:99px;padding:0.3rem 0.9rem;font-family:'Nunito',sans-serif;font-size:0.8rem;font-weight:800;color:var(--orange);cursor:pointer;transition:all 0.2s;}
.hint-btn:hover:not(:disabled){background:#FFF7E6;border-color:var(--orange);}
.hint-btn:disabled{opacity:0.35;cursor:not-allowed;}
.hint-text{font-size:0.82rem;font-weight:700;color:var(--orange);display:none;}
.hint-text.show{display:inline;animation:fadeIn 0.3s ease;}
@keyframes fadeIn{from{opacity:0;transform:translateY(-4px);}to{opacity:1;transform:translateY(0);}}

/* ===== OPTIONS ===== */
.options{width:100%;max-width:700px;display:grid;grid-template-columns:1fr 1fr;gap:0.9rem;margin-bottom:1.25rem;position:relative;z-index:2;}
.option-btn{
  background:white;border:2.5px solid #E5E7EB;border-radius:18px;
  padding:1rem 1.2rem;font-family:'Nunito',sans-serif;
  font-size:0.95rem;font-weight:700;color:var(--text);
  cursor:pointer;text-align:left;display:flex;align-items:center;gap:0.65rem;
  box-shadow:0 4px 0 #E0E0E0,0 4px 12px rgba(0,0,0,0.04);
  position:relative;overflow:hidden;
  transition:border-color 0.2s, background 0.2s, box-shadow 0.2s;
}
.option-btn:hover:not(:disabled){
  border-color:var(--lvl);background:var(--lvl-bg);
  transform:translateY(-4px) scale(1.02);
  box-shadow:0 7px 0 var(--lvl-dark),0 10px 24px rgba(0,0,0,0.12);
  transition:transform 0.18s cubic-bezier(0.34,1.4,0.64,1), box-shadow 0.18s ease, border-color 0.2s;
}
/* CORRECT — the big grow-up pop */
.option-btn.correct{
  background:var(--lvl-bg);border-color:var(--lvl);color:var(--lvl-dark);
  animation:correctGrow 0.55s cubic-bezier(0.34,1.56,0.64,1) forwards;
  box-shadow:0 8px 0 var(--lvl-dark),0 16px 36px rgba(0,0,0,0.18);
}
@keyframes correctGrow{
  0%  {transform:scale(1) translateY(0);}
  25% {transform:scale(1.18) translateY(-10px);}
  55% {transform:scale(1.09) translateY(-5px);}
  80% {transform:scale(1.06) translateY(-3px);}
  100%{transform:scale(1.05) translateY(-2px);}
}
/* WRONG — aggressive shake */
.option-btn.wrong{
  background:#FFF0F0;border-color:var(--red);color:var(--red);
  animation:wrongShake 0.5s ease forwards;
  box-shadow:0 4px 0 #b01010,0 8px 20px rgba(255,75,75,0.25);
}
@keyframes wrongShake{
  0%,100%{transform:translateX(0);}
  15%{transform:translateX(-10px);}
  30%{transform:translateX(10px);}
  45%{transform:translateX(-7px);}
  60%{transform:translateX(7px);}
  75%{transform:translateX(-4px);}
  90%{transform:translateX(4px);}
}
.option-btn.reveal{
  background:#F0FFF0;border-color:var(--green);color:var(--green-dark);
  animation:revealGlow 0.6s ease;
}
@keyframes revealGlow{
  0%,100%{box-shadow:0 4px 0 var(--green-dark);}
  50%{box-shadow:0 4px 0 var(--green-dark),0 0 0 8px rgba(88,204,2,0.18);}
}
.option-key{
  width:30px;height:30px;border-radius:9px;background:#F3F4F6;
  display:flex;align-items:center;justify-content:center;
  font-weight:900;font-size:0.8rem;flex-shrink:0;
  transition:all 0.25s ease;
}
.option-btn.correct .option-key{background:var(--lvl);color:white;transform:rotate(-8deg) scale(1.15);}
.option-btn.wrong .option-key{background:var(--red);color:white;}
.option-btn.reveal .option-key{background:var(--green);color:white;}
/* tick / cross on answered buttons */
.option-btn.correct::after{content:'✓';position:absolute;top:8px;right:12px;font-size:1.2rem;font-weight:900;color:var(--lvl);animation:tickPop 0.4s cubic-bezier(0.34,1.56,0.64,1) 0.15s both;}
.option-btn.wrong::after{content:'✗';position:absolute;top:8px;right:12px;font-size:1.2rem;font-weight:900;color:var(--red);animation:tickPop 0.4s cubic-bezier(0.34,1.56,0.64,1) 0.15s both;}
@keyframes tickPop{from{opacity:0;transform:scale(0) rotate(-20deg);}to{opacity:1;transform:scale(1) rotate(0);}}

/* ===== FLOATING SCORE POP ===== */
.score-pop{
  position:fixed;font-family:'Fredoka One',cursive;font-size:2.2rem;
  pointer-events:none;z-index:999;
  text-shadow:0 2px 8px rgba(0,0,0,0.25);
  animation:scoreFly 1s ease-out forwards;
}
@keyframes scoreFly{
  0%{opacity:1;transform:translateY(0) scale(1);}
  50%{opacity:1;transform:translateY(-55px) scale(1.4);}
  100%{opacity:0;transform:translateY(-100px) scale(0.7);}
}

/* ===== STAR BURST ===== */
.starburst{position:fixed;pointer-events:none;z-index:999;font-size:1.5rem;animation:starFly 0.9s ease-out forwards;}
@keyframes starFly{0%{opacity:1;transform:translate(0,0) scale(1);}100%{opacity:0;transform:translate(var(--tx),var(--ty)) scale(0.2);}}

/* ===== FEEDBACK ===== */
.feedback{
  width:100%;max-width:700px;border-radius:18px;
  padding:1rem 1.25rem;font-weight:800;font-size:1rem;
  display:none;align-items:center;gap:0.75rem;
  margin-bottom:1rem;position:relative;z-index:2;
}
.feedback.show{display:flex;animation:fbSlide 0.35s cubic-bezier(0.34,1.2,0.64,1) both;}
@keyframes fbSlide{from{opacity:0;transform:translateY(12px);}to{opacity:1;transform:translateY(0);}}
.feedback.correct-fb{background:var(--lvl-bg);border:2.5px solid var(--lvl);color:var(--lvl-dark);}
.feedback.wrong-fb{background:#FFF0F0;border:2.5px solid var(--red);color:var(--red);}
.fb-icon{font-size:1.6rem;flex-shrink:0;}
.xp-badge{
  margin-left:auto;background:var(--lvl);color:white;
  padding:0.2rem 0.75rem;border-radius:99px;
  font-size:0.8rem;font-weight:900;flex-shrink:0;
  animation:xpPop 0.4s cubic-bezier(0.34,1.56,0.64,1) 0.2s both;
}
@keyframes xpPop{from{opacity:0;transform:scale(0.4);}to{opacity:1;transform:scale(1);}}

/* ===== NEXT BUTTON ===== */
.next-btn{
  display:none;width:100%;max-width:700px;padding:1.1rem;
  background:linear-gradient(135deg,var(--lvl),var(--lvl-light));
  border:none;border-radius:16px;color:white;
  font-family:'Nunito',sans-serif;font-size:1.1rem;font-weight:900;
  cursor:pointer;
  box-shadow:0 5px 0 var(--lvl-dark),0 8px 24px rgba(0,0,0,0.15);
  transition:all 0.25s ease;letter-spacing:0.03em;
  position:relative;z-index:2;
}
.next-btn.show{display:block;animation:btnAppear 0.4s cubic-bezier(0.34,1.56,0.64,1) both;}
@keyframes btnAppear{from{opacity:0;transform:scale(0.8) translateY(10px);}to{opacity:1;transform:scale(1) translateY(0);}}
.next-btn:hover{transform:translateY(-3px);box-shadow:0 8px 0 var(--lvl-dark),0 14px 28px rgba(0,0,0,0.18);}
.next-btn:active{transform:translateY(3px);box-shadow:0 2px 0 var(--lvl-dark);}

form{display:none;}

@media(max-width:520px){
  .options{grid-template-columns:1fr;}
  .q-text{font-size:1.15rem;}
}
</style>
</head>
<body>

<div class="quiz-header">
  <a href="dashboard.php" class="back-btn">← Back</a>
  <div class="quiz-logo">🛡️ CyberQuest</div>
  <div class="hearts-area" id="hearts">❤️❤️❤️</div>
</div>

<div class="level-badge"><?=$t['icon']?> Level <?=$level?> — <?=$t['name']?></div>

<div class="combo-banner" id="comboBanner">🔥 <span id="comboText">Combo!</span></div>

<div class="progress-area">
  <div class="progress-info">
    <span id="qCounter">Question 1 / <?=count($questions)?></span>
    <span id="scoreLabel">⭐ 0 XP</span>
  </div>
  <div class="prog-track"><div class="prog-fill" id="progFill" style="width:0%"></div></div>
  <div class="step-dots" id="stepDots"></div>
</div>

<div class="q-card" id="questionCard">
  <div class="q-label">Level <?=$level?> — <?=$t['name']?> Quiz</div>
  <div class="q-text" id="questionText"></div>
  <div class="hint-row">
    <button class="hint-btn" id="hintBtn" onclick="showHint()">💡 Use Hint (-5 XP)</button>
    <span class="hint-text" id="hintText"></span>
  </div>
</div>

<div class="options" id="optionsArea"></div>

<div class="feedback" id="feedback">
  <span class="fb-icon" id="fbIcon"></span>
  <span id="fbText"></span>
</div>

<button class="next-btn" id="nextBtn" onclick="nextQuestion()">Continue →</button>

<form method="POST" action="result.php" id="submitForm">
  <input type="hidden" name="score" id="finalScore">
  <input type="hidden" name="total" id="finalTotal">
  <input type="hidden" name="level" value="<?=$level?>">
</form>

<script>
const questions = <?=json_encode($questions)?>;
const TOTAL = questions.length;
const keys = ['A','B','C','D'];
let current = 0, score = 0, lives = 3, combo = 0, hintUsed = false;

/* ---- Particles ---- */
(function(){
  const colors = ['<?=$t['color']?>','#FFD900','#1CB0F6','#CE82FF','#FF9600'];
  for(let i=0;i<20;i++){
    const p = document.createElement('div');
    p.className = 'particle';
    const s = 6+Math.random()*10;
    p.style.cssText=`width:${s}px;height:${s}px;left:${Math.random()*100}vw;background:${colors[i%colors.length]};animation-duration:${12+Math.random()*14}s;animation-delay:${Math.random()*14}s;`;
    document.body.appendChild(p);
  }
})();

/* ---- Step dots ---- */
function buildDots(){
  const c = document.getElementById('stepDots');
  for(let i=0;i<TOTAL;i++){
    const d = document.createElement('div');
    d.className = 'step-dot' + (i===0?' active':'');
    d.id = 'dot'+i; d.textContent = i+1;
    c.appendChild(d);
  }
}

/* ---- Update dots ---- */
function updateDots(){
  for(let i=0;i<TOTAL;i++){
    const d = document.getElementById('dot'+i);
    if(!d) continue;
    if(i<current){d.className='step-dot done';d.textContent='✓';}
    else if(i===current){d.className='step-dot active';d.textContent=i+1;}
    else{d.className='step-dot';d.textContent=i+1;}
  }
}

/* ---- Load question ---- */
function loadQuestion(){
  hintUsed = false;
  document.getElementById('hintBtn').disabled = false;
  document.getElementById('hintText').className = 'hint-text';
  document.getElementById('hintText').textContent = '';
  document.getElementById('comboBanner').className = 'combo-banner';

  const q = questions[current];
  document.getElementById('questionText').textContent = q.question;
  document.getElementById('qCounter').textContent = `Question ${current+1} / ${TOTAL}`;
  document.getElementById('progFill').style.width = `${(current/TOTAL)*100}%`;
  updateDots();

  const opts = document.getElementById('optionsArea');
  opts.innerHTML = '';
  [q.option1,q.option2,q.option3,q.option4].forEach((opt,i)=>{
    const btn = document.createElement('button');
    btn.className = 'option-btn';
    btn.style.cssText = `animation:slideIn 0.4s cubic-bezier(0.34,1.2,0.64,1) ${i*0.07}s both;`;
    btn.innerHTML = `<span class="option-key">${keys[i]}</span>${opt}`;
    btn.onclick = ()=> selectAnswer(i+1, parseInt(q.correct_option), btn);
    opts.appendChild(btn);
  });

  document.getElementById('feedback').className = 'feedback';
  document.getElementById('nextBtn').className = 'next-btn';

  const card = document.getElementById('questionCard');
  card.style.animation='none'; void card.offsetWidth;
  card.style.animation='slideIn 0.45s cubic-bezier(0.34,1.2,0.64,1) both';
}

/* ---- Hint ---- */
function showHint(){
  if(hintUsed) return;
  hintUsed = true;
  document.getElementById('hintBtn').disabled = true;
  const correct = parseInt(questions[current].correct_option);
  let done = false;
  document.querySelectorAll('.option-btn').forEach((b,i)=>{
    if(!done && (i+1)!==correct){
      b.disabled=true; b.style.opacity='0.3'; b.style.transform='scale(0.93)';
      done=true;
    }
  });
  const ht = document.getElementById('hintText');
  ht.textContent = '1 wrong answer removed! (-5 XP if correct)';
  ht.className = 'hint-text show';
}

/* ---- Select answer ---- */
function selectAnswer(selected, correct, btn){
  document.querySelectorAll('.option-btn').forEach(b=>b.disabled=true);
  const allBtns = document.querySelectorAll('.option-btn');
  const fb = document.getElementById('feedback');

  if(selected===correct){
    btn.classList.add('correct');
    const gained = hintUsed ? 5 : 10;
    score += gained;
    combo++;
    document.getElementById('scoreLabel').textContent = `⭐ ${score} XP`;

    // Floating score pop
    const rect = btn.getBoundingClientRect();
    const pop = document.createElement('div');
    pop.className = 'score-pop';
    pop.textContent = '+'+gained+' XP ⭐';
    pop.style.color = '<?=$t['color']?>';
    pop.style.left = (rect.left+rect.width/2-50)+'px';
    pop.style.top  = (rect.top+window.scrollY-10)+'px';
    document.body.appendChild(pop);
    setTimeout(()=>pop.remove(),1100);

    // Star burst
    const cx = rect.left+rect.width/2, cy = rect.top+window.scrollY+rect.height/2;
    ['⭐','✨','💫','🌟','⚡','🎉','💥','🔥'].forEach((em,i)=>{
      const s = document.createElement('div');
      s.className='starburst'; s.textContent=em;
      const angle=(i/8)*360, dist=55+Math.random()*35;
      s.style.setProperty('--tx', Math.cos(angle*Math.PI/180)*dist+'px');
      s.style.setProperty('--ty', Math.sin(angle*Math.PI/180)*dist-40+'px');
      s.style.left=cx+'px'; s.style.top=cy+'px';
      s.style.animationDelay=(i*0.05)+'s';
      document.body.appendChild(s);
      setTimeout(()=>s.remove(),1000);
    });

    // Feedback
    fb.className='feedback correct-fb show';
    document.getElementById('fbIcon').textContent='🎉';
    const msgs=['Great job!','Awesome!','Nailed it!','You\'re on fire!','Brilliant!','Incredible!'];
    document.getElementById('fbText').innerHTML = msgs[Math.min(combo-1,msgs.length-1)]+` +${gained} XP`;
    const badge = document.createElement('span'); badge.className='xp-badge'; badge.textContent='+'+gained+' XP'; fb.appendChild(badge);

    // Combo banner
    if(combo>=2){
      const m={2:'🔥 2× Combo!',3:'💥 3× Combo! On fire!',4:'⚡ 4× Combo! Unstoppable!',5:'🏆 5× Perfect Combo!'};
      document.getElementById('comboText').textContent = m[Math.min(combo,5)]||`🔥 ${combo}× Combo!`;
      document.getElementById('comboBanner').className='combo-banner show';
    }

    // Mark dot
    const dot=document.getElementById('dot'+current);
    if(dot){dot.className='step-dot done';dot.textContent='✓';}

  } else {
    btn.classList.add('wrong');
    allBtns[correct-1].classList.add('reveal');
    combo = 0; lives--;
    // Heart shake
    const ha = document.getElementById('hearts');
    let h=''; for(let i=0;i<3;i++) h+=(i<lives?'❤️':'🖤');
    ha.textContent=h;
    ha.style.animation='none'; void ha.offsetWidth;
    ha.style.animation='wrongShake 0.4s ease';
    fb.className='feedback wrong-fb show';
    document.getElementById('fbIcon').textContent='❌';
    document.getElementById('fbText').innerHTML=`Wrong! Correct: <strong>${keys[correct-1]}</strong>`;
  }

  document.getElementById('nextBtn').className='next-btn show';
}

/* ---- Next ---- */
function nextQuestion(){
  current++;
  if(current>=TOTAL||lives<=0){
    document.getElementById('finalScore').value=score;
    document.getElementById('finalTotal').value=TOTAL*10;
    document.getElementById('submitForm').submit();
  } else {
    loadQuestion();
  }
}

buildDots();
loadQuestion();
</script>
</body>
</html>
