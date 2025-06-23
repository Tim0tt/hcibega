# HCI Project - Sistem Absensi Renungan Pagi

## Overview
Sistem absensi digital untuk renungan pagi perusahaan dengan fitur Zoom integration, rate limiting, dan validasi waktu. Sistem ini dirancang untuk menangani concurrent access dan memastikan data integrity.

## Fitur Utama

### 1. Manajemen Karyawan
- Daftar karyawan untuk dropdown selection
- CRUD operations untuk data karyawan

### 2. Absensi Manual (Bagian A)
- Input absensi manual oleh admin/HR
- Validasi data dan pencegahan duplikasi
- Status: Hadir, Absen, Terlambat

### 3. Absensi Zoom Integration (Bagian A)
- Absensi otomatis melalui Zoom join
- Validasi waktu: 07:10 - 08:00
- Validasi hari: Senin, Rabu, Jumat
- Auto-detect status berdasarkan waktu join (cutoff: 07:28)
- Rate limiting: maksimal 5 percobaan per karyawan per hari

### 4. Dashboard (Bagian C)
- View semua data absensi renungan pagi
- View data cuti karyawan
- Relasi data dengan informasi karyawan

### 5. Keamanan & Reliability
- Database transactions untuk data consistency
- Unique constraints untuk mencegah duplikasi
- Comprehensive logging untuk debugging
- Error handling yang robust
- Rate limiting middleware

## Tech Stack
- **Backend**: Laravel 10.x
- **Database**: MySQL
- **Cache**: File-based caching
- **Logging**: Laravel Log dengan multiple channels
- **API**: RESTful API dengan JSON responses

## Quick Setup

### Prerequisites
- PHP 8.1+
- Composer
- MySQL
- XAMPP/WAMP (untuk development)

### Installation Steps

1. **Clone & Install Dependencies**
   ```bash
   cd c:\xampp\htdocs\hci_project
   composer install
   ```

2. **Environment Setup**
   ```bash
   # File .env sudah ada dan dikonfigurasi untuk:
   # - Database: hci
   # - MySQL: localhost:3306
   # - Username: root (no password)
   ```

3. **Database Setup**
   ```bash
   # Buat database 'hci' di MySQL
   php artisan migrate
   php artisan db:seed
   ```

4. **Start Development Server**
   ```bash
   php artisan serve
   # Server akan berjalan di http://localhost:8000
   ```

## API Endpoints

### Base URL: `http://localhost:8000/api/ga`

| Method | Endpoint | Description | Middleware |
|--------|----------|-------------|------------|
| GET | `/employees` | Get all employees | - |
| POST | `/morning-reflections` | Manual attendance entry | Rate Limit |
| POST | `/zoom-join` | Zoom attendance entry | Rate Limit |
| GET | `/morning-reflections` | Get all attendance data | - |
| GET | `/leaves` | Get all leave data | - |

### API Examples

#### 1. Get Employees
```bash
GET /api/ga/employees
Response: {
  "data": [
    {"id": 1, "full_name": "John Doe"},
    {"id": 2, "full_name": "Jane Smith"}
  ]
}
```

#### 2. Zoom Join (Success)
```bash
POST /api/ga/zoom-join
Body: {
  "employee_id": 1,
  "zoom_link": "https://zoom.us/j/123456789"
}

Response: {
  "data": {
    "id": 1,
    "employee_id": 1,
    "date": "2025-01-16",
    "status": "Hadir",
    "join_time": "2025-01-16 07:25:00"
  },
  "message": "Absensi Zoom berhasil dicatat"
}
```

#### 3. Rate Limit Error
```bash
Response (429): {
  "errors": {
    "rate_limit": "Terlalu banyak percobaan absensi. Silakan coba lagi nanti atau hubungi admin."
  }
}
```

## Testing

### Postman Collection
Gunakan file `postman_collection.json` untuk testing API endpoints.

### Manual Testing Scenarios

1. **Normal Flow**
   - Test pada Senin 07:20 → Status: Hadir
   - Test pada Rabu 07:35 → Status: Terlambat

2. **Validation Errors**
   - Test pada Selasa → Error: Hari tidak valid
   - Test pada 06:00 → Error: Waktu tidak valid
   - Test pada 09:00 → Error: Waktu tidak valid

3. **Rate Limiting**
   - Lakukan 6 request berturut-turut → Request ke-6 akan di-rate limit

4. **Concurrency**
   - Simulasi multiple request bersamaan untuk employee yang sama
   - Hanya 1 request yang berhasil, sisanya akan mendapat error duplikasi

## Database Schema

### Tables

1. **employees**
   - id (PK)
   - full_name
   - timestamps

2. **morning_reflections**
   - id (PK)
   - employee_id (FK)
   - date
   - status (enum: Hadir, Absen, Terlambat)
   - join_time (nullable)
   - timestamps
   - UNIQUE(employee_id, date)

3. **leaves**
   - id (PK)
   - employee_id (FK)
   - start_date
   - end_date
   - status (enum: Pending, Approved, Rejected)
   - timestamps

## Monitoring & Maintenance

### Logs Location
- Application logs: `storage/logs/laravel.log`
- Rate limiting logs: Logged dengan level WARNING
- Error logs: Logged dengan level ERROR

### Cache Management
- Rate limiting menggunakan file-based cache
- Cache keys: `attendance_attempt_{employee_id}_{date}`
- Auto-expire pada akhir hari

### Database Maintenance
- Regular backup database `hci`
- Monitor unique constraint violations
- Clean up old log files secara berkala

## Production Deployment

### Environment Configuration
```env
APP_ENV=production
APP_DEBUG=false
LOG_LEVEL=warning
CACHE_DRIVER=redis  # Recommended for production
```

### Security Checklist
- [ ] Set APP_DEBUG=false
- [ ] Configure proper database credentials
- [ ] Set up SSL/HTTPS
- [ ] Configure rate limiting sesuai kebutuhan
- [ ] Set up log rotation
- [ ] Configure backup strategy

## Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Pastikan MySQL service berjalan
   - Cek kredensial database di .env
   - Pastikan database 'hci' sudah dibuat

2. **Rate Limit Issues**
   - Clear cache: `php artisan cache:clear`
   - Cek log untuk detail rate limiting

3. **Migration Errors**
   - Drop dan recreate database jika diperlukan
   - Jalankan `php artisan migrate:fresh --seed`

### Support
Untuk pertanyaan teknis atau bug report, silakan cek:
- Application logs di `storage/logs/`
- Error details di response API
- Database constraint violations

## Documentation Files
- `CONCURRENCY_FIX_README.md` - Detail perbaikan race condition
- `TIME_VALIDATION_UPDATE.md` - Detail implementasi validasi waktu
- `postman_collection.json` - Collection untuk API testing
