<?php

namespace Database\Seeders;

use App\Models\Lokasi;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class InitialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // create role
        $role = Role::firstOrCreate(['name' => 'SuperAdmin']);
        $roleadmingallery = Role::firstOrCreate(['name' => 'AdminGallery']);
        $rolepurchasing = Role::firstOrCreate(['name' => 'Purchasing']);
        $rolesales = Role::firstOrCreate(['name' => 'Sales']);
        $rolekasirgallery = Role::firstOrCreate(['name' => 'KasirGallery']);
        $rolekasiroutlet = Role::firstOrCreate(['name' => 'KasirOutlet']);
        $rolefinance = Role::firstOrCreate(['name' => 'Finance']);
        $roleauditor = Role::firstOrCreate(['name' => 'Auditor']);
        $rolesalmen = Role::firstOrCreate(['name' => 'SalesManager']);
        
        // create user default
        $user = User::firstOrCreate(
            ['email' => 'superadmin@gmail.com'],
            [
                'name' => 'SuperAdmin', 
                'username' => 'superadmin',
                'password' => '123'
            ]
        );
        $useradmingallery = User::firstOrCreate(
            ['email' => 'admingallery@gmail.com'],
            [
                'name' => 'AdminGallery', 
                'username' => 'admingallery',
                'password' => '123'
            ]
        );
        $userpurchasing = User::firstOrCreate(
            ['email' => 'purchasing@gmail.com'],
            [
                'name' => 'Purchasing', 
                'username' => 'purchasing',
                'password' => '123'
            ]
        );
        $usersales = User::firstOrCreate(
            ['email' => 'sales@gmail.com'],
            [
                'name' => 'Sales', 
                'username' => 'sales',
                'password' => '123'
            ]
        );
        $userkasirgallery = User::firstOrCreate(
            ['email' => 'kasirgallery@gmail.com'],
            [
                'name' => 'KasirGallery', 
                'username' => 'kasirgallery',
                'password' => '123'
            ]
        );
        $userkasiroutlet = User::firstOrCreate(
            ['email' => 'kasiroutlet@gmail.com'],
            [
                'name' => 'KasirOutlet', 
                'username' => 'kasiroutlet',
                'password' => '123'
            ]
        );
        $userfinance = User::firstOrCreate(
            ['email' => 'finance@gmail.com'],
            [
                'name' => 'Finance', 
                'username' => 'finance',
                'password' => '123'
            ]
        );
        $userauditor = User::firstOrCreate(
            ['email' => 'auditor@gmail.com'],
            [
                'name' => 'Auditor', 
                'username' => 'auditor',
                'password' => '123'
            ]
        );
        $usersalesmanger = User::firstOrCreate(
            ['email' => 'salesmanager@gmail.com'],
            [
                'name' => 'Salesmanager', 
                'username' => 'salmen',
                'password' => '123'
            ]
);

        // permission
        // basic permission
            $basiPermissionList = [
                'home.index',
                'login',
                'login.perform',
                'getBulan',
                'getKode',
                'getKategori',
                'logout',
                'checkPromo',
                'getPromo',
                'getProdukTerjual',
                'addKomponen',
                'getProdukDo',
                'rekeningPerLokasi',
                'dashboard.index',
                'getTopProduk',
                'getTopMinusProduk',
                'getTopSales',
                'getLoyalty',
            ];

        // super admin
            $permissionssuperadmin = Permission::pluck('id');

        // admingallery
            $admingalleryPermissionList = [
                'kontrak',
                'do_sewa',
                'kembali_sewa',
                'invoice_sewa',
                'pembayaran_sewa',
                'kas_gallery',
                'inven_galeri',
                'form',
                'tradisional',
                'gift',
                'customer',
                'karyawan',
                'rekening',
                'akun',
                'aset',
                'promo',
                'penjualan',
                'dopenjualan',
                'pembayaran',
                'komponenpenjualan.store',
                'komponenmutasi.store',
                'komponenretur.store',
                'pdfinvoicepenjualan.generate',
                'formpenjualan',
                'returpenjualan',
                'mutasigalery',
                'formmutasigalery.cetak',
                'mutasioutlet',
                'pemakaian_sendiri',
                'pembelian.index',
                'pembelian.edit',
                'pembelian.show',
                'pembelian.update',
            ];
            $admingalleryPermissionList = array_merge($basiPermissionList, $admingalleryPermissionList);
            $query = Permission::query();
            foreach ($admingalleryPermissionList as $prefix) {
                $query->orWhere('name', 'like', $prefix . '%');
            }
            $permissionsadmingallery = $query->pluck('id');

        // purchasing
            $purchasingPermissionList = [
                'pembelian',
                'invoicebeli',
                'invoicebiasa',
                'invoicepo',
                'editinvoice',
                'invoice',
                'gambarpo',
                'invoicepurchase',
                'invoice_purchase',
                'pembelianinden',
                'inden',
                'returbeli',
                'pembayaranbeli',
                'retur_purchase.update',
                'supplier',
                'mutasighgalery',
                'getProductsByLokasi',
                'inven_greenhouse',
                'mutasiindengh',
                'create.retur',
                'returninden.index',
                'show.returinden',
                'inven_inden',
                'inven_gudang',
                'mutasigalerygalery'
            ];
            $purchasingPermissionList = array_merge($basiPermissionList, $purchasingPermissionList);
            $query = Permission::query();
            foreach ($purchasingPermissionList as $prefix) {
                $query->orWhere('name', 'like', $prefix . '%');
            }
            $permissionspurchasing = $query->pluck('id');

        // kasir gallery
            $kasrigalleryPermissionList = [
                'penjualan',
                'customer',
                'dopenjualan',
                'pembayaran',
                'formpenjualan',
                'returpenjualan',
                'komponenpenjualan.store',
                'komponenmutasi.store',
                'komponenretur.store',
                'pdfinvoicepenjualan.generate',
                'pdfdopenjualan.generate',
                'auditpenjualan',
                'auditdopenjualan',
                'auditretur',
                'auditmutasigalery',
                'auditmutasioutlet',
                'formpenjualan.cetak',
                'formmutasigalery.cetak'
            ];
            $kasrigalleryPermissionList = array_merge($basiPermissionList, $kasrigalleryPermissionList);
            $query = Permission::query();
            foreach ($kasrigalleryPermissionList as $prefix) {
                $query->orWhere('name', 'like', $prefix . '%');
            }
            $permissionskasirgallery = $query->pluck('id');

        // kasir outlet
            $kasiroutletPermissionList = [
                'penjualan',
                'customer',
                'dopenjualan',
                'komponenretur.store',
                'pembayaran',
                'returpenjualan',
                'inven_outlet',
                'mutasigalery.index',
                'mutasigalery.acc',
                'mutasigalery.show',
                'mutasigalery.view',
                'mutasioutlet',
                'pdfinvoicepenjualan.generate',
                'pdfdopenjualan.generate',
                'auditpenjualan',
                'auditdopenjualan',
                'auditretur',
                'auditmutasigalery',
                'auditmutasioutlet',
                'formpenjualan.cetak',
                'formmutasigalery.cetak'
            ];
            $kasiroutletPermissionList = array_merge($basiPermissionList, $kasiroutletPermissionList);
            $query = Permission::query();
            foreach ($kasiroutletPermissionList as $prefix) {
                $query->orWhere('name', 'like', $prefix . '%');
            }
            $permissionskasiroutlet = $query->pluck('id');

        // finance
            $financePermissionList = [
                'karyawan',
                'rekening',
                'akun',
                'aset',
                'promo',
                'penjualan.index',
                'penjualan.show',
                'penjualan.edit',
                'penjualan.update',
                'komponenpenjualan.store',
                'komponenmutasi.store',
                'komponenretur.store',
                'pdfinvoicepenjualan.generate',
                'auditpenjualan.edit',
                'auditpenjualan.show',
                'penjualan.view',
                'dopenjualan.index',
                'dopenjualan.show',
                'pdfdopenjualan.generate',
                'auditdopenjualan.edit',
                'auditdopenjualan.update',
                'pembayaran.index',
                'pembayaran.show',
                'pembayaran.edit',
                'pembayaran.update',
                'form.index',
                'form.show',
                'form.edit',
                'form.update',
                'form.cetak',
                'formpenjualan.index',
                'formpenjualan.show',
                'formpenjualan.update',
                'formpenjualan.cetak',
                'returpenjualan.index',
                'returpenjualan.show',
                'auditretur.edit',
                'auditretur.update',
                'returpenjualan.view',
                'pembelian.index',
                'pembelian.show',
                'pembelian.editaudit',
                'pembelian.updateaudit',
                'invoicebeli.index',
                'editinvoice.edit',
                'editinvoice.update',
                'invoice.edit',
                'invoice.update',
                'invoice.show',
                'gambapo.update',
                'returbeli.index',
                'returfinance.update',
                'bayarrefund.store',
                'refundinden.store',
                'returbeli.show',
                'returbeli.edit',
                'kontrak.index',
                'kontrak.show',
                'kontrak.edit',
                'kontrak.update',
                'kontrak.pdfKontrak',
                'kontrak.excelPergantian',
                'do_sewa.index',
                'do_sewa.show',
                'do_sewa.edit',
                'do_sewa.update',
                'kembali_sewa.index',
                'kembali_sewa.show',
                'kembali_sewa.edit',
                'kembali_sewa.update',
                'invoice_sewa.index',
                'invoice_sewa.show',
                'invoice_sewa.edit',
                'invoice_sewa.update',
                'invoice_sewa.cetak',
                'pembayaran_sewa.index',
                'pembayaran_sewa.show',
                'pembayaran_sewa.edit',
                'pembayaran_sewa.update',
                'mutasigaleri.index',
                'mutasigaleri.show',
                'auditmutasigaleri.edit',
                'auditmutasigaleri.update',
                'mutasigaleri.view',
                'formmutasigalery.cetak',
                'mutasioutlet.index',
                'mutasioutlet.show',
                'auditmutasioutlet.edit',
                'auditmutasioutlet.update',
                'mutasioutlet.view',
                'mutasighgalery.index',
                'mutasighgalery.show',
                'auditmutasighgalery.edit',
                'auditmutasighgalery.update',
                'mutasighgalery.view',
                'getProductByLokasi',
                'mutasiindengh.index',
                'mutasiindengh.edit',
                'mutasiindengh.show',
                'mutasiindengh.update',
                'returinden.index',
                'show.returinden',
                'inven_inden.index',
                'inven_inden.show',
                'inven_inden.edit',
                'inven_inden.update',
                'inven_gudang.index',
                'inven_gudang.show',
                'inven_gudang.edit',
                'inven_gudang.update',
                'mutasigalerygalery.index',
                'mutasigalerygalery.show',
                'auditmutasigalerygalery.edit',
                'auditmutasigalerygalery.update',
                'mutasigalerygalery.view',
                'kas_pusat',
                'kas_gallery.index',
                'kas_gallery.show',
                'kas_gallery.edit',
                'kas_gallery.update',
                'pemakaian_sendiri.index',
                'pemakaian_sendiri.show',
                'pemakaian_sendiri.edit',
                'pemakaian_sendiri.update',
                'auditor.update',
                'bukakunci.store',
            ];
            $financePermissionList = array_merge($basiPermissionList, $financePermissionList);
            $query = Permission::query();
            foreach ($financePermissionList as $prefix) {
                $query->orWhere('name', 'like', $prefix . '%');
            }
            $permissionsfinance = $query->pluck('id');

        // auditor
            $auditorPermissionList = [
                'penjualan.index',
                'penjualan.show',
                'penjualan.edit',
                'penjualan.update',
                'komponenpenjualan.store',
                'komponenmutasi.store',
                'komponenretur.store',
                'pdfinvoicepenjualan.generate',
                'auditpenjualan.edit',
                'auditpenjualan.show',
                'penjualan.view',
                'dopenjualan.index',
                'dopenjualan.show',
                'pdfdopenjualan.generate',
                'auditdopenjualan.edit',
                'auditdopenjualan.update',
                'pembayaran.index',
                'pembayaran.show',
                'pembayaran.edit',
                'pembayaran.update',
                'form.index',
                'form.show',
                'form.edit',
                'form.update',
                'form.cetak',
                'formpenjualan.index',
                'formpenjualan.show',
                'formpenjualan.update',
                'formpenjualan.cetak',
                'returpenjualan.index',
                'returpenjualan.show',
                'auditretur.edit',
                'auditretur.update',
                'returpenjualan.view',
                'pembelian.index',
                'pembelian.show',
                'pembelian.editaudit',
                'pembelian.updateaudit',
                'invoicebeli.index',
                'editinvoice.edit',
                'editinvoice.update',
                'invoice.edit',
                'invoice.update',
                'invoice.show',
                'gambapo.update',
                'returbeli.index',
                'returfinance.update',
                'bayarrefund.store',
                'refundinden.store',
                'returbeli.show',
                'returbeli.edit',
                'kontrak.index',
                'kontrak.show',
                'kontrak.edit',
                'kontrak.update',
                'kontrak.pdfKontrak',
                'kontrak.excelPergantian',
                'do_sewa.index',
                'do_sewa.show',
                'do_sewa.edit',
                'do_sewa.update',
                'kembali_sewa.index',
                'kembali_sewa.show',
                'kembali_sewa.edit',
                'kembali_sewa.update',
                'invoice_sewa.index',
                'invoice_sewa.show',
                'invoice_sewa.edit',
                'invoice_sewa.update',
                'invoice_sewa.cetak',
                'pembayaran_sewa.index',
                'pembayaran_sewa.show',
                'pembayaran_sewa.edit',
                'pembayaran_sewa.update',
                'mutasigaleri.index',
                'mutasigaleri.show',
                'auditmutasigaleri.edit',
                'auditmutasigaleri.update',
                'mutasigaleri.view',
                'formmutasigalery.cetak',
                'mutasioutlet.index',
                'mutasioutlet.show',
                'auditmutasioutlet.edit',
                'auditmutasioutlet.update',
                'mutasioutlet.view',
                'mutasighgalery.index',
                'mutasighgalery.show',
                'auditmutasighgalery.edit',
                'auditmutasighgalery.update',
                'mutasighgalery.view',
                'getProductByLokasi',
                'mutasiindengh.index',
                'mutasiindengh.edit',
                'mutasiindengh.show',
                'mutasiindengh.update',
                'returinden.index',
                'show.returinden',
                'inven_inden.index',
                'inven_inden.show',
                'inven_inden.edit',
                'inven_inden.update',
                'inven_gudang.index',
                'inven_gudang.show',
                'inven_gudang.edit',
                'inven_gudang.update',
                'mutasigalerygalery.index',
                'mutasigalerygalery.show',
                'auditmutasigalerygalery.edit',
                'auditmutasigalerygalery.update',
                'mutasigalerygalery.view',
                'kas_gallery.index',
                'kas_pusat.index',
                'pemakaian_sendiri.index',
                'auditor.update',
            ];
            $auditorPermissionList = array_merge($basiPermissionList, $auditorPermissionList);
            $query = Permission::query();
            foreach ($auditorPermissionList as $prefix) {
                $query->orWhere('name', 'like', $prefix . '%');
            }
            $permissionsauditor = $query->pluck('id');

        // sales
            $permissionssales = Permission::whereIn('id',[1, 3, 4])->pluck('id')->all();

        // sync permission to role
        $role->syncPermissions($permissionssuperadmin);
        $roleadmingallery->syncPermissions($permissionsadmingallery);
        $rolepurchasing->syncPermissions($permissionspurchasing);
        $rolesales->syncPermissions($permissionssales);
        $rolekasirgallery->syncPermissions($permissionskasirgallery);
        $rolekasiroutlet->syncPermissions($permissionskasiroutlet);
        $rolefinance->syncPermissions($permissionsfinance);
        $roleauditor->syncPermissions($permissionsauditor);

        // assign role to user default
        $user->assignRole([$role->id]);
        $useradmingallery->assignRole([$roleadmingallery->id]);
        $userpurchasing->assignRole([$rolepurchasing->id]);
        $usersales->assignRole([$rolesales->id]);
        $userkasirgallery->assignRole([$rolekasirgallery->id]);
        $userkasiroutlet->assignRole([$rolekasiroutlet->id]);
        $userfinance->assignRole([$rolefinance->id]);
        $userauditor->assignRole([$roleauditor->id]);

        // create lokasi
        Lokasi::firstOrCreate(
            ['nama' => 'Galery Semarang'],
            [
                'tipe_lokasi' => 1,
                'operasional_id' => 2,
                'alamat' => 'semarang',
                'pic' => 'yvon'
            ]
        );
        Lokasi::firstOrCreate(
            ['nama' => 'Galery Surabaya'],
            [
                'tipe_lokasi' => 1,
                'operasional_id' => 2,
                'alamat' => 'Surabaya',
                'pic' => 'sby'
            ]
        );
        Lokasi::firstOrCreate(
            ['nama' => 'Galery Yogyakarta'],
            [
                'tipe_lokasi' => 1,
                'operasional_id' => 2,
                'alamat' => 'Yogyakarta',
                'pic' => 'ygy'
            ]
        );
        Lokasi::firstOrCreate(
            ['nama' => 'Outlet Semarang'],
            [
                'tipe_lokasi' => 2,
                'operasional_id' => 2,
                'alamat' => 'semarang',
                'pic' => 'dian'
            ]
        );
    }
}
