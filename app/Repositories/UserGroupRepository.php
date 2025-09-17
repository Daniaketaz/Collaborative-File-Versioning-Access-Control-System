<?php


namespace App\Repositories;


use App\Models\Group;
use App\Models\User_Group;
use App\Repositories\Contracts\UserGroupRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class UserGroupRepository implements UserGroupRepositoryInterface
{
    protected $model;

    public function __construct(User_Group $model)
    {
        $this->model = $model;
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * Get the admin ID of a group by group ID.
     *
     * @param int $groupId
     * @return int|null
     */
    public function getGroupAdminId($group_id)
    {
        return Group::query()->find($group_id)->admin_id;
    }


    public function getAllGroupsAsMember($id)
    {
        return Group::all();
    }

    public function delete($id)
    {
        return $this->model->destroy($id);
    }

    public function findByUserId($userId)
    {
        return $this->model->where('user_id', $userId)->get();
    }

    public function findUserGroup($group_id)
    {
        return User_Group::query()->where('user_id', Auth::id())
            ->where('group_id', $group_id)->first();
    }

    public function CheckIfUserInGroup($group_id)
    {
        $user_group = User_Group::query()->where('user_id', Auth::id())
            ->where('group_id', $group_id)->first();
        if (!$user_group) return false;
        return true;
    }

}
