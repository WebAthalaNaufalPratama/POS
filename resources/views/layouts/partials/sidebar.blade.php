<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                <li class="submenu">
                    <a href="index.html"><img src="/assets/img/icons/dashboard.svg" alt="img"><span> Dashboard</span> </a>
                </li>
                <li class="submenu">
                    <a href="javascript:void(0);"><i data-feather="box"></i><span> Master</span> <span class="menu-arrow"></span></a>
                    <ul>
                        <li><a href="{{ route('tipe_produk.index') }}" class="{{ request()->is('tipe_produks*') ? 'active' : '' }}">Tipe Produk</a></li>
                        <li><a href="{{ route('produks.index') }}" class="{{ request()->is('produks*') ? 'active' : '' }}">Produk</a></li>
                        <li><a href="{{ route('kondisi.index') }}" class="{{ request()->is('kondisi*') ? 'active' : '' }}">Kondisi</a></li>
                        <li><a href="{{ route('tipe_lokasi.index') }}" class="{{ request()->is('tipe_lokasi*') ? 'active' : ''}}">Tipe Lokasi</a></li>
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
                <li class="submenu">
                    <a href="javascript:void(0);"><img src="/assets/img/icons/product.svg" alt="img"><span> Produk Jual</span> <span class="menu-arrow"></span></a>
                    <ul>
                        <li><a href="{{ route('tradisional.index') }}" class="{{ request()->is('tradisional*') ? 'active' : '' }}">Tradisional</a></li>
                        <li><a href="{{ route('gift.index') }}" class="{{ request()->is('gift*') ? 'active' : '' }}">Gift</a></li>
                    </ul>
                </li>
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
                <li class="submenu">

                    <a href="javascript:void(0);"><img src="/assets/img/icons/product.svg" alt="img"><span> Penjualan</span> <span class="menu-arrow"></span></a>
                    <ul>
                        <li><a href="{{ route('penjualan.index') }}" class="{{ request()->is('penjualan*') ? 'active' : '' }}">Invoice</a></li>
                        <li><a href="{{ route('formpenjualan.index', ['jenis_rangkaian' => 'Penjualan']) }}" class="{{ request()->is('formpenjualan*') ? 'active' : '' }}">Perangkai</a></li>
                        <li><a href="{{ route('pembayaran.index') }}" class="{{ request()->is('pembayaran*') && !request()->is('pembayaran_sewa*') ? 'active' : '' }}">Pembayaran</a></li>
                        <li><a href="{{ route('dopenjualan.index') }}" class="{{ request()->is('dopenjualan*') ? 'active' : '' }}">Delivery Order</a></li>
                        <li><a href="{{ route('returpenjualan.index') }}" class="{{ request()->is('retur*') ? 'active' : '' }}">Retur</a></li>
                        <!-- <li><a href="{{ route('gift.index') }}" class="{{ request()->is('gift*') ? 'active' : '' }}">Gift</a></li> -->
                    </ul>
                </li>
                <li class="submenu">

                    <a href="javascript:void(0);"><img src="/assets/img/icons/quotation1.svg" alt="img"><span> Mutasi</span> <span class="menu-arrow"></span></a>
                    <ul>
                        <li><a href="{{ route('mutasigalery.index') }}" class="{{ request()->is('mutasigalery*')  ? 'active' : '' }}">Mutasi Galery ke Outlet</a></li>
                        <li><a href="{{ route('mutasioutlet.index') }}" class="{{ request()->is('mutasioutlet*') ? 'active' : '' }}">Mutasi Outlet ke Galery</a></li>
                        <li><a href="{{ route('mutasighgalery.index') }}" class="{{ request()->is('mutasighgalery*') ? 'active' : '' }}">Mutasi GH ke Galery</a></li>
                        <li><a href="#" class="">Mutasi Inden ke GH</a></li>
                        <li><a href="#" class="">Mutasi Inden Ke Galery</a></li>
                        <li><a href="#" class="">Mutasi Galery Ke Inden</a></li>
                    </ul>
                </li>
                <li class="submenu">
                    <a href="javascript:void(0);"><img src="/assets/img/icons/dollar-square.svg" alt="img"><span> Pembelian</span> <span class="menu-arrow"></span></a>
                    <ul>
                        <li><a href="{{ route('pembelian.index') }}" class="{{ request()->is('purchase/pembelian*') ? 'active' : '' }}">Purchase Order</a></li>
                        <li><a href="{{ route('invoicebeli.index') }}" class="{{ request()->is('purchase/invoice*') ? 'active' : '' }}">Invoice Pembelian</a></li>
                    </ul>
                </li>
                
                <li class="submenu">
                    <a href="javascript:void(0);"><i class="fa fa-archive" data-bs-toggle="tooltip" title="" data-bs-original-title="fa fa-archive" aria-label="fa fa-archive"></i><span> Inventory</span> <span class="menu-arrow"></span></a>
                    <ul>
                        <li><a href="{{ route('inven_galeri.index') }}" class="{{ request()->is('inven_galeri*') ? 'active' : '' }}">Gallery</a></li>
                        <li><a href="{{ route('inven_outlet.index')}}" class="{{ request()->is('inven_outlet*') ? 'active' : '' }}">Outlet</a></li>
                        <li><a href="{{ route('inven_greenhouse.index')}}" class="{{ request()->is('inven_greenhouse*') ? 'active' : '' }}">GreenHouse</a></li>
                        <li><a href="#" class="">Inden</a></li>
                    </ul>
                </li>
                <li class="submenu">
                    <a href="javascript:void(0);"><img src="/assets/img/icons/wallet1.svg" alt="img"><span> Kas</span> <span class="menu-arrow"></span></a>
                    <ul>
                        <li><a href="{{ route('kas_pusat.index') }}" class="{{ request()->is('kas_pusat*') ? 'active' : '' }}">Pusat</a></li>
                        <li><a href="{{ route('kas_gallery.index')}}" class="{{ request()->is('kas_gallery*') ? 'active' : '' }}">Gallery</a></li>
                    </ul>
                </li>
                <li class="submenu">
                {{-- <li class="submenu">
<a href="javascript:void(0);"><img src="/assets/img/icons/sales1.svg" alt="img"><span> Sales</span> <span class="menu-arrow"></span></a>
<ul>
<li><a href="saleslist.html">Sales List</a></li>
<li><a href="pos.html">POS</a></li>
<li><a href="pos.html">New Sales</a></li>
<li><a href="salesreturnlists.html">Sales Return List</a></li>
<li><a href="createsalesreturns.html">New Sales Return</a></li>
</ul>
</li>
<li class="submenu">
<a href="javascript:void(0);"><img src="/assets/img/icons/purchase1.svg" alt="img"><span> Purchase</span> <span class="menu-arrow"></span></a>
<ul>
<li><a href="purchaselist.html">Purchase List</a></li>
<li><a href="addpurchase.html">Add Purchase</a></li>
<li><a href="importpurchase.html">Import Purchase</a></li>
</ul>
</li>
<li class="submenu">
<a href="javascript:void(0);"><img src="/assets/img/icons/expense1.svg" alt="img"><span> Expense</span> <span class="menu-arrow"></span></a>
<ul>
<li><a href="expenselist.html">Expense List</a></li>
<li><a href="createexpense.html">Add Expense</a></li>
<li><a href="expensecategory.html">Expense Category</a></li>
</ul>
</li>
<li class="submenu">
<a href="javascript:void(0);"><img src="/assets/img/icons/quotation1.svg" alt="img"><span> Quotation</span> <span class="menu-arrow"></span></a>
<ul>
<li><a href="quotationList.html">Quotation List</a></li>
 <li><a href="addquotation.html">Add Quotation</a></li>
</ul>
</li>
<li class="submenu">
<a href="javascript:void(0);"><img src="/assets/img/icons/transfer1.svg" alt="img"><span> Transfer</span> <span class="menu-arrow"></span></a>
<ul>
<li><a href="transferlist.html">Transfer List</a></li>
<li><a href="addtransfer.html">Add Transfer </a></li>
<li><a href="importtransfer.html">Import Transfer </a></li>
</ul>
</li>
<li class="submenu">
<a href="javascript:void(0);"><img src="/assets/img/icons/return1.svg" alt="img"><span> Return</span> <span class="menu-arrow"></span></a>
<ul>
<li><a href="salesreturnlist.html">Sales Return List</a></li>
<li><a href="createsalesreturn.html">Add Sales Return </a></li>
<li><a href="purchasereturnlist.html">Purchase Return List</a></li>
<li><a href="createpurchasereturn.html">Add Purchase Return </a></li>
</ul>
</li>
<li class="submenu">
<a href="javascript:void(0);"><img src="/assets/img/icons/users1.svg" alt="img"><span> People</span> <span class="menu-arrow"></span></a>
<ul>
<li><a href="customerlist.html">Customer List</a></li>
<li><a href="addcustomer.html">Add Customer </a></li>
<li><a href="supplierlist.html">Supplier List</a></li>
<li><a href="addsupplier.html">Add Supplier </a></li>
<li><a href="userlist.html">User List</a></li>
<li><a href="adduser.html">Add User</a></li>
<li><a href="storelist.html">Store List</a></li>
<li><a href="addstore.html">Add Store</a></li>
</ul>
</li> <li class="submenu">
<a href="javascript:void(0);"><img src="/assets/img/icons/places.svg" alt="img"><span> Places</span> <span class="menu-arrow"></span></a>
<ul>
<li><a href="newcountry.html">New Country</a></li>
<li><a href="countrieslist.html">Countries list</a></li>
<li><a href="newstate.html">New State </a></li>
<li><a href="statelist.html">State list</a></li>
</ul>
</li>
<li>
<a href="components.html"><i data-feather="layers"></i><span> Components</span> </a>
</li>
<li>
<a href="blankpage.html"><i data-feather="file"></i><span> Blank Page</span> </a>
</li>
<li class="submenu">
<a href="javascript:void(0);"><i data-feather="alert-octagon"></i> <span> Error Pages </span> <span class="menu-arrow"></span></a>
<ul>
<li><a href="error-404.html">404 Error </a></li>
<li><a href="error-500.html">500 Error </a></li>
</ul>
</li>
<li class="submenu">
<a href="javascript:void(0);"><i data-feather="box"></i> <span>Elements </span> <span class="menu-arrow"></span></a>
<ul>
<li><a href="sweetalerts.html">Sweet Alerts</a></li>
<li><a href="tooltip.html">Tooltip</a></li>
<li><a href="popover.html">Popover</a></li>
<li><a href="ribbon.html">Ribbon</a></li>
<li><a href="clipboard.html">Clipboard</a></li>
<li><a href="drag-drop.html">Drag & Drop</a></li>
<li><a href="rangeslider.html">Range Slider</a></li>
<li><a href="rating.html">Rating</a></li>
<li><a href="toastr.html">Toastr</a></li>
<li><a href="text-editor.html">Text Editor</a></li>
<li><a href="counter.html">Counter</a></li>
<li><a href="scrollbar.html">Scrollbar</a></li>
<li><a href="spinner.html">Spinner</a></li>
<li><a href="notification.html">Notification</a></li>
<li><a href="lightbox.html">Lightbox</a></li>
<li><a href="stickynote.html">Sticky Note</a></li>
<li><a href="timeline.html">Timeline</a></li>
<li><a href="form-wizard.html">Form Wizard</a></li>
</ul>
</li>
<li class="submenu">
<a href="javascript:void(0);"><i data-feather="bar-chart-2"></i> <span> Charts </span> <span class="menu-arrow"></span></a>
<ul>
<li><a href="chart-apex.html">Apex Charts</a></li>
<li><a href="chart-js.html">Chart Js</a></li>
<li><a href="chart-morris.html">Morris Charts</a></li>
<li><a href="chart-flot.html">Flot Charts</a></li>
<li><a href="chart-peity.html">Peity Charts</a></li>
</ul>
</li>
<li class="submenu">
<a href="javascript:void(0);"><i data-feather="award"></i><span> Icons </span> <span class="menu-arrow"></span></a>
<ul>
<li><a href="icon-fontawesome.html">Fontawesome Icons</a></li>
<li><a href="icon-feather.html">Feather Icons</a></li>
<li><a href="icon-ionic.html">Ionic Icons</a></li>
<li><a href="icon-material.html">Material Icons</a></li>
<li><a href="icon-pe7.html">Pe7 Icons</a></li>
<li><a href="icon-simpleline.html">Simpleline Icons</a></li>
<li><a href="icon-themify.html">Themify Icons</a></li>
<li><a href="icon-weather.html">Weather Icons</a></li>
<li><a href="icon-typicon.html">Typicon Icons</a></li>
<li><a href="icon-flag.html">Flag Icons</a></li>
</ul>
</li>
<li class="submenu">
<a href="javascript:void(0);"><i data-feather="columns"></i> <span> Forms </span> <span class="menu-arrow"></span></a>
<ul>
<li><a href="form-basic-inputs.html">Basic Inputs </a></li>
<li><a href="form-input-groups.html">Input Groups </a></li>
<li><a href="form-horizontal.html">Horizontal Form </a></li>
<li><a href="form-vertical.html"> Vertical Form </a></li>
<li><a href="form-mask.html">Form Mask </a></li>
<li><a href="form-validation.html">Form Validation </a></li>
<li><a href="form-select2.html">Form Select2 </a></li>
<li><a href="form-fileupload.html">File Upload </a></li>
</ul>
</li>
<li class="submenu">
<a href="javascript:void(0);"><i data-feather="layout"></i> <span> Table </span> <span class="menu-arrow"></span></a>
<ul>
<li><a href="tables-basic.html">Basic Tables </a></li>
<li><a href="data-tables.html">Data Table </a></li>
</ul>
</li>
<li class="submenu">
<a href="javascript:void(0);"><img src="/assets/img/icons/product.svg" alt="img"><span> Application</span> <span class="menu-arrow"></span></a>
<ul>
<li><a href="chat.html">Chat</a></li>
<li><a href="calendar.html">Calendar</a></li>
<li><a href="email.html">Email</a></li>
</ul>
</li> --}}
                {{-- <li class="submenu">
<a href="javascript:void(0);"><img src="/assets/img/icons/time.svg" alt="img"><span> Report</span> <span class="menu-arrow"></span></a>
<ul>
<li><a href="purchaseorderreport.html">Purchase order report</a></li>
<li><a href="inventoryreport.html">Inventory Report</a></li>
<li><a href="salesreport.html">Sales Report</a></li>
<li><a href="invoicereport.html">Invoice Report</a></li>
<li><a href="purchasereport.html">Purchase Report</a></li>
<li><a href="supplierreport.html">Supplier Report</a></li>
<li><a href="customerreport.html">Customer Report</a></li>
</ul>
</li> --}}
                <li class="submenu">
                    <a href="javascript:void(0);"><img src="/assets/img/icons/users1.svg" alt="img"><span> Users</span> <span class="menu-arrow"></span></a>
                    <ul>
                        <li><a href="/roles" class="{{ request()->is('roles*') ? 'active' : '' }}">Roles </a></li>
                        <li><a href="/permissions" class="{{ request()->is('permissions*') ? 'active' : '' }}">Permissions </a></li>
                        <li><a href="/posts" class="{{ request()->is('posts*') ? 'active' : '' }}">Log Activity </a></li>
                        <li><a href="{{ route('users.index') }}" class="{{ request()->is('users*') ? 'active' : '' }}">User </a></li>
                    </ul>
                </li>
                <li class="submenu">
                    <a href="javascript:void(0);"><img src="/assets/img/icons/settings.svg" alt="img"><span> Settings</span> <span class="menu-arrow"></span></a>
                    <ul>
                        <li><a href="generalsettings.html">General Settings</a></li>
                        <li><a href="emailsettings.html">Email Settings</a></li>
                        <li><a href="paymentsettings.html">Payment Settings</a></li>
                        <li><a href="currencysettings.html">Currency Settings</a></li>
                        <li><a href="grouppermissions.html">Group Permissions</a></li>
                        <li><a href="taxrates.html">Tax Rates</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>