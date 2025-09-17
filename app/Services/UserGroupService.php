<?php


namespace App\Services;


use App\Repositories\UserGroupRepository;
use Illuminate\Support\Facades\Log;

class UserGroupService
{
    protected $userGroupRepository;

    public function __construct(UserGroupRepository $userGroupRepository)
    {
        $this->userGroupRepository = $userGroupRepository;
    }

    /**
     * Assign new users to a specific group if the current user is the group admin.
     *
     * @param int $groupId
     * @param array $userIds
     * @return void
     */
    public function assignNewUsersToGroup(int $groupId, array $userIds)
    {
        $adminId = $this->userGroupRepository->getGroupAdminId($groupId);

        if ($adminId !== auth()->id()) {
            Log::warning("User " . auth()->id() . " does not have permission to assign users to group {$groupId}.");

        }

        $this->assignUsersToGroup($groupId, $userIds);
    }

    /**
     * Assign users to a group.
     *
     * @param int $groupId
     * @param array $userIds
     * @return void
     */
    public function assignUsersToGroup(int $groupId, array $userIds)
    {
        foreach ($userIds as $userId) {
            try {
                $this->userGroupRepository->create([
                    'group_id' => $groupId,
                    'user_id' => $userId,
                ]);
            } catch (\Exception $exception) {
                Log::error("Failed to assign user {$userId} to group {$groupId}: " . $exception->getMessage());
            }
        }
    }
    /**
     * Check if the current user is the admin of a specific group.
     *
     * @param int $groupId
     * @return bool
     */
    public function isGroupAdmin($groupId)
    {
        $adminId = $this->userGroupRepository->getGroupAdminId($groupId);
        return $adminId == auth()->user()->id;

    }

    public function removeUserFromGroup(int $id)
{
    try {
        return $this->userGroupRepository->delete($id);
    } catch (\Exception $e) {
        Log::error("Failed to remove user from group with association ID {$id}: " . $e->getMessage());
        return false;
    }
}

    /**
     * Retrieve all groups associated with a specific user.
     *
     * @param int $userId
     * @return mixed
     */
    public function getUserGroups(int $userId)
    {
        try {
            return $this->userGroupRepository->findByUserId($userId);
        } catch (\Exception $e) {
            Log::error("Failed to retrieve groups for user {$userId}: " . $e->getMessage());
            return null;
        }
    }
}

