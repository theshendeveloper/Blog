<?php

namespace App\Http\Traits;

use App\Models\Role;

trait HasRoles {

    public function hasRole($roles)
    {
        return $roles->intersect($this->roles)->isNotEmpty();
    }


    public function assignRole(Role $role)
    {
        $this->roles()->sync($role, false);
    }
}
