<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>⚠️ You've Been Phished! — CyberQuest</title>
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Fredoka+One&display=swap" rel="stylesheet">
<style>
:root{--red:#FF4B4B;--orange:#FF9600;--green:#58CC02;--blue:#1CB0F6;--text:#1F2937;--text-light:#6B7280;}
*{margin:0;padding:0;box-sizing:border-box;}
body{font-family:'Nunito',sans-serif;background:#0D0D0D;min-height:100vh;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:2rem;overflow:hidden;position:relative;}

/* Matrix rain background */
canvas{position:fixed;top:0;left:0;z-index:0;opacity:0.15;}

/* Warning overlay */
.warning-flash{position:fixed;inset:0;background:red;opacity:0;z-index:1;pointer-events:none;animation:flash 0.5s ease 0.2s 3;}
@keyframes flash{0%,100%{opacity:0;}50%{opacity:0.15;}}

.container{position:relative;z-index:2;width:100%;max-width:640px;}

/* Top warning bar */
.alert-bar{background:var(--red);color:white;text-align:center;padding:0.75rem;border-radius:12px 12px 0 0;font-weight:900;font-size:0.9rem;letter-spacing:0.05em;animation:blink 1s step-end infinite;}
@keyframes blink{0%,100%{opacity:1;}50%{opacity:0.6;}}

/* Main card */
.card{background:#1A1A2E;border:2px solid #FF4B4B;border-radius:0 0 24px 24px;padding:2rem;color:white;box-shadow:0 0 40px rgba(255,75,75,0.3);}

.warning-icon{font-size:4rem;text-align:center;display:block;margin-bottom:0.5rem;animation:shake 0.5s ease 0.5s 3;}
@keyframes shake{0%,100%{transform:translateX(0);}25%{transform:translateX(-8px);}75%{transform:translateX(8px);}}

h1{font-family:'Fredoka One',cursive;font-size:2.2rem;text-align:center;color:var(--red);margin-bottom:0.5rem;}
.subtitle{text-align:center;color:#aaa;font-weight:700;margin-bottom:2rem;font-size:0.95rem;}

/* Data exposed section */
.exposed-title{font-size:0.8rem;font-weight:900;text-transform:uppercase;letter-spacing:0.1em;color:var(--orange);margin-bottom:0.75rem;}
.data-grid{display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;margin-bottom:1.5rem;}
.data-item{background:#0D1117;border:1.5px solid #30363D;border-radius:12px;padding:0.9rem;animation:revealItem 0.4s ease both;}
.data-item:nth-child(1){animation-delay:0.5s;}
.data-item:nth-child(2){animation-delay:0.7s;}
.data-item:nth-child(3){animation-delay:0.9s;}
.data-item:nth-child(4){animation-delay:1.1s;}
.data-item:nth-child(5){animation-delay:1.3s;}
.data-item:nth-child(6){animation-delay:1.5s;}
@keyframes revealItem{from{opacity:0;transform:translateX(-20px);}to{opacity:1;transform:translateX(0);}}
.data-label{font-size:0.75rem;font-weight:800;color:#6B7280;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.3rem;}
.data-value{font-weight:800;color:#E6EDF3;font-size:0.9rem;word-break:break-all;}
.data-value.danger{color:var(--red);}
.data-value.warn{color:var(--orange);}

/* What could happen */
.danger-list{margin-bottom:1.75rem;}
.danger-item{display:flex;align-items:flex-start;gap:0.75rem;padding:0.75rem;background:#1F0000;border:1.5px solid #4A0000;border-radius:12px;margin-bottom:0.6rem;font-size:0.9rem;font-weight:700;color:#FFB3B3;}
.danger-item-icon{font-size:1.2rem;flex-shrink:0;}

/* Safety tips */
.tips-section{background:#001A0D;border:1.5px solid #1A4A2A;border-radius:16px;padding:1.25rem;margin-bottom:1.5rem;}
.tips-title{font-size:0.8rem;font-weight:900;text-transform:uppercase;letter-spacing:0.1em;color:var(--green);margin-bottom:0.75rem;}
.tip-item{display:flex;align-items:flex-start;gap:0.6rem;font-size:0.88rem;font-weight:700;color:#B3FFD1;margin-bottom:0.5rem;}

/* Buttons */
.btn-row{display:flex;flex-direction:column;gap:0.75rem;}
.btn-safe{display:block;padding:1rem;background:linear-gradient(135deg,var(--green),#46CC0A);border:none;border-radius:14px;color:white;font-family:'Nunito',sans-serif;font-size:1rem;font-weight:900;cursor:pointer;text-align:center;text-decoration:none;box-shadow:0 4px 0 #46a302;transition:all 0.2s;}
.btn-safe:hover{transform:translateY(-2px);}
.btn-learn{display:block;padding:1rem;background:transparent;border:2px solid #30363D;border-radius:14px;color:#8B949E;font-family:'Nunito',sans-serif;font-size:1rem;font-weight:800;cursor:pointer;text-align:center;text-decoration:none;transition:all 0.2s;}
.btn-learn:hover{border-color:var(--blue);color:var(--blue);}

/* Countdown */
.countdown{text-align:center;color:#6B7280;font-size:0.85rem;font-weight:700;margin-bottom:1rem;}
#countNum{color:var(--orange);font-weight:900;}

@media(max-width:500px){.data-grid{grid-template-columns:1fr;}}
</style>
</head>
<body>

<canvas id="matrix"></canvas>
<div class="warning-flash"></div>

<div class="container">
  <div class="alert-bar">⚠️ SIMULATION — THIS IS A CYBERQUEST EDUCATIONAL EXERCISE ⚠️</div>
  <div class="card">
    <span class="warning-icon">🎣</span>
    <h1>You Got Phished!</h1>
    <p class="subtitle">If this were a real attack, here's what a hacker would already know about you...</p>

    <div class="exposed-title">🔍 Data Exposed in Seconds:</div>
    <div class="data-grid">
      <div class="data-item">
        <div class="data-label">🌐 Your IP Address</div>
        <div class="data-value danger" id="ipVal">Detecting...</div>
      </div>
      <div class="data-item">
        <div class="data-label">🖥️ Browser</div>
        <div class="data-value warn" id="browserVal">Detecting...</div>
      </div>
      <div class="data-item">
        <div class="data-label">💻 Operating System</div>
        <div class="data-value warn" id="osVal">Detecting...</div>
      </div>
      <div class="data-item">
        <div class="data-label">📍 Timezone</div>
        <div class="data-value" id="tzVal">Detecting...</div>
      </div>
      <div class="data-item">
        <div class="data-label">🖥️ Screen Size</div>
        <div class="data-value" id="screenVal">Detecting...</div>
      </div>
      <div class="data-item">
        <div class="data-label">🌍 Language</div>
        <div class="data-value" id="langVal">Detecting...</div>
      </div>
    </div>

    <div class="exposed-title">☠️ What a real attacker could do:</div>
    <div class="danger-list">
      <div class="danger-item"><span class="danger-item-icon">🔓</span> Steal your passwords and login credentials</div>
      <div class="danger-item"><span class="danger-item-icon">💳</span> Access your bank accounts and credit cards</div>
      <div class="danger-item"><span class="danger-item-icon">📧</span> Take over your email and social media accounts</div>
      <div class="danger-item"><span class="danger-item-icon">🕵️</span> Track your location and spy on your activity</div>
      <div class="danger-item"><span class="danger-item-icon">🔒</span> Lock your device and demand ransom (ransomware)</div>
    </div>

    <div class="tips-section">
      <div class="tips-title">✅ How to Protect Yourself:</div>
      <div class="tip-item">✅ Always check the URL before clicking any link</div>
      <div class="tip-item">✅ Never enter your password on unknown websites</div>
      <div class="tip-item">✅ Enable Two-Factor Authentication (2FA) everywhere</div>
      <div class="tip-item">✅ Use a password manager — never reuse passwords</div>
      <div class="tip-item">✅ Verify emails from "banks" or "services" by calling them directly</div>
    </div>

    <div class="countdown">Returning to safety in <span id="countNum">10</span> seconds...</div>

    <div class="btn-row">
      <a href="dashboard.php" class="btn-safe">🛡️ I've Learned My Lesson — Back to Safety!</a>
      <a href="quiz.php?level=1" class="btn-learn">📚 Take the Phishing Quiz to Learn More</a>
    </div>
  </div>
</div>

<script>
// Matrix rain
const canvas = document.getElementById('matrix');
const ctx = canvas.getContext('2d');
canvas.width = window.innerWidth;
canvas.height = window.innerHeight;
const cols = Math.floor(canvas.width / 20);
const drops = Array(cols).fill(1);
const chars = '01アイウエオカキクケコABCDEF0123456789';
function drawMatrix() {
  ctx.fillStyle = 'rgba(0,0,0,0.05)';
  ctx.fillRect(0,0,canvas.width,canvas.height);
  ctx.fillStyle = '#00FF41';
  ctx.font = '14px monospace';
  drops.forEach((y, i) => {
    const char = chars[Math.floor(Math.random() * chars.length)];
    ctx.fillText(char, i * 20, y * 20);
    if(y * 20 > canvas.height && Math.random() > 0.975) drops[i] = 0;
    drops[i]++;
  });
}
setInterval(drawMatrix, 50);

// Detect browser info
const ua = navigator.userAgent;
let browser = 'Unknown';
if(ua.includes('Chrome') && !ua.includes('Edg')) browser = 'Google Chrome';
else if(ua.includes('Firefox')) browser = 'Mozilla Firefox';
else if(ua.includes('Safari') && !ua.includes('Chrome')) browser = 'Apple Safari';
else if(ua.includes('Edg')) browser = 'Microsoft Edge';

let os = 'Unknown';
if(ua.includes('Windows')) os = 'Windows';
else if(ua.includes('Mac')) os = 'macOS';
else if(ua.includes('Android')) os = 'Android';
else if(ua.includes('iPhone') || ua.includes('iPad')) os = 'iOS';
else if(ua.includes('Linux')) os = 'Linux';

document.getElementById('browserVal').textContent = browser;
document.getElementById('osVal').textContent = os;
document.getElementById('tzVal').textContent = Intl.DateTimeFormat().resolvedOptions().timeZone;
document.getElementById('screenVal').textContent = `${screen.width} × ${screen.height}`;
document.getElementById('langVal').textContent = navigator.language || 'Unknown';

// Simulate IP
document.getElementById('ipVal').textContent = '192.168.' + Math.floor(Math.random()*255) + '.' + Math.floor(Math.random()*255) + ' (simulated)';

// Countdown
let count = 10;
const countEl = document.getElementById('countNum');
const timer = setInterval(() => {
  count--;
  countEl.textContent = count;
  if(count <= 0) { clearInterval(timer); window.location.href = 'dashboard.php'; }
}, 1000);
</script>
</body>
</html>
