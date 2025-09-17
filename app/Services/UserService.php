<?php


namespace App\Services;


use App\Models\Group;
use App\Models\User;
use App\Repositories\GroupRepository;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserService
{
    protected $groupRepository;
    protected $userRepository;

    public function __construct(GroupRepository $groupRepository, UserRepository $userRepository)
    {
        $this->groupRepository = $groupRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Retrieve all users who can be added to a specific group, excluding the admin.
     *
     * @param int $groupId
     * @return \Illuminate\Database\Eloquent\Collection
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getEligibleUsersForGroup(int $groupId)
    {
        $group = $this->groupRepository->findGroupById($groupId);
        $adminId = $group->admin_id;

        return $this->userRepository->getUsersNotInGroup($groupId, $adminId);
    }
    /**
     * Search for a user by name who can be added to a specific group, excluding the admin.
     *
     * @param int $groupId
     * @param string $userName
     * @return \Illuminate\Database\Eloquent\Collection
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function searchUserForGroup(int $groupId, string $userName)
    {
        $group = $this->groupRepository->findGroupById($groupId);
        $adminId = $group->admin_id;

        return $this->userRepository->searchUserNotInGroup($groupId, $adminId, $userName);
    }
    /**
     * Get all users who are not the admin of the specified group.
     *
     * @param int $groupId
     * @return \Illuminate\Database\Eloquent\Collection|null
     */
    public function getUsersNotGroupAdmin( )
    {
        try {

            return $this->userRepository->getUsersNotGroupAdmin();
        } catch (ModelNotFoundException $e) {
            return null;
        }
    }
    public function getGroupUsers($group_id)
    {return$this->userRepository->getGroupUser($group_id);
    }
    public function getUserLogs($userId, $group_id)
    {
        return $this->userRepository->getUserLogs($userId, $group_id);
    }

    public function getAllUserLogs()
    {
        return $this->userRepository->getAllUserLogs();
    }
}
