# **Employee REST API – Laravel 11 + SQLite**

## 📌 **Deskripsi**

Project ini adalah REST API sederhana untuk mengelola data karyawan (**Employee**) menggunakan **Laravel 11** dan **SQLite**.
API mendukung operasi CRUD, validasi input, filtering, searching, dan pagination.

Struktur response dirancang konsisten dan mudah digunakan pada sistem frontend.

---

## 🛠️ **Teknologi yang Digunakan**

-   **Laravel 11.46.1**
-   **PHP 8.2.12**
-   **SQLite Database**
-   **Composer**
-   **Laravel HTTP API Resource Routing**

---

## 📁 **Struktur Data – Tabel `employees`**

Tabel `employees` dibuat melalui migration dan memiliki kolom:

| Kolom      | Tipe          | Keterangan                              |
| ---------- | ------------- | --------------------------------------- |
| id         | bigIncrements | Primary key                             |
| name       | string(100)   | Required                                |
| email      | string        | Required, unique                        |
| position   | string        | Required                                |
| salary     | integer       | Required, min 2.000.000, max 50.000.000 |
| status     | string        | active / inactive, default: active      |
| hired_at   | date          | Optional                                |
| created_at | timestamp     | Otomatis                                |
| updated_at | timestamp     | Otomatis                                |
| deleted_at | timestamp     | Soft delete                             |

---

## 🚀 **Cara Menjalankan Project**

### 1. Clone / Download Repo

```bash
git clone https://github.com/LightDani/nirwana-employee-api.git
cd nirwana-employee-api
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Buat File SQLite

```bash
touch database/database.sqlite
```

Atau buat manual pada folder `database/`.

### 4. Atur `.env` untuk SQLite

Ubah konfigurasi database menjadi:

```env
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

### 5. Jalankan Migration

```bash
php artisan migrate
```

### 6. Jalankan Development Server

```bash
php artisan serve
```

Server tersedia di:

```
http://127.0.0.1:8000
```

---

## 🧩 **Daftar Endpoint**

### 📍 **1. GET /api/employees**

Ambil daftar karyawan dengan pagination + filter + search.

**Query Params Opsional:**

-   `status=active|inactive`
-   `search=<keyword>`
-   `per_page=<number>`
-   `page=<number>`

**Contoh:**

```
GET /api/employees?status=active&search=budi&per_page=5
```

---

### 📍 **2. GET /api/employees/{id}**

Ambil detail 1 karyawan berdasarkan `id`.

**Response:**

-   `200 OK` jika ditemukan
-   `404 Not Found` jika tidak ada

---

### 📍 **3. POST /api/employees**

Tambah karyawan baru.

**Body JSON:**

```json
{
    "name": "Budi",
    "email": "budi@example.com",
    "position": "Staff",
    "salary": 3000000,
    "status": "active",
    "hired_at": "2024-01-10"
}
```

**Rules Validasi:**

-   name: required, string, max:100
-   email: required, valid email, unique
-   position: required
-   salary: integer, min 2000000, max 50000000
-   status: active/inactive (optional)
-   hired_at: date (optional)

**Response:**

-   `201 Created`
-   `422 Validation Error`

---

### 📍 **4. PUT /api/employees/{id}**

Update data karyawan.

**Catatan:**

-   Email harus unique kecuali untuk dirinya sendiri.

**Response:**

-   `200 OK`
-   `422 Validation Error`
-   `404 Not Found`

---

### 📍 **5. DELETE /api/employees/{id}**

Menghapus data karyawan (**soft delete**).

**Response:**

-   `200 OK`
-   `404 Not Found`

---

## 📦 **Contoh Response Sukses**

### **GET /api/employees**

```json
{
    "success": true,
    "message": "Employee list retrieved successfully.",
    "data": [
        {
            "id": 1,
            "name": "Budi",
            "email": "budi@example.com",
            "position": "Staff",
            "salary": 3000000,
            "status": "active",
            "hired_at": "2024-01-10",
            "created_at": "2024-02-01T12:00:00",
            "updated_at": "2024-02-01T12:00:00"
        }
    ],
    "meta": {
        "current_page": 1,
        "per_page": 10,
        "total": 1,
        "last_page": 1,
        "from": 1,
        "to": 1
    }
}
```

---

## 🛡️ **Error Formats**

### **Validasi Gagal (422)**

```json
{
    "success": false,
    "message": "Validation failed.",
    "errors": {
        "email": ["The email field must be a valid email address."]
    }
}
```

### **404 Not Found**

```json
{
    "success": false,
    "message": "Employee not found."
}
```

---

## 📘 **Struktur Project (Ringkas)**

```
app/
 └── Http/
     └── Controllers/
         └── EmployeeController.php
database/
 ├── database.sqlite
 └── migrations/
routes/
 ├── api.php
 └── web.php
bootstrap/
 └── app.php (registrasi route API)
```

---

## 🙌 **Fitur Tambahan**

-   Soft delete (`deleted_at`)
-   Pagination + Metadata lengkap
-   Pencarian fleksibel (`LIKE %keyword%`)
-   Struktur JSON konsisten untuk seluruh endpoint

---
