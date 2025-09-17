<?php
namespace App\Services;

use App\Models\Check;
use App\Models\User;
use App\Models\User_Group;
use App\Repositories\FileRepository;
use App\Repositories\GroupRepository;
use App\Repositories\ReportRepository;

class ReportService {

    protected $ReportRepository;
    protected $FileRepository;
    protected $GroupRepository;
    public function __construct(ReportRepository $ReportRepository ,
                                FileRepository $fileRepository,
                                GroupRepository $groupRepository)
    {
        $this->ReportRepository=$ReportRepository;
        $this->FileRepository=$fileRepository;
        $this->GroupRepository=$groupRepository;
    }

    public function SeeUserReport($group_id){
        $user_ids = User_Group::query() ->where('group_id', $group_id)->pluck('user_id');
        $checks=Check::query()->whereIn('user_id',$user_ids)->get();
        $reports=collect();
        foreach ($checks as $check){
            $file_id=$check->file_id;
            $reports->push(['user_name'=>$this->ReportRepository->FindCheckUserName($check->id),
                'checkin on file'=>$this->FileRepository->findFileById($file_id)->name,
                'checkin date'=>$this->ReportRepository->getFileCheckDate($check->id) ,
                'checkout date'=>$this->ReportRepository->getFileReturnDate($check->id)]);

        }
        return $reports;
    }

    public function SeeFileReport($file_id){
        $checks=$this->ReportRepository->FindChecksByFile($file_id);
        $reports=collect();
        foreach ($checks as $check){
            $file_id=$check->file_id;
            $reports->push([
                'user_name'=>$this->ReportRepository->FindCheckUserName($check->id),
                'checkin on file'=>$this->FileRepository->findFileById($file_id)->name,
                'checkin date'=>$this->ReportRepository->getFileCheckDate($check->id) ,
                'checkout date'=>$this->ReportRepository->getFileReturnDate($check->id)]);

        }
        return $reports;
    }


    //---------------------- admin report ----------
    public function getAllFilesLogs()
    {
        return $this->ReportRepository->getAllFilesLogs();
    }

    public function getAllUsersLogs()
    {
        return $this->ReportRepository->getAllUsersLogs();
    }

    public function getAllGroupsLogs()
    {
        return $this->ReportRepository->getAllGroupsLogs();
    }

    public function getFileLog($fileId)
    {
        return $this->ReportRepository->getFileLog($fileId);
    }

    public function getUserLog($userId)
    {
        return $this->ReportRepository->getUserLog($userId);
    }

    public function getGroupLog($groupId)
    {
        return $this->ReportRepository->getGroupLog($groupId);
    }
    public  function getUserFileLog($userId)
    {
        return $this->ReportRepository->getUserFileLog($userId);
    }
    public function getUserUpdatesLogs($userId)
    {
        return $this->ReportRepository->getUserUpdatesLogs($userId);
    }

    public function getFileUpdatesLogs($fileId)
    {
        return $this->ReportRepository->getFileUpdatesLogs($fileId);
    }
    public function getAllLogs()
    {
        return $this->ReportRepository->getAllLogs();
    }
    public function getFailedLogs()
    {
        return $this->ReportRepository->getFailedLogs();
    }
    public function getSuccessfulLogs()
    {
        return $this->ReportRepository->getSuccessfulLogs();
    }
}
