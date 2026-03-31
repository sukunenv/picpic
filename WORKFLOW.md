# Workflow Pengembangan

## Branch Layout
- **`development`**: Digunakan untuk pengerjaan fitur baru, perbaikan bug, dan pengujian lokal.
- **`main`**: Digunakan untuk versi produksi. Perubahan di branch ini akan otomatis di-deploy ke Railway.

## Cara Menggunakan
1. Pastikan Anda selalu bekerja di branch `development`.
2. Setelah fitur selesai dan diuji secara lokal, merge ke branch `main`.

### Proses Merge ke Production (`main`)
Jalankan perintah berikut:
```bash
git checkout main
git merge development
git push origin main
git checkout development
```
