<?php


namespace App\Repositories;

use App\Models\Invitation;

class InvitationRepository
{
    public function createInvitation(array $data)
    {
        return Invitation::create($data);
    }

    public function getReceivedInvitations(int $userId)
    {
        return Invitation::with('group')
            ->where('invited_user_id', $userId)
            ->where('status', 'pending')
            ->get();
    }

    public function getSentInvitations(int $userId)
    {
        return Invitation::with('group', 'invitedUser')
            ->where('invited_by', $userId)
            ->get();
    }
    public function findById(int $id)
    {
        return Invitation::find($id);
    }

    public function getInvitationById(int $invitationId)
    {
        return Invitation::with('group', 'invitedBy', 'invitedUser')->findOrFail($invitationId);
    }

    public function updateInvitationStatus(int $invitationId, string $status)
    {
        $invitation = $this->getInvitationById($invitationId);
        $invitation->status = $status;
        $invitation->save();

        return $invitation;
    }
}

