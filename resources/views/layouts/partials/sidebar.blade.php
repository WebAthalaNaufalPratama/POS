<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                @php
                    $roles = Auth::user()->roles()->get();
                    $user = Auth::user();
                    $lokasi = \App\Models\Karyawan::where('user_id', $user->id)->first();
                    if($user->hasRole(['SuperAdmin'])) {
                        $rolePermissions = \Spatie\Permission\Models\Permission::pluck('name')->toArray();
                    } else {
                        $rolePermissions = [];
                        if (!$roles->isEmpty()) {
                            $rolePermissions = $roles->flatMap->permissions->pluck('name')->toArray();
                        }
                    }
                @endphp
                <li class="submenu">
                    <a href="javascript:void(0);"><i data-feather="box"></i><span> Dashboard</span> <span class="menu-arrow"></span></a>
                    <ul>
                        <li><a href="{{ route('dashboard.index') }}" class="{{ request()->is('dashboard*') ? 'active' : '' }}">Dashboard</a></li>
                    </ul>
                </li>

                @if($user->hasRole(['SuperAdmin', 'Finance']))
                <li class="submenu">
                    <a href="javascript:void(0);"><i data-feather="box"></i><span> Master</span> <span class="menu-arrow"></span></a>
                    <ul>
                        <li><a href="{{ route('tipe_produk.index') }}" class="{{ request()->is('tipe_produks*') ? 'active' : '' }}">Tipe Produk</a></li>
                        <li><a href="{{ route('produks.index') }}" class="{{ request()->is('produks*') ? 'active' : '' }}">Produk</a></li>
                        <li><a href="{{ route('kondisi.index') }}" class="{{ request()->is('kondisi*') ? 'active' : '' }}">Kondisi</a></li>
                        <li><a href="{{ route('tipe_lokasi.index') }}" class="{{ request()->is('tipe_lokasi*') ? 'active' : ''}}">Tipe Lokasi</a></li>
                        <li><a href="{{ route('operasional.index') }}" class="{{ request()->is('operasional*') ? 'active' : ''}}">Operasional</a></li>
                        <li><a href="{{ route('lokasi.index') }}" class="{{ request()->is('lokasi*') ? 'active' : '' }}">Lokasi</a></li>
                        <li><a href="{{ route('supplier.index') }}" class="{{ request()->is('supplier*') ? 'active' : '' }}">Supplier</a></li>
                        <li><a href="{{ route('ongkir.index') }}" class="{{ request()->is('ongkir*') ? 'active' : '' }}">Ongkir</a></li>
                        <li><a href="{{ route('customer.index') }}" class="{{ request()->is('customer*') ? 'active' : '' }}">Customer</a></li>
                        <li><a href="{{ route('jabatan.index') }}" class="{{ request()->is('jabatan*') ? 'active' : '' }}">Jabatan</a></li>
                        <li><a href="{{ route('karyawan.index') }}" class="{{ request()->is('karyawan*') ? 'active' : '' }}">Karyawan</a></li>
                        <li><a href="{{ route('rekening.index') }}" class="{{ request()->is('rekening*') ? 'active' : '' }}">Rekening</a></li>
                        <li><a href="{{ route('akun.index') }}" class="{{ request()->is('akun*') ? 'active' : '' }}">Akun</a></li>
                        <li><a href="{{ route('aset.index') }}" class="{{ request()->is('aset*') ? 'active' : '' }}">Aset</a></li>
                        <li><a href="{{ route('promo.index') }}" class="{{ request()->is('promo*') ? 'active' : '' }}">Promo</a></li>
                    </ul>
                </li>
                @endif
                @if(in_array('tradisional.index', $rolePermissions) && in_array('gift.index', $rolePermissions))
                <li class="submenu">
                    <a href="javascript:void(0);"><img src="/assets/img/icons/product.svg" alt="img"><span> Produk Jual</span> <span class="menu-arrow"></span></a>
                    <ul>
                        <li><a href="{{ route('tradisional.index') }}" class="{{ request()->is('tradisional*') ? 'active' : '' }}">Tradisional</a></li>
                        <li><a href="{{ route('gift.index') }}" class="{{ request()->is('gift*') ? 'active' : '' }}">Gift</a></li>
                    </ul>
                </li>
                @endif
                @if(in_array('kontrak.index', $rolePermissions))
                <li class="submenu">
                    <a href="javascript:void(0);"><i data-feather="file-text"></i><span> Sewa</span> <span class="menu-arrow"></span></a>
                    <ul>
                        <li><a href="{{ route('kontrak.index') }}" class="{{ request()->is('kontrak*') ? 'active' : '' }}">Kontrak</a></li>
                        <li><a href="{{ route('form.index', ['jenis_rangkaian' => 'Sewa']) }}" class="{{ request()->is('form*') ? 'active' : '' }}">Perangkai</a></li>
                        <li><a href="{{ route('do_sewa.index') }}" class="{{ request()->is('do_sewa*') ? 'active' : '' }}">Delivery Order</a></li>
                        <li><a href="{{ route('kembali_sewa.index') }}" class="{{ request()->is('kembali_sewa*') ? 'active' : '' }}">Barang Kembali</a></li>
                        <li><a href="{{ route('invoice_sewa.index') }}" class="{{ request()->is('invoice_sewa*') ? 'active' : '' }}">Invoice</a></li>
                        <li><a href="{{ route('pembayaran_sewa.index') }}" class="{{ request()->is('pembayaran_sewa*') ? 'active' : '' }}">Pembayaran</a></li>
                    </ul>
                </li>
                @endif
                <li class="submenu">
                    @if((in_array('penjualan.index', $rolePermissions) && (isset($lokasi->lokasi) && $lokasi->lokasi->tipe_lokasi != 2)))
                    <a href="javascript:void(0);"><img src="/assets/img/icons/product.svg" alt="img"><span> Penjualan Galery</span> <span class="menu-arrow"></span></a>
                    <ul>
                        <li><a href="{{ route('penjualan.index') }}" class="{{ request()->is('penjualan*') ? 'active' : '' }}">Invoice</a></li>
                        <li><a href="{{ route('formpenjualan.index', ['jenis_rangkaian' => 'Penjualan']) }}" class="{{ request()->is('formpenjualan*') ? 'active' : '' }}">Perangkai</a></li>
                        <li><a href="{{ route('pembayaran.index') }}" class="{{ request()->is('pembayaran*') && !request()->is('pembayaran_sewa*') ? 'active' : '' }}">Pembayaran</a></li>
                        <li><a href="{{ route('dopenjualan.index') }}" class="{{ request()->is('dopenjualan*') ? 'active' : '' }}">Delivery Order</a></li>
                        <li><a href="{{ route('returpenjualan.index') }}" class="{{ request()->is('retur*') ? 'active' : '' }}">Retur</a></li>
                        <!-- <li><a href="{{ route('gift.index') }}" class="{{ request()->is('gift*') ? 'active' : '' }}">Gift</a></li> -->
                    </ul>
                    @endif
                    @if(in_array('penjualan.index', $rolePermissions) && isset($lokasi->lokasi) && $lokasi->lokasi->tipe_lokasi != 1)
                    <a href="javascript:void(0);"><img src="/assets/img/icons/product.svg" alt="img"><span> Penjualan Outlet</span> <span class="menu-arrow"></span></a>
                    <ul>
                        <li><a href="{{ route('penjualan.index') }}" class="{{ request()->is('penjualan*') ? 'active' : '' }}">Invoice</a></li>
                        <!-- <li><a href="{{ route('formpenjualan.index', ['jenis_rangkaian' => 'Penjualan']) }}" class="{{ request()->is('formpenjualan*') ? 'active' : '' }}">Perangkai</a></li> -->
                        <li><a href="{{ route('pembayaran.index') }}" class="{{ request()->is('pembayaran*') && !request()->is('pembayaran_sewa*') ? 'active' : '' }}">Pembayaran</a></li>
                        <li><a href="{{ route('dopenjualan.index') }}" class="{{ request()->is('dopenjualan*') ? 'active' : '' }}">Delivery Order</a></li>
                        <li><a href="{{ route('returpenjualan.index') }}" class="{{ request()->is('retur*') ? 'active' : '' }}">Retur</a></li>
                        <!-- <li><a href="{{ route('gift.index') }}" class="{{ request()->is('gift*') ? 'active' : '' }}">Gift</a></li> -->
                    </ul>
                    @endif
                </li>
                @if(in_array('pembelian.index', $rolePermissions))
                <li class="submenu">
                    <a href="javascript:void(0);"><img src="/assets/img/icons/dollar-square.svg" alt="img"><span> Pembelian</span> <span class="menu-arrow"></span></a>
                    <ul>
                        
                        <li><a href="{{ route('pembelian.index') }}" class="{{ request()->is('purchase/pembelian*') ? 'active' : '' }}">Purchase Order</a></li>
                        @if(in_array('invoicebeli.index', $rolePermissions))
                        <li><a href="{{ route('invoicebeli.index') }}" class="{{ request()->is('purchase/invoice*') ? 'active' : '' }}">Invoice Pembelian</a></li>
                        @endif
                        @if(in_array('returbeli.index', $rolePermissions))
                        <li><a href="{{ route('returbeli.index') }}" class="{{ request()->is('purchase/retur*') ? 'active' : '' }}">Retur Pembelian</a></li>
                        @endif
                        @if(in_array('pembayaranbeli.index', $rolePermissions))
                        <li><a href="{{ route('pembayaranbeli.index') }}" class="{{ request()->is('purchase/pembayaran*') ? 'active' : '' }}">Pembayaran Pembelian</a></li>
                        @endif
                    </ul>
                </li>
                @endif
                <li class="submenu">

                    <a href="javascript:void(0);"><img src="/assets/img/icons/quotation1.svg" alt="img"><span> Mutasi</span> <span class="menu-arrow"></span></a>
                    <ul>
                        @if(in_array('mutasigalery.index', $rolePermissions))
                            <li><a href="{{ route('mutasigalery.index') }}" class="{{ request()->is('mutasiGO*')  ? 'active' : '' }}">Mutasi Galery ke Outlet</a></li>
                        @endif
                        @if(in_array('mutasioutlet.index', $rolePermissions))
                            <li><a href="{{ route('mutasioutlet.index') }}" class="{{ request()->is('mutasiOG*') ? 'active' : '' }}">Mutasi Outlet ke Galery</a></li>
                        @endif
                        @if(in_array('mutasighgalery.index', $rolePermissions))
                            <li><a href="{{ route('mutasighgalery.index') }}" class="{{ request()->is('mutasiGG*') ? 'active' : '' }}">Mutasi GH ke Galery</a></li>
                        @endif
                        @if(in_array('mutasigalerygalery.index', $rolePermissions))
                            <li><a href="{{ route('mutasigalerygalery.index') }}" class="{{ request()->is('mutasiGAG*') ? 'active' : '' }}">Mutasi Galery ke Galery</a></li>
                            {{-- <li><a href="#" class="">Mutasi Inden ke GH</a></li>
                            <li><a href="#" class="">Mutasi Inden Ke Galery</a></li>
                            <li><a href="#" class="">Mutasi Galery Ke Inden</a></li> --}}
                            <li><a href="{{ route('mutasiindengh.index') }}" class="{{ request()->is('mutasiIG*') ? 'active' : '' }}">Mutasi Inden Ke Galery/GreenHouse</a></li>
                            <li><a href="#" class="">Mutasi Galery/Greenhouse Ke Inden</a></li>
                        @endif
                    </ul>
                </li>
                
                
                <li class="submenu">
                    <a href="javascript:void(0);"><i class="fa fa-archive" data-bs-toggle="tooltip" title="" data-bs-original-title="fa fa-archive" aria-label="fa fa-archive"></i><span> Inventory</span> <span class="menu-arrow"></span></a>
                    <ul>
                        @if(in_array('inven_galeri.index', $rolePermissions))
                        <li><a href="{{ route('inven_galeri.index') }}" class="{{ request()->is('inven_galeri*') ? 'active' : '' }}">Gallery</a></li>
                        @endif
                        @if((in_array('inven_outlet.index', $rolePermissions)))
                        <li><a href="{{ route('inven_outlet.index')}}" class="{{ request()->is('inven_outlet*') ? 'active' : '' }}">Outlet</a></li>
                        @endif
                        @if(in_array('inven_greenhouse.index', $rolePermissions))
                        <li><a href="{{ route('inven_greenhouse.index')}}" class="{{ request()->is('inven_greenhouse*') ? 'active' : '' }}">GreenHouse</a></li>
                        @endif
                        @if(in_array('inven_inden.index', $rolePermissions))
                        <li><a href="{{ route('inven_inden.index')}}" class="{{ request()->is('inven_inden*') ? 'active' : '' }}">Inden</a></li>
                        @endif
                        {{-- <li><a href="#" class="">Inden</a></li> --}}
                    </ul>
                </li>
                @if(in_array('kas_galery.index', $rolePermissions))
                <li class="submenu">
                    <a href="javascript:void(0);"><img src="/assets/img/icons/wallet1.svg" alt="img"><span> Kas</span> <span class="menu-arrow"></span></a>
                    <ul>
                        <li><a href="{{ route('kas_pusat.index') }}" class="{{ request()->is('kas_pusat*') ? 'active' : '' }}">Pusat</a></li>
                        <li><a href="{{ route('kas_gallery.index')}}" class="{{ request()->is('kas_gallery*') ? 'active' : '' }}">Gallery</a></li>
                    </ul>
                </li>
                @endif
                @if($user->hasRole(['SuperAdmin', 'Finance']))
                <li class="submenu">
                    <a href="javascript:void(0);"><img src="/assets/img/icons/users1.svg" alt="img"><span> Users</span> <span class="menu-arrow"></span></a>
                    <ul>
                        <li><a href="/roles" class="{{ request()->is('roles*') ? 'active' : '' }}">Roles </a></li>
                        <li><a href="/permissions" class="{{ request()->is('permissions*') ? 'active' : '' }}">Permissions </a></li>
                        <li><a href="/posts" class="{{ request()->is('posts*') ? 'active' : '' }}">Log Activity </a></li>
                        <li><a href="{{ route('users.index') }}" class="{{ request()->is('users*') ? 'active' : '' }}">User </a></li>
                    </ul>
                </li>
                @endif
            </ul>
        </div>
        <img src="/assets/img/bunga.png" alt="Gambar Bunga" id="bawah" style="width:100%">
    </div>
</div>