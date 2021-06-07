<?php

namespace App\Models;

use App\Http\Traits\HasPermissions;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasPermissions;

    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

}
