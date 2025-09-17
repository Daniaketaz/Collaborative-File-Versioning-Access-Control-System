<?php


namespace App\Repositories;


use App\Models\Group;
use App\Models\JoinGroup;
use App\Models\User_Group;
use App\Repositories\Contracts\UserGroupRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class JoinGroupRepository
{
    protected $model;

    public function __construct(JoinGroup $model)
    {
        $this->model = $model;
    }

    public function create($group_id)
    {
        return $this->model->create(["user_id"=>Auth::id(),"group_id"=>$group_id]);
    }

    public function Accept($join_id,$group_id){
        User_Group::create(['user_id'=>JoinGroup::find($join_id)->user_id,
                 'group_id'=>Group::find($group_id)->id]);

    }
    public function delete($join_id){
      JoinGroup::query()->find($join_id)->delete();
    }

    public function getAllRequests($group_id){
        return JoinGroup::query()->where("group_id",$group_id)->get();
    }

}
