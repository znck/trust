<?php namespace Znck\Trust\Events;

class PermissionUsed
{
    /**
     * @var \Znck\Trust\Contracts\Permissible
     */
    public $user;
    /**
     * @var string
     */
    public $permission;

    public function __construct($user, string $permission)
    {
        $this->user = $user;
        $this->permission = $permission;
    }
}
