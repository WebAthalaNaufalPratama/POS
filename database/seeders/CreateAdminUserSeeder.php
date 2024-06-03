<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CreateAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {   
        $duplicate = User::where('email', 'superadmin@gmail.com')->first();
        $duplicateadmingallery = User::where('email', 'admingallery@gmail.com')->first();
        $duplicatepurchasing = User::where('email', 'purchasing@gmail.com')->first();
        $duplicatesales = User::where('email', 'sales@gmail.com')->first();
        $duplicatekasirgallery = User::where('email', 'kasirgallery@gmail.com')->first();
        $duplicatekasiroutlet = User::where('email', 'kasiroutlet@gmail.com')->first();
        $duplicatefinance = User::where('email', 'finance@gmail.com')->first();
        $duplicateauditor = User::where('email', 'auditor@gmail.com')->first();
        if($duplicate){
            $duplicate->delete();
        }
        if($duplicateadmingallery){
            $duplicateadmingallery->delete();
        }
        if($duplicatepurchasing){
            $duplicatepurchasing->delete();
        }
        if($duplicatesales){
            $duplicatesales->delete();
        }
        if($duplicatekasirgallery){
            $duplicatekasirgallery->delete();
        }
        if($duplicatekasiroutlet){
            $duplicatekasiroutlet->delete();
        }
        if($duplicatefinance){
            $duplicatefinance->delete();
        }
        if($duplicateauditor){
            $duplicateauditor->delete();
        }
        $user = User::create([
            'name' => 'SuperAdmin', 
            'email' => 'superadmin@gmail.com',
            'username' => 'superadmin',
            'password' => bcrypt('superadmin123')
        ]);

        $useradmingallery = User::create([
            'name' => 'AdminGallery', 
            'email' => 'admingallery@gmail.com',
            'username' => 'admingallery',
            'password' => bcrypt('admingallery123')
        ]);
        
        $userpurchasing = User::create([
            'name' => 'Purchasing', 
            'email' => 'purchasing@gmail.com',
            'username' => 'purchasing',
            'password' => bcrypt('purchasing123')
        ]);

        $usersales = User::create([
            'name' => 'Sales', 
            'email' => 'sales@gmail.com',
            'username' => 'sales',
            'password' => bcrypt('sales123')
        ]);

        $userkasirgallery = User::create([
            'name' => 'KasirGallery', 
            'email' => 'kasirgallery@gmail.com',
            'username' => 'kasirgallery',
            'password' => bcrypt('kasirgallery123')
        ]);

        $userkasiroutlet = User::create([
            'name' => 'KasirOutlet', 
            'email' => 'kasiroutlet@gmail.com',
            'username' => 'kasiroutlet',
            'password' => bcrypt('kasiroutlet123')
        ]);

        $userfinance = User::create([
            'name' => 'Finance', 
            'email' => 'finance@gmail.com',
            'username' => 'finance',
            'password' => bcrypt('finance123')
        ]);

        $userauditor = User::create([
            'name' => 'Auditor', 
            'email' => 'auditor@gmail.com',
            'username' => 'auditor',
            'password' => bcrypt('auditor123')
        ]);

        $role = Role::create(['name' => 'SuperAdmin']);
        $roleadmingallery = Role::create(['name' => 'AdminGallery']);
        $rolepurchasing = Role::create(['name' => 'Purchasing']);
        $rolesales = Role::create(['name' => 'Sales']);
        $rolekasirgallery = Role::create(['name' => 'KasirGallery']);
        $rolekasiroutlet = Role::create(['name' => 'KasirOutlet']);
        $rolefinance = Role::create(['name' => 'Finance']);
        $roleauditor = Role::create(['name' => 'Auditor']);
     
        $permissions = Permission::pluck('id','id')->all();
        $permissionsadmingallery = Permission::whereIn('id',[1, 3, 4])->pluck('id')->all();
        $permissionspurchasing = Permission::whereIn('id',[1, 3, 4])->pluck('id')->all();
        $permissionssales = Permission::whereIn('id',[1, 3, 4])->pluck('id')->all();
        $permissionskasirgallery = Permission::whereIn('id',[1, 3, 4])->pluck('id')->all();
        $permissionskasiroutlet = Permission::whereIn('id',[1, 3, 4])->pluck('id')->all();
        $permissionsfinance = Permission::whereIn('id',[1, 3, 4])->pluck('id')->all();
        $permissionsauditor = Permission::whereIn('id',[1, 3, 4])->pluck('id')->all();
   
        $role->syncPermissions($permissions);
        $roleadmingallery->syncPermissions($permissionsadmingallery);
        $rolepurchasing->syncPermissions($permissionspurchasing);
        $rolesales->syncPermissions($permissionssales);
        $rolekasirgallery->syncPermissions($permissionskasirgallery);
        $rolekasiroutlet->syncPermissions($permissionskasiroutlet);
        $rolefinance->syncPermissions($permissionsfinance);
        $roleauditor->syncPermissions($permissionsauditor);
     
        $user->assignRole([$role->id]);
        $useradmingallery->assignRole([$roleadmingallery->id]);
        $userpurchasing->assignRole([$rolepurchasing->id]);
        $usersales->assignRole([$rolesales->id]);
        $userkasirgallery->assignRole([$rolekasirgallery->id]);
        $userkasiroutlet->assignRole([$rolekasiroutlet->id]);
        $userfinance->assignRole([$rolefinance->id]);
        $userauditor->assignRole([$roleauditor->id]);
    }
}
