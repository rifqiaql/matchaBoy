# MatchaBoy - Setup Guide

## Project Overview

MatchaBoy adalah aplikasi tea house modern dengan tampilan dua-kolom (image + form) untuk Login dan Sign Up.

**Tech Stack:**

- Laravel 12.58.0
- Vite (asset bundler)
- Tailwind CSS (installed, optional untuk nanti)
- Manual CSS (current styling)

## Initial Setup (First Time)

### 1. Clone Repository

```powershell
git clone https://github.com/USERNAME/matchaboy.git
cd matchaboy
```

### 2. Install PHP Dependencies

```powershell
composer install
```

### 3. Setup Environment

```powershell
cp .env.example .env
php artisan key:generate
```

### 4. Install Node Dependencies

**Requirement:** Node.js v20.19.0 atau lebih tinggi

```powershell
npm install
```

Jika Node terlalu lama, gunakan nvm-windows:

```powershell
nvm install 20.19.0
nvm use 20.19.0
npm install
```

### 5. Start Vite Dev Server (Terminal 1)

```powershell
npm run dev
```

### 6. Start Laravel Server (Terminal 2)

```powershell
php artisan serve
```

Akses di: **http://127.0.0.1:8000**

## File Structure

```
matchaboy/
├── resources/
│   ├── css/
│   │   └── app.css          ← Main CSS (Login, Register styles)
│   ├── views/
│   │   ├── auth/
│   │   │   ├── login.blade.php      ← Login page
│   │   │   ├── register.blade.php   ← Sign Up page
│   │   │   └── ...
│   │   └── layouts/
│   │       └── app.blade.php        ← Main layout (@vite di sini)
│   └── js/
│       └── app.js
├── routes/
│   └── web.php              ← Route definitions
├── vite.config.js
├── package.json
└── composer.json
```

## CSS Architecture

**Current Approach:** Manual CSS (vanilla CSS in `resources/css/app.css`)

**CSS Classes:**

- `.login-split` — Container dua-kolom
- `.login-left` — Kolom gambar/teks (image side)
- `.login-right` — Kolom form (white card)
- `.card` — White container form
- `.form-group` — Input wrapper
- `.form-input` — Input styling
- `.btn-primary` — Green button
- `.small`, `.muted` — Text utilities

**Cara Edit CSS:**

1. Edit `resources/css/app.css`
2. Vite auto-reload → browser refresh

**Future:** Dapat refactor ke Tailwind CSS nanti kalau perlu.

## Pages & Routes

| Page    | Route       | File                                      |
| ------- | ----------- | ----------------------------------------- |
| Login   | `/login`    | `resources/views/auth/login.blade.php`    |
| Sign Up | `/register` | `resources/views/auth/register.blade.php` |
| Welcome | `/`         | `resources/views/welcome.blade.php`       |

## Notes untuk Tim

- ✓ Login & Register pages selesai dengan design dua-kolom
- ✓ CSS terpisah di `app.css` (maintainable)
- ✓ Responsive (mobile-first collapse di < 900px)
- ⚠ Password reset belum di-setup (rute `password.request` belum ada)
- ⚠ Registration validation belum lengkap
- ⚠ Email verification belum

## Next Steps

1. Setup password reset flow
2. Add database migrations & seeders
3. Implement user authentication logic
4. Add email verification
5. Create dashboard page

## Common Commands

```powershell
# Fresh start (reset everything)
php artisan migrate:fresh --seed

# Run tests
php artisan test

# Tinker (interactive shell)
php artisan tinker

# Build for production
npm run build
```

## Troubleshooting

**Vite error "manifest.json not found"**

- Jalankan `npm run dev` di terminal terpisah

**Node version error**

- Update Node ke v20.19.0+: `nvm install 20.19.0 && nvm use 20.19.0`

**CSS tidak update**

- Refresh browser (F5)
- Check Vite dev server terminal untuk error

---

_Last updated: May 6, 2026_
