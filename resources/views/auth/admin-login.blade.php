<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign In — ONYX Legal</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    :root {
      --primary:   #5D3A1A;
      --primary-d: #4A2D13;
      --accent:    #C4956A;
      --body-bg:   #F7F2EC;
      --card-bg:   #FFFFFF;
      --border:    #E8DDD4;
      --text:      #1A0F07;
      --muted:     #7A6555;
      --radius:    4px;
    }
    html, body {
      height: 100%;
      font-family: 'Poppins', sans-serif;
      background: var(--body-bg);
      display: flex;
      align-items: center;
      justify-content: center;
    }
    body {
      background-image:
        radial-gradient(ellipse at 20% 50%, rgba(93,58,26,0.06) 0%, transparent 60%),
        radial-gradient(ellipse at 80% 20%, rgba(196,149,106,0.08) 0%, transparent 50%);
    }
    .login-wrap {
      width: 100%;
      max-width: 420px;
      padding: 16px;
    }
    .login-card {
      background: var(--card-bg);
      border: 1px solid var(--border);
      border-radius: 8px;
      box-shadow: 0 8px 32px rgba(93,58,26,0.12);
      overflow: hidden;
    }
    .login-header {
      background: linear-gradient(135deg, #1C120A 0%, #3A2010 100%);
      padding: 28px 24px 22px;
      text-align: center;
    }
    @media (max-width: 480px) {
      body { align-items: flex-start; padding-top: 24px; }
      .login-wrap { padding: 12px; }
      .login-body { padding: 20px 18px 24px; }
      .login-header { padding: 24px 18px 18px; }
      .login-header h1 { font-size: 1.125rem; }
    }
    .login-logo {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 54px; height: 54px;
      background: linear-gradient(135deg, var(--primary), var(--accent));
      border-radius: 10px;
      font-family: 'Georgia', serif;
      font-size: 1.5rem;
      font-weight: 900;
      color: #fff;
      margin-bottom: 16px;
    }
    .login-header h1 {
      font-size: 1.375rem;
      font-weight: 700;
      color: #fff;
      letter-spacing: 0.04em;
    }
    .login-header p {
      font-size: 0.8125rem;
      color: rgba(255,243,230,0.55);
      margin-top: 5px;
    }
    .login-body { padding: 28px 32px 32px; }
    .form-group { margin-bottom: 16px; }
    .form-group label {
      display: block;
      font-size: 0.75rem;
      font-weight: 600;
      color: var(--text);
      margin-bottom: 5px;
    }
    .input-wrap { position: relative; }
    .input-wrap i {
      position: absolute;
      left: 12px; top: 50%;
      transform: translateY(-50%);
      color: var(--muted);
      font-size: 0.8125rem;
    }
    .input-wrap input {
      width: 100%;
      padding: 10px 12px 10px 36px;
      border: 1px solid var(--border);
      border-radius: var(--radius);
      font-family: 'Poppins', sans-serif;
      font-size: 0.875rem;
      color: var(--text);
      transition: border-color 0.15s, box-shadow 0.15s;
    }
    .input-wrap input:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(93,58,26,0.08);
    }
    .check-row {
      display: flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 22px;
      margin-top: 4px;
    }
    .check-row input { accent-color: var(--primary); }
    .check-row label { font-size: 0.8125rem; color: var(--muted); cursor: pointer; }
    .btn-login {
      width: 100%;
      padding: 11px;
      background: var(--primary);
      color: #fff;
      border: none;
      border-radius: var(--radius);
      font-family: 'Poppins', sans-serif;
      font-size: 0.9375rem;
      font-weight: 600;
      cursor: pointer;
      transition: background 0.15s;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
    }
    .btn-login:hover { background: var(--primary-d); }
    .alert {
      display: flex;
      align-items: flex-start;
      gap: 9px;
      padding: 10px 14px;
      border-radius: var(--radius);
      font-size: 0.8125rem;
      border-left: 3px solid;
      margin-bottom: 16px;
    }
    .alert-error   { background: rgba(220,38,38,0.08); border-color: #DC2626; color: #7F1D1D; }
    .alert-info    { background: rgba(29,78,216,0.08); border-color: #1D4ED8; color: #1E3A8A; }
    .login-footer {
      text-align: center;
      padding-top: 18px;
      font-size: 0.6875rem;
      color: var(--muted);
      border-top: 1px solid var(--border);
      margin-top: 20px;
    }
  </style>
</head>
<body>

<div class="login-wrap">
  <div class="login-card">

    <div class="login-header">
      <div class="login-logo">OL</div>
      <h1>ONYX Legal</h1>
      <p>Secure staff portal — authorised access only</p>
    </div>

    <div class="login-body">

      @if(session('status'))
        <div class="alert alert-info">
          <i class="fas fa-info-circle"></i>
          {{ session('status') }}
        </div>
      @endif

      @if($errors->any())
        <div class="alert alert-error">
          <i class="fas fa-circle-exclamation"></i>
          <div>
            @foreach($errors->all() as $error)
              <div>{{ $error }}</div>
            @endforeach
          </div>
        </div>
      @endif

      <form method="POST" action="{{ route('admin.login') }}">
        @csrf

        <div class="form-group">
          <label for="email">Email Address</label>
          <div class="input-wrap">
            <i class="fas fa-envelope"></i>
            <input type="email" id="email" name="email"
              value="{{ old('email') }}"
              placeholder="your@email.com"
              required autofocus autocomplete="email">
          </div>
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <div class="input-wrap">
            <i class="fas fa-lock"></i>
            <input type="password" id="password" name="password"
              placeholder="••••••••"
              required autocomplete="current-password">
          </div>
        </div>

        <div class="check-row">
          <input type="checkbox" id="remember" name="remember">
          <label for="remember">Keep me signed in</label>
        </div>

        <button type="submit" class="btn-login">
          <i class="fas fa-right-to-bracket"></i>
          Sign In
        </button>
      </form>

      <div class="login-footer">
        &copy; {{ date('Y') }} ONYX Legal &mdash; All rights reserved
      </div>

    </div>
  </div>
</div>

</body>
</html>
