<?php

namespace App\Http\Controllers;

use App\Services\ExportService;
use Illuminate\Http\Request;

class ExportController extends Controller
{

    protected $ExportService;

    public function __construct(ExportService $exportService)
    {
        $this->ExportService = $exportService;
    }

    public function exportFileReport($file_id, Request $request)
    {
        $format = $request->query('format', 'pdf'); // الافتراضي PDF
        $reports = $this->ExportService->generateFileReport($file_id);

        if ($reports->isEmpty()) {
            return response()->json(['message' => 'No data found for the file'], 404);
        }

        $fileName = "file_report_" . now()->format('Ymd_His');

        if ($format === 'pdf') {
            $filePath = $this->ExportService->exportReportToPDF($reports, $fileName);
        } elseif ($format === 'csv') {
            $filePath = $this->ExportService->exportReportToCSV($reports, $fileName);
        } else {
            return response()->json(['message' => 'Invalid format'], 400);
        }

        return response()->download(storage_path("app/$filePath"))->deleteFileAfterSend(true);
    }

}
