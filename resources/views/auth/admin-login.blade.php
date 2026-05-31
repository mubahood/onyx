<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign In — ONYX Legal</title>
  <link rel="stylesheet" href="{{ asset('vendor/fa/css/all.min.css') }}">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --dk:   #0D0905;
      --sb:   #1C120A;
      --br:   #5D3A1A;
      --br-d: #4A2D13;
      --br-l: #7A4E2D;
      --ac:   #C4956A;
      --gold: #D4AA70;
      --cr:   #F7F3EE;
      --wh:   #FFFFFF;
      --bd:   #E5D9CF;
      --tx:   #1A0F07;
      --mt:   #7A6555;
    }

    html, body { height: 100%; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif; }

    /* ═══════════════════════════════════════════
       SHELL — full-viewport split
    ═══════════════════════════════════════════ */
    .lp {
      min-height: 100vh;
      display: flex;
    }

    /* ───────────────────────────────────────────
       LEFT — brand panel
    ─────────────────────────────────────────── */
    .lp-left {
      width: 52%;
      min-height: 100vh;
      background: var(--sb);
      display: flex;
      flex-direction: column;
      padding: 52px 60px 44px;
      position: relative;
      overflow: hidden;
    }

    /* Atmospheric glow layers */
    .lp-left::before {
      content: '';
      position: absolute;
      inset: 0;
      background:
        radial-gradient(ellipse 70% 55% at 15% 10%,  rgba(196,149,106,.14) 0%, transparent 100%),
        radial-gradient(ellipse 60% 60% at 85% 85%,  rgba(93,58,26,.6)     0%, transparent 100%),
        radial-gradient(ellipse 50% 40% at 50% 50%,  rgba(28,18,10,.4)     0%, transparent 100%);
      pointer-events: none;
    }

    /* Large decorative scales watermark */
    .lp-watermark {
      position: absolute;
      right: -80px;
      bottom: -80px;
      width: 460px;
      height: 460px;
      opacity: .035;
      pointer-events: none;
    }

    /* Decorative top corner lines */
    .lp-corner {
      position: absolute;
      top: 0; right: 0;
      width: 180px; height: 180px;
      opacity: .06;
      pointer-events: none;
    }

    .lp-top, .lp-mid, .lp-bot {
      position: relative;
      z-index: 2;
    }
    .lp-mid { flex: 1; display: flex; flex-direction: column; justify-content: center; }

    /* ── Logo ───────────────────────────────── */
    .lp-logo {
      display: inline-flex;
      align-items: center;
      gap: 15px;
    }
    .lp-logo-badge {
      width: 52px; height: 52px;
      flex-shrink: 0;
      filter: drop-shadow(0 4px 16px rgba(0,0,0,.4));
    }
    .lp-logo-words { line-height: 1; }
    .lp-logo-name {
      display: block;
      font-size: 1.4375rem;
      font-weight: 800;
      color: #fff;
      letter-spacing: .1em;
      text-transform: uppercase;
    }
    .lp-logo-tag {
      display: block;
      font-size: .625rem;
      font-weight: 600;
      color: var(--ac);
      letter-spacing: .22em;
      text-transform: uppercase;
      margin-top: 4px;
    }

    /* ── Mid content ──────────────────────── */
    .lp-rule {
      width: 52px; height: 2px;
      background: linear-gradient(90deg, var(--gold) 0%, transparent 100%);
      margin-bottom: 32px;
    }

    .lp-heading {
      font-size: clamp(2.25rem, 3.5vw, 3.25rem);
      font-weight: 900;
      color: #fff;
      line-height: 1.1;
      letter-spacing: -.025em;
      margin-bottom: 20px;
    }
    .lp-heading em {
      font-style: normal;
      color: var(--ac);
    }

    .lp-desc {
      font-size: .9375rem;
      color: rgba(247,243,238,.48);
      line-height: 1.7;
      max-width: 330px;
      margin-bottom: 44px;
    }

    /* Feature rows */
    .lp-feats { display: flex; flex-direction: column; gap: 16px; }
    .lp-feat {
      display: flex;
      align-items: center;
      gap: 14px;
    }
    .lp-feat-icon {
      width: 36px; height: 36px;
      border-radius: 9px;
      background: rgba(196,149,106,.12);
      border: 1px solid rgba(196,149,106,.22);
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      color: var(--ac);
      font-size: .8125rem;
    }
    .lp-feat-text {
      font-size: .8125rem;
      color: rgba(247,243,238,.6);
      font-weight: 500;
    }

    /* ── Bottom ───────────────────────────── */
    .lp-fine {
      font-size: .625rem;
      color: rgba(247,243,238,.22);
      line-height: 1.7;
    }
    .lp-fine strong { color: rgba(247,243,238,.38); font-weight: 600; }

    /* ═══════════════════════════════════════════
       RIGHT — form panel
    ═══════════════════════════════════════════ */
    .lp-right {
      flex: 1;
      background: var(--cr);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 48px 52px;
    }

    .lp-form-wrap { width: 100%; max-width: 400px; }

    /* ── Form header ──────────────────────── */
    .lp-form-eyebrow {
      font-size: .625rem;
      font-weight: 800;
      letter-spacing: .2em;
      text-transform: uppercase;
      color: var(--ac);
      margin-bottom: 10px;
    }
    .lp-form-title {
      font-size: 2rem;
      font-weight: 800;
      color: var(--tx);
      letter-spacing: -.02em;
      line-height: 1.2;
      margin-bottom: 8px;
    }
    .lp-form-sub {
      font-size: .875rem;
      color: var(--mt);
      line-height: 1.6;
      margin-bottom: 32px;
    }

    /* ── Alerts ───────────────────────────── */
    .lp-alert {
      display: flex;
      align-items: flex-start;
      gap: 10px;
      padding: 12px 15px;
      border-radius: 7px;
      font-size: .8125rem;
      line-height: 1.55;
      margin-bottom: 22px;
      border-left: 3px solid;
    }
    .lp-alert i { margin-top: 1px; flex-shrink: 0; }
    .lp-alert-err { background: rgba(220,38,38,.07);  border-color: #DC2626; color: #7F1D1D; }
    .lp-alert-inf { background: rgba(29,78,216,.07);  border-color: #1D4ED8; color: #1E3A8A; }

    /* ── Fields ───────────────────────────── */
    .lp-field { margin-bottom: 20px; }
    .lp-label {
      display: block;
      font-size: .6875rem;
      font-weight: 800;
      color: var(--tx);
      letter-spacing: .06em;
      text-transform: uppercase;
      margin-bottom: 8px;
    }
    .lp-inr { position: relative; }
    .lp-ico {
      position: absolute;
      left: 15px; top: 50%;
      transform: translateY(-50%);
      color: #B5A598;
      font-size: .8125rem;
      pointer-events: none;
      transition: color .15s;
    }
    .lp-inp {
      width: 100%;
      height: 50px;
      padding: 0 15px 0 44px;
      border: 1.5px solid var(--bd);
      border-radius: 8px;
      background: var(--wh);
      font-family: inherit;
      font-size: .9375rem;
      color: var(--tx);
      transition: border-color .15s, box-shadow .15s;
      -webkit-appearance: none;
    }
    .lp-inp:focus {
      outline: none;
      border-color: var(--br);
      box-shadow: 0 0 0 3.5px rgba(93,58,26,.1);
    }
    .lp-inp:focus + .lp-ico,
    .lp-inr:focus-within .lp-ico { color: var(--br); }
    .lp-inp::placeholder { color: #C8BCB3; font-size: .875rem; }

    /* Password toggle */
    .lp-eye {
      position: absolute;
      right: 15px; top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      cursor: pointer;
      color: #B5A598;
      font-size: .875rem;
      padding: 0;
      line-height: 1;
      transition: color .15s;
    }
    .lp-eye:hover { color: var(--br); }

    /* ── Bottom row ───────────────────────── */
    .lp-check-row {
      display: flex;
      align-items: center;
      gap: 9px;
      margin-bottom: 28px;
    }
    .lp-check-row input[type="checkbox"] {
      width: 17px; height: 17px;
      accent-color: var(--br);
      cursor: pointer;
      flex-shrink: 0;
    }
    .lp-check-row label {
      font-size: .8125rem;
      color: var(--mt);
      cursor: pointer;
      user-select: none;
    }

    /* ── Submit ───────────────────────────── */
    .lp-submit {
      position: relative;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      width: 100%;
      height: 52px;
      background: var(--br);
      color: #fff;
      border: none;
      border-radius: 8px;
      font-family: inherit;
      font-size: 1rem;
      font-weight: 700;
      letter-spacing: .02em;
      cursor: pointer;
      transition: background .15s, transform .1s, box-shadow .15s;
      box-shadow: 0 4px 20px rgba(93,58,26,.3);
      overflow: hidden;
    }
    .lp-submit::after {
      content: '';
      position: absolute;
      inset: 0;
      background: linear-gradient(135deg, rgba(255,255,255,.09) 0%, transparent 60%);
    }
    .lp-submit:hover  { background: var(--br-d); box-shadow: 0 6px 28px rgba(93,58,26,.38); }
    .lp-submit:active { transform: translateY(1px); box-shadow: 0 2px 12px rgba(93,58,26,.25); }

    /* ── Security notice ──────────────────── */
    .lp-secure {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 7px;
      margin-top: 22px;
      font-size: .6875rem;
      color: #BCAFA5;
    }
    .lp-secure-dot { color: var(--bd); }
    .lp-secure i { color: #8BB98B; font-size: .625rem; }

    /* ── Footer ───────────────────────────── */
    .lp-form-foot {
      margin-top: 44px;
      padding-top: 22px;
      border-top: 1px solid var(--bd);
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    .lp-foot-copy {
      font-size: .6875rem;
      color: #C5B9B0;
    }
    .lp-foot-badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      font-size: .625rem;
      font-weight: 700;
      color: #C5B9B0;
      letter-spacing: .08em;
      text-transform: uppercase;
    }
    .lp-foot-badge svg { opacity: .5; }

    /* ═══════════════════════════════════════════
       RESPONSIVE
    ═══════════════════════════════════════════ */
    @media (max-width: 960px) {
      .lp { flex-direction: column; }
      .lp-left {
        width: 100%;
        min-height: auto;
        padding: 36px 32px 32px;
        flex-direction: row;
        align-items: center;
        flex-wrap: wrap;
        gap: 0;
      }
      .lp-mid, .lp-bot, .lp-watermark, .lp-corner { display: none; }
      .lp-right { padding: 40px 32px 52px; }
    }
    @media (max-width: 560px) {
      .lp-left  { padding: 28px 20px; }
      .lp-right { padding: 32px 20px 48px; }
      .lp-form-title { font-size: 1.625rem; }
    }
  </style>
</head>
<body>

<div class="lp">

  <!-- ═══════════ LEFT BRAND ═══════════ -->
  <div class="lp-left">

    {{-- Atmospheric corner lines --}}
    <svg class="lp-corner" viewBox="0 0 180 180" fill="none">
      <line x1="180" y1="0"   x2="0"   y2="180" stroke="white" stroke-width="1"/>
      <line x1="180" y1="30"  x2="30"  y2="180" stroke="white" stroke-width="1"/>
      <line x1="180" y1="60"  x2="60"  y2="180" stroke="white" stroke-width="1"/>
      <line x1="180" y1="90"  x2="90"  y2="180" stroke="white" stroke-width="1"/>
      <line x1="180" y1="120" x2="120" y2="180" stroke="white" stroke-width="1"/>
    </svg>

    {{-- Large watermark scales --}}
    <svg class="lp-watermark" viewBox="0 0 320 320" fill="none">
      <line x1="160" y1="20"  x2="160" y2="300" stroke="white" stroke-width="5" stroke-linecap="round"/>
      <line x1="30"  y1="78"  x2="290" y2="78"  stroke="white" stroke-width="5" stroke-linecap="round"/>
      <line x1="30"  y1="78"  x2="30"  y2="148" stroke="white" stroke-width="3" stroke-linecap="round"/>
      <path d="M0 148 Q30 185 60 148" stroke="white" stroke-width="4" fill="none" stroke-linecap="round"/>
      <line x1="290" y1="78"  x2="290" y2="138" stroke="white" stroke-width="3" stroke-linecap="round"/>
      <path d="M260 138 Q290 175 320 138" stroke="white" stroke-width="4" fill="none" stroke-linecap="round"/>
      <circle cx="160" cy="20" r="8"   fill="white"/>
      <rect   x="130" y="295" width="60" height="10" rx="5" fill="white"/>
    </svg>

    <!-- TOP: Brand mark -->
    <div class="lp-top">
      <div style="background:#fff;border-radius:10px;padding:8px 16px;display:inline-flex;align-items:center;">
        <img src="{{ asset('images/logo-horizontal.png') }}"
             alt="ONYX Advocates"
             style="height:40px;width:auto;display:block;">
      </div>
    </div>

    <!-- MID: Headlines + features -->
    <div class="lp-mid">
      <div class="lp-rule"></div>

      <h1 class="lp-heading">
        Justice.<br>Clarity.<br><em>Results.</em>
      </h1>

      <p class="lp-desc">
        Uganda's premier advocates &mdash; case management, client records, documents, and finances. All in one place.
      </p>

      <div class="lp-feats">
        <div class="lp-feat">
          <div class="lp-feat-icon"><i class="fas fa-scale-balanced"></i></div>
          <span class="lp-feat-text">Full legal case lifecycle management</span>
        </div>
        <div class="lp-feat">
          <div class="lp-feat-icon"><i class="fas fa-folder-open"></i></div>
          <span class="lp-feat-text">Secure client &amp; document vault</span>
        </div>
        <div class="lp-feat">
          <div class="lp-feat-icon"><i class="fas fa-money-bill-transfer"></i></div>
          <span class="lp-feat-text">Finance, accounts &amp; reporting</span>
        </div>
        <div class="lp-feat">
          <div class="lp-feat-icon"><i class="fas fa-users"></i></div>
          <span class="lp-feat-text">Multi-role team collaboration</span>
        </div>
      </div>
    </div>

    <!-- BOTTOM: Legal fine print -->
    <div class="lp-bot">
      <p class="lp-fine">
        <strong>ONYX Legal · Kampala, Uganda</strong><br>
        Authorised staff access only. All activity is monitored and recorded.
      </p>
    </div>

  </div>

  <!-- ═══════════ RIGHT FORM ═══════════ -->
  <div class="lp-right">
    <div class="lp-form-wrap">

      <div class="lp-form-eyebrow">Staff Portal</div>
      <h2 class="lp-form-title">Sign in to your<br>account</h2>
      <p class="lp-form-sub">Enter your staff email and password to access the system.</p>

      @if(session('status'))
        <div class="lp-alert lp-alert-inf">
          <i class="fas fa-info-circle"></i>
          <div>{{ session('status') }}</div>
        </div>
      @endif

      @if($errors->any())
        <div class="lp-alert lp-alert-err">
          <i class="fas fa-circle-exclamation"></i>
          <div>
            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
          </div>
        </div>
      @endif

      <form method="POST" action="{{ route('admin.login') }}">
        @csrf

        <div class="lp-field">
          <label class="lp-label" for="email">Email Address</label>
          <div class="lp-inr">
            <input class="lp-inp" type="email" id="email" name="email"
              value="{{ old('email') }}"
              placeholder="you@onyxlegal.ug"
              required autofocus autocomplete="email">
            <i class="fas fa-envelope lp-ico"></i>
          </div>
        </div>

        <div class="lp-field">
          <label class="lp-label" for="password">Password</label>
          <div class="lp-inr">
            <input class="lp-inp" type="password" id="pw" name="password"
              placeholder="••••••••••••"
              required autocomplete="current-password"
              style="padding-right:44px;">
            <i class="fas fa-lock lp-ico"></i>
            <button type="button" class="lp-eye" id="eyeBtn" title="Show / hide password" tabindex="-1">
              <i class="fas fa-eye" id="eyeIco"></i>
            </button>
          </div>
        </div>

        <div class="lp-check-row">
          <input type="checkbox" id="remember" name="remember">
          <label for="remember">Keep me signed in</label>
        </div>

        <button type="submit" class="lp-submit">
          <i class="fas fa-right-to-bracket"></i>
          Sign In
        </button>
      </form>

      <div class="lp-secure">
        <i class="fas fa-shield-halved"></i>
        <span>Encrypted connection</span>
        <span class="lp-secure-dot">&bull;</span>
        <span>Access logged</span>
        <span class="lp-secure-dot">&bull;</span>
        <span>Authorised staff only</span>
      </div>

      <div class="lp-form-foot">
        <span class="lp-foot-copy">&copy; {{ date('Y') }} ONYX Legal &mdash; All rights reserved</span>
        <span class="lp-foot-badge">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M12 2L3 7v5c0 5.25 3.75 10.15 9 11.25C17.25 22.15 21 17.25 21 12V7L12 2z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>
          Uganda
        </span>
      </div>

    </div>
  </div>

</div>

<script>
(function(){
  var btn = document.getElementById('eyeBtn');
  var inp = document.getElementById('pw');
  var ico = document.getElementById('eyeIco');
  if (btn) btn.addEventListener('click', function(){
    if (inp.type === 'password') {
      inp.type = 'text';
      ico.className = 'fas fa-eye-slash';
    } else {
      inp.type = 'password';
      ico.className = 'fas fa-eye';
    }
  });
})();
</script>

</body>
</html>
