# 🤖 GDGOC Laravel AI Chatbot

> Demo aplikasi Chatbot yang mengimplementasikan **5 modul inti Laravel AI SDK** — dibangun untuk sesi belajar GDGOC (Google Developer Groups on Campus).

![Laravel](https://img.shields.io/badge/Laravel-13.x-FF2D20?style=flat-square&logo=laravel)
![Livewire](https://img.shields.io/badge/Livewire-4.x-4E56A6?style=flat-square)
![Laravel AI SDK](https://img.shields.io/badge/Laravel_AI_SDK-0.8.x-F59E0B?style=flat-square)
![Gemini](https://img.shields.io/badge/Google_Gemini-2.5_Flash-4285F4?style=flat-square&logo=google)

---

## ✨ Fitur

- 💬 **Real-time Chat** — Powered by Livewire + Google Gemini API
- 🎛️ **Interactive Sidebar** — Konfigurasi model, system prompt, dan tools secara live
- ⚙️ **Tool Calling** — AI bisa memanggil PHP code (`GetSystemInfo`) secara otomatis
- 🧠 **Memory** — Riwayat percakapan disimpan di SQLite dan di-load ulang saat refresh
- 📊 **Live Stats** — Lihat response time, tool calls, dan conversation stats di sidebar

---

## 🗂️ 5 Modul Laravel AI SDK

| # | Modul | Implementasi |
|---|-------|-------------|
| 1 | **Models** | `#[Provider('gemini')]` + runtime model selector |
| 2 | **Prompts** | `instructions()` method di `AssistantAgent` |
| 3 | **Agents** | `AssistantAgent` implements `Agent`, `Conversational`, `HasTools` |
| 4 | **Tools** | `GetSystemInfo` implements `Tool` — returns server time & DB stats |
| 5 | **Memory** | `RemembersConversations` trait + SQLite `agent_conversations` table |

---

## 🚀 Setup & Instalasi

### 1. Clone repo

```bash
git clone https://github.com/farizarvin/gdgoc-laravel-ai-sdk.git
cd gdgoc-laravel-ai-sdk
```

### 2. Install dependencies

```bash
composer install
npm install
```

### 3. Setup environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` dan isi API key:

```env
GEMINI_API_KEY=AIzaSy...   # Dapatkan di https://aistudio.google.com/apikey
```

### 4. Setup database

```bash
touch database/database.sqlite
php artisan migrate
php artisan db:seed
```

### 5. Jalankan

```bash
composer run dev
```

Buka: **http://localhost:8000/chat**

---

## 📁 Struktur File Penting

```
app/
├── Agents/
│   └── AssistantAgent.php     # Agent utama (Models + Prompts + Agents)
├── Livewire/
│   └── Chatbot.php            # Livewire component + config state
└── Tools/
    └── GetSystemInfo.php      # Custom tool (Tools module)

config/
└── ai.php                     # Provider & conversations config

database/
├── migrations/
│   └── ..._create_agent_conversations_table.php
└── seeders/
    ├── DatabaseSeeder.php
    ├── UserSeeder.php
    └── AgentConversationSeeder.php

resources/views/livewire/
└── chatbot.blade.php          # Chat UI + interactive sidebar
```

---

## 🎓 Skenario Demo (untuk presentasi)

### Demo 1 — Models
Buka sidebar → **Models** → ganti model → kirim pesan → bandingkan response time

### Demo 2 — Prompts  
Sidebar → **Prompts** → edit system instructions → kirim pesan → AI berubah karakter

### Demo 3 — Tools
Kirim: *"Berapa jumlah user di database dan jam berapa sekarang?"*  
Lihat blok **TOOL RESULT** di chat & badge **CALLED!** di sidebar

### Demo 4 — Memory
1. Kirim: *"Nama saya Budi"*
2. Refresh halaman (F5)
3. Kirim: *"Masih ingat nama saya?"* → AI jawab "Budi"!

---

## ⚙️ Konfigurasi

### Models yang tersedia

| Model | RPM Free | Keterangan |
|-------|---------|-----------|
| `gemini-2.5-flash` | 10 | Terbaru & terpintar (default) |
| `gemini-2.0-flash` | 15 | Stabil & cepat |
| `gemini-2.0-flash-lite` | 30 | Paling cepat, limit tinggi |

### Database Commands

```bash
php artisan migrate:fresh --seed    # Reset + seed ulang
php artisan db:seed                 # Seed tanpa reset
php artisan db:seed --class=AgentConversationSeeder  # Seed conversations saja
```

---

## 🛠️ Tech Stack

- **Framework**: Laravel 13
- **Frontend**: Livewire 4 + Tailwind CSS v4
- **AI SDK**: `laravel/ai` v0.8
- **AI Provider**: Google Gemini (via `gemini` driver)
- **Database**: SQLite
- **Build Tool**: Vite

---

## 📝 Lisensi

MIT — bebas digunakan untuk keperluan belajar dan demo.

---

*Dibuat untuk **GDGOC Laravel Sesi 2** · Laravel AI SDK Demo*
