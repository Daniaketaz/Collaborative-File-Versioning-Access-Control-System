<?php

namespace App\Http\Controllers;

use App\Exceptions\UnauthorizedException;
use App\Http\Requests\AssignNewUsersToGroupRequest;
use App\Http\Requests\CreateGroupRequest;
use App\Services\GroupService;
use App\Services\InvitationService;
use App\Services\UserGroupService;
use App\Services\UserService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class GroupController extends Controller
{
    protected $groupService;
    protected $userGroupService;
    protected $userService;
    protected $invitationService;

    public function __construct(GroupService $groupService, UserGroupService $userGroupService, UserService $userService, InvitationService $invitationService)
    {
        $this->groupService = $groupService;
        $this->userGroupService = $userGroupService;
        $this->userService = $userService;
        $this->invitationService = $invitationService;
    }


    public function createGroup(CreateGroupRequest $request)
    {
        $adminId = Auth::id();
        $membersIds = $request->input('members_ids', []);
        $groupData = [
            'admin_id' => $adminId,
            'group_name' => $request->input('group_name'),
        ];
        try {

            $group = $this->groupService->createGroup($groupData);
            $this->userGroupService->assignNewUsersToGroup($group->id, [$group->admin_id]);
            $this->groupService->sendInvitationsForGroup($group->id, $membersIds);
            return response()->json([
                'message' => 'Group created successfully and invitations sent.',
                'group_data' => $group,
                'invited_members_ids' => $membersIds,
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to create group or send invitations'], 500);
        }
    }

    public function sendInvitation(Request $request)
    {
        $groupId = $request->input('group_id');
        $membersIds = $request->input('members_ids', []);
        try {

            $this->groupService->sendInvitationsForGroup($groupId, $membersIds);

            return response()->json([
                'message' => 'invitations sent successfully.',
                'invited_members_ids' => $membersIds,
            ], 200);
        } catch (\Exception $e) {
            Log::error("Failed to  send invitations: " . $e->getMessage());
            return response()->json(['message' => 'Failed to send invitations'], 500);
        }
    }

    ////بحاجة تعديل
    public function getSentInvitations(Request $request)
    {

        $userId = auth()->user()->id;
        $invitations = $this->invitationService->getSentInvitations($userId);

        return response()->json([
            'success' => true,
            'data' => $invitations
        ]);
    }

    public function getReceivedInvitations()
    {

        $userId = auth()->user()->id;
        $invitations = $this->invitationService->getReceivedInvitations($userId);

        return response()->json([
            'success' => true,
            'data' => $invitations,
        ]);
    }

    public function rejectInvitation(int $invitationId)
    {
        $this->invitationService->updateInvitationStatus($invitationId, 'rejected');

        return response()->json(['message' => 'Invitation rejected successfully.']);
    }

    public function assignNewUsersToGroup(AssignNewUsersToGroupRequest $request)
    {
        try {
            $groupId = $request->input('group_id');
            $userIds = $request->input('user_ids');

            $this->userGroupService->assignNewUsersToGroup($groupId, $userIds);

            return response()->json([
                'success' => true,
                'message' => 'Users assigned to group successfully.',
                'membersIds' => $userIds
            ]);
        } catch (UnauthorizedException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 403);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'An error occurred while assigning users to the group.',
            ], 500);
        }
    }

    public function isGroupAdmin(Request $request)
    {
        $groupId = $request->validate([
            'group_id' => 'required|integer|exists:groups,id',
        ])['group_id'];
        $isAdmin = $this->userGroupService->isGroupAdmin($groupId);
        return response()->json([
            'success' => true,
            'is_admin' => $isAdmin,
        ]);
    }

    public function getAllGroupsAsAdmin()
    {
        {
            $userId = auth()->user()->id;
            try {
                $adminGroups = $this->groupService->getAllGroupsAsAdmin($userId);
                return response()->json([
                    'success' => true,
                    'groups' => $adminGroups,
                ]);
            } catch (\Exception $e) {
                Log::error("Failed to retrieve admin groups: " . $e->getMessage());
                return response()->json(['message' => 'Failed to retrieve admin groups'], 500);
            }
        }
    }

    public function getEligibleUsers(Request $request)
    {
        $groupId = $request->input('group_id');

        try {
            $users = $this->userService->getEligibleUsersForGroup($groupId);
            return response()->json($users);
        } catch (ModelNotFoundException $e) {
            Log::error("Group not found: " . $e->getMessage());
            return response()->json(['message' => 'Group not found'], 404);
        } catch (\Exception $e) {
            Log::error("Failed to retrieve eligible users: " . $e->getMessage());
            return response()->json(['message' => 'Failed to retrieve eligible users'], 500);
        }
    }

    public function searchUserForGroup(Request $request)
    {
        $groupId = $request->input('group_id');
        $userName = $request->input('user_name');

        if (!$groupId || !$userName) {
            return response()->json(['message' => 'Group ID and user name are required.'], 400);
        }

        try {
            $users = $this->userService->searchUserForGroup($groupId, $userName);

            if ($users->isEmpty()) {
                return response()->json(['user' => null], 404);
            }

            return response()->json(['user' => $users]);

        } catch (ModelNotFoundException $e) {
            Log::error("Group not found: " . $e->getMessage());
            return response()->json(['message' => 'Group not found'], 404);
        } catch (\Exception $e) {
            Log::error("Failed to search for user: " . $e->getMessage());
            return response()->json(['message' => 'Failed to search for user'], 500);
        }
    }

    public function getUsersNotGroupAdmin()
    {
        $users = $this->userService->getUsersNotGroupAdmin();

        if ($users === null) {
            return response()->json([
                'message' => 'Group not found',
            ], 404);
        }

        return response()->json($users);
    }

    public function acceptInvitation(int $invitationId)
    {

        $this->invitationService->acceptInvitation($invitationId);

        return response()->json(['message' => 'Invitation accepted and joined the group.']);
    }

    public function showGroupsUserNotTheAdminIn()
    {
        $groups = $this->groupService->ShowGroupsNotAdmin();
        if ($groups) {
            return response()->json(['groups' => $groups]);
        } else {
            return response()->json(['message' => 'you didnt join to any group']);
        }
    }

    public function ShowAllGroupsNotIn()
    {
        $groups = $this->groupService->ShowAllGroupsNotIn();
        if ($groups) {
            return response()->json(['groups' => $groups]);
        } else {
            return response()->json(['message' => 'you didnt join to any group']);
        }
    }

    public function getGroupUser($group_id)
    {
        $userIds = $this->userService->getGroupUsers($group_id);
        return response()->json($userIds);
    }

}
