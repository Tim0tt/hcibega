# Time Validation Update

## Overview
Penambahan validasi waktu untuk fitur Zoom join pada sistem absensi renungan pagi. Fitur ini membatasi akses absensi Zoom hanya pada jam tertentu untuk memastikan karyawan mengikuti jadwal yang telah ditentukan.

## Fitur yang Ditambahkan

### 1. Validasi Waktu Akses
- **Jam Akses**: 07:10 - 08:00
- **Tujuan**: Memastikan karyawan hanya dapat melakukan absensi pada waktu yang tepat
- **Error Message**: "Absensi Zoom hanya dapat dilakukan antara pukul 07:10 - 08:00."

### 2. Validasi Hari
- **Hari yang Diizinkan**: Senin, Rabu, Jumat
- **Tujuan**: Sesuai dengan jadwal worship pagi perusahaan
- **Error Message**: "Worship pagi hanya diadakan pada Senin, Rabu, dan Jumat."

### 3. Status Attendance Logic
- **Hadir**: Join sebelum atau tepat 07:28
- **Terlambat**: Join setelah 07:28 (tetapi masih dalam window 07:10-08:00)

## Technical Implementation

### Modified Files:
1. `app/Http/Controllers/Api/GeneralAffairController.php`
   - Menambahkan validasi waktu pada method `recordZoomJoin()`
   - Menggunakan Carbon untuk pengecekan waktu yang akurat
   - Implementasi logic untuk menentukan status kehadiran

### Code Changes:
```php
// Validasi waktu: hanya boleh join antara 07:10 - 08:00
$startTime = Carbon::today()->setTime(7, 10); // 07:10
$endTime = Carbon::today()->setTime(8, 0);    // 08:00

if ($now->lt($startTime) || $now->gt($endTime)) {
    return response()->json([
        'errors' => ['time' => 'Absensi Zoom hanya dapat dilakukan antara pukul 07:10 - 08:00.']
    ], 422);
}

// Tentukan status berdasarkan waktu klik
$cutoffTime = Carbon::today()->setTime(7, 28); // 07:28
$status = $now->lte($cutoffTime) ? 'Hadir' : 'Terlambat';
```

## API Response Examples

### Success Response (Hadir):
```json
{
    "data": {
        "id": 1,
        "employee_id": 1,
        "date": "2025-01-16",
        "status": "Hadir",
        "join_time": "2025-01-16 07:25:00"
    },
    "message": "Absensi Zoom berhasil dicatat",
    "zoom_link": "https://zoom.us/j/meeting"
}
```

### Success Response (Terlambat):
```json
{
    "data": {
        "id": 2,
        "employee_id": 2,
        "date": "2025-01-16",
        "status": "Terlambat",
        "join_time": "2025-01-16 07:35:00"
    },
    "message": "Absensi Zoom berhasil dicatat",
    "zoom_link": "https://zoom.us/j/meeting"
}
```

### Error Response (Waktu Tidak Valid):
```json
{
    "errors": {
        "time": "Absensi Zoom hanya dapat dilakukan antara pukul 07:10 - 08:00."
    }
}
```

### Error Response (Hari Tidak Valid):
```json
{
    "errors": {
        "day": "Worship pagi hanya diadakan pada Senin, Rabu, dan Jumat."
    }
}
```

## Testing Scenarios

### 1. Valid Time Window
- Test join pada 07:15 (Hadir)
- Test join pada 07:30 (Terlambat)
- Test join pada 07:59 (Terlambat)

### 2. Invalid Time Window
- Test join pada 06:00 (Error)
- Test join pada 08:30 (Error)

### 3. Valid Days
- Test pada Senin, Rabu, Jumat (Success)

### 4. Invalid Days
- Test pada Selasa, Kamis, Sabtu, Minggu (Error)

## Benefits

1. **Kontrol Waktu**: Memastikan absensi hanya dilakukan pada jam yang tepat
2. **Fairness**: Semua karyawan memiliki window waktu yang sama
3. **Compliance**: Sesuai dengan kebijakan perusahaan
4. **Data Integrity**: Mencegah absensi di luar jam kerja
5. **User Experience**: Error message yang jelas dan informatif

## Future Enhancements

1. **Configurable Time Window**: Membuat jam akses dapat dikonfigurasi melalui admin panel
2. **Holiday Detection**: Integrasi dengan kalender libur nasional
3. **Timezone Support**: Dukungan untuk multiple timezone
4. **Grace Period**: Implementasi grace period untuk situasi khusus