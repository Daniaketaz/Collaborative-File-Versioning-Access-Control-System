<?php


namespace App\Services;

use App\Repositories\ReportRepository;
use App\Repositories\FileRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class ExportService
{
    protected $reportRepository;
    protected $fileRepository;

    public function __construct(ReportRepository $reportRepository, FileRepository $fileRepository)
    {
        $this->reportRepository = $reportRepository;
        $this->fileRepository = $fileRepository;
    }

    public function generateFileReport($file_id): Collection
    {
        $checks = $this->reportRepository->FindChecksByFile($file_id);
        $reports = collect();

        foreach ($checks as $check) {
            $file_id = $check->file_id;
            $reports->push([
                'user_name' => $this->reportRepository->FindCheckUserName($check->id),
                'checkin_on_file' => $this->fileRepository->findFileById($file_id)->name,
                'checkin_date' => $this->reportRepository->getFileCheckDate($check->id),
                'checkout_date' => $this->reportRepository->getFileReturnDate($check->id),
            ]);
        }

        return $reports;
    }

    public function exportReportToPDF($reports, $fileName)
    {
        $pdf = Pdf::loadView('reports.file_report', ['reports' => $reports]);
        $filePath = "exports/$fileName.pdf";
        Storage::disk('local')->put($filePath, $pdf->output());

        return $filePath;
    }

    public function exportReportToCSV($reports, $fileName)
    {
        $csvData = "User Name,Check-in File,Check-in Date,Check-out Date\n";

        foreach ($reports as $report) {
            $csvData .= "{$report['user_name']},{$report['checkin_on_file']},{$report['checkin_date']},{$report['checkout_date']}\n";
        }

        $filePath = "exports/$fileName.csv";
        Storage::disk('local')->put($filePath, $csvData);

        return $filePath;
    }
}
