<?php


namespace App\Services;

use App\Http\Controllers\Controller;
use App\Repositories\InvitationRepository;
use App\Models\Group;

class InvitationService
{
    protected $invitationRepository;
    protected $userGroupService;

    public function __construct(InvitationRepository $invitationRepository, UserGroupService $userGroupService)
    {
        $this->invitationRepository = $invitationRepository;
        $this->userGroupService = $userGroupService;
    }

    public function sendInvitations(int $groupId, array $userIds): void
    {
        foreach ($userIds as $userId) {
            $this->invitationRepository->createInvitation([
                'group_id' => $groupId,
                'invited_user_id' => $userId,
                'status' => 'pending',
            ]);
        }
    }

    public function getReceivedInvitations(int $userId)
    {
        return $this->invitationRepository->getReceivedInvitations($userId);
    }

    public function getSentInvitations(int $userId)
    {
        return $this->invitationRepository->getSentInvitations($userId);
    }
    public function acceptInvitation(int $invitationId)
    {
        $invitation = $this->invitationRepository->findById($invitationId);

        if (!$invitation) {
            throw new \Exception("Invitation not found.");
        }

        if ($invitation->status !== 'pending') {
            throw new \Exception("Cannot accept this invitation as it is not pending.");
        }


        $invitation->status = 'accepted';
        $invitation->save();


//return [$invitation->group_id, $invitation->invited_user_id];
        $this->userGroupService->assignUsersToGroup($invitation->group_id, [$invitation->invited_user_id]);
    }


    public function updateInvitationStatus(int $invitationId, string $status)
    {
        $invitation = $this->invitationRepository->findById($invitationId);

        if (!$invitation) {
            throw new \Exception("Invitation not found.");
        }

        $invitation->status = $status;
        $invitation->save();

        return $invitation;
    }

    public function respondToInvitation(int $invitationId, string $status)
    {
        $invitation = $this->invitationRepository->updateInvitationStatus($invitationId, $status);

        if ($status === 'accepted') {
            $invitation->group->users()->attach($invitation->invited_user_id);
        }

        return $invitation;
    }
}

