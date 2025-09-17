<?php

namespace App\Services;

use App\Notifications\FileStatusChanged;
use App\Repositories\FileRepository;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CheckService
{

    protected $fileRepository;

    public function __construct(FileRepository $fileRepository)
    {
        $this->fileRepository = $fileRepository;
    }

    public function showBackups($file_id)
    {
        $file = $this->fileRepository->findFileById($file_id);
        if (!$file) {
            return null;
        }
        $allBackupFiles = Storage::disk('backups')->files();
        $backupFiles = array_filter($allBackupFiles, function ($backupFile) use ($file) {
            return str_contains($backupFile, '_' . $file->name);
        });

        if (empty($backupFiles)) {
            return null;
        }
        return $backupFiles;
    }

    public function downloadBackup($file_name)
    {
        if (Storage::disk('backups')->exists($file_name)) {
            return Storage::disk('backups')->path($file_name);
        }
        return null;
    }

    public function checkIn(array $fileIds)
    {
        $locked = [];
        $userId = Auth::id();

        try {
            DB::beginTransaction();

            foreach ($fileIds as $fileId) {
                $lockKey = "file-checkout-$fileId";
                $lock = Cache::lock($lockKey, 10);

                if (!$lock->get()) {
                    return [
                        'success' => false,
                        'message' => 'File ' . $this->fileRepository->getFileName($fileId) . ' is currently being processed by another user.',
                        'code' => 400,
                    ];
                }

                $locked[] = $lock;
            }

            $files = $this->fileRepository->findFileById($fileIds);

            if ($files->contains(function ($file) {
                return !$file->free;
            })) {
                return [
                    'success' => false,
                    'message' => 'One or more files are already checked out.',
                    'code' => 400,
                ];
            }

            $this->fileRepository->markFilesAsReserved($fileIds);

            foreach ($fileIds as $fileId) {
                $this->fileRepository->createCheckIn($userId, $fileId);
                $file = $this->fileRepository->findFileById($fileId);
                $this->createBackup($file->name);
                $groupMembers = $file->user_group->group->user_groups->pluck('user');
                $groupMembers->each->notify(new FileStatusChanged($file->name, 'booked', Auth::user()->name));
            }

            DB::commit();

            return [
                'success' => true,
                'message' => 'Files successfully checked in.',
                'code' => 200,
            ];
        } catch (Exception $e) {
            DB::rollBack();

            foreach ($locked as $lock) {
                $lock->release();
            }

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'code' => 400,
            ];
        } finally {
            foreach ($locked as $lock) {
                if ($lock->isOwnedByCurrentProcess()) {
                    $lock->release();
                }
            }
        }
    }

    public function createBackup($fileName)
    {
        if (!Storage::disk('files')->exists($fileName)) return null;

        $backupFileName = now()->format('YmdHis') . '_' . $fileName;
        Storage::disk('backups')->put($backupFileName, Storage::disk('files')->get($fileName));
        return $backupFileName;

    }

    public function checkOut($fileId)
    {
        $userId = Auth::id();
        $lockKey = "file-checkout-$fileId";
        $lock = Cache::lock($lockKey, 10);

        try {
            $file = $this->fileRepository->findFileById($fileId);
            if (!$file) {
                return [
                    'success' => false,
                    'message' => 'Invalid file ID.',
                    'code' => 404,
                ];
            }

            if (!$lock->get()) {
                return [
                    'success' => false,
                    'message' => 'File ' . $this->fileRepository->getFileName($fileId) . ' is currently being processed by another user.',
                    'code' => 400,
                ];
            }

            DB::beginTransaction();

            if (!$this->fileRepository->isCheckedIn($fileId)) {
                return [
                    'success' => false,
                    'message' => 'The file is not currently booked.',
                    'code' => 400,
                ];
            }

            if (!$this->fileRepository->isCheckedInByUser($fileId, $userId)) {
                return [
                    'success' => false,
                    'message' => 'The file is not booked by the current user.',
                    'code' => 400,
                ];
            }

            $this->fileRepository->markFilesAsFree($fileId);
            $this->fileRepository->createCheckOut($userId, $fileId);
            $groupMembers = $file->user_group->group->user_groups->pluck('user');
            $groupMembers->each->notify(new FileStatusChanged($file->name, 'unbooked', Auth::user()->name));

            DB::commit();

            return [
                'success' => true,
                'message' => 'File successfully checked out.',
                'code' => 200,
            ];
        } catch (Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'code' => 400,
            ];
        } finally {
            if ($lock->isOwnedByCurrentProcess()) {
                $lock->release();
            }
        }
    }

}
