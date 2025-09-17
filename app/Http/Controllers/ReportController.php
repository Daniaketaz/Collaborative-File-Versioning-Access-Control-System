<?php

namespace App\Http\Controllers;

use App\Models\Check;
use App\Models\File;
use App\Models\User;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    protected $ReportService;

    public function __construct(ReportService $reportService)
    {
        $this->ReportService = $reportService;
    }

    public function exportAllFilesLogs($format)
    {
        $response = $this->getAllFilesLogs();

        $data = json_decode($response->getContent(), true);

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
        $response = $this->getAllUsersLogs();

        $data = json_decode($response->getContent(), true);

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
        $response = $this->getAllGroupsLogs();

        $data = json_decode($response->getContent(), true);

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
        $response = $this->getFileLog($request);

        $data = json_decode($response->getContent(), true);

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
        $response = $this->getUserLog($request);

        $data = json_decode($response->getContent(), true);

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
        $response = $this->getGroupLog($request);

        $data = json_decode($response->getContent(), true);

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
        $response = $this->getUserFileLog($request);

        $data = json_decode($response->getContent(), true);

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

        $response = $this->getFileUpdatesLogs($request);
        $data = json_decode($response->getContent(), true);

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
        $response = $this->getUserUpdatesLogs($request);
        $data = json_decode($response->getContent(), true);

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
        $response = $this->getAllLogs();

        $data = json_decode($response->getContent(), true);

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
        $response = $this->getFailedLogs();

        $data = json_decode($response->getContent(), true);

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
        $response = $this->getSuccessfulLogs();

        $data = json_decode($response->getContent(), true);

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

            if (!empty($logs)) {
                fputcsv($handle, array_keys($logs[0]));
            }

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

    public function getAllFilesLogs()
    {
        $logs = $this->ReportService->getAllFilesLogs();
        return response()->json([
            'success' => true,
            'data' => $logs
        ]);
    }

    public function getAllUsersLogs()
    {
        $logs = $this->ReportService->getAllUsersLogs();
        return response()->json([
            'success' => true,
            'data' => $logs
        ]);
    }

    public function getAllGroupsLogs()
    {
        $logs = $this->ReportService->getAllGroupsLogs();
        return response()->json([
            'success' => true,
            'data' => $logs
        ]);
    }

    public function getFileLog(Request $request)
    {
        $fileId = $request->input('file_id');
        $logs = $this->ReportService->getFileLog($fileId);
        return response()->json([
            'success' => true,
            'data' => $logs
        ]);
    }

    public function getUserLog(Request $request)
    {
        $userId = $request->input('user_id');
        $logs = $this->ReportService->getUserLog($userId);
        return response()->json([
            'success' => true,
            'data' => $logs
        ]);
    }

    public function getGroupLog(Request $request)
    {
        $groupId = $request->input('group_id');
        $logs = $this->ReportService->getGroupLog($groupId);
        return response()->json([
            'success' => true,
            'data' => $logs
        ]);
    }

    public function getUserFileLog(Request $request)
    {
        $userId = $request->input('user_id');
        $logs = $this->ReportService->getUserFileLog($userId);
        return response()->json([
            'success' => true,
            'data' => $logs
        ]);
    }

    public function getUserUpdatesLogs(Request $request)
    {
        $userId = $request->input('user_id');
        $logs = $this->ReportService->getUserUpdatesLogs($userId);
        return response()->json([
            'success' => true,
            'data' => $logs
        ]);
    }

    public function getFileUpdatesLogs(Request $request)
    {
        $userId = $request->input('file_id');
        $logs = $this->ReportService->getFileUpdatesLogs($userId);
        return response()->json([
            'success' => true,
            'data' => $logs
        ]);
    }

    public function getAllLogs()
    {
        $logs = $this->ReportService->getAllLogs();
        return response()->json([
            'success' => true,
            'data' => $logs
        ]);
    }

    public function getFailedLogs()
    {
        $logs = $this->ReportService->getFailedLogs();

        return response()->json([
            'success' => true,
            'data' => $logs
        ]);
    }

    public function getSuccessfulLogs()
    {
        $logs = $this->ReportService->getSuccessfulLogs();

        return response()->json([
            'success' => true,
            'data' => $logs
        ]);
    }




}
