<?php


namespace App\Services;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\JoinGroup;
use App\Repositories\GroupRepository;
use App\Repositories\JoinGroupRepository;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Type\Integer;

class JoinGroupService
{
    protected $GroupRepository;
    protected $JoinGroupRepository;
    public function __construct(GroupRepository $GroupRepository, JoinGroupRepository $JoinGroupRepository)
    {
        $this->GroupRepository = $GroupRepository;
        $this->JoinGroupRepository = $JoinGroupRepository;
    }



    public function JoinGroup( $group_id){
       $this->JoinGroupRepository->create($group_id);
        return true;
    }

    public function AcceptJoin($join_id,$group_id)
    {
        if($this->GroupRepository->checkGroupAdmin($group_id)){
            $this->JoinGroupRepository->Accept($join_id,$group_id);
            $this->JoinGroupRepository->delete($join_id);
            return true;
        }
        return false;
    }

    public function RejectJoin($join_id,$group_id)
    {
        if($this->GroupRepository->checkGroupAdmin($group_id)){
            $this->JoinGroupRepository->delete($join_id);
            return true;
        }
        return false;
    }

    public function GetAllRequests($group_id){
        if($this->GroupRepository->checkGroupAdmin($group_id)){
            return $this->JoinGroupRepository->getAllRequests($group_id);

        }
        return false;
    }


}
