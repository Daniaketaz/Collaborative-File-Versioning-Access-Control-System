<?php

namespace App\Http\Controllers;

use App\Services\AdminService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminController extends Controller
{
    protected $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    public function getAllFiles()
    {
        $files = $this->adminService->getAllFiles();
        return response()->json(['success' => true, 'data' => $files]);
    }

    public function getAllGroups()
    {
        $groups = $this->adminService->getAllGroups();
        return response()->json(['success' => true, 'data' => $groups]);
    }

    public function getAllUsers()
    {
        $users = $this->adminService->getAllUsers();
        return response()->json(['success' => true, 'data' => $users]);
    }

    public function getAllFilesLogs()
    {
        $logs = $this->adminService->getAllFilesLogs();
        return response()->json([
            'success' => true,
            'data' => $logs->items(),
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

    public function getAllUsersLogs()
    {
        $logs = $this->adminService->getAllUsersLogs();
        return response()->json([
            'success' => true,
            'data' => $logs->items(),
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

    public function getAllGroupsLogs()
    {
        $logs = $this->adminService->getAllGroupsLogs();
        return response()->json([
            'success' => true,
            'data' => $logs->items(),
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

    public function getFileLog(Request $request)
    {
        $fileId = $request->input('file_id');
        $logs = $this->adminService->getFileLog($fileId);
        return response()->json([
            'success' => true,
            'data' => $logs->items(),
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

    public function getUserLog(Request $request)
    {
        $userId = $request->input('user_id');
        $logs = $this->adminService->getUserLog($userId);
        return response()->json([
            'success' => true,
            'data' => $logs->items(),
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

    public function getGroupLog(Request $request)
    {
        $groupId = $request->input('group_id');
        $logs = $this->adminService->getGroupLog($groupId);
        return response()->json([
            'success' => true,
            'data' => $logs->items(),
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

    public function getUserFileLog(Request $request)
    {
        $userId = $request->input('user_id');
        $logs = $this->adminService->getUserFileLog($userId);
        return response()->json([
            'success' => true,
            'data' => $logs->items(),
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

    public function getUserUpdatesLogs(Request $request)
    {
        $userId = $request->input('user_id');
        $logs = $this->adminService->getUserUpdatesLogs($userId);
        return response()->json([
            'success' => true,
            'data' => $logs->items(),
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

    public function getFileUpdatesLogs(Request $request)
    {
        $userId = $request->input('file_id');
        $logs = $this->adminService->getFileUpdatesLogs($userId);
        return response()->json([
            'success' => true,
            'data' => $logs->items(),
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
        $logs = $this->adminService->getAllLogs();
        return response()->json([
            'success' => true,
            'data' => $logs->items(),
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

    public function getFailedLogs()
    {
        $logs = $this->adminService->getFailedLogs();

        return response()->json([
            'success' => true,
            'data' => $logs->items(),
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

    public function getSuccessfulLogs()
    {
        $logs = $this->adminService->getSuccessfulLogs();

        return response()->json([
            'success' => true,
            'data' => $logs->items(),
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


//new'
    public function exportAllFilesLogs($format)
    {
        // استدعاء نفس الـ API الموجودة للحصول على البيانات
        $response = $this->getAllFilesLogs();

        // تحويل استجابة JSON إلى بيانات
        $data = json_decode($response->getContent(), true);

        // استخراج البيانات من الاستجابة
        $logs = $data['data'] ?? [];

        switch ($format) {
            case 'csv':
                return $this->exportLogsAsCSV($logs);
            case 'pdf':
                return $this->exportLogsAsPDF($logs);
            default:
                return response()->json(['error' => 'Invalid format'], 400);
        }
    }

    public function exportAllUsersLogs($format)
    {
        // استدعاء نفس الـ API الموجودة للحصول على البيانات
        $response = $this->getAllUsersLogs();

        // تحويل استجابة JSON إلى بيانات
        $data = json_decode($response->getContent(), true);

        // استخراج البيانات من الاستجابة
        $logs = $data['data'] ?? [];

        switch ($format) {
            case 'csv':
                return $this->exportLogsAsCSV($logs);
            case 'pdf':
                return $this->exportLogsAsPDF($logs);
            default:
                return response()->json(['error' => 'Invalid format'], 400);
        }
    }

    public function exportAllGroupsLogs($format)
    {
        // استدعاء نفس الـ API الموجودة للحصول على البيانات
        $response = $this->getAllGroupsLogs();

        // تحويل استجابة JSON إلى بيانات
        $data = json_decode($response->getContent(), true);

        // استخراج البيانات من الاستجابة
        $logs = $data['data'] ?? [];

        switch ($format) {
            case 'csv':
                return $this->exportLogsAsCSV($logs);
            case 'pdf':
                return $this->exportLogsAsPDF($logs);
            default:
                return response()->json(['error' => 'Invalid format'], 400);
        }
    }

    public function exportFileLog($format , Request $request)
    {
        // استدعاء نفس الـ API الموجودة للحصول على البيانات
        $response = $this->getFileLog($request);

        // تحويل استجابة JSON إلى بيانات
        $data = json_decode($response->getContent(), true);

        // استخراج البيانات من الاستجابة
        $logs = $data['data'] ?? [];

        switch ($format) {
            case 'csv':
                return $this->exportLogsAsCSV($logs);
            case 'pdf':
                return $this->exportLogsAsPDF($logs);
            default:
                return response()->json(['error' => 'Invalid format'], 400);
        }
    }

    public function exportUserLog($format , Request $request)
    {
        // استدعاء نفس الـ API الموجودة للحصول على البيانات
        $response = $this->getUserLog($request);

        // تحويل استجابة JSON إلى بيانات
        $data = json_decode($response->getContent(), true);

        // استخراج البيانات من الاستجابة
        $logs = $data['data'] ?? [];

        switch ($format) {
            case 'csv':
                return $this->exportLogsAsCSV($logs);
            case 'pdf':
                return $this->exportLogsAsPDF($logs);
            default:
                return response()->json(['error' => 'Invalid format'], 400);
        }
    }

    public function exportGroupLog($format , Request $request)
    {
        // استدعاء نفس الـ API الموجودة للحصول على البيانات
        $response = $this->getGroupLog($request);

        // تحويل استجابة JSON إلى بيانات
        $data = json_decode($response->getContent(), true);

        // استخراج البيانات من الاستجابة
        $logs = $data['data'] ?? [];

        switch ($format) {
            case 'csv':
                return $this->exportLogsAsCSV($logs);
            case 'pdf':
                return $this->exportLogsAsPDF($logs);
            default:
                return response()->json(['error' => 'Invalid format'], 400);
        }
    }

    public function exportUserFileLog($format , Request $request)
    {
        // استدعاء نفس الـ API الموجودة للحصول على البيانات
        $response = $this->getUserFileLog($request);

        // تحويل استجابة JSON إلى بيانات
        $data = json_decode($response->getContent(), true);

        // استخراج البيانات من الاستجابة
        $logs = $data['data'] ?? [];

        switch ($format) {
            case 'csv':
                return $this->exportLogsAsCSV($logs);
            case 'pdf':
                return $this->exportLogsAsPDF($logs);
            default:
                return response()->json(['error' => 'Invalid format'], 400);
        }
    }

    public function exportFileUpdatesLogs($format , Request $request)
    {
        // استدعاء نفس الـ API الموجودة للحصول على البيانات
        $response = $this->getFileUpdatesLogs($request);

        // تحويل استجابة JSON إلى بيانات
        $data = json_decode($response->getContent(), true);

        // استخراج البيانات من الاستجابة
        $logs = $data['data'] ?? [];

        switch ($format) {
            case 'csv':
                return $this->exportLogsAsCSV($logs);
            case 'pdf':
                return $this->exportLogsAsPDF($logs);
            default:
                return response()->json(['error' => 'Invalid format'], 400);
        }
    }

    public function exportUserUpdatesLogs($format , Request $request)
    {
        // استدعاء نفس الـ API الموجودة للحصول على البيانات
        $response = $this->getUserUpdatesLogs($request);

        // تحويل استجابة JSON إلى بيانات
        $data = json_decode($response->getContent(), true);

        // استخراج البيانات من الاستجابة
        $logs = $data['data'] ?? [];

        switch ($format) {
            case 'csv':
                return $this->exportLogsAsCSV($logs);
            case 'pdf':
                return $this->exportLogsAsPDF($logs);
            default:
                return response()->json(['error' => 'Invalid format'], 400);
        }
    }

    public function exportAllLogs($format )
    {
        // استدعاء نفس الـ API الموجودة للحصول على البيانات
        $response = $this->getAllLogs();

        // تحويل استجابة JSON إلى بيانات
        $data = json_decode($response->getContent(), true);

        // استخراج البيانات من الاستجابة
        $logs = $data['data'] ?? [];

        switch ($format) {
            case 'csv':
                return $this->exportLogsAsCSV($logs);
            case 'pdf':
                return $this->exportLogsAsPDF($logs);
            default:
                return response()->json(['error' => 'Invalid format'], 400);
        }
    }

    public function exportFailedLogs($format )
    {
        // استدعاء نفس الـ API الموجودة للحصول على البيانات
        $response = $this->getFailedLogs();

        // تحويل استجابة JSON إلى بيانات
        $data = json_decode($response->getContent(), true);

        // استخراج البيانات من الاستجابة
        $logs = $data['data'] ?? [];

        switch ($format) {
            case 'csv':
                return $this->exportLogsAsCSV($logs);
            case 'pdf':
                return $this->exportLogsAsPDF($logs);
            default:
                return response()->json(['error' => 'Invalid format'], 400);
        }
    }

    public function exportSuccessfulLogs($format )
    {
        // استدعاء نفس الـ API الموجودة للحصول على البيانات
        $response = $this->getSuccessfulLogs();

        // تحويل استجابة JSON إلى بيانات
        $data = json_decode($response->getContent(), true);

        // استخراج البيانات من الاستجابة
        $logs = $data['data'] ?? [];

        switch ($format) {
            case 'csv':
                return $this->exportLogsAsCSV($logs);
            case 'pdf':
                return $this->exportLogsAsPDF($logs);
            default:
                return response()->json(['error' => 'Invalid format'], 400);
        }
    }

    protected function exportLogsAsCSV($logs)
    {
        $response = new StreamedResponse(function () use ($logs) {
            $handle = fopen('php://output', 'w');

            // كتابة العناوين إذا كانت البيانات غير فارغة
            if (!empty($logs)) {
                fputcsv($handle, array_keys($logs[0]));
            }

            // كتابة البيانات
            foreach ($logs as $log) {
                fputcsv($handle, $log);
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="files_logs.csv"');
        return $response;
    }

    protected function exportLogsAsPDF($logs)
    {
        $pdf = Pdf::loadView('reports.files_logs_pdf', ['logs' => $logs]);
        return $pdf->download('files_logs.pdf');
    }







}
