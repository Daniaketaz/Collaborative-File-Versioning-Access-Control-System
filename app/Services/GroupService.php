<?php


namespace App\Services;

use App\Models\Group;
use App\Models\User_Group;
use App\Exceptions\UnauthorizedException;
use App\Http\Requests\AssignNewUsersToGroupRequest;
use App\Repositories\GroupRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GroupService
{
    protected $groupRepository;
    protected $userGroupService;

    protected $invitationService;


    public function __construct(GroupRepository $groupRepository, UserGroupService $userGroupService, InvitationService $invitationService)
    {
        $this->groupRepository = $groupRepository;
        $this->userGroupService = $userGroupService;
        $this->invitationService = $invitationService;
    }


    public function createGroup(array $groupData)
    {
        return $this->groupRepository->createGroup($groupData);
    }

    public function sendInvitationsForGroup(int $groupId, array $userIds): void
    {
        $this->invitationService->sendInvitations($groupId, $userIds);
    }
    //  for admin
    public function getAllGroupsAsAdmin($adminId)
    {
        return $this->groupRepository->getAllGroupsAsAdmin($adminId);
    }

    public function getGroupById($id)
    {
        return $this->groupRepository->getGroupById($id);
    }

    public function updateGroup($id, array $data)
    {
        return $this->groupRepository->updateGroup($id, $data);
    }

    public function deleteGroup($id)
    {
        return $this->groupRepository->deleteGroup($id);
    }

    public function ShowGroupsNotAdmin() {
        $user_id = Auth::id();
        $user_groups = User_Group::query()
            ->where('user_id', $user_id)
            ->whereHas('group', function ($query) use ($user_id) {
                $query->where('admin_id', '!=', $user_id);
            })->get();
        return $user_groups->map(function ($user_group) {
            return [
                'user_group_id' => $user_group->id,
                'group_id' => $user_group->group->id,
                'group_name' => $user_group->group->group_name,
            ];
        });
    }

    public function ShowAllGroupsNotIn(){
        $user_id = Auth::id();

        $groups = Group::query()
            ->whereNotIn('id', function ($query) use ($user_id) {
                $query->select('group_id')
                    ->from('user_groups')
                    ->where('user_id', $user_id);
            })->get();

        return $groups->map(function ($group) {
            return [
                'group_id' => $group->id,
                'group_name' => $group->group_name  ,
            ];
        });
    }



}
