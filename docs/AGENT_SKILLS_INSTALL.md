# Instalasi Skill Tambahan untuk Workflow Development

File ini berisi catatan instalasi alat bantu yang diminta pada brief project. Alat ini tidak wajib untuk menjalankan Laravel, tetapi dapat dipakai untuk workflow coding agent.

## Superpowers

Repository: https://github.com/obra/superpowers

Contoh instalasi untuk OpenCode:

```json
{
  "plugin": ["superpowers@git+https://github.com/obra/superpowers.git"]
}
```

Setelah itu restart OpenCode dan verifikasi dengan menanyakan daftar skill yang tersedia.

## RTK

Repository: https://github.com/rtk-ai/rtk

Contoh instalasi berbasis Cargo:

```bash
cargo install --git https://github.com/rtk-ai/rtk
rtk gain
rtk init -g
```

RTK dipakai untuk mengurangi output command yang terlalu panjang pada workflow coding agent. Untuk Windows Git Bash, pastikan perintah shell dan dependency seperti jq tersedia bila integrasi hook digunakan.
