# SantuyTL Discord Bot (Modular & Standalone)

Bot Discord kustom dengan database lokal (SQLite) untuk sistem XP/Economy, namun tetap mendukung integrasi opsional dengan Laravel.

## Fitur
- **Standalone Economy**: Sistem XP, Level, dan Coins menggunakan database lokal (`data/santuy.db`).
- **Job System**: Pilih pekerjaan dan bekerja untuk dapat gaji (`/job`, `/work`). 
- **Premium Spin**: Gacha game premium gratis dari stok channel (`/spin`).
- **Modular Structure**: Codebase terpisah (API, Commands, Events, Services).
- **Express API**: Integrasi HTTP untuk menerima notifikasi dari Web/Laravel.
- **Discord Voice**: Join/Leave dan status channel.
- **Slash Commands**: Ping, Meme, Poll, Leaderboard (Lokal), dll.
- **Auto Sync**: Sinkronisasi Game & Event dari Discord ke Web (Opsional).

## Struktur Folder
- `data/` - Database SQLite (`santuy.db`)
- `src/api` - Router Express API
- `src/db` - Konfigurasi Database
- `src/commands` - Slash Commands handlers
- `src/config` - Konfigurasi env dan client
- `src/events` - Event handlers (ready, messageCreate, dll)
- `src/services` - Logic bisnis (AI, Voice, Meme)
- `src/utils` - Utilitas
- `src/cron` - Penjadwal tugas (node-cron)

## Cara Install

1. Install dependencies:
   ```bash
   npm install
   ```

2. Setup `.env`:
   Copy `.env.example` ke `.env` dan isi token/API key.

3. Deploy Slash Commands:
   ```bash
   npm run deploy-commands
   ```

4. Jalankan Bot:
   ```bash
   npm start
   ```

## Integrasi Laravel

Bot membuka HTTP server di port `3001` (default). Laravel dapat mengirim request ke:
- `http://localhost:3001/gacha/notify`
- `http://localhost:3001/voice/join`
dll.

Pastikan `WEBHOOK_URL` di `.env` bot mengarah ke endpoint Laravel yang valid untuk menerima update dari bot (XP, Coins).
