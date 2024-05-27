<?php

namespace App\Http\Controllers;

use App\Models\Invoice_po;
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
use Poinden;
use SebastianBergmann\Invoker\Invoker;

class PembelianController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $datapos = Pembelian::orderBy('created_at', 'desc')->get();
        $datainden = ModelsPoinden::orderBy('created_at', 'desc')->get();
        $datainv = Invoicepo::get();
       
        // $databayars = Pembayaran::where('invoice_purchase_id', $id_inv)->where('status_bayar','LUNAS')->get();

        return view('purchase.index',compact('datapos','datainv','datainden'));
    }

    public function index_retur()
    {
        $dataretur = Returpembelian::orderBy('created_at', 'desc')->get();
        $datainv = Invoicepo::get();
       
        // $databayars = Pembayaran::where('invoice_purchase_id', $id_inv)->where('status_bayar','LUNAS')->get();

        return view('purchase.returindex',compact('dataretur','datainv'));
    }

    public function invoice()
    {
       
        $invoices = Invoicepo::whereNull('poinden_id')
            ->orderBy('tgl_inv', 'desc')
            ->get();

        $invoiceinden = Invoicepo::whereNotNull('poinden_id')
            ->orderBy('tgl_inv', 'desc')
            ->get();
        return view('purchase.invoice', compact('invoices','invoiceinden'));

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
        $lokasis = Lokasi::get();
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

    public function create_retur()
    {
        $produks = Produk::get();
        $suppliers = Supplier::where('tipe_supplier','inden')->get();
        $nomor_poinden = $this->generatePOIndenNumber();
        return view('purchase.createretur', compact('produks','suppliers','nomor_poinden'));

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
            'tgl_diterima' => 'required|date',
            'no_do' => 'required',
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
        $pembelian->no_do_suplier = $request->no_do;
        $pembelian->pembuat = $request->pembuat; // ID pengguna yang membuat pembelian
        $pembelian->pemeriksa = $request->pemeriksa; // ID pengguna yang membuat pembelian
        $pembelian->penerima = $request->penerima; // ID pengguna yang membuat pembelian
        $pembelian->status_dibuat = $request->status_dibuat; // Status pembuatan
        $pembelian->status_diterima = $request->status_diterima; // Status pembuatan
        $pembelian->status_diperiksa = $request->status_diperiksa; // Status pembuatan
       
        $pembelian->tgl_dibuat = $request->tgl_dibuat; // Tanggal pembuatan saat ini
        $pembelian->tgl_diterima_ttd = $request->tgl_diterima; // Tanggal pembuatan saat ini
        $pembelian->tgl_diperiksa = $request->tgl_diperiksa; // Tanggal pembuatan saat ini
        
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
            $produkBeli->kondisi_id = $kondisiIds[$index];
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
    
        // Simpan data pembelian
        $pembelian = new ModelsPoinden();
        $pembelian->no_po = $request->nopo;
        $pembelian->supplier_id = $request->id_supplier;
        $pembelian->bulan_inden = $request->bulan_inden;
        $pembelian->pembuat = $request->pembuat; // ID pengguna yang membuat pembelian
        $pembelian->pemeriksa = $request->pemeriksa; // ID pengguna yang membuat pembelian
        $pembelian->tgl_dibuat = $request->tgl_dibuat; // Tanggal pembuatan saat ini
        $pembelian->tgl_diperiksa = $request->tgl_diperiksa; // Tanggal pembuatan saat ini
        $pembelian->status_dibuat = $request->status_dibuat; // Status pembuatan
        $pembelian->status_diperiksa = $request->status_diperiksa; // Status pembuatan
       
        $check1 = $pembelian->save();
    
        // Ambil nomor PO yang baru dibuat
        $no_po = $pembelian->no_po;
    
        // Simpan data produk beli
        $kode_indens = $request->kode_inden;
        $kategori= $request->kategori;
        $jumlah = $request->qty;
        $ket = $request->ket;
        $check2 = true;
    
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

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Pembelian  $pembelian
     * @return \Illuminate\Http\Response
     */
    public function show($datapo)
    {
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
    public function update_invoice(Request $request, Pembelian $pembelian)
    {
        //
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
                $rekenings = Rekening::all();
                $no_invpo = $this->generatebayarPONumber();
                $nomor_inv = $this->generateINVPONumber();

                return view('purchase.createinv', compact('beli', 'produkbelis', 'rekenings', 'no_invpo', 'nomor_inv'));
            }
        } elseif ($type === 'poinden') {
            // Jika type adalah poinden (Inden Order)
            $beli = ModelsPoinden::find($datapo);
            if ($beli) {
                $produkbelis = Produkbeli::where('poinden_id', $datapo)->get();
                $rekenings = Rekening::all();
                $no_invpo = $this->generatebayarPONumber();
                $nomor_inv = $this->generateINVPONumber();

                return view('purchase.createinvinden', compact('beli', 'produkbelis', 'rekenings', 'no_invpo', 'nomor_inv'));
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

        return view('purchase.editinv', compact('inv_po', 'produkbelis', 'beli', 'rekenings', 'no_bypo', 'nomor_inv', 'databayars', 'pembuat', 'pembuku', 'pembuatjbt', 'pembukujbt'));

    } elseif ($type === 'poinden') {
        $inv_po = Invoicepo::where('poinden_id', $datapo)->first();
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

        return view('purchase.editinvinden', compact('inv_po', 'produkbelis', 'beli', 'rekenings', 'no_bypo', 'nomor_inv', 'databayars', 'pembuat', 'pembuku', 'pembuatjbt', 'pembukujbt'));

    } else {
        return redirect()->back()->withErrors('Tipe tidak valid.');
    }

    



        // $inv_po = Invoicepo::where('pembelian_id', $datapo)->first();
        // $id_inv = $inv_po->id;
        // $databayars = Pembayaran::where('invoice_purchase_id', $id_inv)
        //             ->get()
        //             ->sortByDesc('created_at');
        // $pembuat = Karyawan::where('user_id', $inv_po->pembuat)->first()->nama;
        // $pembuatjbt = Karyawan::where('user_id', $inv_po->pembuat)->first()->jabatan;
        // $pembuku = Karyawan::where('user_id', $inv_po->pembuku)->first()->nama;
        // $pembukujbt = Karyawan::where('user_id', $inv_po->pembuku)->first()->jabatan;
        // // return $databayars;
        // $produkbelis = Produkbeli::where('pembelian_id', $datapo)->get();
        // $beli = Pembelian::find($datapo);
        // $rekenings = Rekening::all();
        // $no_bypo = $this->generatebayarPONumber(); 
        // $nomor_inv = $this->generateINVPONumber();

        // return view('purchase.editinv',compact('inv_po','produkbelis','beli','rekenings','no_bypo','nomor_inv', 'databayars','pembuat','pembuku','pembuatjbt','pembukujbt'));

    }
}
