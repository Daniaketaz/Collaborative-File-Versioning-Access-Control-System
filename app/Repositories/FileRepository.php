<?php


namespace App\Repositories;


use App\Models\Check;
use App\Models\File;
use App\Models\FileLoge;
use App\Models\LogRecord;
use App\Models\User_Group;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

//use App\Repositories\Contracts\GroupRepositoryInterface;

class FileRepository
{
    protected $model;

    public function __construct(File $model)
    {
        $this->model = $model;
    }

    public function create(array $data)
    {
        File::query()->create($data);
    }

    public function findFileByName($File_name)
    {
        return File::query()->where('name', $File_name);
    }

    public function deleteFile($file_id)
    {
        $file = $this->findFileById($file_id);
        Storage::disk('files')->delete($file->name);
        $file->delete();
        return true;
    }

    public function findFileById($File_id)
    {
        return $this->model->findOrFail($File_id);
    }

    public function AcceptFile($file_id)
    {
        return File::query()->find($file_id)->update(['accepted' => true]);
    }

    public function getUnacceptedFiles($group_id)
    {
        return File::query()
            ->where('accepted', 0)
            ->whereHas('user_group', function ($query) use ($group_id) {
                $query->where('group_id', $group_id);
            })
            ->get();

    }

    //------------------


    public function markFilesAsReserved(array $fileIds)
    {
        return File::query()->whereIn('id', $fileIds)
            ->update(['free' => false]);
    }

    public function markFilesAsFree($fileId)
    {
        return File::query()->where('id', $fileId)
            ->update(['free' => true]);
    }

    public function isCheckedInByUser($fileId, $userId)
    {
        return Check::query()
            ->where('user_id', $userId)
            ->where('file_id', $fileId)
            ->whereNull('time_check_out')
            ->exists();
    }

    public function createCheckIn($userId, $fileId)
    {
        return Check::query()->create([
            'user_id' => $userId,
            'file_id' => $fileId,
            'time_check_in' => Carbon::now(),
        ]);
    }


    public function createCheckOut($userId, $fileId)
    {
        return Check::query()
            ->where('user_id', $userId)
            ->where('file_id', $fileId)
            ->whereNull('time_check_out')
            ->latest()
            ->update(['time_check_out' => Carbon::now()]);
    }

    public function getFileName(int $fileId)
    {
        return File::query()->findOrFail($fileId)['name'];
    }

    public function isCheckedIn($fileId)
    {
        return File::query()->where('id', $fileId)->where('free', false)->exists();
    }

    public function logAction($fileId, $userId, $action, $details = null)
    {
        FileLoge::create([
            'file_id' => $fileId,
            'user_id' => $userId,
            'action' => $action,
            'details' => $details,
        ]);
    }

    public function getFileLogs($fileId)
    {
        return LogRecord::query()
            ->where('file_id', $fileId
            )->orderBy('created_at', 'desc')->paginate(10);

    }

    public function getAllFileLogs()
    {
        return FileLoge::all();
    }

    public function readFile($file)
    {
        $filePath = $file->getRealPath();

        // Determine the extension based on the file type
        if ($file instanceof \Illuminate\Http\UploadedFile) {
            $extension = $file->getClientOriginalExtension(); // Uploaded file
        } else {
            $extension = pathinfo($filePath, PATHINFO_EXTENSION); // Other file types
        }

        // Handle file based on extension
        if ($extension === 'docx') {
            return $this->readDocxFile($filePath);
        }

        return $this->readTextFile($filePath);
    }

    private function readDocxFile($filePath)
    {
        $zip = new ZipArchive;
        if ($zip->open($filePath) === true) {
            $content = $zip->getFromName('word/document.xml');
            $zip->close();
            return $this->splitIntoLinesWithEmptyLines($content);
        }
        throw new \Exception("Unable to read .docx file.");
    }

    private function splitIntoLinesWithEmptyLines($content)
    {
        if (is_array($content)) {
            $content = implode("\n", $content);
        }
        $normalizedContent = strip_tags($content);
        $lines = preg_split("/\r\n|\n|\r/", $normalizedContent);

        return array_map(function ($line) {
            return rtrim($line, "\r");
        }, $lines);
    }

    private function readTextFile($filePath)
    {
        return $this->splitIntoLinesWithEmptyLines(file_get_contents($filePath));
    }

    /**
     * Compare the old and new content, considering the differences in spaces and empty lines.
     */
    public function compareContents($oldContent, $newContent)
    {
        $oldLines = $this->splitIntoLinesWithEmptyLines($oldContent);
        $newLines = $this->splitIntoLinesWithEmptyLines($newContent);

        $differences = [];
        $maxLines = max(count($oldLines), count($newLines));

        for ($i = 0; $i < $maxLines; $i++) {
            $oldLine = $oldLines[$i] ?? '';
            $newLine = $newLines[$i] ?? '';

            if ($oldLine !== $newLine) {
                $differences[] = [
                    'line' => $i + 1,
                    'old' => $oldLine,
                    'new' => $newLine,
                ];
            }
        }
        return $differences;
    }

    public function getGroupFiles($group_id)
    {
        $user_group_ids = User_Group::query()->where('group_id', $group_id)->get('id');
        return File::query()->whereIn('user_group_id', $user_group_ids)->
        where('accepted', 1)->get();
    }


}
