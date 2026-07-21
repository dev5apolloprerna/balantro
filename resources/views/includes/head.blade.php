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
