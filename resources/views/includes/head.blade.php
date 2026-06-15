<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>@yield('title', 'Balantro')</title>

<script src="https://cdn.tailwindcss.com"></script>

<link href="https://fonts.googleapis.com/css2?family=Outfit&family=Inter&display=swap" rel="stylesheet">
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<script>
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            fontFamily: {
              sans: ["Inter", "sans-serif"],
              display: ["Outfit", "sans-serif"],
            },
            colors: {
              balantro: {
                navy: "#02040a",
                primary: "#0EA5E9",
                secondary: "#22D3EE",
                glow: "#1d4ed8",
              },
            },
            animation: {
              "float-slow": "float 8s ease-in-out infinite",
            },
            keyframes: {
              float: {
                "0%, 100%": { transform: "translateY(0)" },
                "50%": { transform: "translateY(-15px)" },
              },
            },
          },
        },
      };
    </script>
<link href="{{ asset('css/style.css') }}" rel="stylesheet">
<!-- Intro Animation Logic (Head) -->
    <script>
      if (sessionStorage.getItem("balantroIntroPlayed")) {
        document.documentElement.classList.add("skip-intro");
      } else {
        document.documentElement.classList.add("intro-running");
      }
    </script>
    <style>
      html.intro-running body,
      html.intro-running body * {
        animation-play-state: paused !important;
      }

      html.skip-intro #intro-overlay {
        display: none !important;
      }

      #intro-overlay {
        position: fixed;
        inset: 0;
        background-color: #02040a;
        z-index: 99999;
        display: flex;
        align-items: center;
        justify-content: center;
        transition:
          opacity 0.8s ease-in-out,
          visibility 0.8s ease-in-out;
      }

      #intro-white-bg {
        position: absolute;
        top: 50%;
        left: 0;
        width: 100%;
        height: 0;
        background-color: #ffffff;
        transform: translateY(-50%);
        transition: height 0.6s cubic-bezier(0.85, 0, 0.15, 1);
        z-index: 1;
      }

      #intro-white-bg.expand-line {
        height: 1px;
      }

      #intro-white-bg.expand-full {
        height: 100%;
      }

      .intro-container {
        display: flex;
        align-items: center;
        justify-content: center;
        transform: scale(1.15);
        transition:
          transform 2.5s cubic-bezier(0.16, 1, 0.3, 1),
          opacity 1.2s ease;
        position: relative;
        z-index: 2;
        opacity: 0;
      }

      .intro-container.show {
        opacity: 1;
      }

      .intro-logo-img {
        height: clamp(60px, 12vw, 180px);
        z-index: 10;
        position: relative;
        transition: transform 2.5s cubic-bezier(0.16, 1, 0.3, 1);
      }

      .intro-text-wrapper {
        overflow: hidden;
        max-width: 0;
        opacity: 0;
        transition:
          max-width 2.5s cubic-bezier(0.16, 1, 0.3, 1),
          opacity 0.8s ease,
          filter 1.5s ease;
        display: flex;
        align-items: center;
        filter: blur(10px);
      }

      .intro-text-wrapper.expand {
        max-width: 1500px;
        opacity: 1;
        filter: blur(0);
      }

      .intro-text {
        font-family: "Outfit", sans-serif;
        font-size: clamp(60px, 18vw, 220px);
        font-weight: 700;
        color: #02040a;
        padding-left: clamp(10px, 2vw, 24px);
        letter-spacing: -0.04em;
        white-space: nowrap;
        line-height: 1;
      }

      .intro-container.zoom-out {
        transform: scale(1);
        opacity: 0;
      }

      #intro-overlay.hide {
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
      }
    </style>