<?php

namespace App\Http\Controllers;
use App\Http\Requests\BoookignRequest;
use App\Services\CheckService;
use Exception;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    protected $checkService;

    public function __construct(CheckService $checkService)
    {
        $this->checkService = $checkService;
    }

    public function check_in(BoookignRequest $request)
    {

        try {
            $result = $this->checkService->checkIn($request->file_id);
            return response()->json([
                'success' => $result['success'],
                'message' => $result['message']
            ], $result['code']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function check_out(Request $request)
    {
        $fileId = $request->input('file_id');
        try {
            $result = $this->checkService->checkOut($fileId);
            return response()->json([
                'success' => $result['success'],
                'message' => $result['message']
            ], $result['code']);
        }
        catch (Exception $e) {
          return response()->json(['success' => false,'message' => $e->getMessage()], 400);

        }
    }

    public function showBackups($file_id){
        $backups = $this->checkService->showBackups($file_id);
        if ($backups===null) return response()->json('file dosnt exists');
        else return response()->json($backups);
    }

    public function downloadBackup($File_name){
        $backup= $this->checkService->downloadBackup($File_name);
        if ($backup===null) return response()->json('file dosnt exists');
        else return response()->download($backup);
    }
}
