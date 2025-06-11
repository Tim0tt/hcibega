<?php
     namespace App\Http\Controllers\Api;
     use App\Http\Controllers\Controller;
     use App\Models\Employee;
     use App\Models\MorningReflection;
     use App\Models\Leave;
     use Illuminate\Http\Request;
     use Illuminate\Support\Facades\Validator;
     use Carbon\Carbon;

     class GeneralAffairController extends Controller
     {
         // Get all employees for dropdown (Bagian A)
         public function getEmployees()
         {
             $employees = Employee::select('id', 'full_name')->get();
             return response()->json(['data' => $employees], 200);
         }

         // Store morning reflection attendance manually (Bagian A)
         public function storeMorningReflection(Request $request)
         {
             $validator = Validator::make($request->all(), [
                 'employee_id' => 'required|exists:employees,id',
                 'date' => 'required|date',
                 'status' => 'required|in:Hadir,Absen,Terlambat',
             ]);

             if ($validator->fails()) {
                 return response()->json(['errors' => $validator->errors()], 422);
             }

             // Cek duplikasi absensi
             if (MorningReflection::where('employee_id', $request->employee_id)->where('date', $request->date)->exists()) {
                 return response()->json(['errors' => ['date' => 'Absensi untuk pegawai ini pada tanggal ini sudah ada.']], 422);
             }

             $morningReflection = MorningReflection::create($request->all());
             return response()->json(['data' => $morningReflection, 'message' => 'Absensi berhasil disimpan'], 201);
         }

         // Record Zoom join for morning worship (Bagian A - Zoom Integration)
         public function recordZoomJoin(Request $request)
         {
             $validator = Validator::make($request->all(), [
                 'employee_id' => 'required|exists:employees,id',
                 'zoom_link' => 'nullable|url', // Opsional, untuk mencatat link Zoom
             ]);

             if ($validator->fails()) {
                 return response()->json(['errors' => $validator->errors()], 422);
             }

             // Cek hari (Senin, Rabu, Jumat)
             $now = Carbon::now();
             $dayOfWeek = $now->dayOfWeek; // 1 = Senin, 3 = Rabu, 5 = Jumat
             if (!in_array($dayOfWeek, [1, 3, 5])) {
                 return response()->json(['errors' => ['day' => 'Worship pagi hanya diadakan pada Senin, Rabu, dan Jumat.']], 422);
             }

             // Cek duplikasi absensi untuk hari ini
             $date = $now->toDateString();
             if (MorningReflection::where('employee_id', $request->employee_id)->where('date', $date)->exists()) {
                 return response()->json(['errors' => ['date' => 'Absensi untuk pegawai ini hari ini sudah ada.']], 422);
             }

             // Tentukan status berdasarkan waktu klik
             $joinTime = $now;
             $cutoffTime = Carbon::today()->setTime(7, 28); // 07:28
             $status = $joinTime->lte($cutoffTime) ? 'Hadir' : 'Terlambat';

             // Simpan absensi
             $morningReflection = MorningReflection::create([
                 'employee_id' => $request->employee_id,
                 'date' => $date,
                 'status' => $status,
                 'join_time' => $joinTime,
             ]);

             // Kembalikan data absensi dan link Zoom
             return response()->json([
                 'data' => $morningReflection,
                 'message' => 'Absensi Zoom berhasil dicatat',
                 'zoom_link' => $request->zoom_link ?? 'https://zoom.us/j/meeting'
             ], 201);
         }

         // Get all morning reflections for dashboard (Bagian C)
         public function getMorningReflections()
         {
             $reflections = MorningReflection::with('employee')->get();
             return response()->json(['data' => $reflections], 200);
         }

         // Get all leaves for dashboard (Bagian C)
         public function getLeaves()
         {
             $leaves = Leave::with('employee')->get();
             return response()->json(['data' => $leaves], 200);
         }
     }