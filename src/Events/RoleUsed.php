<?php namespace Znck\Trust\Events;

class RoleUsed
{
    /**
     * @var \Znck\Trust\Contracts\Permissible
     */
    public $user;

    /**
     * @var string
     */
    public $role;

    public function __construct($user, string $role)
    {
        $this->user = $user;
        $this->role = $role;
    }
}
