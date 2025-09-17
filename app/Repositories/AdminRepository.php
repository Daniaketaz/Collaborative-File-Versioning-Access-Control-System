<?php


namespace App\Repositories;

use App\Models\File;
use App\Models\Group;
use App\Models\LogRecord;
use App\Models\User;

class AdminRepository
{
    public function getAllFiles()
    {
        return File::all();
    }

    public function getAllGroups()
    {
        return Group::all();
    }

    public function getAllUsers()
    {
        return User::all();
    }

    public function getAllFilesLogs()
    {
        return LogRecord::query()->whereNotNull('file_id')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function getAllUsersLogs()
    {
        return LogRecord::query()->whereNotNull('user_id')->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function getAllGroupsLogs()
    {
        return LogRecord::query()->whereNotNull('group_id')->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function getFileLog($fileId)
    {
        return LogRecord::query()->where('file_id', $fileId)->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function getUserLog($userId)
    {
        return LogRecord::query()->where('user_id', $userId)->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function getGroupLog($groupId)
    {
        return LogRecord::query()->where('group_id', $groupId)->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function getUserFileLog($userId)
    {
        return LogRecord::query()->where('user_id', $userId)->whereNotNull('file_id')->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function getUserUpdatesLogs($userId)
    {
        return LogRecord::query()->where('user_id', $userId)
            ->where("action", "Completed  request : updateFile")
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function getFileUpdatesLogs($fileId)
    {
        return LogRecord::query()->where('file_id', $fileId)
            ->where("action", "Completed  request : updateFile")
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function getAllLogs()
    {
        return LogRecord::query()
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function getFailedLogs()
    {
        return LogRecord::query()
            ->where(function ($query) {
                $query->whereJsonContains('details->status', 400)
                    ->orWhereJsonContains('details->status', 401)
                    ->orWhereJsonContains('details->status', 403)
                    ->orWhereJsonContains('details->status', 404)
                    ->orWhereJsonContains('details->status', 500)
                    ->orWhereJsonContains('details->status', 502)
                    ->orWhereJsonContains('details->status', 503)
                    ->orWhereJsonContains('details->status', 504);
            })
            ->orWhereJsonContains('details->response_message', 'error')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function getSuccessfulLogs()
    {
        return LogRecord::query()
            ->where(function ($query) {
                $query->whereJsonContains('details->status', 200)
                    ->orWhereJsonContains('details->status', 201)
                    ->orWhereJsonContains('details->status', 204);
            })
            ->whereJsonDoesntContain('details->response_message', 'error')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }
}

