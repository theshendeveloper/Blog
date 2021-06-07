<?php

namespace App\Http\Traits;


use App\Models\Permission;

trait HasPermissions{

    public function hasPermission($permission)
    {
        return $this->hasRole($permission->roles);
    }
    protected function getAllPermissions(array $permissions)
    {
        return Permission::whereIn('name',$permissions)->get();
    }

    public function givePermissionTo(...$permissions)
    {
        $this->permissions()->sync($this->getAllPermissions($permissions),false);
    }
}
