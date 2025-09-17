<?php
namespace App\Services;



use App\Models\File;
use App\Models\Group;
use App\Models\User;
use App\Models\User_Group;
use App\Repositories\FileRepository;
use App\Repositories\GroupRepository;
use App\Repositories\UserGroupRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FileService{

    protected $FileRepository;
    protected $GroupRepository;
    protected $UserGroupRepository;
    public function __construct(FileRepository $FileRepository ,
                                GroupRepository $groupRepository ,
                                UserGroupRepository $userGroupRepository)
    {
        $this->FileRepository = $FileRepository;
        $this->GroupRepository=$groupRepository;
        $this->UserGroupRepository=$userGroupRepository;
    }

    public function upload($file , $group_id ){
        if( !$file->isValid()){
            return null;
        }
        $extension = $file->extension();
        $filePath=Storage::disk('files')->putFileAs('',
            $file, str()->uuid().'.'.$extension);

        $user_group=User_Group::query()->where('group_id',$group_id)
            ->where('user_id',Auth::id())->first();

        $admin_id=$this->GroupRepository->getGroupAdmin($group_id);
        $user_id=Auth::id();
        $uploadedFile=File::create([
            'user_group_id' => $user_group->id,
            'name' => $filePath,
            'file_suffix' => $extension,
        ]);
        if($admin_id==$user_id){
            $uploadedFile->accepted=true;
        }
        $uploadedFile->save();
        return $uploadedFile;
    }

    public function download($id){
        $file = $this->FileRepository->findFileById($id);
        if ($file && Storage::disk('files')->exists($file->name)) {
            return Storage::disk('files')->path($file->name);
        }
        return null;
    }

    public function ShowAllFiles($group_id){

        if(!$this->UserGroupRepository->CheckIfUserInGroup($group_id)) return false;
        $files=$this->FileRepository->getGroupFiles($group_id);
        return $files->map(function ($file) {
            return [
                'id'=>$file->id,
                'name'=>$file->name,
                'file_suffix'=>$file->file_suffix,
                'free'=>$file->free
            ];
        });
    }

    public function ShowBookedFiles(){
        $checkeds = auth()->user()->checks()->where('time_check_out',null)->get();

        return $checkeds->map(function ($checked) {
            return [
                'id' => $checked->id,
                'user_id'=>$checked->user_id,
                'name' => File::find($checked->file_id)->name  ,
                'file_id'=>$checked->file_id
            ];
        });
    }

    public function ShowDownloadedFiles($group_id){
        if(!$this->GroupRepository->checkGroupAdmin($group_id)) return false ;
        $files=$this->FileRepository->getUnacceptedFiles($group_id);
        return $files->map(function ($file) {
            return [
                'file_id'=>$file->id,
                'user_name'=>$file->user_group->user-> user_Name,
                'name'=>$file->name,
                'file_suffix'=>$file->file_suffix,
            ];
        });
    }

    public function AcceptFile($file_id ,$group_id){
        if(!$this->GroupRepository->checkGroupAdmin($group_id)) return false ;
        return $this->FileRepository->AcceptFile($file_id);
    }

    public function RejectFile($file_id ,$group_id){
        if(!$this->GroupRepository->checkGroupAdmin($group_id)) return false ;
        return $this->FileRepository->deleteFile($file_id);

    }

    public function Update($file_id ,$file){
        $existingFile = $this->FileRepository->findFileById($file_id);
        if ($existingFile && Storage::disk('files')->exists($existingFile->name)) {
            if ($file->getClientOriginalName() === $existingFile->name) {
                Storage::disk('files')->put($existingFile->name, file_get_contents($file));

                return true;
            } else {
                return false;
            }
        }
        if (!$existingFile || !Storage::disk('files')->exists($existingFile->name)) {
            return 1;
        }

        if ($file->getClientOriginalName() !== $existingFile->name) {
            return false;
        }

        $oldContent = Storage::disk('files')->get($existingFile->name);
        $newContent = file_get_contents($file);
        $diff = $this->calculateDiff($oldContent, $newContent);
        Storage::disk('files')->put($existingFile->name, $newContent);
        DB::table('log_records')->insert([
            'file_id' => $file_id,
            'user_id' => auth()->id(),
            'group_id' => $existingFile->group_id,
            'action' => 'update',
            'details' => json_encode(['diff' => $diff]),
            'action_time' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return true;
    }

    public function compare($oldFile, $newFile)
    {
        $oldLines = $this->FileRepository->readFile($oldFile);
        $newLines = $this->FileRepository->readFile($newFile);
        return $this->FileRepository->compareContents($oldLines, $newLines);
    }

    public function updateAndCompare($file_id, $newFile)
    {

        $existingFile = $this->FileRepository->findFileById($file_id);
        if (!$existingFile || !Storage::disk('files')->exists($existingFile->name)) {
            return [
                'message' => 'File not found or does not exist in storage.',
                'status' => 404,
            ];
        }

        // مقارنة الملفات
        $oldFilePath = Storage::disk('files')->path($existingFile->name);
        $oldContent = Storage::disk('files')->get($existingFile->name);
        $differences = $this->compare(new \SplFileInfo($oldFilePath), $newFile);


        $differencesText = $this->formatDifferences($differences);

        // محاولة تحديث الملف
        $isUpdated = $this->updateFileInStorage($existingFile, $newFile);

        if ($isUpdated === true) {
            return [
                'message' => 'File updated successfully.',
                'differences' => $differencesText,
                'status' => 200,
            ];
        }

        if ($isUpdated === false) {
            return [
                'message' => 'The uploaded file name does not match the existing file name.',
                'differences' => $differencesText,
                'status' => 400,
            ];
        }

        return [
            'message' => 'File not found or does not exist in storage.',
            'status' => 404,
        ];
    }

    private function formatDifferences($differences)
    {
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
        return $differencesText;
    }

    private function updateFileInStorage($existingFile, $newFile)
    {
        if ($newFile->getClientOriginalName() === $existingFile->name) {
            Storage::disk('files')->put($existingFile->name, file_get_contents($newFile));
            return true;
        }
        return false;
    }
    //----------------------------------------------------------------------
    public function getFileLogs($fileId)
    {
        return $this->FileRepository->getFileLogs($fileId);
    }

    public function getAllFileLogs()
    {
        return $this->FileRepository->getAllFileLogs();
    }
}
