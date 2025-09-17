<?php
namespace App\Repositories\Contracts;
interface UserGroupRepositoryInterface
{
    public function create(array $data);
    public  function getGroupAdminId($group_id);

    public function getAllGroupsAsMember($id);

    public function delete($id);

    public function findByUserId($userId);
}
