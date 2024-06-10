<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Karyawan;

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
            'password' => 'superadmin123'
        ]);

        $useradmingallery = User::create([
            'name' => 'AdminGallery', 
            'email' => 'admingallery@gmail.com',
            'username' => 'admingallery',
            'password' => 'admingallery123'
        ]);
        
        $userpurchasing = User::create([
            'name' => 'Purchasing', 
            'email' => 'purchasing@gmail.com',
            'username' => 'purchasing',
            'password' => 'purchasing123'
        ]);

        $usersales = User::create([
            'name' => 'Sales', 
            'email' => 'sales@gmail.com',
            'username' => 'sales',
            'password' => 'sales123'
        ]);

        $userkasirgallery = User::create([
            'name' => 'KasirGallery', 
            'email' => 'kasirgallery@gmail.com',
            'username' => 'kasirgallery',
            'password' => 'kasirgallery123'
        ]);

        $userkasiroutlet = User::create([
            'name' => 'KasirOutlet', 
            'email' => 'kasiroutlet@gmail.com',
            'username' => 'kasiroutlet',
            'password' => 'kasiroutlet123'
        ]);

        $userfinance = User::create([
            'name' => 'Finance', 
            'email' => 'finance@gmail.com',
            'username' => 'finance',
            'password' => 'finance123'
        ]);

        $userauditor = User::create([
            'name' => 'Auditor', 
            'email' => 'auditor@gmail.com',
            'username' => 'auditor',
            'password' => 'auditor123'
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
        $permissionAG = Permission::where(function ($query) {
            $query->where('name', 'like', 'home%')
                  ->orWhere('name', 'like', 'register%')
                  ->orWhere('name', 'like', 'login%')
                  ->orWhere('name', 'like', 'logout%')
                  ->orWhere('name', 'like', 'checkpromo%')
                  ->orWhere('name', 'like', 'getpromo%')
                  ->orWhere('name', 'like', 'getprodukTerjual%')
                  ->orWhere('name', 'like', 'addKomponen%')
                  ->orWhere('name', 'like', 'customer%')
                  ->orWhere('name', 'like', 'promo%')
                  ->orWhere('name', 'like', 'penjualan%')
                  ->orWhere('name', 'like', 'komponenmutasi%')
                  ->orWhere('name', 'like', 'komponenretur%')
                  ->orWhere('name', 'like', 'komponenpenjulan%')
                  ->orWhere('name', 'like', 'dopenjualan%')
                  ->orWhere('name', 'like', 'pembayaran%')
                  ->orWhere('name', 'like', 'formpenjualan%')
                  ->orWhere('name', 'like', 'returpenjualan%')
                  ->orWhere('name', 'like', 'inven_galeri%')
                  ->orWhere('name', 'like', 'mutasigalery%')
                  ->orWhere('name', 'like', 'kas_galery%')
                  ->orWhere('name', 'like', 'produks%')
                  ->orWhere('name', 'like', 'tipe_produk%')
                  ->orWhere('name', 'like', 'kondisi%')
                  ->orWhere('name', 'like', 'ongkir%')
                  ->orWhere('name', 'like', 'tradisional%')
                  ->orWhere('name', 'like', 'gift%')
                  ->orWhere('name', 'like', 'kontrak%')
                  ->orWhere('name', 'like', 'form%')
                  ->orWhere('name', 'like', 'do_sewa%')
                  ->orWhere('name', 'like', 'kembali_sewa%')
                  ->orWhere('name', 'like', 'invoice_sewa%')
                  ->orWhere('name', 'like', 'formmutasi%')
                  ->orWhere('name', 'like', 'kas_galery%')
                  ->orWhere('name', 'like', 'mutasi%')
                  ->orWhere('n ame', 'like', 'pdfinvoicepenjualan%')
                  ->orWhere('name', 'like', 'pdfdopenjualan%')
                  ->orWhere('name', 'like', 'mutasigalerygalery%');
        })->pluck('name')->all();
        
        $permissionsadmingallery = Permission::whereIn('name',$permissionAG)->pluck('id')->all();
        $permissionspurchasing = Permission::whereIn('id',[1, 3, 4])->pluck('id')->all();
        $permissionssales = Permission::whereIn('id',[1, 3, 4])->pluck('id')->all();

        $permissionKG = Permission::where(function ($query) {
            $query->where('name', 'like', 'home%')
                  ->orWhere('name', 'like', 'register%')
                  ->orWhere('name', 'like', 'login%')
                  ->orWhere('name', 'like', 'logout%')
                  ->orWhere('name', 'like', 'checkpromo%')
                  ->orWhere('name', 'like', 'getpromo%')
                  ->orWhere('name', 'like', 'getprodukTerjual%')
                  ->orWhere('name', 'like', 'addKomponen%')
                  ->orWhere('name', 'like', 'customer%')
                  ->orWhere('name', 'like', 'akun%')
                  ->orWhere('name', 'like', 'aset%')
                  ->orWhere('name', 'like', 'promo%')
                  ->orWhere('name', 'like', 'penjualan%')
                  ->orWhere('name', 'like', 'komponenmutasi%')
                  ->orWhere('name', 'like', 'komponenretur%')
                  ->orWhere('name', 'like', 'komponenpenjulan%')
                  ->orWhere('name', 'like', 'dopenjualan%')
                  ->orWhere('name', 'like', 'pembayaran%')
                  ->orWhere('name', 'like', 'formpenjualan%')
                  ->orWhere('name', 'like', 'returpenjualan%')
                  ->orWhere('name', 'like', 'inven_galeri%')
                  ->orWhere('name', 'like', 'mutasigalery%')
                  ->orWhere('name', 'like', 'kas_galery%')
                  ->orWhere('name', 'like', 'produks%')
                  ->orWhere('name', 'like', 'tipe_produk%')
                  ->orWhere('name', 'like', 'kondisi%')
                  ->orWhere('name', 'like', 'ongkir%')
                  ->orWhere('name', 'like', 'tradisional%')
                  ->orWhere('name', 'like', 'gift%')
                  ->orWhere('name', 'like', 'kontrak%')
                  ->orWhere('name', 'like', 'form%')
                  ->orWhere('name', 'like', 'do_sewa%')
                  ->orWhere('name', 'like', 'kembali_sewa%')
                  ->orWhere('name', 'like', 'invoice_sewa%')
                  ->orWhere('name', 'like', 'formmutasi%')
                  ->orWhere('name', 'like', 'kas_galery%')
                  ->orWhere('name', 'like', 'mutasi%')
                  ->orWhere('name', 'like', 'pdfinvoicepenjualan%')
                  ->orWhere('name', 'like', 'pdfdopenjualan%')
                  ->orWhere('name', 'like', 'mutasigalerygalery%');
        })->pluck('name')->all();

        $permissionskasirgallery = Permission::whereIn('name', $permissionKG)->pluck('id')->all();

        $permissionKO = Permission::where(function ($query) {
            $query->where('name', 'like', 'home%')
                  ->orWhere('name', 'like', 'register%')
                  ->orWhere('name', 'like', 'login%')
                  ->orWhere('name', 'like', 'logout%')
                  ->orWhere('name', 'like', 'checkpromo%')
                  ->orWhere('name', 'like', 'getpromo%')
                  ->orWhere('name', 'like', 'getprodukTerjual%')
                  ->orWhere('name', 'like', 'addKomponen%')
                  ->orWhere('name', 'like', 'customer%')
                  ->orWhere('name', 'like', 'penjualan%')
                  ->orWhere('name', 'like', 'komponenmutasi%')
                  ->orWhere('name', 'like', 'komponenretur%')
                  ->orWhere('name', 'like', 'komponenpenjulan%')
                  ->orWhere('name', 'like', 'dopenjualan%')
                  ->orWhere('name', 'like', 'pembayaran%')
                  ->orWhere('name', 'like', 'formpenjualan%')
                  ->orWhere('name', 'like', 'returpenjualan%')
                  ->orWhere('name', 'like', 'inven_outlet%')
                  ->orWhere('name', 'like', 'mutasi%')
                  ->orWhere('name', 'like', 'formmutasi%')
                  ->orWhere('name', 'like', 'mutasioutlet%')
                  ->orWhere('name', 'like', 'pdfinvoicepenjualan%')
                  ->orWhere('name', 'like', 'pdfdopenjualan%')
                  ->orWhere('name', 'like', 'mutasigalery%');
        })->pluck('name')->all();

        $permissionskasiroutlet = Permission::whereIn('name',$permissionKO)->pluck('id')->all();
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


        Karyawan::create([
            'user_id' => $userkasiroutlet->id,
            'nama' => $userkasiroutlet->name,
            'jabatan' => 'kasir',
            'lokasi_id' => 2,
            'handphone' => 0,
            'alamat' => 'semarang'
        ]);

        Karyawan::create([
            'user_id' => $useradmingallery->id,
            'nama' => $useradmingallery->name,
            'jabatan' => 'admin',
            'lokasi_id' => 1,
            'handphone' => 0,
            'alamat' => 'semarang'
        ]);
    }
}
