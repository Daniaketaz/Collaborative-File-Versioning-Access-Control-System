<?php


namespace App\Repositories;


use App\Models\Group;
use App\Models\LogRecord;
use App\Models\User;
use App\Models\User_Group;
use App\Models\UserLoge;

class UserRepository
{
    protected $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }


    /**
     * Get all users who are not already members of the specified group and are not the admin.
     *
     * @param int $groupId
     * @param int $adminId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUsersNotInGroup(int $groupId, int $adminId)
    {
        return $this->model->where('id', '!=', $adminId)
            ->whereDoesntHave('user_groups', function ($query) use ($groupId) {
                $query->where('group_id', $groupId);
            })
            ->get();
    }

    /**
     * Search for users by name who are not in a specified group and are not the admin.
     *
     * @param int $groupId
     * @param int $adminId
     * @param string $userName
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function searchUserNotInGroup(int $groupId, int $adminId, string $userName)
    {
        return $this->model
            ->where('id', '!=', $adminId)
            ->where('name', 'LIKE', '%' . $userName . '%')
            ->whereDoesntHave('user_groups', function ($query) use ($groupId) {
                $query->where('group_id', $groupId);
            })
            ->get();
    }

    /**
     * Get all users who are not the admin of the group.
     *
     * @param int $groupId
     * @return \Illuminate\Database\Eloquent\Collection|null
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getUsersNotGroupAdmin()
    {
        $admin_id = auth()->user()->id;
        return User::query()->where('id', '!=', $admin_id)->get();
    }

    public function logAction($userId, $action, $details = null)
    {
        UserLoge::create([
            'user_id' => $userId,
            'action' => $action,
            'details' => $details,
        ]);
    }

    public function getGroupUser($group_id)
    {
        $groupAdminId = Group::query()->findOrFail($group_id)->admin->id;
        return User_Group::query()
            ->where('group_id', $group_id)
            ->where('user_id', '!=', $groupAdminId)->get();
    }

    public function getUserLogs($userId, $group_id)
    {
        return LogRecord::query()
            ->where('user_id', $userId)
            ->where('group_id', $group_id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function getAllUserLogs()
    {
        return UserLoge::all();
    }
}
