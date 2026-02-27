<?php

$pageTitle = $pageTitle ?? 'Rappelez-moi';
$apiUrl = '/rappel/api';
?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($pageTitle) ?> — Rappelez-moi</title>
<meta name="description" content="La plateforme de mise en relation intelligente entre particuliers et experts de confiance.">

<!-- Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<!-- Tailwind CSS CDN Play -->
<script src="https://cdn.tailwindcss.com"></script>
<script>
tailwind.config = {
  theme: {
    extend: {
      colors: {
        brand: {
          50: '#f4f6fb', 100: '#e9edf7', 200: '#c8d2eb', 300: '#a7b7df',
          400: '#6581c7', 500: '#234bae', 600: '#1e4093', 700: '#19357b',
          800: '#142a62', 900: '#0E1648', 950: '#0a1036',
        },
        accent: {
          50: '#f6fdf4', 100: '#edf9e9', 200: '#d1f1c7', 300: '#b6e9a5',
          400: '#80d961', 500: '#7CCB63', 600: '#70b759', 700: '#568c44',
          800: '#436d35', 900: '#37592b',
        },
        navy: {
          50: '#f5f6f8', 100: '#eceef2', 200: '#cfd4de', 300: '#b1bac9',
          400: '#7786a0', 500: '#3d5277', 600: '#374a6b', 700: '#2e3d59',
          800: '#253147', 900: '#0E1648', 950: '#0a1036',
        },
      },
      fontFamily: {
        sans: ['Inter', 'system-ui', 'sans-serif'],
        display: ['Outfit', 'Inter', 'sans-serif'],
      },
      boxShadow: {
        'glass': '0 8px 32px 0 rgba(31, 38, 135, 0.07)',
        'premium': '0 20px 50px -12px rgba(0, 0, 0, 0.08)',
        'glow': '0 0 20px rgba(16, 185, 129, 0.25)',
      },
      animation: {
        'fade-in-up': 'fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards',
        'fade-in': 'fadeIn 0.5s ease-out forwards',
        'float': 'float 6s ease-in-out infinite',
        'shimmer': 'shimmer 2.5s infinite linear',
      },
      keyframes: {
        fadeInUp: { '0%': { opacity: '0', transform: 'translateY(30px)' }, '100%': { opacity: '1', transform: 'translateY(0)' } },
        fadeIn:   { '0%': { opacity: '0' }, '100%': { opacity: '1' } },
        float:    { '0%, 100%': { transform: 'translateY(0)' }, '50%': { transform: 'translateY(-20px)' } },
        shimmer:  { '0%': { backgroundPosition: '-1000px 0' }, '100%': { backgroundPosition: '1000px 0' } },
      }
    }
  }
}
</script>

<!-- Custom CSS -->
<link rel="stylesheet" href="/rappel/public/assets/css/app.css?v=4.4">

<!-- Lucide Icons -->
<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>

<?= $extraHead ?? '' ?>
