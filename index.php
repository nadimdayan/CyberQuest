<?php
session_start();
if(isset($_SESSION['user'])) {
    header("Location: dashboard.php");
    exit();
}

$error = "";
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once 'config/db.php';
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if($user && password_verify($password, $user['password'])) {
        // Streak logic
        $today = date('Y-m-d');
        $last = $user['last_login'];
        $streak = $user['streak'];

        if($last == date('Y-m-d', strtotime('-1 day'))) {
            $streak += 1;
        } elseif($last != $today) {
            $streak = 1;
        }

        $update = $conn->prepare("UPDATE users SET streak=?, last_login=? WHERE id=?");
        $update->bind_param("isi", $streak, $today, $user['id']);
        $update->execute();

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['xp'] = $user['xp'];
        $_SESSION['streak'] = $streak;
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid email or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>CyberQuest — Login</title>
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Fredoka+One&display=swap" rel="stylesheet">
<style>
  :root {
    --green: #58CC02;
    --green-dark: #46a302;
    --blue: #1CB0F6;
    --purple: #CE82FF;
    --orange: #FF9600;
    --red: #FF4B4B;
    --yellow: #FFD900;
    --bg: #F7F9FC;
    --card: #FFFFFF;
    --text: #1F2937;
    --text-light: #6B7280;
  }

  * { margin: 0; padding: 0; box-sizing: border-box; }

  body {
    font-family: 'Nunito', sans-serif;
    background: var(--bg);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    position: relative;
  }

  /* Animated background blobs */
  .blob {
    position: fixed;
    border-radius: 50%;
    filter: blur(80px);
    opacity: 0.35;
    animation: float 8s ease-in-out infinite;
    pointer-events: none;
    z-index: 0;
  }
  .blob-1 { width: 500px; height: 500px; background: #58CC02; top: -150px; left: -150px; animation-delay: 0s; }
  .blob-2 { width: 400px; height: 400px; background: #1CB0F6; bottom: -100px; right: -100px; animation-delay: 2s; }
  .blob-3 { width: 300px; height: 300px; background: #CE82FF; top: 50%; left: 60%; animation-delay: 4s; }
  .blob-4 { width: 250px; height: 250px; background: #FFD900; bottom: 20%; left: 5%; animation-delay: 1s; }

  @keyframes float {
    0%, 100% { transform: translate(0, 0) scale(1); }
    33% { transform: translate(30px, -30px) scale(1.05); }
    66% { transform: translate(-20px, 20px) scale(0.95); }
  }

  /* Floating icons background */
  .floating-icons {
    position: fixed; inset: 0; pointer-events: none; z-index: 0; overflow: hidden;
  }
  .fi {
    position: absolute;
    font-size: 2rem;
    opacity: 0.12;
    animation: floatUp 15s linear infinite;
  }
  @keyframes floatUp {
    0% { transform: translateY(110vh) rotate(0deg); opacity: 0; }
    10% { opacity: 0.12; }
    90% { opacity: 0.12; }
    100% { transform: translateY(-10vh) rotate(360deg); opacity: 0; }
  }

  /* Main layout */
  .page-wrapper {
    display: flex;
    width: 100%;
    max-width: 1000px;
    min-height: 100vh;
    align-items: center;
    justify-content: center;
    padding: 2rem;
    position: relative;
    z-index: 1;
    gap: 3rem;
  }

  /* Left hero panel */
  .hero-panel {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 1.5rem;
    animation: slideInLeft 0.8s cubic-bezier(0.34, 1.56, 0.64, 1) both;
  }
  @keyframes slideInLeft {
    from { opacity: 0; transform: translateX(-60px); }
    to { opacity: 1; transform: translateX(0); }
  }

  .logo {
    display: flex;
    align-items: center;
    gap: 0.75rem;
  }
  .logo-icon {
    width: 56px; height: 56px;
    background: linear-gradient(135deg, var(--green), var(--blue));
    border-radius: 16px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.8rem;
    box-shadow: 0 8px 24px rgba(88,204,2,0.35);
    animation: pulse 2s ease-in-out infinite;
  }
  @keyframes pulse {
    0%, 100% { box-shadow: 0 8px 24px rgba(88,204,2,0.35); }
    50% { box-shadow: 0 8px 36px rgba(88,204,2,0.55); }
  }
  .logo-text {
    font-family: 'Fredoka One', cursive;
    font-size: 2rem;
    background: linear-gradient(135deg, var(--green), var(--blue));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
  }

  .hero-title {
    font-family: 'Fredoka One', cursive;
    font-size: 3rem;
    line-height: 1.1;
    color: var(--text);
  }
  .hero-title span { color: var(--green); }

  .hero-subtitle {
    font-size: 1.1rem;
    color: var(--text-light);
    font-weight: 600;
    line-height: 1.6;
  }

  .hero-features {
    display: flex; flex-direction: column; gap: 0.75rem;
  }
  .feature-pill {
    display: flex; align-items: center; gap: 0.6rem;
    background: white;
    border-radius: 50px;
    padding: 0.6rem 1rem;
    font-weight: 700;
    font-size: 0.9rem;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    width: fit-content;
    animation: popIn 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) both;
  }
  .feature-pill:nth-child(1) { animation-delay: 0.4s; }
  .feature-pill:nth-child(2) { animation-delay: 0.55s; }
  .feature-pill:nth-child(3) { animation-delay: 0.7s; }
  @keyframes popIn {
    from { opacity: 0; transform: scale(0.7); }
    to { opacity: 1; transform: scale(1); }
  }

  /* Card */
  .card {
    background: white;
    border-radius: 28px;
    padding: 2.5rem;
    width: 100%;
    max-width: 420px;
    box-shadow: 0 24px 64px rgba(0,0,0,0.1), 0 4px 16px rgba(0,0,0,0.06);
    animation: slideInRight 0.8s cubic-bezier(0.34, 1.56, 0.64, 1) both;
    position: relative;
    overflow: hidden;
  }
  @keyframes slideInRight {
    from { opacity: 0; transform: translateX(60px); }
    to { opacity: 1; transform: translateX(0); }
  }
  .card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 5px;
    background: linear-gradient(90deg, var(--green), var(--blue), var(--purple));
  }

  .card-title {
    font-family: 'Fredoka One', cursive;
    font-size: 1.8rem;
    color: var(--text);
    margin-bottom: 0.25rem;
  }
  .card-subtitle {
    color: var(--text-light);
    font-size: 0.95rem;
    font-weight: 600;
    margin-bottom: 1.75rem;
  }

  .form-group { margin-bottom: 1.25rem; }

  label {
    display: block;
    font-weight: 800;
    font-size: 0.85rem;
    color: var(--text);
    margin-bottom: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }

  input {
    width: 100%;
    padding: 0.9rem 1.1rem;
    border: 2.5px solid #E5E7EB;
    border-radius: 14px;
    font-size: 1rem;
    font-family: 'Nunito', sans-serif;
    font-weight: 600;
    color: var(--text);
    background: #FAFAFA;
    transition: all 0.25s ease;
    outline: none;
  }
  input:focus {
    border-color: var(--blue);
    background: white;
    box-shadow: 0 0 0 4px rgba(28,176,246,0.12);
    transform: translateY(-1px);
  }

  .error-msg {
    background: #FFF0F0;
    border: 2px solid var(--red);
    color: var(--red);
    padding: 0.75rem 1rem;
    border-radius: 12px;
    font-weight: 700;
    font-size: 0.9rem;
    margin-bottom: 1rem;
    display: flex; align-items: center; gap: 0.5rem;
  }

  .btn-primary {
    width: 100%;
    padding: 1rem;
    background: linear-gradient(135deg, var(--green), #46CC0A);
    border: none;
    border-radius: 14px;
    color: white;
    font-family: 'Nunito', sans-serif;
    font-size: 1.05rem;
    font-weight: 900;
    cursor: pointer;
    transition: all 0.25s ease;
    box-shadow: 0 4px 0 var(--green-dark), 0 8px 24px rgba(88,204,2,0.3);
    position: relative;
    top: 0;
    letter-spacing: 0.02em;
  }
  .btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 0 var(--green-dark), 0 12px 28px rgba(88,204,2,0.4);
  }
  .btn-primary:active {
    transform: translateY(2px);
    box-shadow: 0 2px 0 var(--green-dark);
  }

  .divider {
    display: flex; align-items: center; gap: 1rem;
    margin: 1.5rem 0;
    color: var(--text-light);
    font-weight: 700;
    font-size: 0.85rem;
  }
  .divider::before, .divider::after {
    content: '';
    flex: 1;
    height: 2px;
    background: #E5E7EB;
    border-radius: 99px;
  }

  .link-btn {
    width: 100%;
    padding: 1rem;
    background: white;
    border: 2.5px solid #E5E7EB;
    border-radius: 14px;
    color: var(--blue);
    font-family: 'Nunito', sans-serif;
    font-size: 1rem;
    font-weight: 800;
    cursor: pointer;
    transition: all 0.25s ease;
    text-decoration: none;
    display: block;
    text-align: center;
  }
  .link-btn:hover {
    border-color: var(--blue);
    background: #F0F9FF;
    transform: translateY(-1px);
  }

  /* Responsive */
  @media (max-width: 768px) {
    .hero-panel { display: none; }
    .page-wrapper { padding: 1rem; }
  }
</style>
</head>
<body>

<!-- Animated blobs -->
<div class="blob blob-1"></div>
<div class="blob blob-2"></div>
<div class="blob blob-3"></div>
<div class="blob blob-4"></div>

<!-- Floating background icons -->
<div class="floating-icons" id="floatingIcons"></div>

<div class="page-wrapper">

  <!-- Left Hero Panel -->
  <div class="hero-panel">
    <div class="logo">
      <div class="logo-icon">🛡️</div>
      <div class="logo-text">CyberQuest</div>
    </div>

    <h1 class="hero-title">
      Learn<br><span>Cybersecurity</span><br>the fun way!
    </h1>

    <p class="hero-subtitle">
      Level up your digital skills with<br>
      quizzes, XP, streaks & certificates!
    </p>

    <div class="hero-features">
      <div class="feature-pill">🔥 Daily Streaks to keep you motivated</div>
      <div class="feature-pill">⭐ XP & Badges for every achievement</div>
      <div class="feature-pill">🏆 Earn a real Certificate of Completion</div>
    </div>
  </div>

  <!-- Login Card -->
  <div class="card">
    <h2 class="card-title">Welcome back! 👋</h2>
    <p class="card-subtitle">Log in to continue your journey</p>

    <?php if($error): ?>
    <div class="error-msg">❌ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="form-group">
        <label>📧 Email Address</label>
        <input type="email" name="email" placeholder="you@example.com" required>
      </div>
      <div class="form-group">
        <label>🔒 Password</label>
        <input type="password" name="password" placeholder="Enter your password" required>
      </div>
      <button type="submit" class="btn-primary">🚀 Log In</button>
    </form>

    <div class="divider">or</div>

    <a href="register.php" class="link-btn">✨ Create a New Account</a>
  </div>

</div>

<script>
  // Generate floating background icons
  const icons = ['🔒','🛡️','🔐','💻','🌐','⚠️','🔑','🛡','💡','🕵️'];
  const container = document.getElementById('floatingIcons');
  for(let i = 0; i < 20; i++) {
    const el = document.createElement('div');
    el.className = 'fi';
    el.textContent = icons[Math.floor(Math.random() * icons.length)];
    el.style.left = Math.random() * 100 + 'vw';
    el.style.animationDuration = (10 + Math.random() * 15) + 's';
    el.style.animationDelay = (Math.random() * 15) + 's';
    el.style.fontSize = (1.2 + Math.random() * 2) + 'rem';
    container.appendChild(el);
  }

  // Parallax on mouse move
  document.addEventListener('mousemove', (e) => {
    const x = (e.clientX / window.innerWidth - 0.5) * 30;
    const y = (e.clientY / window.innerHeight - 0.5) * 30;
    document.querySelectorAll('.blob').forEach((b, i) => {
      const factor = (i + 1) * 0.4;
      b.style.transform = `translate(${x * factor}px, ${y * factor}px)`;
    });
  });
</script>
</body>
</html>
