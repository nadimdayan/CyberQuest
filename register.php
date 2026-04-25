<?php
session_start();
if(isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = "";
$success = "";

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once 'config/db.php';
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];

    if(strlen($username) < 3) {
        $error = "Username must be at least 3 characters!";
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email!";
    } elseif(strlen($password) < 6) {
        $error = "Password must be at least 6 characters!";
    } elseif($password !== $confirm) {
        $error = "Passwords do not match!";
    } else {
        // Check if email exists
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if($check->num_rows > 0) {
            $error = "Email already registered! Try logging in.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $today  = date('Y-m-d');
            $stmt = $conn->prepare("INSERT INTO users (username, email, password, streak, xp, last_login) VALUES (?, ?, ?, 1, 0, ?)");
            $stmt->bind_param("ssss", $username, $email, $hashed, $today);
            if($stmt->execute()) {
                $success = "Account created! You can now log in 🎉";
            } else {
                $error = "Something went wrong. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>CyberQuest — Create Account</title>
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Fredoka+One&display=swap" rel="stylesheet">
<style>
  :root {
    --green: #58CC02; --green-dark: #46a302;
    --blue: #1CB0F6; --purple: #CE82FF;
    --orange: #FF9600; --red: #FF4B4B; --yellow: #FFD900;
    --bg: #F7F9FC; --card: #FFFFFF;
    --text: #1F2937; --text-light: #6B7280;
  }
  * { margin: 0; padding: 0; box-sizing: border-box; }

  body {
    font-family: 'Nunito', sans-serif;
    background: var(--bg);
    min-height: 100vh;
    display: flex; align-items: center; justify-content: center;
    overflow: hidden; position: relative;
  }

  .blob {
    position: fixed; border-radius: 50%;
    filter: blur(80px); opacity: 0.35;
    animation: float 8s ease-in-out infinite;
    pointer-events: none; z-index: 0;
  }
  .blob-1 { width: 450px; height: 450px; background: #CE82FF; top: -100px; right: -100px; animation-delay: 0s; }
  .blob-2 { width: 400px; height: 400px; background: #FF9600; bottom: -100px; left: -100px; animation-delay: 2s; }
  .blob-3 { width: 300px; height: 300px; background: #1CB0F6; top: 60%; right: 10%; animation-delay: 4s; }
  @keyframes float {
    0%,100% { transform: translate(0,0) scale(1); }
    33% { transform: translate(20px,-20px) scale(1.05); }
    66% { transform: translate(-20px,20px) scale(0.95); }
  }

  .floating-icons { position: fixed; inset: 0; pointer-events: none; z-index: 0; overflow: hidden; }
  .fi {
    position: absolute; font-size: 2rem; opacity: 0.1;
    animation: floatUp 15s linear infinite;
  }
  @keyframes floatUp {
    0% { transform: translateY(110vh) rotate(0deg); opacity: 0; }
    10% { opacity: 0.1; } 90% { opacity: 0.1; }
    100% { transform: translateY(-10vh) rotate(360deg); opacity: 0; }
  }

  .page-wrapper {
    display: flex; width: 100%; max-width: 1000px; min-height: 100vh;
    align-items: center; justify-content: center;
    padding: 2rem; position: relative; z-index: 1; gap: 3rem;
  }

  /* Steps panel */
  .steps-panel {
    flex: 1;
    display: flex; flex-direction: column; gap: 1.5rem;
    animation: slideInLeft 0.8s cubic-bezier(0.34,1.56,0.64,1) both;
  }
  @keyframes slideInLeft {
    from { opacity: 0; transform: translateX(-60px); }
    to { opacity: 1; transform: translateX(0); }
  }

  .logo { display: flex; align-items: center; gap: 0.75rem; }
  .logo-icon {
    width: 56px; height: 56px;
    background: linear-gradient(135deg, var(--purple), var(--blue));
    border-radius: 16px; display: flex; align-items: center; justify-content: center;
    font-size: 1.8rem;
    box-shadow: 0 8px 24px rgba(206,130,255,0.4);
    animation: pulse 2s ease-in-out infinite;
  }
  @keyframes pulse {
    0%,100% { box-shadow: 0 8px 24px rgba(206,130,255,0.4); }
    50% { box-shadow: 0 8px 36px rgba(206,130,255,0.6); }
  }
  .logo-text {
    font-family: 'Fredoka One', cursive; font-size: 2rem;
    background: linear-gradient(135deg, var(--purple), var(--blue));
    -webkit-background-clip: text; -webkit-text-fill-color: transparent;
  }

  .steps-title {
    font-family: 'Fredoka One', cursive; font-size: 1.5rem; color: var(--text);
  }

  .step-item {
    display: flex; align-items: flex-start; gap: 1rem;
    background: white; padding: 1rem 1.25rem;
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    animation: popIn 0.5s cubic-bezier(0.34,1.56,0.64,1) both;
  }
  .step-item:nth-child(1) { animation-delay: 0.3s; }
  .step-item:nth-child(2) { animation-delay: 0.45s; }
  .step-item:nth-child(3) { animation-delay: 0.6s; }
  .step-item:nth-child(4) { animation-delay: 0.75s; }
  @keyframes popIn {
    from { opacity: 0; transform: scale(0.7); }
    to { opacity: 1; transform: scale(1); }
  }

  .step-icon {
    width: 42px; height: 42px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.3rem; flex-shrink: 0;
  }
  .s1 { background: #FFF3E0; }
  .s2 { background: #E8F5E9; }
  .s3 { background: #E3F2FD; }
  .s4 { background: #F3E5F5; }

  .step-text strong {
    display: block; font-weight: 800; color: var(--text); font-size: 0.95rem;
  }
  .step-text span {
    font-size: 0.85rem; color: var(--text-light); font-weight: 600;
  }

  /* Card */
  .card {
    background: white; border-radius: 28px; padding: 2.5rem;
    width: 100%; max-width: 420px;
    box-shadow: 0 24px 64px rgba(0,0,0,0.1), 0 4px 16px rgba(0,0,0,0.06);
    animation: slideInRight 0.8s cubic-bezier(0.34,1.56,0.64,1) both;
    position: relative; overflow: hidden;
  }
  @keyframes slideInRight {
    from { opacity: 0; transform: translateX(60px); }
    to { opacity: 1; transform: translateX(0); }
  }
  .card::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 5px;
    background: linear-gradient(90deg, var(--purple), var(--orange), var(--yellow));
  }

  .card-title { font-family: 'Fredoka One', cursive; font-size: 1.8rem; color: var(--text); margin-bottom: 0.25rem; }
  .card-subtitle { color: var(--text-light); font-size: 0.95rem; font-weight: 600; margin-bottom: 1.75rem; }

  .form-group { margin-bottom: 1.15rem; }
  label {
    display: block; font-weight: 800; font-size: 0.82rem; color: var(--text);
    margin-bottom: 0.45rem; text-transform: uppercase; letter-spacing: 0.05em;
  }
  input {
    width: 100%; padding: 0.9rem 1.1rem;
    border: 2.5px solid #E5E7EB; border-radius: 14px;
    font-size: 1rem; font-family: 'Nunito', sans-serif; font-weight: 600;
    color: var(--text); background: #FAFAFA; transition: all 0.25s ease; outline: none;
  }
  input:focus {
    border-color: var(--purple); background: white;
    box-shadow: 0 0 0 4px rgba(206,130,255,0.12);
    transform: translateY(-1px);
  }

  .error-msg {
    background: #FFF0F0; border: 2px solid var(--red); color: var(--red);
    padding: 0.75rem 1rem; border-radius: 12px;
    font-weight: 700; font-size: 0.9rem; margin-bottom: 1rem;
    display: flex; align-items: center; gap: 0.5rem;
  }
  .success-msg {
    background: #F0FFF0; border: 2px solid var(--green); color: var(--green-dark);
    padding: 0.75rem 1rem; border-radius: 12px;
    font-weight: 700; font-size: 0.9rem; margin-bottom: 1rem;
    display: flex; align-items: center; gap: 0.5rem;
  }

  .btn-primary {
    width: 100%; padding: 1rem;
    background: linear-gradient(135deg, var(--purple), #B56DFF);
    border: none; border-radius: 14px; color: white;
    font-family: 'Nunito', sans-serif; font-size: 1.05rem; font-weight: 900;
    cursor: pointer; transition: all 0.25s ease;
    box-shadow: 0 4px 0 #a055e0, 0 8px 24px rgba(206,130,255,0.35);
    position: relative; top: 0; letter-spacing: 0.02em;
  }
  .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 0 #a055e0, 0 12px 28px rgba(206,130,255,0.45); }
  .btn-primary:active { transform: translateY(2px); box-shadow: 0 2px 0 #a055e0; }

  .divider {
    display: flex; align-items: center; gap: 1rem;
    margin: 1.5rem 0; color: var(--text-light); font-weight: 700; font-size: 0.85rem;
  }
  .divider::before, .divider::after { content: ''; flex: 1; height: 2px; background: #E5E7EB; border-radius: 99px; }

  .link-btn {
    width: 100%; padding: 1rem; background: white;
    border: 2.5px solid #E5E7EB; border-radius: 14px; color: var(--green);
    font-family: 'Nunito', sans-serif; font-size: 1rem; font-weight: 800;
    cursor: pointer; transition: all 0.25s ease;
    text-decoration: none; display: block; text-align: center;
  }
  .link-btn:hover { border-color: var(--green); background: #F0FFF0; transform: translateY(-1px); }

  @media(max-width:768px) { .steps-panel { display: none; } .page-wrapper { padding: 1rem; } }
</style>
</head>
<body>
<div class="blob blob-1"></div>
<div class="blob blob-2"></div>
<div class="blob blob-3"></div>
<div class="floating-icons" id="floatingIcons"></div>

<div class="page-wrapper">

  <div class="steps-panel">
    <div class="logo">
      <div class="logo-icon">🛡️</div>
      <div class="logo-text">CyberQuest</div>
    </div>
    <p class="steps-title">Your journey starts here! 🚀</p>

    <div class="step-item">
      <div class="step-icon s1">📝</div>
      <div class="step-text">
        <strong>Create your account</strong>
        <span>Free to join, forever!</span>
      </div>
    </div>
    <div class="step-item">
      <div class="step-icon s2">🎯</div>
      <div class="step-text">
        <strong>Take fun quizzes</strong>
        <span>Learn cybersecurity step by step</span>
      </div>
    </div>
    <div class="step-item">
      <div class="step-icon s3">⭐</div>
      <div class="step-text">
        <strong>Earn XP & badges</strong>
        <span>Level up as you learn more</span>
      </div>
    </div>
    <div class="step-item">
      <div class="step-icon s4">🏆</div>
      <div class="step-text">
        <strong>Get your certificate</strong>
        <span>Show off your achievement!</span>
      </div>
    </div>
  </div>

  <div class="card">
    <h2 class="card-title">Join CyberQuest! ✨</h2>
    <p class="card-subtitle">Create your free account in seconds</p>

    <?php if($error): ?>
    <div class="error-msg">❌ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if($success): ?>
    <div class="success-msg">✅ <?= htmlspecialchars($success) ?> <a href="index.php">Login →</a></div>
    <?php endif; ?>

    <form method="POST">
      <div class="form-group">
        <label>👤 Username</label>
        <input type="text" name="username" placeholder="Your cool username" required>
      </div>
      <div class="form-group">
        <label>📧 Email Address</label>
        <input type="email" name="email" placeholder="you@example.com" required>
      </div>
      <div class="form-group">
        <label>🔒 Password</label>
        <input type="password" name="password" placeholder="Min. 6 characters" required>
      </div>
      <div class="form-group">
        <label>🔒 Confirm Password</label>
        <input type="password" name="confirm_password" placeholder="Repeat your password" required>
      </div>
      <button type="submit" class="btn-primary">🎉 Create My Account</button>
    </form>

    <div class="divider">already have an account?</div>
    <a href="index.php" class="link-btn">🔑 Log In Instead</a>
  </div>

</div>

<script>
  const icons = ['🔒','🛡️','🔐','💻','🌐','⚠️','🔑','💡','🕵️','🏆'];
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

  document.addEventListener('mousemove', (e) => {
    const x = (e.clientX / window.innerWidth - 0.5) * 25;
    const y = (e.clientY / window.innerHeight - 0.5) * 25;
    document.querySelectorAll('.blob').forEach((b, i) => {
      b.style.transform = `translate(${x * (i+1) * 0.4}px, ${y * (i+1) * 0.4}px)`;
    });
  });
</script>
</body>
</html>
