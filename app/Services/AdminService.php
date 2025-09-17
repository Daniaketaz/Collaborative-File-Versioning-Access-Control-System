<?php
namespace App\Services;

use App\Repositories\AdminRepository;

class AdminService
{
protected $adminRepository;

public function __construct(AdminRepository $adminRepository)
{
$this->adminRepository = $adminRepository;
}

public function getAllFiles()
{
return $this->adminRepository->getAllFiles();
}

public function getAllGroups()
{
return $this->adminRepository->getAllGroups();
}

public function getAllUsers()
{
return $this->adminRepository->getAllUsers();
}

public function getAllFilesLogs()
{
return $this->adminRepository->getAllFilesLogs();
}

public function getAllUsersLogs()
{
return $this->adminRepository->getAllUsersLogs();
}

public function getAllGroupsLogs()
{
return $this->adminRepository->getAllGroupsLogs();
}

public function getFileLog($fileId)
{
return $this->adminRepository->getFileLog($fileId);
}

public function getUserLog($userId)
{
return $this->adminRepository->getUserLog($userId);
}

public function getGroupLog($groupId)
{
return $this->adminRepository->getGroupLog($groupId);
}
public  function getUserFileLog($userId)
{
    return $this->adminRepository->getUserFileLog($userId);
}
public function getUserUpdatesLogs($userId)
{
    return $this->adminRepository->getUserUpdatesLogs($userId);
}

public function getFileUpdatesLogs($fileId)
{
    return $this->adminRepository->getFileUpdatesLogs($fileId);
}
    public function getAllLogs()
    {
        return $this->adminRepository->getAllLogs();
    }
    public function getFailedLogs()
    {
        return $this->adminRepository->getFailedLogs();
    }
    public function getSuccessfulLogs()
    {
        return $this->adminRepository->getSuccessfulLogs();
    }
}
