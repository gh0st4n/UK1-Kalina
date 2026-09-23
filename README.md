# 🔐 Laporan Pentest — Aplikasi Management Data Siswa

Repositori ini berisi laporan hasil **Penetration Testing** terhadap aplikasi *Management Data Siswa* yang berjalan di lingkungan lab.

## 📋 Isi Laporan

Laporan lengkap dapat dilihat di:

👉 **[Laporan/Laporan.md](Laporan/Laporan.md)**

## 📊 Ringkasan Temuan

| Severity    | Jumlah |
|-------------|--------|
| 🔴 Critical | 2 |
| 🟠 High     | 1 |
| 🟡 Medium   | 2 |
| 🟢 Low      | 1 |

**Temuan utama:**
- Git Repository Exposure
- Credential Leak di Git History
- Database Dump Ke-commit
- Directory Listing Aktif
- Session Hijacking via HTTP
- Business Logic — Validasi NISN

## 🎯 Tujuan

Laporan ini dibuat untuk keperluan **pembelajaran** dan **authorized pentest**. Segala aktivitas yang dilakukan hanya pada lingkungan lab yang telah diizinkan.

## 📖 Cara Membaca

1. Buka file **[Laporan/Laporan.md](Laporan/Laporan.md)**
2. Baca **Ringkasan Eksekutif** untuk gambaran umum
3. Lihat **Temuan** untuk detail teknis + PoC
4. Ikuti **Rekomendasi Perbaikan** untuk mitigasi

## ⚠️ Disclaimer

> Laporan ini dibuat untuk keperluan pembelajaran / authorized pentest.  
> Penggunaan tanpa izin adalah **ilegal**.

---

**Author:** gh0st4n  
**Tanggal:** 21–23 September 2026
