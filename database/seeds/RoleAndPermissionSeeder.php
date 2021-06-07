<?php


use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create(['name'=>'Write-Post','label' => 'نوشتن پست']);
        Permission::create(['name'=>'Edit-Post','label' => 'ویرایش پست']);
        Permission::create(['name'=>'Publish-Post','label' => 'انتشار پست']);
        Permission::create(['name'=>'Publish-Comment','label' => 'انتشار کامنت']);
        Permission::create(['name'=>'Delete-Comment','label' => 'حذف کامنت']);
        Permission::create(['name'=>'Unpublish-Post','label' => 'عدم انتشار پست']);
        Permission::create(['name'=>'Unpublish-Comment','label' => 'عدم انتشار کامنت']);


        $role = Role::create(['name' => 'Writer', 'label' => 'نویسنده']);
        $role->givePermissionTo('Write-Post','Edit-Post');

        $role = Role::create(['name' => 'Moderator', 'label' => 'ناظر']);
        $role->givePermissionTo('Publish-Post','Unpublish-Post','Publish-Comment','Unpublish-Comment','Delete-Comment');

        $role = Role::create(['name' => 'Admin' ,'label' => 'ادمین']);
        foreach (Permission::all() as $permission){
            $role->givePermissionTo($permission->name);
        }

    }
}
