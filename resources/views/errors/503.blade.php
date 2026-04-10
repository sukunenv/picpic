<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Picpic – Sebentar ya...</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #6367FF;
            --primary-light: #8589FF;
            --primary-dark: #4a4ed4;
            --glow: rgba(99, 103, 255, 0.4);
        }

        html, body {
            height: 100%;
            font-family: 'Outfit', sans-serif;
            background-color: var(--primary);
            overflow: hidden;
        }

        /* === Animated background blobs === */
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            position: relative;
        }

        .blob {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.35;
            animation: float 8s ease-in-out infinite;
            pointer-events: none;
            z-index: 0;
        }

        .blob-1 {
            width: 500px;
            height: 500px;
            background: #a0a3ff;
            top: -150px;
            left: -150px;
            animation-delay: 0s;
        }

        .blob-2 {
            width: 400px;
            height: 400px;
            background: #4a4ed4;
            bottom: -100px;
            right: -100px;
            animation-delay: 3s;
        }

        .blob-3 {
            width: 300px;
            height: 300px;
            background: #c3c5ff;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            animation-delay: 1.5s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) scale(1); }
            50% { transform: translateY(-30px) scale(1.05); }
        }

        .blob-3 {
            animation: float-center 8s ease-in-out infinite;
            animation-delay: 1.5s;
        }

        @keyframes float-center {
            0%, 100% { transform: translate(-50%, -50%) scale(1); }
            50% { transform: translate(-50%, calc(-50% - 20px)) scale(1.08); }
        }

        /* === Card container === */
        .card {
            position: relative;
            z-index: 10;
            text-align: center;
            padding: 3rem 2.5rem 2.5rem;
            max-width: 440px;
            width: 90%;
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 28px;
            box-shadow:
                0 8px 32px rgba(0, 0, 0, 0.2),
                0 0 80px rgba(99, 103, 255, 0.25),
                inset 0 1px 0 rgba(255,255,255,0.2);
            animation: card-appear 0.7s cubic-bezier(0.34, 1.56, 0.64, 1) both;
        }

        @keyframes card-appear {
            0% { opacity: 0; transform: translateY(30px) scale(0.95); }
            100% { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* === Logo === */
        .logo-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 2rem;
        }

        .logo-ring {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 110px;
            height: 110px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.12);
            border: 2px solid rgba(255, 255, 255, 0.25);
            box-shadow: 0 0 40px rgba(99, 103, 255, 0.5);
            animation: pulse-ring 3s ease-in-out infinite;
        }

        @keyframes pulse-ring {
            0%, 100% { box-shadow: 0 0 40px rgba(99, 103, 255, 0.5); }
            50% { box-shadow: 0 0 60px rgba(99, 103, 255, 0.8), 0 0 80px rgba(160, 163, 255, 0.4); }
        }

        .logo-ring img {
            width: 72px;
            height: 72px;
            object-fit: contain;
            border-radius: 16px;
            animation: logo-breathe 4s ease-in-out infinite;
        }

        @keyframes logo-breathe {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.06); }
        }

        /* === Heading === */
        .headline {
            font-size: 2.4rem;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.5px;
            line-height: 1.15;
            margin-bottom: 0.75rem;
            text-shadow: 0 2px 20px rgba(0,0,0,0.2);
        }

        .sub {
            font-size: 1rem;
            font-weight: 400;
            color: rgba(255, 255, 255, 0.78);
            line-height: 1.6;
            margin-bottom: 2.5rem;
            padding: 0 0.5rem;
        }

        /* === Three dots loading === */
        .dots-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 2.5rem;
        }

        .dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.9);
            animation: bounce-dot 1.4s ease-in-out infinite;
            box-shadow: 0 0 12px rgba(255,255,255,0.6);
        }

        .dot:nth-child(1) { animation-delay: 0s; }
        .dot:nth-child(2) { animation-delay: 0.2s; }
        .dot:nth-child(3) { animation-delay: 0.4s; }

        @keyframes bounce-dot {
            0%, 80%, 100% {
                transform: translateY(0) scale(1);
                opacity: 0.5;
            }
            40% {
                transform: translateY(-14px) scale(1.2);
                opacity: 1;
            }
        }

        /* === Divider === */
        .divider {
            width: 100%;
            height: 1px;
            background: rgba(255, 255, 255, 0.15);
            margin-bottom: 1.5rem;
        }

        /* === Footer === */
        .footer-text {
            font-size: 0.82rem;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.45);
        }

        /* === Responsive === */
        @media (max-width: 400px) {
            .headline { font-size: 2rem; }
            .card { padding: 2.5rem 1.75rem 2rem; }
            .logo-ring { width: 96px; height: 96px; }
            .logo-ring img { width: 62px; height: 62px; }
        }
    </style>
</head>
<body>

    <!-- Background blobs -->
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>

    <!-- Main card -->
    <div class="card">

        <!-- Logo -->
        <div class="logo-wrapper">
            <div class="logo-ring">
                <img src="/logo.png" alt="Picpic Logo">
            </div>
        </div>

        <!-- Headline -->
        <h1 class="headline">Sebentar ya...</h1>

        <!-- Sub -->
        <p class="sub">
            Kami sedang menyiapkan sesuatu<br>
            yang lebih baik ☕
        </p>

        <!-- Loading dots -->
        <div class="dots-wrapper" aria-label="Loading...">
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
        </div>

        <!-- Divider -->
        <div class="divider"></div>

        <!-- Footer -->
        <p class="footer-text">Picpic &mdash; kumpul mencerita</p>

    </div>

</body>
</html>
