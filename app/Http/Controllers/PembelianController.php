<?php

namespace App\Http\Controllers;

use App\Models\InventoryGallery;
use App\Models\InventoryGreenHouse;
use App\Models\InventoryInden;
use App\Models\Invoicepo;
use App\Models\Karyawan;
use App\Models\Kondisi;
use App\Models\Lokasi;
use App\Models\Pembayaran;
use App\Models\Pembelian;
use App\Models\Poinden as ModelsPoinden;
use App\Models\Produk;
use App\Models\Produkbeli;
use App\Models\Rekening;
use App\Models\Returpembelian;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Spatie\Activitylog\Models\Activity;
use App\Models\Produkretur;
use PhpParser\Node\Stmt\Foreach_;
use Poinden;
use ProdukBelis;
use SebastianBergmann\Invoker\Invoker;

class PembelianController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $req)
    {
        $query = Pembelian::orderBy('created_at', 'desc');
        if ($req->supplier) {
            $query->where('supplier_id', $req->input('supplier'));
        }
        if ($req->gallery) {
            $query->where('lokasi_id', $req->input('gallery'));
        }
        if ($req->status) {
            if ($req->status == 'Lunas') {
                $query->whereHas('invoice', function($q) {
                    $q->where('sisa', 0);
                });
            } else {
                $query->whereDoesntHave('invoice')
                      ->orWhereHas('invoice', function($q) {
                          $q->where('sisa', '!=', 0);
                      });
            }
        }        
        if ($req->dateStart) {
            $query->where('tgl_dibuat', '>=', $req->input('dateStart'));
        }
        if ($req->dateEnd) {
            $query->where('tgl_dibuat', '<=', $req->input('dateEnd'));
        }
        $datapos = $query->get();

        $query2 = ModelsPoinden::orderBy('created_at', 'desc');
        if ($req->supplierInd) {
            $query2->where('supplier_id', $req->input('supplierInd'));
        }
        if ($req->statusInd) {
            if ($req->statusInd == 'Lunas') {
                $query2->whereHas('invoice', function($q) {
                    $q->where('sisa', 0);
                });
            } else {
                $query2->whereDoesntHave('invoice')
                      ->orWhereHas('invoice', function($q) {
                          $q->where('sisa', '!=', 0);
                      });
            }
        }        
        if ($req->dateStartInd) {
            $query2->where('tgl_dibuat', '>=', $req->input('dateStartInd'));
        }
        if ($req->dateEndInd) {
            $query2->where('tgl_dibuat', '<=', $req->input('dateEndInd'));
        }

        $datainden = $query2->get();
        $datainv = Invoicepo::get();

        $supplierTrd = Pembelian::select('suppliers.id', 'suppliers.nama')
        ->distinct()
        ->join('suppliers', 'pembelians.supplier_id', '=', 'suppliers.id')
        ->orderBy('suppliers.nama')
        ->get();

        $supplierInd = ModelsPoinden::select('suppliers.id', 'suppliers.nama')
        ->distinct()
        ->join('suppliers', 'poinden.supplier_id', '=', 'suppliers.id')
        ->orderBy('suppliers.nama')
        ->get();

        $galleryTrd = Pembelian::select('lokasis.id', 'lokasis.nama')
        ->distinct()
        ->join('lokasis', 'pembelians.lokasi_id', '=', 'lokasis.id')
        ->orderBy('lokasis.nama')
        ->get();
       
        // $databayars = Pembayaran::where('invoice_purchase_id', $id_inv)->where('status_bayar','LUNAS')->get();

        return view('purchase.index',compact('datapos','datainv','datainden', 'supplierTrd', 'supplierInd', 'galleryTrd'));
    }

    public function index_retur(Request $req)
    {
        $query = Returpembelian::orderBy('created_at', 'desc');
        if ($req->gallery) {
            $query->whereHas('invoice', function($r) use($req){
                $r->whereHas('pembelian', function($q) use($req){
                    $q->where('lokasi_id', $req->input('gallery'));
                });
            });
        }
        if ($req->dateStart) {
            $query->where('tgl_dibuat', '>=', $req->input('dateStart'));
        }
        if ($req->dateEnd) {
            $query->where('tgl_dibuat', '<=', $req->input('dateEnd'));
        }
        $dataretur = $query->get();
        $datainv = Invoicepo::get();

        $gallery = Returpembelian::select('lokasis.id', 'lokasis.nama')
        ->distinct()
        ->join('invoicepo', 'returpembelians.invoicepo_id', '=', 'invoicepo.id')
        ->join('pembelians', 'invoicepo.pembelian_id', '=', 'pembelians.id')
        ->join('lokasis', 'pembelians.lokasi_id', '=', 'lokasis.id')
        ->orderBy('lokasis.nama')
        ->get();
       
        // $databayars = Pembayaran::where('invoice_purchase_id', $id_inv)->where('status_bayar','LUNAS')->get();

        return view('purchase.returindex',compact('dataretur','datainv', 'gallery'));
    }

    public function invoice(Request $req)
    {
       
        $query= Invoicepo::with('pembelian')->whereNull('poinden_id')->orderBy('tgl_inv', 'desc');
        if ($req->supplier) {
            $query->whereHas('pembelian', function($q) use($req){
                $q->where('supplier_id', $req->input('supplier'));
            });
        }
        if ($req->gallery) {
            $query->whereHas('pembelian', function($q) use($req){
                $q->where('lokasi_id', $req->input('gallery'));
            });
        }
        if ($req->status) {
            if ($req->status == 'Lunas') {
                $query->whereHas('pembelian.invoice', function($q) {
                    $q->where('sisa', 0);
                });
            } else {
                $query->whereDoesntHave('pembelian.invoice')
                        ->orWhereHas('pembelian.invoice', function($q) {
                            $q->where('sisa', '!=', 0);
                        });
            }
        }        
        if ($req->dateStart) {
            $query->where('tgl_dibuat', '>=', $req->input('dateStart'));
        }
        if ($req->dateEnd) {
            $query->where('tgl_dibuat', '<=', $req->input('dateEnd'));
        }
        $invoices = $query->get();

        $query2 = Invoicepo::with('pembelian')->whereNotNull('poinden_id')
            ->orderBy('tgl_inv', 'desc');
        if ($req->supplierInd) {
            $query->whereHas('pembelian', function($q) use($req){
                $q->where('supplier_id', $req->input('supplier'));
            });
        }
        if ($req->statusInd) {
            if ($req->statusInd == 'Lunas') {
                $query2->whereHas('poinden.invoice', function($q) {
                    $q->where('sisa', 0);
                });
            } else {
                $query2->whereDoesntHave('poinden.invoice')
                        ->orWhereHas('poinden.invoice', function($q) {
                            $q->where('sisa', '!=', 0);
                        });
            }
        }        
        if ($req->dateStartInd) {
            $query2->where('tgl_dibuat', '>=', $req->input('dateStartInd'));
        }
        if ($req->dateEndInd) {
            $query2->where('tgl_dibuat', '<=', $req->input('dateEndInd'));
        }
        $invoiceinden = $query2->get();

        $supplierTrd = Invoicepo::select('suppliers.id', 'suppliers.nama')
        ->distinct()
        ->join('pembelians', 'invoicepo.pembelian_id', '=', 'pembelians.id')
        ->join('suppliers', 'pembelians.supplier_id', '=', 'suppliers.id')
        ->orderBy('suppliers.nama')
        ->get();

        $supplierInd = Invoicepo::select('suppliers.id', 'suppliers.nama')
        ->distinct()
        ->join('poinden', 'invoicepo.poinden_id', '=', 'poinden.id')
        ->join('suppliers', 'poinden.supplier_id', '=', 'suppliers.id')
        ->orderBy('suppliers.nama')
        ->get();

        $galleryTrd = Invoicepo::select('lokasis.id', 'lokasis.nama')
        ->distinct()
        ->join('pembelians', 'invoicepo.pembelian_id', '=', 'pembelians.id')
        ->join('lokasis', 'pembelians.lokasi_id', '=', 'lokasis.id')
        ->orderBy('lokasis.nama')
        ->get();

        $Invoice = Pembayaran::latest()->first();
        if ($Invoice != null) {
            $substring = substr($Invoice->no_invoice_bayar, 11);
            $invoice_bayar = substr($substring, 0, 3);
        } else {
            $invoice_bayar = 0;
        }
        $bankpens = Rekening::get();
        $dataretur = Returpembelian::get();
        return view('purchase.invoice', compact('invoices','dataretur','invoiceinden', 'supplierTrd', 'supplierInd', 'galleryTrd', 'bankpens', 'invoice_bayar'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function generatePONumber() {
        // Ambil tanggal hari ini dalam format 'Y-m-d'
        $tgl_today = date('Y-m-d');
        
        // Cari urutan terakhir nomor PO pada hari ini
        $lastPO = Pembelian::whereDate('created_at', $tgl_today)->orderBy('id', 'desc')->first();
    
        // Jika tidak ada nomor PO pada hari ini, urutan diinisialisasi dengan 1
        $urutan = 1;
        if ($lastPO) {
            // Jika ada nomor PO pada hari ini, ambil urutan berikutnya
            $urutan = intval(substr($lastPO->no_po, -3)) + 1;
        }
    
        // Format nomor PO dengan pola 'PO_tgl_urutanpo'
        $nomor_po = 'PO_' . date('Ymd') . '_' . str_pad($urutan, 3, '0', STR_PAD_LEFT);
        
        return $nomor_po;
    }

    public function generatePOIndenNumber() {
        // Ambil tanggal hari ini dalam format 'Y-m-d'
        $tgl_today = date('Y-m-d');
        
        // Cari urutan terakhir nomor PO pada hari ini
        $lastPOinden = ModelsPoinden::whereDate('created_at', $tgl_today)->orderBy('id', 'desc')->first();
    
        // Jika tidak ada nomor PO pada hari ini, urutan diinisialisasi dengan 1
        $urutan = 1;
        if ($lastPOinden) {
            // Jika ada nomor PO pada hari ini, ambil urutan berikutnya
            $urutan = intval(substr($lastPOinden->no_po, -3)) + 1;
        }
    
        // Format nomor PO dengan pola 'PO_tgl_urutanpo'
        $nomor_poinden = 'PO_Inden_' . date('Ymd') . '_' . str_pad($urutan, 3, '0', STR_PAD_LEFT);
        
        return $nomor_poinden;
    }
    
    
    public function generatebayarPONumber() {
        $date = date('Ymd');  // Tanggal hari ini dalam format YYYYMMDD
        $prefix = 'BYPO_' . $date . '_';
        $lastPayment = Pembayaran::where('no_invoice_bayar', 'like', $prefix . '%')
                        ->orderBy('no_invoice_bayar', 'desc')
                        ->first();
    
        if (!$lastPayment) {
            return $prefix . '001';
        }
    
        $lastNumber = intval(substr($lastPayment->no_invoice_bayar, -3));
        $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
    
        return $prefix . $newNumber; 
    }
    

    public function generateINVPONumber() {
        $tgl_today = date('Y-m-d');
        
        // Cari urutan terakhir nomor PO pada hari ini
        $lastInv = Invoicepo::whereDate('created_at', $tgl_today)->orderBy('id', 'desc')->first();
    
        // Jika tidak ada nomor PO pada hari ini, urutan diinisialisasi dengan 1
        $urutan = 1;
        if ($lastInv) {
            // Jika ada nomor PO pada hari ini, ambil urutan berikutnya
            $urutan = intval(substr($lastInv->no_inv, -3)) + 1;
        }
    
        // Format nomor PO dengan pola 'PO_tgl_urutanpo'
        $nomor_inv = 'INV_PO_' . date('Ymd') . '_' . str_pad($urutan, 3, '0', STR_PAD_LEFT);
        
        return $nomor_inv;
    }
    
    public function create() {
        // Generate nomor PO
        $nomor_po = $this->generatePONumber();
    
        // Ambil data yang diperlukan
        $produks = Produk::get();
        $suppliers = Supplier::where('tipe_supplier','tradisional')->get();
        $lokasis = Lokasi::whereIn('tipe_lokasi', [1, 3])->get();
        $kondisis = Kondisi::get();
    
        return view('purchase.create', compact('produks', 'suppliers', 'lokasis', 'kondisis', 'nomor_po'));
    }
    

    public function createinden()
    {
        $produks = Produk::get();
        $suppliers = Supplier::where('tipe_supplier','inden')->get();
        $nomor_poinden = $this->generatePOIndenNumber();
        return view('purchase.createinden', compact('produks','suppliers','nomor_poinden'));

    }

    public function create_retur(Request $req)
    {
        $invoice = Invoicepo::with('pembelian', 'pembelian.produkbeli', 'pembelian.produkbeli.produk')->find($req->invoice);
        $lokasi = Lokasi::find(Auth::user()->karyawans->lokasi_id);
        // $nomor_poinden = $this->generatePOIndenNumber();
        $Invoice = ReturPembelian::where('no_retur', 'LIKE', 'RPM%')->latest()->first();
        // dd($Invoice);
        if ($Invoice != null) {
            $substring = substr($Invoice->invoicepo_id, 12);
            $cekInvoice = substr($substring, 0, 3);
            // dd($cekInvoice);
        } else {
            $cekInvoice = 000;
        }
        // dd($invoice->pembelian->produkbeli);
        return view('purchase.createretur', compact('cekInvoice', 'lokasi', 'invoice'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store_po(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'nopo' => 'required',
            'id_supplier' => 'required',
            'id_lokasi' => 'required',
            'tgl_kirim' => 'required|date',
            // 'tgl_diterima' => 'required|date',
            // 'no_do' => 'required',
            'status_dibuat' => 'required',
            // Tambahkan validasi lainnya sesuai kebutuhan
        ]);
    
        // Periksa apakah validasi gagal
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return redirect()->back()->withInput()->with('fail', $errors);
        }
    
        // Simpan data pembelian
        $pembelian = new Pembelian();
        $pembelian->no_po = $request->nopo;
        $pembelian->supplier_id = $request->id_supplier;
        $pembelian->lokasi_id = $request->id_lokasi;
        $pembelian->tgl_kirim = $request->tgl_kirim;
        $pembelian->tgl_diterima = $request->tgl_diterima;
        $pembelian->no_do_suplier = $request->no_do ?? null;
        $pembelian->pembuat = $request->pembuat; // ID pengguna yang membuat pembelian
        $pembelian->pemeriksa = $request->pemeriksa ?? null; // ID pengguna yang membuat pembelian
        $pembelian->penerima = $request->penerima ?? null; // ID pengguna yang membuat pembelian
        $pembelian->status_dibuat = $request->status_dibuat; // Status pembuatan
        $pembelian->status_diterima = $request->status_diterima ?? null; // Status pembuatan
        $pembelian->status_diperiksa = $request->status_diperiksa ?? null; // Status pembuatan
       
        $pembelian->tgl_dibuat = $request->tgl_dibuat; // Tanggal pembuatan saat ini
        $pembelian->tgl_diterima_ttd = $request->tgl_diterima_ttd ?? null; // Tanggal pembuatan saat ini
        $pembelian->tgl_diperiksa = $request->tgl_diperiksa ?? null; // Tanggal pembuatan saat ini
        
        if ($request->hasFile('filedo')) {
            $file = $request->file('filedo');
            $fileName = $request->nopo . date('YmdHis') . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('bukti_do_supplier', $fileName, 'public');
            $pembelian->file_do_suplier = $filePath; // Simpan path file ke dalam model jika ada
        }

        $check1 = $pembelian->save();
    
        // Ambil nomor PO yang baru dibuat
        $no_po = $pembelian->no_po;
    
        // Simpan data produk beli
        $produkIds = $request->produk;
        $qtyKirim = $request->qtykrm;
        $qtyTerima = $request->qtytrm;
        $kondisiIds = $request->kondisi;
        $check2 = true;
    
        // Loop untuk setiap produk yang ditambahkan
        foreach ($produkIds as $index => $produkId) {
            $produkBeli = new Produkbeli();
            $produkBeli->pembelian_id = $pembelian->id;
            $produkBeli->produk_id = $produkId;
            $produkBeli->jml_dikirim = $qtyKirim[$index];
            $produkBeli->jml_diterima = $qtyTerima[$index];
            $produkBeli->kondisi_id = $kondisiIds[$index] ?? null;          
            $check2 = $produkBeli->save();

            // $lokasi = Lokasi::find($request->id_lokasi);
            // $produk = Produk::find($produkId)->first();
            // if ($lokasi->tipe_lokasi == 1) {
            //     $checkInven = InventoryGallery::where('kode_produk', $produk->kode)->where('kondisi_id', $kondisiIds[$index])->where('lokasi_id', $lokasi->id)->first();
            //     if($checkInven){
            //         $checkInven->jumlah += $qtyTerima[$index];
            //         $checkInven->update();
            //     } else {
            //         $createProduk = new InventoryGallery();
            //         $createProduk->kode_produk = $produk->kode;
            //         $createProduk->kondisi_id = $kondisiIds[$index];
            //         $createProduk->jumlah = $qtyTerima[$index];
            //         $createProduk->lokasi_id = $lokasi->id;
            //     }
            // } 
        }
    
        // Periksa keberhasilan penyimpanan data
        if (!$check1 || !$check2) {
            return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
        } else {
            return redirect(route('pembelian.index'))->with('success', 'Data pembelian berhasil disimpan. Nomor PO: ' . $no_po);
        }
    }
    
    public function store_inden(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'nopo' => 'required',
            'id_supplier' => 'required',
            'bulan_inden' => 'required',
            // 'status_dibuat' => 'required',
            // Tambahkan validasi lainnya sesuai kebutuhan
        ]);
    
        // Periksa apakah validasi gagal
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return redirect()->back()->withInput()->with('fail', $errors);
        }
    
        $existingProduct = ModelsPoinden::where('supplier_id', $request->id_supplier)
        ->where('bulan_inden', $request->bulan_inden)
        ->first();

        if ($existingProduct) {
        return redirect()->back()->withInput()->with('fail', 'Data sudah ada.');
        }

        // Simpan data pembelian
        $pembelian = new ModelsPoinden();
        $pembelian->no_po = $request->nopo;
        $pembelian->supplier_id = $request->id_supplier;
        $pembelian->bulan_inden = $request->bulan_inden;
        $pembelian->pembuat = $request->pembuat; // ID pengguna yang membuat pembelian
        $pembelian->pemeriksa = $request->pemeriksa; // ID pengguna yang membuat pembelian
        $pembelian->tgl_dibuat = $request->tgl_dibuat; // Tanggal pembuatan saat ini
        $pembelian->tgl_diperiksa = $request->tgl_diperiksa ?? null; // Tanggal pembuatan saat ini
        $pembelian->status_dibuat = $request->status_dibuat ?? null; // Status pembuatan
        $pembelian->status_diperiksa = $request->status_diperiksa ?? null; // Status pembuatan
        $no_po = $pembelian->no_po;
    
        // Simpan data produk beli
        $kode_indens = $request->kode_inden;
        $kategori= $request->kategori;
        $jumlah = $request->qty;
        $ket = $request->ket;
        $check2 = true;
        
            // Pengecekan duplikasi kode indens
        if (count($kode_indens) !== count(array_unique($kode_indens))) {
            return redirect()->back()->withInput()->with('fail', 'kode inden tidak boleh sama');
        }


        $check1 = $pembelian->save();
    
        // Ambil nomor PO yang baru dibuat
        

        // Loop untuk setiap produk yang ditambahkan
        foreach ($kode_indens as $index => $kode_inden) {
            $produkBeli = new Produkbeli();
            $produkBeli->poinden_id = $pembelian->id;
            $produkBeli->kode_produk_inden= $kode_inden;
            $produkBeli->produk_id= $kategori[$index];
            $produkBeli->jumlahInden = $jumlah[$index];
            $produkBeli->keterangan = $ket[$index];
            // Tambahkan atribut lainnya sesuai kebutuhan
            $check2 = $produkBeli->save();
        }
    
        // Periksa keberhasilan penyimpanan data
        if (!$check1 || !$check2) {
            return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
        } else {
            return redirect(route('pembelian.index'))->with('success', 'Data pembelian berhasil disimpan. Nomor PO: ' . $no_po);
        }
    }

    public function store_retur(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'invoicepo_id' => 'required',
            'no_retur' => 'required',
            'tgl_retur' => 'required',
            'komplain' => 'required',
            'subtotal' => 'required',
            // 'total_harga' => 'required',
        ]);

        $error = $validator->errors()->all();
        if ($validator->fails()) return redirect()->back()->withInput()->with('fail', $error);

        $data = $request->except(['_token', '_method', 'file']);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('bukti_retur_pembelian', $fileName, 'public');
            $data['foto'] = $filePath;
        }

        $data['ongkir'] = 0;
        $data['total'] = 0;
        $jenis = $data['komplain'];

        $save = ReturPembelian::create($data);

        if ($save) {
            $newSubtotal = 0;
            for ($i = 0; $i < count($data['nama_produk']); $i++) {
                $produkReturBeli = [
                    'returpembelian_id' => $save->id,
                    'produkbeli_id' => $data['nama_produk'][$i],
                    'alasan' => $data['alasan'][$i],
                    'jumlah' => $data['jumlah'][$i],
                    'harga' => $data['harga_satuan'][$i],
                    'diskon' => $data['diskon'][$i],
                    'totharga' => $data['harga_total'][$i]
                ];
                $newSubtotal += $data['harga_total'][$i];
                $produk_terjual = Produkretur::create($produkReturBeli);

                $diskon = $produk_terjual->jumlah * $produk_terjual->diskon;
                $getProdukBeli = Produkbeli::where('id', $data['nama_produk'][$i])->first();
                $updateproduk = [
                    'type_komplain' => $jenis,
                    'diskon_retur' => $diskon,  
                    'jml_diterima' => $getProdukBeli->jml_diterima - $data['jumlah'][$i],
                    'totalharga' => ($getProdukBeli->jml_diterima - $data['jumlah'][$i]) * ($getProdukBeli->harga - $getProdukBeli->diskon),
                ];
                $newSubtotal += ($getProdukBeli->jml_diterima - $data['jumlah'][$i]) * ($getProdukBeli->harga - $getProdukBeli->diskon);

                $update = Produkbeli::where('id', $data['nama_produk'][$i])->update($updateproduk);

                if (!$update) {
                    return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
                }
            }

            if($jenis == 'Retur' || $jenis == 'Diskon'){
                // update invoice
                $getInvoice = Invoicepo::find($data['invoicepo_id']);
                $getInvoice->subtotal = $newSubtotal;
                $getInvoice->total_tagihan = $newSubtotal + $getInvoice->biaya_kirim;
                $getInvoice->sisa = $newSubtotal + $getInvoice->biaya_kirim - $getInvoice->dp;
                $check = $getInvoice->update();
                if(!$check) return redirect()->back()->withInput()->with('fail', 'Gagal Update Invoice');
    
                // create po retur
                // $getPO = $getInvoice->pembelian ? Pembelian::find($getInvoice->pembelian_id) : Pembelian::find($getInvoice->poinden_id);

                // if ($getPO) {
                //     $newPO = $getPO->replicate();

                //     $newPO->no_po = $getPO . '/Retur';
                //     $newPO->tgl_kirim = now();
                //     $newPO->tgl_dibuat = $data['tgl_retur'];
                //     $newPO->tgl_dibuat = $data['tgl_retur'];
                    
                //     // Simpan salinan PO baru ke database
                //     $checkPO = $newPO->save();

                //     // Jika ada relasi yang perlu diduplikasi, lakukan hal yang sama untuk relasi tersebut
                //     // $this->duplicateRelations($getPO, $newPO);

                // }
            }
            return redirect()->back()->withInput()->with('success', 'Berhasil Menyimpan Data');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Pembelian  $pembelian
     * @return \Illuminate\Http\Response
     */
    public function show ($datapo, Request $request)
    {

        $type = $request->query('type');
        // return "Type: $type, Datapo: $datapo";
        if ($type === 'pembelian') {
        $beli = Pembelian::find($datapo);
        // return $beli;
        $pembuat = Karyawan::where('user_id', $beli->pembuat)->first()->nama;
        $pembuatjbt = Karyawan::where('user_id', $beli->pembuat)->first()->jabatan;
        $penerima = Karyawan::where('user_id', $beli->penerima)->first()->nama;
        $penerimajbt = Karyawan::where('user_id', $beli->penerima)->first()->jabatan;
        $pemeriksa = Karyawan::where('user_id', $beli->pemeriksa)->first()->nama;
        $pemeriksajbt = Karyawan::where('user_id', $beli->pemeriksa)->first()->jabatan;
        $produkbelis = Produkbeli::where('pembelian_id', $datapo)->get();
        
        return view('purchase.showpo',compact('beli','produkbelis','pembuat','penerima','pemeriksa','pembuatjbt','penerimajbt','pemeriksajbt'));
        }elseif ($type === 'poinden') {
            $beli = ModelsPoinden::find($datapo);
            // return $beli;
            $pembuat = Karyawan::where('user_id', $beli->pembuat)->first()->nama;
            $pembuatjbt = Karyawan::where('user_id', $beli->pembuat)->first()->jabatan;
            $pemeriksa = Karyawan::where('user_id', $beli->pemeriksa)->first()->nama;
            $pemeriksajbt = Karyawan::where('user_id', $beli->pemeriksa)->first()->jabatan;
            $produkbelis = Produkbeli::where('poinden_id', $datapo)->get();
            
            return view('purchase.showpoinden',compact('beli','produkbelis','pembuat','pemeriksa','pembuatjbt','pemeriksajbt'));
           
        }
     }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Pembelian  $pembelian
     * @return \Illuminate\Http\Response
     */
    

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Pembelian  $pembelian
     * @return \Illuminate\Http\Response
     */
    public function update_invoice(Request $request, Pembelian $pembelian, $idinv)
    {
        $validator = Validator::make($request->all(), [
            'status_dibukukan' => 'required',
            'tgl_dibukukan' => 'required|date',
        ]);
    
        // Periksa apakah validasi gagal
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return redirect()->back()->withInput()->with('fail', $errors);
        }

        $data = $request->only(['status_dibukukan', 'tgl_dibukukan']);

        $getInvoice = Invoicepo::find($idinv);
        $getInvoice->status_dibuku = $data['status_dibukukan'];
        $getInvoice->tgl_dibukukan = $data['tgl_dibukukan'];
        $getInvoice->pembuku = Auth::user()->id;
        $check = $getInvoice->update();

        if($getInvoice->pembelian()->exists()){
            $tipe = 'pembelian';
            $datapo = $getInvoice->pembelian_id;
        } else if($getInvoice->poinden()->exists()){
            $tipe = 'poinden';
            $datapo = $getInvoice->poinden_id;
        }

        if(!$check) return redirect()->back()->withInput()->with('fail', 'Gagal update pembukuan');
        return redirect(route('invoice.show', ['datapo' => $datapo, 'type' => $tipe]))->with('success', 'Berhasil update pembuku');
    }

    public function gambarpo_update(Request $req, Pembelian $datapo)
    {
        if ($req->hasFile('file')) {
            $file = $req->file('file');
            $fileName = $datapo->no_po . date('YmdHis') . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('bukti_do_supplier', $fileName, 'public');
            $datapo->file_do_suplier = $filePath; // Simpan path file ke dalam model jika ada
            $datapo->save(); // Simpan perubahan pada model
    
            return redirect()->back()->with('success', 'File tersimpan');
        } else {
            return redirect()->back()->with('fail', 'Gagal menyimpan file');
        }
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Pembelian  $pembelian
     * @return \Illuminate\Http\Response
     */
    public function destroy(Pembelian $pembelian)
    {
        //
    }

    public function createinvoice($type, $datapo)
    {
        if ($type === 'pembelian') {
            // Jika type adalah pembelian (Purchase Order)
            $beli = Pembelian::find($datapo);
            if ($beli) {
                $produkbelis = Produkbeli::where('pembelian_id', $datapo)->get();
                $produkkomplains = Produkretur::whereHas('produk', function($q) use($datapo){
                    $q->where('pembelian_id', $datapo);
                })->get();
                $rekenings = Rekening::all();
                $no_invpo = $this->generatebayarPONumber();
                // $nomor_inv = $this->generateINVPONumber();

                return view('purchase.createinv', compact('beli', 'produkbelis', 'rekenings', 'no_invpo', 'produkkomplains'));
            }
        } elseif ($type === 'poinden') {
            // Jika type adalah poinden (Inden Order)
            $beli = ModelsPoinden::find($datapo);
            if ($beli) {
                $produkbelis = Produkbeli::where('poinden_id', $datapo)->get();
                $rekenings = Rekening::all();
                $no_invpo = $this->generatebayarPONumber();
                // $nomor_inv = $this->generateINVPONumber();

                return view('purchase.createinvinden', compact('beli', 'produkbelis', 'rekenings', 'no_invpo'));
            }
        }

        // Jika tidak ditemukan di kedua tabel
        return redirect()->back()->withErrors('ID Purchase Order atau Inden Order tidak valid.');
    }


    public function storeinvoice(Request $request)
    {
        $no_po = $request->input('no_po');
        $type = $request->input('type');

        if ($type === 'pembelian') {
        $umum = Pembelian::where('no_po', $no_po)->first();
        if ($umum) {
            
            $validator = Validator::make($request->all(), [
                'id_po' => 'required',
                'no_inv' => 'required',
                'tgl_inv' => 'required',
                'sub_total' => 'required',
                'total_tagihan' => 'required',
                // Tambahkan validasi lainnya sesuai kebutuhan
            ]);
        
            // Periksa apakah validasi gagal
            if ($validator->fails()) {
                $errors = $validator->errors()->all();
                return redirect()->back()->withInput()->with('fail', $errors);
            }
    
            $inv = new Invoicepo();
            $idpo = $inv->pembelian_id = $request->id_po;
            // $inv->poinden_id = $request->nopo;
            $inv->tgl_inv = $request->tgl_inv;
            $no_inv = $inv->no_inv = $request->no_inv;
    
            $inv->pembuat = $request->pembuat;
            $inv->status_dibuat = $request->status_dibuat;
            $inv->pembuku = $request->pembuku ?? null;
            $inv->status_dibuku = $request->status_dibuku ?? null;
            $inv->tgl_dibuat = $request->tgl_dibuat;
            $inv->tgl_dibukukan = $request->tgl_dibukukan ?? null;
    
            $subtot = $inv->subtotal = $request->sub_total;
            $disk = $inv->diskon = $request->diskon_total;
            if ($request->persen_ppn == null ) {
               $inv->ppn = 0;
            } else { 
                $persen_ppn =  $request->persen_ppn;
                $inv->ppn = $persen_ppn/100 * ($subtot-$disk);
            }
            
    
            $inv->biaya_kirim = $request->biaya_ongkir;
            $inv->total_tagihan = $request->total_tagihan;
            $inv->dp = 0;
            $inv->sisa = $request->total_tagihan;
    
            $check1 = $inv->save();
    
            //kumpulan produkbeli dengan id tersebut
            // $produks = Produkbeli::where('pembelian_id', $idpo)->get();
    
            $produkIds = $request->input('id');
            $hargas = $request->input('harga');
            $diskons = $request->input('diskon');
            $jumlahs = $request->input('jumlah');
        
            // Loop melalui setiap produk untuk update
            foreach ($produkIds as $index => $produkId) {
                // Temukan Produkbeli berdasarkan id
                $produkbeli = Produkbeli::findOrFail($produkId);
        
                // Update harga, diskon, dan jumlah
                $produkbeli->harga = $hargas[$index];
                $produkbeli->diskon = $diskons[$index];
                $produkbeli->totalharga = $jumlahs[$index];
        
                // Simpan perubahan
                $check2 = $produkbeli->save();
            }
        
    
            // Periksa keberhasilan penyimpanan data
            if (!$check1 || !$check2) {
                return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
            } else {
                return redirect()->route('invoice.edit',  ['datapo' => $idpo, 'type' => $type])->with('success', 'Data pembelian berhasil disimpan. Nomor Invoice: ' . $no_inv);        }

       
        }
        } elseif ($type === 'poinden') {
        $inden = ModelsPoinden::where('no_po', $no_po)->first();
        if ($inden) {

            $validator = Validator::make($request->all(), [
                'id_po' => 'required',
                'no_inv' => 'required',
                'tgl_inv' => 'required',
                'sub_total' => 'required',
                'total_tagihan' => 'required',
                // Tambahkan validasi lainnya sesuai kebutuhan
            ]);
        
            // Periksa apakah validasi gagal
            if ($validator->fails()) {
                $errors = $validator->errors()->all();
                return redirect()->back()->withInput()->with('fail', $errors);
            }
    
            $inv = new Invoicepo();
            $idpo = $inv->poinden_id = $request->id_po;
            // $inv->poinden_id = $request->nopo;
            $inv->tgl_inv = $request->tgl_inv;
            $no_inv = $inv->no_inv = $request->no_inv;
    
            $inv->pembuat = $request->pembuat;
            $inv->status_dibuat = $request->status_dibuat;
            $inv->pembuku = $request->pembuku;
            $inv->status_dibuku = $request->status_dibuku;
            $inv->tgl_dibuat = $request->tgl_dibuat;
            $inv->tgl_dibukukan = $request->tgl_dibukukan;
    
            $subtot = $inv->subtotal = $request->sub_total;
            $disk = $inv->diskon = $request->diskon_total;
            if ($request->persen_ppn == null ) {
               $inv->ppn = 0;
            } else { 
                $persen_ppn =  $request->persen_ppn;
                $inv->ppn = $persen_ppn/100 * ($subtot-$disk);
            }
            
    
            // $inv->biaya_kirim = $request->biaya_ongkir;
            $inv->total_tagihan = $request->total_tagihan;
            $inv->dp = 0;
            $inv->sisa = $request->total_tagihan;
    
            $check1 = $inv->save();
    
            //kumpulan produkbeli dengan id tersebut
            // $produks = Produkbeli::where('pembelian_id', $idpo)->get();
    
            $produkIds = $request->input('id');
            $hargas = $request->input('harga');
            $diskons = $request->input('diskon');
            $jumlahs = $request->input('jumlah');
        
            // Loop melalui setiap produk untuk update
            foreach ($produkIds as $index => $produkId) {
                // Temukan Produkbeli berdasarkan id
                $produkbeli = Produkbeli::findOrFail($produkId);
        
                // Update harga, diskon, dan jumlah
                $produkbeli->harga = $hargas[$index];
                $produkbeli->diskon = $diskons[$index];
                $produkbeli->totalharga = $jumlahs[$index];
        
                // Simpan perubahan
                $check2 = $produkbeli->save();
            }
        
    
            // Periksa keberhasilan penyimpanan data
            if (!$check1 || !$check2) {
                return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
            } else {
                return redirect()->route('invoice.edit',  ['datapo' => $idpo, 'type' => $type])->with('success', 'Data pembelian berhasil disimpan. Nomor Invoice: ' . $no_inv);  
             }

        }
        } else {
            return redirect()->back()->withErrors('Tipe tidak valid.');
        }
        

    }

    public function edit_invoice ($datapo, Request $request)
    {

        $type = $request->query('type');

        if ($type === 'pembelian') {
            $inv_po = Invoicepo::where('pembelian_id', $datapo)->first();
            $diskonTot = Produkbeli::where('pembelian_id', $datapo)->get();
            // return $diskonTot;

            $totalDiskon = 0;

            foreach ($diskonTot as $item) {
                $totalDiskon += $item->jml_diterima * $item->diskon;
            }

            $totalDis = formatRupiah2($totalDiskon);

            // dd($inv_po);
            $id_po = $inv_po->pembelian_id;
            $databayars = Pembayaran::where('invoice_purchase_id', $inv_po->id)->get()->sortByDesc('created_at');
            $produkbelis = Produkbeli::where('pembelian_id', $id_po)->get();
            $beli = Pembelian::find($id_po);

            $pembuat = Karyawan::where('user_id', $inv_po->pembuat)->first()->nama;
            $pembuatjbt = Karyawan::where('user_id', $inv_po->pembuat)->first()->jabatan;
            $pembuku = Karyawan::where('user_id', $inv_po->pembuku)->first()->nama;
            $pembukujbt = Karyawan::where('user_id', $inv_po->pembuku)->first()->jabatan;
            $rekenings = Rekening::all();
            $no_bypo = $this->generatebayarPONumber();
            $nomor_inv = $this->generateINVPONumber();

            //riwayat

            $riwayatPembelian = Activity::where('subject_type', Invoicepo::class)->where('subject_id', $inv_po->id)->orderBy('id', 'desc')->get();
            $riwayatPembayaran = Activity::where('subject_type', Pembayaran::class)->orderBy('id', 'desc')->get();
            $produkIds = [$inv_po->id];
            $filteredRiwayat = $riwayatPembayaran->filter(function (Activity $activity) use ($produkIds) {
                $properties = json_decode($activity->properties, true);
                return isset($properties['attributes']['invoice_purchase_id']) && in_array($properties['attributes']['invoice_purchase_id'], $produkIds);
            });
            
            $mergedriwayat = [
                'pembelian' => $riwayatPembelian,
                'pembayaran' => $filteredRiwayat
            ];
            
            $riwayat = collect($mergedriwayat)
                ->flatMap(function ($riwayatItem, $jenis) {
                    return $riwayatItem->map(function ($item) use ($jenis) {
                        $item->jenis = $jenis;
                        return $item;
                    });
                })
                ->sortByDesc('id')
                ->values()
                ->all();


            // dd($riwayat);
                

            return view('purchase.editinv', compact('riwayat','totalDis','inv_po', 'produkbelis', 'beli', 'rekenings', 'no_bypo', 'nomor_inv', 'databayars', 'pembuat', 'pembuku', 'pembuatjbt', 'pembukujbt'));

        } elseif ($type === 'poinden') {
            $inv_po = Invoicepo::where('poinden_id', $datapo)->first();
            $diskonTot = Produkbeli::where('poinden_id', $datapo)->get();
            // return $diskonTot;

            $totalDiskon = 0;

            foreach ($diskonTot as $item) {
                $totalDiskon += $item->jumlahInden * $item->diskon;
            }

            $totalDis = formatRupiah2($totalDiskon);

            // dd($inv_po);
            // return $inv_po;
            $id_po = $inv_po->poinden_id;
            $databayars = Pembayaran::where('invoice_purchase_id', $inv_po->id)->get()->sortByDesc('created_at');
            $produkbelis = Produkbeli::where('poinden_id', $id_po)->get();
            $beli = ModelsPoinden::find($id_po);

            $pembuat = Karyawan::where('user_id', $inv_po->pembuat)->first()->nama;
            $pembuatjbt = Karyawan::where('user_id', $inv_po->pembuat)->first()->jabatan;
            $pembuku = Karyawan::where('user_id', $inv_po->pembuku)->first()->nama;
            $pembukujbt = Karyawan::where('user_id', $inv_po->pembuku)->first()->jabatan;
            $rekenings = Rekening::all();
            $no_bypo = $this->generatebayarPONumber();
            $nomor_inv = $this->generateINVPONumber();

            //riwayat
            // dd($inv_po->id);

            $riwayatPembelian = Activity::where('subject_type', Invoicepo::class)->where('subject_id', $inv_po->id)->orderBy('id', 'desc')->get();
            $riwayatPembayaran = Activity::where('subject_type', Pembayaran::class)->orderBy('id', 'desc')->get();
            $produkIds = [$inv_po->id];
            // dd($produkIds);
            $filteredRiwayat = $riwayatPembayaran->filter(function (Activity $activity) use ($produkIds) {
                $properties = json_decode($activity->properties, true);
                return isset($properties['attributes']['invoice_purchase_id']) && in_array($properties['attributes']['invoice_purchase_id'], $produkIds);
            });
            
            $mergedriwayat = [
                'pembelian' => $riwayatPembelian,
                'pembayaran' => $filteredRiwayat
            ];
            
            $riwayat = collect($mergedriwayat)
                ->flatMap(function ($riwayatItem, $jenis) {
                    return $riwayatItem->map(function ($item) use ($jenis) {
                        $item->jenis = $jenis;
                        return $item;
                    });
                })
                ->sortByDesc('id')
                ->values()
                ->all();

            return view('purchase.editinvinden', compact('riwayat','totalDis','inv_po', 'produkbelis', 'beli', 'rekenings', 'no_bypo', 'nomor_inv', 'databayars', 'pembuat', 'pembuku', 'pembuatjbt', 'pembukujbt'));

        } else {
            return redirect()->back()->withErrors('Tipe tidak valid.');
        }


    }

    public function show_invoice ($datapo, Request $request)
    {

        $type = $request->query('type');

        if ($type === 'pembelian') {
            $inv_po = Invoicepo::where('pembelian_id', $datapo)->first();
            $diskonTot = Produkbeli::where('pembelian_id', $datapo)->get();
            // return $diskonTot;

            $totalDiskon = 0;

            foreach ($diskonTot as $item) {
                $totalDiskon += $item->jml_diterima * $item->diskon;
            }

            $totalDis = formatRupiah2($totalDiskon);

            $id_po = $inv_po->pembelian_id;
            $databayars = Pembayaran::where('invoice_purchase_id', $inv_po->id)->get()->sortByDesc('created_at');
            $produkbelis = Produkbeli::with('produkretur')->where('pembelian_id', $id_po)->get();
            $beli = Pembelian::find($id_po);
            

            $pembuat = Karyawan::where('user_id', $inv_po->pembuat)->first()->nama;
            $pembuatjbt = Karyawan::where('user_id', $inv_po->pembuat)->first()->jabatan;
            $pembuku = Karyawan::where('user_id', $inv_po->pembuku)->first()->nama;
            $pembukujbt = Karyawan::where('user_id', $inv_po->pembuku)->first()->jabatan;
            $rekenings = Rekening::all();
            $no_bypo = $this->generatebayarPONumber();
            $nomor_inv = $this->generateINVPONumber();

            //riwayat

            $riwayatPembelian = Activity::where('subject_type', Invoicepo::class)->where('subject_id', $inv_po->id)->orderBy('id', 'desc')->get();
            $riwayatPembayaran = Activity::where('subject_type', Pembayaran::class)->orderBy('id', 'desc')->get();
            $produkIds = [$inv_po->id];
            // dd($produkIds);
            $filteredRiwayat = $riwayatPembayaran->filter(function (Activity $activity) use ($produkIds) {
                $properties = json_decode($activity->properties, true);
                return isset($properties['attributes']['invoice_purchase_id']) && in_array($properties['attributes']['invoice_purchase_id'], $produkIds);
            });
            
            $mergedriwayat = [
                'pembelian' => $riwayatPembelian,
                'pembayaran' => $filteredRiwayat
            ];
            
            $riwayat = collect($mergedriwayat)
                ->flatMap(function ($riwayatItem, $jenis) {
                    return $riwayatItem->map(function ($item) use ($jenis) {
                        $item->jenis = $jenis;
                        return $item;
                    });
                })
                ->sortByDesc('id')
                ->values()
                ->all();
                

            return view('purchase.showinv', compact('riwayat','totalDis','inv_po', 'produkbelis', 'beli', 'rekenings', 'no_bypo', 'nomor_inv', 'databayars', 'pembuat', 'pembuku', 'pembuatjbt', 'pembukujbt'));

        } elseif ($type === 'poinden') {
            $inv_po = Invoicepo::where('poinden_id', $datapo)->first();
            $diskonTot = Produkbeli::where('poinden_id', $datapo)->get();
            // return $diskonTot;

            $totalDiskon = 0;

            foreach ($diskonTot as $item) {
                $totalDiskon += $item->jumlahInden * $item->diskon;
            }

            $totalDis = formatRupiah2($totalDiskon);

            // return $inv_po;
            $id_po = $inv_po->poinden_id;
            $databayars = Pembayaran::where('invoice_purchase_id', $inv_po->id)->get()->sortByDesc('created_at');
            $produkbelis = Produkbeli::where('poinden_id', $id_po)->get();
            $beli = ModelsPoinden::find($id_po);

            $pembuat = Karyawan::where('user_id', $inv_po->pembuat)->first()->nama;
            $pembuatjbt = Karyawan::where('user_id', $inv_po->pembuat)->first()->jabatan;
            $pembuku = Karyawan::where('user_id', $inv_po->pembuku)->first()->nama;
            $pembukujbt = Karyawan::where('user_id', $inv_po->pembuku)->first()->jabatan;
            $rekenings = Rekening::all();
            $no_bypo = $this->generatebayarPONumber();
            $nomor_inv = $this->generateINVPONumber();

            //riwayat
            // dd($inv_po->id);

            $riwayatPembelian = Activity::where('subject_type', Invoicepo::class)->where('subject_id', $inv_po->id)->orderBy('id', 'desc')->get();
            $riwayatPembayaran = Activity::where('subject_type', Pembayaran::class)->orderBy('id', 'desc')->get();
            $produkIds = [$inv_po->id];
            // dd($produkIds);
            $filteredRiwayat = $riwayatPembayaran->filter(function (Activity $activity) use ($produkIds) {
                $properties = json_decode($activity->properties, true);
                return isset($properties['attributes']['invoice_purchase_id']) && in_array($properties['attributes']['invoice_purchase_id'], $produkIds);
            });
            
            $mergedriwayat = [
                'pembelian' => $riwayatPembelian,
                'pembayaran' => $filteredRiwayat
            ];
            
            $riwayat = collect($mergedriwayat)
                ->flatMap(function ($riwayatItem, $jenis) {
                    return $riwayatItem->map(function ($item) use ($jenis) {
                        $item->jenis = $jenis;
                        return $item;
                    });
                })
                ->sortByDesc('id')
                ->values()
                ->all();

            return view('purchase.showinvinden', compact('riwayat','totalDis','inv_po', 'produkbelis', 'beli', 'rekenings', 'no_bypo', 'nomor_inv', 'databayars', 'pembuat', 'pembuku', 'pembuatjbt', 'pembukujbt'));

        } else {
            return redirect()->back()->withErrors('Tipe tidak valid.');
        }
    }

    public function po_edit ($datapo, Request $request)
    {

        $type = $request->query('type');
        
        if ($type === 'pembelian') {
            
            $produks = Produk::get();
            $suppliers = Supplier::where('tipe_supplier','tradisional')->get();
            $lokasis = Lokasi::get();
            $kondisis = Kondisi::get();
            $beli = Pembelian::find($datapo);

            // return $beli;
            $pembuat = Karyawan::where('user_id', $beli->pembuat)->first()->nama;
            $pembuatjbt = Karyawan::where('user_id', $beli->pembuat)->first()->jabatan;
            $penerima = Karyawan::where('user_id', $beli->penerima)->first()->nama;
            $penerimajbt = Karyawan::where('user_id', $beli->penerima)->first()->jabatan;
            $pemeriksa = Karyawan::where('user_id', $beli->pemeriksa)->first()->nama;
            $pemeriksajbt = Karyawan::where('user_id', $beli->pemeriksa)->first()->jabatan;
            $produkbelis = Produkbeli::where('pembelian_id', $datapo)->get();
            
            return view('purchase.editpo',compact('produks','suppliers','lokasis','kondisis','beli','produkbelis','pembuat','penerima','pemeriksa','pembuatjbt','penerimajbt','pemeriksajbt'));
        
        } elseif ($type === 'poinden') {
            
            
            $produks = Produk::get();
            $suppliers = Supplier::where('tipe_supplier','inden')->get();
            $lokasis = Lokasi::get();
            $kondisis = Kondisi::get();

            $beli = ModelsPoinden::find($datapo);
            // return $beli;
            // return $beli;
            $pembuat = Karyawan::where('user_id', $beli->pembuat)->first()->nama;
            $pembuatjbt = Karyawan::where('user_id', $beli->pembuat)->first()->jabatan;
            // $penerima = Karyawan::where('user_id', $beli->penerima)->first()->nama;
            // $penerimajbt = Karyawan::where('user_id', $beli->penerima)->first()->jabatan;
            $pemeriksa = Karyawan::where('user_id', $beli->pemeriksa)->first()->nama;
            $pemeriksajbt = Karyawan::where('user_id', $beli->pemeriksa)->first()->jabatan;
            $produkbelis = Produkbeli::where('poinden_id', $datapo)->get();
            
            return view('purchase.editpoinden',compact('produks','suppliers','lokasis','kondisis','beli','produkbelis','pembuat','pemeriksa','pembuatjbt','pemeriksajbt'));

        } else {
            return redirect()->back()->withErrors('Tipe tidak valid.');
        }


    }

    public function po_update(Request $request, $datapo)
    {
        // dd($request->all());
        // Validasi input

        $type = $request->type;
        // return $type;

        if ($type === 'pembelian') {

            $validator = Validator::make($request->all(), [
                'tgl_diterima' => 'required|date',
                'kode' => 'required|array',
                'kode.*' => 'required|string',
                'nama' => 'required|array',
                'nama.*' => 'required|string',
                'qtykrm' => 'required|array',
                'qtykrm.*' => 'required|integer',
                'qtytrm' => 'required|array',
                'qtytrm.*' => 'required|integer',
                'kondisi' => 'required|array',
                'kondisi.*' => 'required|exists:kondisis,id',
            ]);
            
            // Periksa apakah validasi gagal
            if ($validator->fails()) {
                $errors = $validator->errors()->all();
                return redirect()->back()->withInput()->with('fail', $errors);
            }
    
        // Cari pembelian berdasarkan ID
        $pembelian = Pembelian::find($datapo);
        if (!$pembelian) {
            return redirect()->back()->with('fail', 'Pembelian tidak ditemukan');
        }
    
        // Update data pembelian

        // $pembelian->no_po = $request->nopo;
        // $pembelian->supplier_id = $request->id_supplier;
        // $pembelian->lokasi_id = $request->id_lokasi;
        // $pembelian->tgl_kirim = $request->tgl_kirim;
        $pembelian->no_do_suplier = $request->no_do ?? '';
        
        $pembelian->tgl_diterima = $request->tgl_diterima;
        $pembelian->status_diterima = $request->status_diterima;
        $pembelian->tgl_diterima_ttd = $request->tgl_diterima_ttd;
        $pembelian->status_diperiksa = $request->status_diperiksa;
        $pembelian->tgl_diperiksa= $request->tgl_diperiksa; // Update tgl_diterima_ttd juga jika diperlukan
    

        if ($request->hasFile('bukti')) {
            $file = $request->file('bukti');
            $fileName = $pembelian->no_po . date('YmdHis') . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('bukti_do_supplier', $fileName, 'public');
            $pembelian->file_do_suplier = $filePath; // Simpan path file ke dalam model jika ada
        }

        $check1 = $pembelian->save();
    
        $produkIds = $request->id;
        $kode = $request->kode;
        $qtyTerima = $request->qtytrm;
        $kondisiIds = $request->kondisi;
        $check2 = true;
        
        foreach ($produkIds as $index => $produkId) {
            $produkBeli = Produkbeli::find($produkId);
        
            if ($produkBeli) {
                $produkBeli->jml_diterima = $qtyTerima[$index];
                $produkBeli->kondisi_id = $kondisiIds[$index];
                $check2 = $produkBeli->save();
        
                $lokasi = Lokasi::find($pembelian->lokasi_id);
                $produk = Produk::where('kode', $kode[$index])->first();
        
                if ($lokasi && $produk) {
                    if ($lokasi->tipe_lokasi == 1) {
                        $checkInven = InventoryGallery::where('kode_produk', $produk->kode)
                            ->where('kondisi_id', $kondisiIds[$index])
                            ->where('lokasi_id', $lokasi->id)
                            ->first();
                        if ($checkInven) {
                            $checkInven->jumlah += $qtyTerima[$index];
                            $checkInven->update();
                        } else {
                            $createProduk = new InventoryGallery();
                            $createProduk->kode_produk = $produk->kode;
                            $createProduk->kondisi_id = $kondisiIds[$index];
                            $createProduk->jumlah = $qtyTerima[$index];
                            $createProduk->lokasi_id = $lokasi->id;
                            $createProduk->save();
                        }
                    } elseif ($lokasi->tipe_lokasi == 3) {
                        $checkInven = InventoryGreenHouse::where('kode_produk', $produk->kode)
                            ->where('kondisi_id', $kondisiIds[$index])
                            ->where('lokasi_id', $lokasi->id)
                            ->first();
                        if ($checkInven) {
                            $checkInven->jumlah += $qtyTerima[$index];
                            $checkInven->update();
                        } else {
                            $createProduk = new InventoryGreenHouse();
                            $createProduk->kode_produk = $produk->kode;
                            $createProduk->kondisi_id = $kondisiIds[$index];
                            $createProduk->jumlah = $qtyTerima[$index];
                            $createProduk->lokasi_id = $lokasi->id;
                            $createProduk->save();
                        }
                    }
                }
            } else {
                $check2 = false;
            }
        }
            if (!$check1 || !$check2) {
                return redirect()->back()->withInput()->with('fail', 'Gagal mengupdate data');
            } else {
                return redirect(route('pembelian.show', ['datapo' => $datapo, 'type'=>'pembelian']))->with('success', 'Data pembelian berhasil diupdate. Nomor PO: ' . $pembelian->no_po);
            }
        
        }elseif ($type === 'poinden') {
            $validator = Validator::make($request->all(), [
                'kode_inden' => 'required|array',
                'kode_inden.*' => 'required|string',
                'kategori' => 'required|array',
                'kategori.*' => 'required|string',
                'kode' => 'required|array',
                'kode.*' => 'required|string',
                'qty' => 'required|array',
                'qty.*' => 'required|integer',
            ]);
        
            if ($validator->fails()) {
                $errors = $validator->errors()->all();
                return redirect()->back()->withInput()->with('fail', $errors);
            }
        
            $pembelian = ModelsPoinden::find($datapo);
            if (!$pembelian) {
                return redirect()->back()->with('fail', 'Pembelian tidak ditemukan');
            }
        
            // $pembelian->supplier_id = $request->id_supplier;
            $pembelian->bulan_inden = $request->bulan_inden;
            $pembelian->status_diperiksa = $request->status_diperiksa;
            $pembelian->tgl_diperiksa = $request->tgl_diperiksa;
            $pembelian->save();
        
            $existingProdukBeli = $pembelian->produkbeli()->pluck('id')->toArray();
        
            $indenid = $request->id;
            $kodeProduk = $request->kode;
            $kodeInden = $request->kode_inden;
            $kategori = $request->kategori;
            $jumlah = $request->qty;
            $keterangan = $request->ket;
        
            foreach ($indenid as $index => $item) {
                $produkBeliData = [
                    'produk_id' => $kategori[$index],
                    'kode_produk_inden' => $kodeInden[$index],
                    'jumlahInden' => $jumlah[$index],
                    'keterangan' => $keterangan[$index],
                ];
        
                if (isset($existingProdukBeli[$index])) {
                    $produkBeli = ProdukBeli::find($existingProdukBeli[$index]);
                    if ($produkBeli) {
                        $produkBeli->update($produkBeliData);
                    }
                } else {
                    $pembelian->produkbeli()->create($produkBeliData);
                }
            }

            $produkbelis = Produkbeli::where('poinden_id', $datapo )->get();
            foreach ($produkbelis as $index => $produkbeli) {
                $kode = $produkbeli->produk->kode;
                $kode_inden = $produkbeli->kode_produk_inden;
                $jumlah = $produkbeli->jumlahInden;
                // return $kode;


                 $inventoryInden = InventoryInden::where('kode_produk',$kode )
                                ->where('supplier_id', $pembelian->supplier_id)
                                ->where('bulan_inden', $pembelian->bulan_inden)
                                ->where('kode_produk_inden', $kode_inden)
                                ->first();

                // return $inventoryInden;

                if ($inventoryInden) {
                        // Update existing InventoryInden
                        $inventoryInden->jumlah += $jumlah;
                        $inventoryInden->save();
                } else {
                // Create new InventoryInden
                    InventoryInden::create([
                            'supplier_id' => $pembelian->supplier_id,
                            'kode_produk_inden' => $kode_inden,
                            'bulan_inden' => $pembelian->bulan_inden,
                            'kode_produk' => $kode,
                            'jumlah' => $jumlah,
                    ]);
                }
            }
            
            return redirect()->route('pembelian.show', ['datapo' => $datapo, 'type'=>'poinden'])->with('success', 'Data pembelian berhasil diupdate');
        }

    }

    public function show_retur ($retur_id, Request $request)
    {
    }

    public function show_returinv ($retur_id, Request $request)
    {
    }


   

}
