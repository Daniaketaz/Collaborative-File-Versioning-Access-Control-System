<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\File;
use App\Services\FileService;
use App\Services\UserService;
use Illuminate\Http\Request;

class LogController extends Controller
{
    protected $fileService;
    protected $userService;

    public function __construct(FileService $fileService, UserService $userService)
    {
        $this->fileService = $fileService;
        $this->userService = $userService;
    }

    public function getFileLogs(Request $request)
    {
        $fileId = $request->input('file_id');
//        $groupId = $request->input('group_id');
        $logs = $this->fileService->getFileLogs($fileId);
        return response()->json([
            'success' => true,
            'data' => $logs->items(), // بيانات الصفحة الحالية
            'pagination' => [
                'current_page' => $logs->currentPage(),
                'per_page' => $logs->perPage(),
                'total' => $logs->total(),
                'total_pages' => $logs->lastPage(),
                'next_page_url' => $logs->nextPageUrl(),
                'prev_page_url' => $logs->previousPageUrl(),
            ],
        ]);
    }

    public function getUserLogs(Request $request)
    {
        $userId = $request->input('user_id');
        $group_id = $request->input('group_id');
       $logs= $this->userService->getUserLogs($userId, $group_id);
        return response()->json([
            'success' => true,
            'data' => $logs->items(), // بيانات الصفحة الحالية
            'pagination' => [
                'current_page' => $logs->currentPage(),
                'per_page' => $logs->perPage(),
                'total' => $logs->total(),
                'total_pages' => $logs->lastPage(),
                'next_page_url' => $logs->nextPageUrl(),
                'prev_page_url' => $logs->previousPageUrl(),
            ],
        ]);
    }

    public function getAllLogs()
    {
        $this->fileService->getAllFileLogs();
        $this->userService->getAllUserLogs();
    }

    public function readLog(Request $request)
    {
        // تحديد مسار ملف السجلات
        $logPath = storage_path('logs/laravel.log');

        // التحقق من وجود الملف
        if (!File::exists($logPath)) {
            return response()->json([
                'success' => false,
                'message' => 'LogRecord file not found.',
            ], 404);
        }

        // قراءة محتوى الملف
        $logContent = File::get($logPath);

        // تقسيم السجلات إلى أسطر
        $logLines = explode(PHP_EOL, $logContent);
        $logLines = array_filter($logLines); // إزالة الأسطر الفارغة

        // إعداد التقسيم
        $linesPerPage = 100; // عدد السجلات لكل صفحة
        $currentPage = $request->query('page', 1); // الصفحة الحالية
        $offset = ($currentPage - 1) * $linesPerPage;
        $formattedLogs = array_map(function ($line) {
            return $this->formatLogLine($line);
        }, $logLines);
        // استخراج السجلات الخاصة بالصفحة
        $pagedLogs = array_slice($formattedLogs, $offset, $linesPerPage);


        return response()->json([
            'success' => true,
            'data' => $pagedLogs,
            'pagination' => [
                'current_page' => (int)$currentPage,
                'per_page' => $linesPerPage,
                'total' => count($logLines),
                'total_pages' => ceil(count($logLines) / $linesPerPage),
            ],
        ]);
    }


    /**
     * تنسيق سطر السجل.
     */
    private function formatLogLine($line)
    {
        // استخراج البيانات من السطر باستخدام التعبيرات العادية
        preg_match('/^\[(.*?)\]\s(.*?)\.(.*?):\s(.*?)(\{.*\}|)$/', $line, $matches);

        return [
            'timestamp' => $matches[1] ?? null,
            'environment' => $matches[2] ?? null,
            'level' => $matches[3] ?? null,
            'message' => $matches[4] ?? null,
            'context' => isset($matches[5]) ? json_decode($matches[5], true) : null,
        ];
    }


    public function getLogsForFile($fileId)
    {
        $logFile = storage_path('logs/laravel.log'); // موقع ملف السجل

        if (!File::exists($logFile)) {
            return response()->json(['error' => 'LogRecord file does not exist.'], 404);
        }

        $logs = File::get($logFile); // قراءة محتوى السجل
        $lines = explode("\n", $logs); // تقسيم السجل إلى أسطر

        // تصفية السجلات التي تحتوي على file_id
        $filteredLogs = array_filter($lines, function ($line) use ($fileId) {
            return str_contains($line, 'file_id: ' . $fileId);
        });

        if (empty($filteredLogs)) {
            return response()->json(['message' => 'No logs found for file_id: ' . $fileId], 404);
        }

        // تنسيق السجلات لإعادة عرضها
        $formattedLogs = array_map(function ($log) {
            return [
                'timestamp' => substr($log, 0, 19), // استخراج الوقت
                'log' => $log
            ];
        }, $filteredLogs);

        return response()->json($formattedLogs);
    }

}
