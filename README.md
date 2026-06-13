# 🤖 GDGOC Laravel AI Chatbot

> Demo aplikasi Chatbot yang mengimplementasikan **5 modul inti Laravel AI SDK** — dibangun untuk sesi belajar GDGOC (Google Developer Groups on Campus).

![Laravel](https://img.shields.io/badge/Laravel-13.x-FF2D20?style=flat-square&logo=laravel)
![Livewire](https://img.shields.io/badge/Livewire-4.x-4E56A6?style=flat-square)
![Laravel AI SDK](https://img.shields.io/badge/Laravel_AI_SDK-0.8.x-F59E0B?style=flat-square)
![Gemini](https://img.shields.io/badge/Google_Gemini-2.5_Flash-4285F4?style=flat-square&logo=google)

---

## 🚀 Setup untuk Peserta (3 langkah)

### 1. Clone & setup otomatis

```bash
git clone https://github.com/farizarvin/gdgoc-laravel-ai-sdk.git
cd gdgoc-laravel-ai-sdk
composer run setup
```

> Perintah `composer run setup` akan otomatis: install dependencies, copy `.env`, generate key, buat SQLite database, jalankan migration, seed data demo, dan build assets.

### 2. Isi API Key Gemini

Buka file `.env`, cari baris `GEMINI_API_KEY` dan isi dengan key kamu:

```env
GEMINI_API_KEY=AIzaSy...isi_key_kamu_di_sini
```

> 🔑 Dapatkan API key **gratis** di: **https://aistudio.google.com/apikey**

### 3. Jalankan

```bash
composer run dev
```

Buka browser: **http://localhost:8000/chat** ✅

---

## 📋 Requirements

Pastikan sudah terinstall sebelum mulai:

| Tool | Versi | Cek |
|------|-------|-----|
| PHP | ≥ 8.3 | `php -v` |
| Composer | ≥ 2.x | `composer -V` |
| Node.js | ≥ 20.x | `node -v` |
| npm | ≥ 10.x | `npm -v` |

---

## ✨ Fitur Aplikasi

- 💬 **Real-time Chat** — Powered by Livewire + Google Gemini API
- 🎛️ **Interactive Sidebar** — Konfigurasi model, system prompt, dan tools secara live
- ⚙️ **Tool Calling** — AI bisa memanggil PHP code (`GetSystemInfo`) secara otomatis
- 🧠 **Memory** — Riwayat percakapan disimpan di SQLite dan di-load ulang saat refresh
- 📊 **Live Stats** — Lihat response time, tool calls, dan conversation stats di sidebar

---

## 🗂️ 5 Modul Laravel AI SDK

| # | Modul | File | Fungsi |
|---|-------|------|--------|
| 1 | **Models** | `app/Agents/AssistantAgent.php` | Pilih model AI via `#[Provider]` + runtime selector |
| 2 | **Prompts** | `app/Agents/AssistantAgent.php` | System instructions via `instructions()` |
| 3 | **Agents** | `app/Agents/AssistantAgent.php` | Orkestrasi semua modul |
| 4 | **Tools** | `app/Tools/GetSystemInfo.php` | AI memanggil PHP code otomatis |
| 5 | **Memory** | `RemembersConversations` trait | History tersimpan di SQLite |

---

## 🎓 Skenario Demo

### Demo 1 — Models
Buka sidebar → **Models** → ganti ke model lain → kirim pesan → bandingkan response time

### Demo 2 — Prompts
Sidebar → **Prompts** → edit system instructions → kirim pesan → lihat perubahan karakter AI

### Demo 3 — Tool Calling
Kirim: *"Berapa jumlah user di database dan jam berapa sekarang?"*
→ Lihat blok **TOOL RESULT** di chat & badge **CALLED!** di sidebar

### Demo 4 — Memory
1. Kirim: *"Nama saya Budi"*
2. Refresh halaman (F5)
3. Kirim: *"Masih ingat nama saya?"* → AI tetap ingat! 🧠

---

## 📁 Struktur File Penting

```
app/
├── Agents/AssistantAgent.php     # Agent utama (Models + Prompts + Agents)
├── Livewire/Chatbot.php          # Livewire component + config state
└── Tools/GetSystemInfo.php       # Custom tool (Tools module)

config/ai.php                     # Provider & conversations config

database/seeders/
├── UserSeeder.php                # 10 demo users
└── AgentConversationSeeder.php   # 3 demo conversations

resources/views/livewire/chatbot.blade.php  # Chat UI + interactive sidebar
```

---

## ⚙️ Commands Berguna

```bash
composer run setup          # Setup lengkap dari awal (jalankan sekali)
composer run dev            # Jalankan development server

php artisan migrate:fresh --seed   # Reset database + seed ulang
php artisan db:seed                # Seed tanpa reset
```

---

## 🛠️ Tech Stack

- **Framework**: Laravel 13 + Livewire 4
- **Styling**: Tailwind CSS v4
- **AI SDK**: `laravel/ai` v0.8
- **AI Provider**: Google Gemini (`gemini-2.5-flash`)
- **Database**: SQLite
- **Build Tool**: Vite

---

## ❓ Troubleshooting

| Error | Solusi |
|-------|--------|
| `GEMINI_API_KEY not set` | Isi `GEMINI_API_KEY` di file `.env` |
| `404: model not found` | Pastikan model di `AssistantAgent.php` adalah `gemini-2.5-flash` |
| `rate limited` | Tunggu 1 menit atau ganti ke `gemini-2.0-flash-lite` (limit lebih tinggi) |
| `Database not found` | Jalankan `php artisan migrate` atau `composer run setup` ulang |
| Enter tidak bisa send | Pastikan `wire:model.live` ada di input field |

---

*Dibuat untuk **GDGOC Laravel Sesi 2** · Laravel AI SDK Demo*
