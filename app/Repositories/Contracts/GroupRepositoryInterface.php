<?php

namespace App\Repositories\Contracts;

interface GroupRepositoryInterface
{
    public function getAllGroupsAsSuperAdmin();

    public function getAllGroupsAsAdmin($adminId);

    public function findGroupById(int $groupId);

    public function createGroup(array $data);

    public function updateGroup($id, array $data);

    public function deleteGroup($id);
}
