# TA_Sistem-Manajemen-Organisasi-Mahasiswa_Kel23

# TA: Sistem Manajemen Organisasi Mahasiswa - Kelompok 23

Sistem ini dirancang untuk mengelola data anggota, periode kepengurusan, dan log aktivitas organisasi secara efisien.

##  Tim Pengembang
* **Bambang irawan** - PM GALAK
* **Farrell Fausto** - Documentation & Git Manager
* **Anisa Anastasya** - Database Engineer
* **Rhea Alya K** - Database Engineer
* **Rabelva Evan Ligar** - Backend Developer
* **Jedan** - Backend Developer
* [Nama teman] - Backend Developer
* **Mirah** - Frontend Developer
* **Aufan** - Frontend Developer

## 🛠️ Tech Stack
* **Language:** PHP Native
* **Database:** MySQL
* **Tools:** XAMPP, VS Code, StarUML/Draw.io

## 🚀 Cara Instalasi
1. Clone repository ini ke folder `htdocs`.
2. Import file database dari `/database/schema.sql` ke phpMyAdmin.
3. Sesuaikan konfigurasi database di `/config/database.php`.
4. Akses melalui browser di `localhost/TA_Sistem-Manajemen-Organisasi-Mahasiswa_Kel23`.

## 📌 Aturan Kontribusi (Git)
1. **Dilarang keras** melakukan push langsung ke branch `main`.
2. Buat branch baru untuk setiap fitur: `feat-namafitur` atau `fix-namafitur`.
3. Setelah selesai, buka **Pull Request** dan tunggu review dari Git Manager.

🛠 Panduan Standardisasi Branch (Internal Audit)

Setiap anggota **WAJIB** mengikuti format nama branch agar mudah di-audit. Jangan ada lagi yang koding di `main`!

### **1. Aturan Penamaan Branch**
| Kategori | Format Nama | Contoh |
| :--- | :--- | :--- |
| **Fitur Baru** | `feat-[nama-fitur]` | `feat-periode`, `feat-log-aktivitas` |
| **Perbaikan Bug/UI** | `fix-[bagian-perbaikan]` | `fix-ui-navbar`, `fix-query-anggota` |
| **Dokumentasi** | `docs-[isi-doc]` | `docs-readme`, `docs-database-erd` |


### **2. Cara Membuat Branch Baru (Terminal)**
Sebelum mulai koding fitur baru, pastikan kalian berada di branch `main` yang paling *update*, lalu buat branch baru:

```bash
# 1. Balik ke main dan ambil update terbaru dari Auditor
git checkout main
git pull origin main

# 2. Buat branch baru sesuai jobdesk kalian
# Misal: lu dapet tugas bikin Log Aktivitas
git checkout -b feat-log-aktivitas
```

### **3. Cara Mengirim Kerjaanke GitHub**
Setelah selesai koding di branch tersebut, kirim ke GitHub agar bisa di-audit:

```bash
# 1. Simpan perubahan
git add .
git commit -m "feat: menyelesaikan sistem log aktivitas"

# 2. Push branch kalian ke GitHub (BUKAN KE MAIN!)
git push origin feat-log-aktivitas
```

---

### **4. Langkah Terakhir: Pull Request (PR)**
Setelah kalian `git push`, buka GitHub dan klik tombol **"Compare & pull request"**.
*   **Tag Auditor**: Mention gua di deskripsi biar gua cek kodingannya.
*   **Tunggu Review**: Jangan di-*merge* sendiri. Tunggu persetujuan (Approve) dari gua sebagai **IT Auditor**.

