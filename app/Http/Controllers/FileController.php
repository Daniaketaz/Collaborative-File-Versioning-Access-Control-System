<?php

namespace App\Http\Controllers;

use App\Http\Requests\AcceptFileRequest;
use App\Http\Requests\UpdateFileRequest;
use App\Http\Requests\UploadFileRequest;
use App\Repositories\FileRepository;
use App\Services\CheckService;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    protected $FileService;
    protected $FileRepository;
    protected $CheckService;
    public function __construct(FileService $FileService ,
                                FileRepository $FileRepository ,
                                CheckService $checkService)
    {
        $this->FileService = $FileService;
        $this->FileRepository = $FileRepository;
        $this->CheckService = $checkService;
    }

    public function upload(UploadFileRequest $request){
        $request->validated();

        $data=$this->FileService->upload($request->file ,$request->group_id);
        if($data){
            return response()->json(["message"=>'file uploaded successfully ']);
        }
        else{
            return  response()->json(["message"=>'file didnt upload ']);
        }


    }

    public function download($id)
    {
        $file = $this->FileService->download($id);

        if ($file) {
            return response()->download($file) ;
        } else {
            return response()->json(['message' => 'File not found'], 404);
        }

    }

    public function downloadAsBytes($id)
    {
        $file = $this->FileRepository->findFileById($id);
        if ($file && Storage::disk('files')->exists($file->name)) {
            $filePath = Storage::disk('files')->path($file->name);
            $fileContents = file_get_contents($filePath);

            return response($fileContents, 200)
                ->header('Content-Type', mime_content_type($filePath))
                ->header('Content-Disposition', 'attachment; filename="' . $file->name . '"');
        }

        return response()->json(['message' => 'File not found'], 404);
    }

    public function ShowAllFiles($group_id){
        $Files = $this->FileService->ShowAllFiles($group_id);
        if(!$Files) return response()->json('no files');
        return response()->json($Files);

            }

    public function ShowBookedFiles(){
        $Files = $this->FileService->ShowBookedFiles();
        return response()->json(['data'=>$Files]);
    }

    public function ShowDownloadedFiles($group_id){
        $Files = $this->FileService->ShowDownloadedFiles($group_id);
        if(!$Files) return response()->json('you are not the admin for this group ');
        else{
            return response()->json($Files);
        }
    }

    public function acceptFile(AcceptFileRequest $request){
       $request->validated();
        $file = $this->FileService->acceptFile($request->file_id, $request->group_id);
        if(!$file) return response()->json(['message'=>'you are not the admin for this group ']);
        else return response()->json(['message'=>'accepted successfully']);
    }

    public function rejectFile(AcceptFileRequest $request){
        $request->validated();
        $file = $this->FileService->RejectFile($request->file_id, $request->group_id);
        if(!$file) return response()->json('you are not the admin for this group ');
        else return response()->json('process done successfully');
    }

//    public function updateFile(UpdateFileRequest $request)
//    {
//        $validated = $request->validated();
//        $file_id = $validated['file_id'];
//        $newFile = $validated['file'];
//      $file=$this->FileService->Update($file_id,$newFile);
//        if ($file===true) {
//
//                return response()->json(['message' => 'File updated successfully.']);
//            } elseif($file===false) {
//                return response()->json(['message' => 'The uploaded file name does not match the existing file name.'], 400);
//            }
//        else  return response()->json(['message' => 'File not found or does not exist in storage.'], 404);
//    }

    public function updateFile(UpdateFileRequest $request)
    {
        $validated = $request->validated();
        $file_id = $validated['file_id'];
        $newFile = $validated['file'];
        $result = $this->FileService->updateAndCompare($file_id, $newFile);
        return response()->json([
            'message' =>[ $result['message'],$result['differences'] ?? null],
        ], $result['status']);
    }

    /**
     * @throws \Exception
     */
    public function compareFiles(Request $request)
    {
        $request->validate([
            'old_file' => 'required|file|mimes:txt,doc,docx',
            'new_file' => 'required|file|mimes:txt,doc,docx',
        ]);
        $differences = $this->FileService->compare($request->file('old_file'), $request->file('new_file'));
        $differencesText = 'The differences are as follows:';
        if (!empty($differences)) {
            foreach ($differences as $difference) {
                $differencesText .= "\nLine " . $difference['line'] . ":\n";
                $differencesText .= "Old: " . $difference['old'] . "\n";
                $differencesText .= "New: " . $difference['new'] . "\n";
                $differencesText .= str_repeat("-", 50) . "\n";
            }
        } else {
            $differencesText .= "\nNo differences found.";
        }
        return response()->json([
            'status' => 'success',
            'message' => $differencesText,
        ]);
    }
}
