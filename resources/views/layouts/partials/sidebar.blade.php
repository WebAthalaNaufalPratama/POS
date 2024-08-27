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

                    //penjualan
                    if($user->hasRole(['AdminGallery', 'KasirGallery'])) {
                        $hitungpenjualan = \App\Models\Penjualan::where('no_invoice', 'LIKE', 'INV%')->where('status', 'TUNDA')->count();
                        $dopenjualan = \App\Models\DeliveryOrder::where('no_do', 'LIKE', 'DOP%')->where('status', 'TUNDA')->count();
                        $returpenjualan = \App\Models\ReturPenjualan::where('no_retur', 'LIKE', 'RTP%')->where('status', 'TUNDA')->count();
                    }else if($user->hasRole(['KasirOutlet'])) {
                        $hitungpenjualanoutlet = \App\Models\Penjualan::where('no_invoice', 'LIKE', 'IPO%')->where('status', 'TUNDA')->count();
                        $returpenjualanoutlet = \App\Models\ReturPenjualan::where('no_retur', 'LIKE', 'RTO%')->where('status', 'TUNDA')->count();
                    }else if($user->hasRole(['Finance'])){
                        $hitungpenjualan = \App\Models\Penjualan::where('lokasi_id', $lokasi->lokasi_id)->where('no_invoice', 'LIKE', 'INV%')->where('status', 'DIKONFIRMASI')->whereNull('dibukukan_id')->count();
                        $hitungpenjualanoutlet = \App\Models\Penjualan::where('lokasi_id', $lokasi->lokasi_id)->where('no_invoice', 'LIKE', 'IPO%')->where('status', 'DIKONFIRMASI')->whereNull('dibukukan_id')->count();
                        $dopenjualan = \App\Models\DeliveryOrder::where('no_do', 'LIKE', 'DOP%')->where('lokasi_pengirim', $lokasi->lokasi_id)->where('status', 'DIKONFIRMASI')->whereNull('penyetuju')->count();
                        $returpenjualan = \App\Models\ReturPenjualan::where('no_retur', 'LIKE', 'RTP%')->where('lokasi_id', $lokasi->lokasi_id)->where('status', 'DIKONFIRMASI')->whereNull('pemeriksa')->count();
                        $returpenjualanoutlet = \App\Models\ReturPenjualan::where('no_retur', 'LIKE', 'RTO%')->where('lokasi_id', $lokasi->lokasi_id)->where('status', 'DIKONFIRMASI')->whereNull('pemeriksa')->count();
                    }else if($user->hasRole(['Auditor'])){
                        $hitungpenjualan = \App\Models\Penjualan::where('lokasi_id', $lokasi->lokasi_id)->where('no_invoice', 'LIKE', 'INV%')->where('status', 'DIKONFIRMASI')->whereNull('auditor_id')->count();
                        $hitungpenjualanoutlet = \App\Models\Penjualan::where('lokasi_id', $lokasi->lokasi_id)->where('no_invoice', 'LIKE', 'IPO%')->where('status', 'DIKONFIRMASI')->whereNull('auditor_id')->count();
                        $dopenjualan = \App\Models\DeliveryOrder::where('no_do', 'LIKE', 'DOP%')->where('lokasi_pengirim', $lokasi->lokasi_id)->where('status', 'DIKONFIRMASI')->whereNull('pemeriksa')->count();
                        $returpenjualan = \App\Models\ReturPenjualan::where('no_retur', 'LIKE', 'RTP%')->where('lokasi_id', $lokasi->lokasi_id)->where('status', 'DIKONFIRMASI')->whereNull('pembuku')->count();
                        $returpenjualanoutlet = \App\Models\ReturPenjualan::where('no_retur', 'LIKE', 'RTO%')->where('lokasi_id', $lokasi->lokasi_id)->where('status', 'DIKONFIRMASI')->whereNull('pembuku')->count();
                    }else{
                        $hitungpenjualan = 0;
                        $hitungpenjualanoutlet = 0;
                        $dopenjualan = 0;
                        $returpenjualan = 0;
                        $returpenjualanoutlet = 0;
                    }
                    if($user->hasRole(['KasirOutlet']) || $user->hasRole(['Finance', 'Auditor', 'SuperAdmin']) && $lokasi->lokasi->tipe_lokasi == 2) {
                        $totaljualoutlet = $hitungpenjualanoutlet + $returpenjualanoutlet;
                    }else if($user->hasRole(['AdminGallery', 'KasirGallery']) || $user->hasRole(['Finance', 'Auditor', 'SuperAdmin']) && $lokasi->lokasi->tipe_lokasi == 1) {
                        $totaljual = $hitungpenjualan + $dopenjualan + $returpenjualan;
                    }

                    //mutasi
                    if($user->hasRole(['AdminGallery', 'KasirGallery'])) {
                        $hitungmutasigo = \App\Models\Mutasi::where('no_mutasi', 'LIKE', 'MGO%')->where('status', 'TUNDA')->count();
                        $hitungmutasiog = \App\Models\Mutasi::where('no_mutasi', 'LIKE', 'MOG%')->where('status', 'DIKONFIRMASI')->where('penerima', $lokasi->lokasi_id)->whereNull('penerima_id')->count();
                        $hitungmutasigag = \App\Models\Mutasi::where('no_mutasi', 'LIKE', 'MGA%')->where('status', 'DIKONFIRMASI')->where('penerima', $lokasi->lokasi_id)->whereNull('penerima_id')->count();
                        $hitungmutasigg = \App\Models\Mutasi::where(function ($query){
                                                                $query->where('no_mutasi', 'LIKE', 'MPG%')
                                                                    ->where('no_mutasi', 'LIKE', 'MGG%');    
                                                                })->orwhere('status', 'DIKONFIRMASI')->where('penerima', $lokasi->lokasi_id)->whereNull('penerima_id')->count();
                    }else if($user->hasRole(['Finance'])){
                        $hitungmutasigo = \App\Models\Mutasi::where('no_mutasi', 'LIKE', 'MGO%')->where('status', 'DIKONFIRMASI')->where('penerima', $lokasi->lokasi_id)->whereNull('diperiksa_id')->count();
                        $hitungmutasiog = \App\Models\Mutasi::where('no_mutasi', 'LIKE', 'MOG%')->where('status', 'DIKONFIRMASI')->where('penerima', $lokasi->lokasi_id)->whereNull('diperiksa_id')->count();
                        $hitungmutasigag = \App\Models\Mutasi::where('no_mutasi', 'LIKE', 'MGA%')->where('status', 'DIKONFIRMASI')->where('penerima', $lokasi->lokasi_id)->whereNull('diperiksa_id')->count();
                        $hitungmutasigg = \App\Models\Mutasi::where(function ($query){
                                                                $query->where('no_mutasi', 'LIKE', 'MPG%')
                                                                    ->orwhere('no_mutasi', 'LIKE', 'MGG%');
                                                                })->orwhere('status', 'DIKONFIRMASI')->where('penerima', $lokasi->lokasi_id)->whereNull('diperiksa_id')->count();
                    }else if($user->hasRole(['Auditor'])){
                        $hitungmutasigo = \App\Models\Mutasi::where('no_mutasi', 'LIKE', 'MGO%')->where('status', 'DIKONFIRMASI')->where('penerima', $lokasi->lokasi_id)->whereNull('dibukukan_id')->count();
                        $hitungmutasiog = \App\Models\Mutasi::where('no_mutasi', 'LIKE', 'MOG%')->where('status', 'DIKONFIRMASI')->where('penerima', $lokasi->lokasi_id)->whereNull('dibukukan_id')->count();
                        $hitungmutasigag = \App\Models\Mutasi::where('no_mutasi', 'LIKE', 'MGA%')->where('status', 'DIKONFIRMASI')->where('penerima', $lokasi->lokasi_id)->whereNull('dibukukan_id')->count();
                        $hitungmutasigg = \App\Models\Mutasi::where(function($query) {
                                                                $query->where('no_mutasi', 'LIKE', 'MPG%')
                                                                    ->orwhere('no_mutasi', 'LIKE', 'MGG%');
                                                            })->where('status', 'DIKONFIRMASI')->where('penerima', $lokasi->lokasi_id)->whereNull('dibukukan_id')->count();
                    }else if($user->hasRole(['KasirOutlet'])) {
                        $hitungmutasigo = \App\Models\Mutasi::where('no_mutasi', 'LIKE', 'MGO%')->where('status', 'DIKONFIRMASI')->where('penerima', $lokasi->lokasi_id)->whereNull('penerima_id')->count();
                        $hitungmutasiog = \App\Models\Mutasi::where('no_mutasi', 'LIKE', 'MOG%')->where('status', 'TUNDA')->count();
                        $hitungmutasigg = 0;
                        $hitungmutasigag = 0;
                    }else if($user->hasRole(['Purchasing'])) {
                        $hitungmutasigo = 0;
                        $hitungmutasiog = 0;
                        $hitungmutasigg = \App\Models\Mutasi::where('no_mutasi', 'LIKE', 'MGG%')->where('status', 'TUNDA')->orwhere('no_mutasi', 'LIKE', 'MPG%')->where('status', 'TUNDA')->count();
                        $hitungmutasigag = \App\Models\Mutasi::where('no_mutasi', 'LIKE', 'MGA%')->where('status', 'TUNDA')->count();
                    }else{
                        $hitungmutasigo = 0;
                        $hitungmutasiog = 0;
                        $hitungmutasigg = 0;
                        $hitungmutasigag = 0;
                    }

                    if($user->hasRole(['KasirOutlet']) || $user->hasRole(['Finance', 'Auditor', 'SuperAdmin']) && $lokasi->lokasi->tipe_lokasi == 2) {
                        $totalmutasi = $hitungmutasiog + $hitungmutasigo;
                    }else if($user->hasRole(['AdminGallery', 'KasirGallery', 'Purchasing', 'SuperAdmin']) || $user->hasRole(['Finance', 'Auditor']) && $lokasi->lokasi->tipe_lokasi == 1) {
                        $totalmutasi = $hitungmutasiog + $hitungmutasigo + $hitungmutasigag + $hitungmutasigg;
                    }
                    
                @endphp
                <li class="active">
                    <a href="{{ route('dashboard.index') }}"><img src="/assets/img/icons/dashboard.svg" alt="img"><span> Dashboard</span> </a>
                    
                </li>
                @if(!$user->hasRole(['Pimpinan']))
                @if(
                        in_array('tipe_produk.index', $rolePermissions) ||
                        in_array('produks.index', $rolePermissions) ||
                        in_array('kondisi.index', $rolePermissions) ||
                        in_array('tipe_lokasi.index', $rolePermissions) ||
                        in_array('operasional.index', $rolePermissions) ||
                        in_array('lokasi.index', $rolePermissions) ||
                        in_array('supplier.index', $rolePermissions) ||
                        in_array('ongkir.index', $rolePermissions) ||
                        in_array('customer.index', $rolePermissions) ||
                        in_array('jabatan.index', $rolePermissions) ||
                        in_array('karyawan.index', $rolePermissions) ||
                        in_array('rekening.index', $rolePermissions) ||
                        in_array('akun.index', $rolePermissions) ||
                        in_array('aset.index', $rolePermissions) ||
                        in_array('promo.index', $rolePermissions)
                    )
                <li class="submenu">
                    <a href="javascript:void(0);"><i data-feather="box"></i><span> Master</span> <span class="menu-arrow"></span></a>
                    <ul>
                        @if(in_array('tipe_produk.index', $rolePermissions))
                            <li><a href="{{ route('tipe_produk.index') }}" class="{{ request()->is('tipe_produks*') ? 'active' : '' }}">Tipe Produk</a></li>
                        @endif
                        
                        @if(in_array('produks.index', $rolePermissions))
                            <li><a href="{{ route('produks.index') }}" class="{{ request()->is('produks*') ? 'active' : '' }}">Produk</a></li>
                        @endif
                        
                        @if(in_array('kondisi.index', $rolePermissions))
                            <li><a href="{{ route('kondisi.index') }}" class="{{ request()->is('kondisi*') ? 'active' : '' }}">Kondisi</a></li>
                        @endif
                        
                        @if(in_array('tipe_lokasi.index', $rolePermissions))
                            <li><a href="{{ route('tipe_lokasi.index') }}" class="{{ request()->is('tipe_lokasi*') ? 'active' : '' }}">Tipe Lokasi</a></li>
                        @endif
                        
                        @if(in_array('operasional.index', $rolePermissions))
                            <li><a href="{{ route('operasional.index') }}" class="{{ request()->is('operasional*') ? 'active' : '' }}">Operasional</a></li>
                        @endif
                        
                        @if(in_array('lokasi.index', $rolePermissions))
                            <li><a href="{{ route('lokasi.index') }}" class="{{ request()->is('lokasi*') ? 'active' : '' }}">Lokasi</a></li>
                        @endif
                        
                        @if(in_array('supplier.index', $rolePermissions))
                            <li><a href="{{ route('supplier.index') }}" class="{{ request()->is('supplier*') ? 'active' : '' }}">Supplier</a></li>
                        @endif
                        
                        @if(in_array('ongkir.index', $rolePermissions))
                            <li><a href="{{ route('ongkir.index') }}" class="{{ request()->is('ongkir*') ? 'active' : '' }}">Ongkir</a></li>
                        @endif
                        
                        @if(in_array('customer.index', $rolePermissions))
                            <li><a href="{{ route('customer.index') }}" class="{{ request()->is('customer*') ? 'active' : '' }}">Customer</a></li>
                        @endif
                        
                        @if(in_array('jabatan.index', $rolePermissions))
                            <li><a href="{{ route('jabatan.index') }}" class="{{ request()->is('jabatan*') ? 'active' : '' }}">Jabatan</a></li>
                        @endif
                        
                        @if(in_array('karyawan.index', $rolePermissions))
                            <li><a href="{{ route('karyawan.index') }}" class="{{ request()->is('karyawan*') ? 'active' : '' }}">Karyawan</a></li>
                        @endif
                        
                        @if(in_array('rekening.index', $rolePermissions))
                            <li><a href="{{ route('rekening.index') }}" class="{{ request()->is('rekening*') ? 'active' : '' }}">Rekening</a></li>
                        @endif
                        
                        @if(in_array('akun.index', $rolePermissions))
                            <li><a href="{{ route('akun.index') }}" class="{{ request()->is('akun*') ? 'active' : '' }}">Akun</a></li>
                        @endif
                        
                        @if(in_array('aset.index', $rolePermissions))
                            <li><a href="{{ route('aset.index') }}" class="{{ request()->is('aset*') ? 'active' : '' }}">Aset</a></li>
                        @endif
                        
                        @if(in_array('promo.index', $rolePermissions))
                            <li><a href="{{ route('promo.index') }}" class="{{ request()->is('promo*') ? 'active' : '' }}">Promo</a></li>
                        @endif
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
                        <li>
                            <a href="{{ route('form.index', ['jenis_rangkaian' => 'Sewa']) }}"
                            class="{{ request()->is('form') && request()->query('jenis_rangkaian') == 'Sewa' ? 'active' : '' }}">
                                Perangkai
                            </a>
                        </li>
                        <li><a href="{{ route('do_sewa.index') }}" class="{{ request()->is('do_sewa*') ? 'active' : '' }}">Delivery Order</a></li>
                        <li><a href="{{ route('kembali_sewa.index') }}" class="{{ request()->is('kembali_sewa*') ? 'active' : '' }}">Barang Kembali</a></li>
                        <li><a href="{{ route('invoice_sewa.index') }}" class="{{ request()->is('invoice_sewa*') ? 'active' : '' }}">Invoice</a></li>
                        <li><a href="{{ route('pembayaran_sewa.index') }}" class="{{ request()->is('pembayaran_sewa*') ? 'active' : '' }}">Pembayaran</a></li>
                    </ul>
                </li>
                @endif
                <li class="submenu">
                    @if(in_array('penjualan.index', $rolePermissions) && $lokasi->lokasi->tipe_lokasi == 1)
                    <a href="javascript:void(0);"><img src="/assets/img/icons/product.svg" alt="img"><span> Penjualan Galery <span class="badge rounded-pill bg-danger ms-auto text-white">{{$totaljual }}</span></span> <span class="menu-arrow"></span></a>
                    <ul>
                        <li><a href="{{ route('penjualan.index') }}" class="{{ request()->is('penjualan*') ? 'active' : '' }}">Invoice <span class="badge rounded-pill bg-danger ms-auto">{{$hitungpenjualan }}</span></a></li>
                        <li>
                            <a href="{{ route('formpenjualan.index', ['jenis_rangkaian' => 'Penjualan']) }}"
                            class="{{ request()->is('formpenjualan') && request()->query('jenis_rangkaian') == 'Penjualan' ? 'active' : '' }}">
                                Perangkai
                            </a>
                        </li>
                        <li><a href="{{ route('pembayaran.index') }}" class="{{ request()->is('pembayaran*') && !request()->is('pembayaran_sewa*') ? 'active' : '' }}">Pembayaran</a></li>
                        <li><a href="{{ route('dopenjualan.index') }}" class="{{ request()->is('dopenjualan*') ? 'active' : '' }}">Delivery Order <span class="badge rounded-pill bg-danger ms-auto">{{$dopenjualan }}</span></a></li>
                        <li><a href="{{ route('returpenjualan.index') }}" class="{{ request()->is('retur*') ? 'active' : '' }}">Retur <span class="badge rounded-pill bg-danger ms-auto">{{$returpenjualan}}</span></a></li>
                        <!-- <li><a href="{{ route('gift.index') }}" class="{{ request()->is('gift*') ? 'active' : '' }}">Gift</a></li> -->
                    </ul>
                    @endif
                    @if(in_array('penjualan.index', $rolePermissions) && $lokasi->lokasi->tipe_lokasi == 2)
                    <a href="javascript:void(0);"><img src="/assets/img/icons/product.svg" alt="img"><span> Penjualan Outlet <span class="badge rounded-pill bg-danger ms-auto text-white">{{$totaljualoutlet }}</span></span> <span class="menu-arrow"></span></a>
                    <ul>
                        <li><a href="{{ route('penjualan.index') }}" class="{{ request()->is('penjualan*') ? 'active' : '' }}">Invoice <span class="badge rounded-pill bg-danger ms-auto">{{$hitungpenjualanoutlet}}</span></a></li>
                        <!-- <li><a href="{{ route('formpenjualan.index', ['jenis_rangkaian' => 'Penjualan']) }}" class="{{ request()->is('formpenjualan*') ? 'active' : '' }}">Perangkai</a></li> -->
                        <li><a href="{{ route('pembayaran.index') }}" class="{{ request()->is('pembayaran*') && !request()->is('pembayaran_sewa*') ? 'active' : '' }}">Pembayaran</a></li>
                        <!-- <li><a href="{{ route('dopenjualan.index') }}" class="{{ request()->is('dopenjualan*') ? 'active' : '' }}">Delivery Order</a></li> -->
                        <li><a href="{{ route('returpenjualan.index') }}" class="{{ request()->is('retur*') ? 'active' : '' }}">Retur <span class="badge rounded-pill bg-danger ms-auto">{{$returpenjualanoutlet}}</span></a></li>
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

                    <a href="javascript:void(0);"><img src="/assets/img/icons/quotation1.svg" alt="img"><span> Mutasi <span class="badge rounded-pill bg-danger ms-auto text-white">{{$totalmutasi ?? 0 }}</span></span> <span class="menu-arrow"></span></a>
                    <ul>
                        @if(in_array('mutasigalery.index', $rolePermissions))
                            <li><a href="{{ route('mutasigalery.index') }}" class="{{ request()->is('mutasiGO*')  ? 'active' : '' }}">Mutasi Galery ke Outlet <span class="badge rounded-pill bg-danger ms-auto">{{$hitungmutasigo}}</span></a></li>
                        @endif
                        @if(in_array('formpenjualan.index', $rolePermissions) && isset($lokasi->lokasi->tipe_lokasi) && $lokasi->lokasi->tipe_lokasi != 2)
                        <li>
                            <a href="{{ route('formpenjualan.index', ['jenis_rangkaian' => 'MUTASIGO']) }}"
                            class="{{ request()->is('formpenjualan') && request()->query('jenis_rangkaian') == 'MUTASIGO' ? 'active' : '' }}">
                                Perangkai Mutasi GO
                            </a>
                        </li>
                        @endif
                        @if(in_array('mutasioutlet.index', $rolePermissions))
                            <li><a href="{{ route('mutasioutlet.index') }}" class="{{ request()->is('mutasiOG*') ? 'active' : '' }}">Mutasi Outlet ke Galery <span class="badge rounded-pill bg-danger ms-auto">{{$hitungmutasiog}}</span></a></li>
                        @endif
                        @if(in_array('mutasighgalery.index', $rolePermissions))
                            <li><a href="{{ route('mutasighgalery.index') }}" class="{{ request()->is('mutasiGG*') ? 'active' : '' }}">Mutasi GH/Pusat <span class="badge rounded-pill bg-danger ms-auto">{{$hitungmutasigg}}</span></a></li>
                        @endif
                        @if(in_array('mutasigalerygalery.index', $rolePermissions))
                            <li><a href="{{ route('mutasigalerygalery.index') }}" class="{{ request()->is('mutasiGAG*') ? 'active' : '' }}">Mutasi Galery ke Galery <span class="badge rounded-pill bg-danger ms-auto">{{$hitungmutasigag}}</span></a></li>
                            {{-- <li><a href="#" class="">Mutasi Inden ke GH</a></li>
                            <li><a href="#" class="">Mutasi Inden Ke Galery</a></li>
                            <li><a href="#" class="">Mutasi Galery Ke Inden</a></li> --}}
                            <li><a href="{{ route('mutasiindengh.index') }}" class="{{ request()->is('mutasiIG*') ? 'active' : '' }}">Mutasi Inden</a></li>
                            <li><a href="{{ route('returinden.index') }}" class="{{ request()->is('returinden*') ? 'active' : '' }}">Retur Inden</a></li>
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
                        @if(in_array('inven_gudang.index', $rolePermissions))
                        <li><a href="{{ route('inven_gudang.index')}}" class="{{ request()->is('inven_gudang*') ? 'active' : '' }}">Gudang</a></li>
                        @endif
                        @if(in_array('inven_inden.index', $rolePermissions))
                        <li><a href="{{ route('inven_inden.index')}}" class="{{ request()->is('inven_inden*') ? 'active' : '' }}">Inden</a></li>
                        @endif
                        {{-- <li><a href="#" class="">Inden</a></li> --}}
                    </ul>
                </li>
               @role(['AdminGallery', 'Finance'])
                <li class="submenu">
                    <a href="javascript:void(0);"><img src="/assets/img/icons/wallet1.svg" alt="img"><span> Kas</span> <span class="menu-arrow"></span></a>
                    <ul>
                        @if(in_array('kas_pusat.index', $rolePermissions))
                        <li><a href="{{ route('kas_pusat.index') }}" class="{{ request()->is('kas_pusat*') ? 'active' : '' }}">Pusat</a></li>
                        @endif
                        @if(in_array('kas_gallery.index', $rolePermissions))
                        <li><a href="{{ route('kas_gallery.index')}}" class="{{ request()->is('kas_gallery*') ? 'active' : '' }}">Gallery</a></li>
                        @endif
                    </ul>
                </li>
                @endrole
                @if(
                    in_array('laporan.kontrak', $rolePermissions) ||
                    in_array('laporan.tagihan_sewa', $rolePermissions) ||
                    in_array('laporan.do_sewa', $rolePermissions) ||
                    in_array('laporan.pergantian_sewa', $rolePermissions) ||
                    in_array('laporan.penjualanproduk', $rolePermissions) ||
                    in_array('laporan.pelanggan', $rolePermissions) ||
                    in_array('laporan.pembayaran', $rolePermissions) ||
                    in_array('laporan.dopenjualan', $rolePermissions) ||
                    in_array('laporan.returpenjualan', $rolePermissions) ||
                    in_array('laporan.penjualan', $rolePermissions) ||
                    in_array('laporan.mutasi', $rolePermissions) ||
                    in_array('laporan.mutasiinden', $rolePermissions) ||
                    in_array('laporan.kas_pusat', $rolePermissions) ||
                    in_array('laporan.kas_gallery', $rolePermissions) ||
                    in_array('laporan.pembelian', $rolePermissions) ||
                    in_array('laporan.pembelian_inden', $rolePermissions) ||
                    in_array('laporan.stok_inden', $rolePermissions) ||
                    in_array('laporan.hutang_supplier', $rolePermissions) ||
                    in_array('laporan.retur_pembelian', $rolePermissions) ||
                    in_array('laporan.retur_pembelian_inden', $rolePermissions) ||
                    in_array('laporan.omset', $rolePermissions) ||
                    in_array('laporan.promo', $rolePermissions) ||
                    in_array('laporan.stok_gallery', $rolePermissions) ||
                    in_array('laporan.stok_pusat', $rolePermissions) ||
                    in_array('laporan.pemakaian_sendiri', $rolePermissions) ||
                    in_array('laporan.bunga_keluar', $rolePermissions) ||
                    in_array('laporan.bunga_datang', $rolePermissions)
                )
                <li class="submenu">
                    <a href="javascript:void(0);"><img src="/assets/img/icons/purchase1.svg" alt="img"><span> Laporan</span> <span class="menu-arrow"></span></a>
                    <ul>
                    @if(in_array('laporan.kontrak', $rolePermissions))
                        <li><a href="{{ route('laporan.kontrak') }}" class="{{ request()->is('laporan/kontrak') ? 'active' : '' }}">Kontrak </a></li>
                    @endif
                    @if(in_array('laporan.tagihan_sewa', $rolePermissions))
                        <li><a href="{{ route('laporan.tagihan_sewa') }}" class="{{ request()->is('laporan/tagihan_sewa') ? 'active' : '' }}">Tagihan Sewa </a></li>
                    @endif
                    @if(in_array('laporan.do_sewa', $rolePermissions))
                        <li><a href="{{ route('laporan.do_sewa') }}" class="{{ request()->is('laporan/do_sewa') ? 'active' : '' }}">Delivery Order Sewa </a></li>
                    @endif
                    @if(in_array('laporan.pergantian_sewa', $rolePermissions))
                        <li><a href="{{ route('laporan.pergantian_sewa') }}" class="{{ request()->is('laporan/pergantian_sewa') ? 'active' : '' }}">Pergantian Sewa </a></li>
                    @endif
                    @if(in_array('laporan.penjualanproduk', $rolePermissions))
                        <li><a href="{{ route('laporan.penjualanproduk') }}" class="{{ request()->is('laporan/penjualanproduk*') ? 'active' : '' }}">Penjualan Produk </a></li>
                    @endif
                    @if(in_array('laporan.pelanggan', $rolePermissions))
                        <li><a href="{{ route('laporan.pelanggan') }}" class="{{ request()->is('laporan/pelanggan*') ? 'active' : '' }}">Pelanggan</a></li>
                    @endif
                    @if(in_array('laporan.pembayaran', $rolePermissions))
                        <li><a href="{{ route('laporan.pembayaran') }}" class="{{ request()->is('laporan/pembayaran*') ? 'active' : '' }}">Pembayaran</a></li>
                    @endif
                    @if(in_array('laporan.dopenjualan', $rolePermissions))
                        <li><a href="{{ route('laporan.dopenjualan') }}" class="{{ request()->is('laporan/dopenjualan*') ? 'active' : '' }}">Delivery Order</a></li>
                    @endif
                    @if(in_array('laporan.returpenjualan', $rolePermissions))
                        <li><a href="{{ route('laporan.returpenjualan') }}" class="{{ request()->is('laporan/returpenjualan*') ? 'active' : '' }}">Retur Penjualan</a></li>
                    @endif
                    @if(in_array('laporan.penjualan', $rolePermissions))
                        <li><a href="{{ route('laporan.penjualan') }}" class="{{ request()->is('laporan/penjualan') ? 'active' : '' }}">Penjualan</a></li>
                    @endif
                    @if(in_array('laporan.mutasi', $rolePermissions))
                        <li><a href="{{ route('laporan.mutasi') }}" class="{{ request()->is('laporan/mutasi') ? 'active' : '' }}">Mutasi</a></li>
                    @endif
                    @if(in_array('laporan.mutasiinden', $rolePermissions))
                        <li><a href="{{ route('laporan.mutasiinden') }}" class="{{ request()->is('laporan/mutasiinden*') ? 'active' : '' }}">Mutasi Inden</a></li>
                    @endif
                    @if(in_array('laporan.kas_pusat', $rolePermissions))
                        <li><a href="{{ route('laporan.kas_pusat') }}" class="{{ request()->is('laporan/kas_pusat*') ? 'active' : '' }}">Kas Pusat</a></li>
                    @endif
                    @if(in_array('laporan.kas_gallery', $rolePermissions))
                        <li><a href="{{ route('laporan.kas_gallery') }}" class="{{ request()->is('laporan/kas_gallery*') ? 'active' : '' }}">Kas Gallery</a></li>
                    @endif
                    @if(in_array('laporan.pembelian', $rolePermissions))
                        <li><a href="{{ route('laporan.pembelian') }}" class="{{ request()->is('laporan/pembelian') ? 'active' : '' }}">Pembelian</a></li>
                    @endif
                    @if(in_array('laporan.pembelian_inden', $rolePermissions))
                        <li><a href="{{ route('laporan.pembelian_inden') }}" class="{{ request()->is('laporan/pembelian_inden*') ? 'active' : '' }}">Pembelian Inden</a></li>
                    @endif
                    @if(in_array('laporan.stok_inden', $rolePermissions))
                        <li><a href="{{ route('laporan.stok_inden') }}" class="{{ request()->is('laporan/stok_inden*') ? 'active' : '' }}">Stok Inden</a></li>
                    @endif
                    @if(in_array('laporan.hutang_supplier', $rolePermissions))
                        <li><a href="{{ route('laporan.hutang_supplier') }}" class="{{ request()->is('laporan/hutang_supplier*') ? 'active' : '' }}">Hutang Supplier</a></li>
                    @endif
                    @if(in_array('laporan.retur_pembelian', $rolePermissions))
                        <li><a href="{{ route('laporan.retur_pembelian') }}" class="{{ request()->is('laporan/retur_pembelian') ? 'active' : '' }}">Retur Pembelian</a></li>
                    @endif
                    @if(in_array('laporan.retur_pembelian_inden', $rolePermissions))
                        <li><a href="{{ route('laporan.retur_pembelian_inden') }}" class="{{ request()->is('laporan/retur_pembelian_inden*') ? 'active' : '' }}">Retur Pembelian Inden</a></li>
                    @endif
                    @if(in_array('laporan.omset', $rolePermissions))
                        <li><a href="{{ route('laporan.omset') }}" class="{{ request()->is('laporan/omset*') ? 'active' : '' }}">Omset</a></li>
                    @endif
                    @if(in_array('laporan.promo', $rolePermissions))
                        <li><a href="{{ route('laporan.promo') }}" class="{{ request()->is('laporan/promo*') ? 'active' : '' }}">Promo</a></li>
                    @endif
                    @if(in_array('laporan.stok_gallery', $rolePermissions))
                        <li><a href="{{ route('laporan.stok_gallery') }}" class="{{ request()->is('laporan/stok_gallery*') ? 'active' : '' }}">Stok Gallery</a></li>
                    @endif
                    @if(in_array('laporan.stok_pusat', $rolePermissions))
                        <li><a href="{{ route('laporan.stok_pusat') }}" class="{{ request()->is('laporan/stok_pusat*') ? 'active' : '' }}">Stok Pusat</a></li>
                    @endif
                    @if(in_array('laporan.pemakaian_sendiri', $rolePermissions))
                        <li><a href="{{ route('laporan.pemakaian_sendiri') }}" class="{{ request()->is('laporan/pemakaian_sendiri*') ? 'active' : '' }}">Pemakaian Sendiri</a></li>
                    @endif
                    @if(in_array('laporan.bunga_keluar', $rolePermissions))
                        <li><a href="{{ route('laporan.bunga_keluar') }}" class="{{ request()->is('laporan/bunga_keluar*') ? 'active' : '' }}">Bunga Kelaur</a></li>
                    @endif
                    @if(in_array('laporan.bunga_datang', $rolePermissions))
                        <li><a href="{{ route('laporan.bunga_datang') }}" class="{{ request()->is('laporan/bunga_datang*') ? 'active' : '' }}">Bunga Datang</a></li>
                    @endif
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
                @endif
            </ul>
            
        </div>
        <!-- <img src="/assets/img/bunga.png" alt="Gambar Bunga" id="bawah" style="width:100%"> -->
    </div>
</div>