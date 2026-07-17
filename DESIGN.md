---
version: alpha
name: Absensi App
description: Sistem presensi karyawan modern dengan validasi perangkat dan lokasi.
colors:
  primary: "#1e293b"
  secondary: "#64748b"
  tertiary: "#2563eb"
  neutral: "#f8fafc"
  on-primary: "#ffffff"
  on-tertiary: "#ffffff"
typography:
  h1:
    fontFamily: Inter
    fontSize: 2.5rem
    fontWeight: 700
    lineHeight: 1.2
  body-md:
    fontFamily: Inter
    fontSize: 1rem
    lineHeight: 1.6
components:
  button-primary:
    backgroundColor: "{colors.tertiary}"
    textColor: "{colors.on-tertiary}"
    rounded: "{rounded.sm}"
    padding: 10px
  card:
    backgroundColor: "{colors.neutral}"
    textColor: "{colors.primary}"
    rounded: "{rounded.md}"
    padding: 20px
---

## Overview
Aplikasi Absensi adalah sistem presensi yang mengutamakan keamanan (device locking) dan kemudahan penggunaan bagi karyawan. Desain berfokus pada kejelasan informasi dan respons cepat untuk proses scan presensi.

## Colors
- **Primary ({colors.primary}):** Warna dominan untuk header dan teks utama.
- **Tertiary ({colors.tertiary}):** Warna aksi (tombol absen, tombol simpan).

## Typography
Menggunakan Inter untuk keterbacaan tinggi di berbagai ukuran layar.

## Komponen
- `button-primary` digunakan sebagai aksi utama (Absen/Simpan).
- `card` digunakan untuk membungkus data riwayat absensi.
