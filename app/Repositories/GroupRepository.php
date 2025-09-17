<?php


namespace App\Repositories;


use App\Models\Group;
use App\Models\User_Group;
use App\Repositories\Contracts\GroupRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class GroupRepository implements GroupRepositoryInterface
{
    protected $model;

    public function __construct(Group $model)
    {
        $this->model = $model;
    }

    public function getAllGroupsAsSuperAdmin()
    {
        return Group::all();
    }

    public function getAllGroupsAsAdmin($adminId)
    {

        $user_id = Auth::id();
        $user_groups = User_Group::query()
            ->where('user_id', $user_id)
            ->whereHas('group', function ($query) use ($user_id) {
                $query->where('admin_id', '=', $user_id);
            })->get();
        return $user_groups->map(function ($user_group) {
            return [
                'user_group_id' => $user_group->id,
                'group_id' => $user_group->group->id,
                'group_name' => $user_group->group->group_name,
            ];
        });
    }

    /**
     * Find a group by its ID.
     *
     * @param int $groupId
     * @return \App\Models\Group
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findGroupById(int $groupId): Group
    {
        return $this->model->findOrFail($groupId);
    }

    /**
     * Create a new group.
     *
     * @param array $data
     */
    public function createGroup(array $data)
    {

        $group = Group::query()->create($data);
        $user = User_Group::query()->create(['user_id' => Auth::id(),
            'group_id' => $group->id,
            'accept' => '1']);
        return $group;
    }

    public function updateGroup($id, array $data)
    {
        $group = Group::findOrFail($id);
        $group->update($data);
        return $group;
    }

    public function deleteGroup($id)
    {
        $group = Group::findOrFail($id);
        $group->delete();
    }

    public function checkGroupAdmin($group_id)
    {
        $Group = $this->getGroupAdmin($group_id);
        if ($Group == Auth::id()) return true;
        return false;
    }

    public function getGroupAdmin($group_id)
    {
        return Group::query()->find($group_id)->admin_id;
    }

    public function getAllGroupsNotIn()
    {
        $user_id = Auth::id();
        return Group::query()
            ->whereNotIn('id', function ($query) use ($user_id) {
                $query->select('group_id')
                    ->from('user_groups')
                    ->where('user_id', $user_id);
            })->get();
    }

    public function getAllGroupsNotAdmin()
    {
        $user_id = Auth::id();
        return User_Group::query()
            ->where('user_id', $user_id)
            ->whereHas('group', function ($query) use ($user_id) {
                $query->where('admin_id', '!=', $user_id);
            })->get();
    }

}
