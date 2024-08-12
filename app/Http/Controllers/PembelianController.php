<?php

namespace App\Http\Controllers;

use App\Models\InventoryGallery;
use App\Models\InventoryGreenHouse;
use App\Models\InventoryGudang;
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
        $query = Pembelian::query();
        $dataretur = Returpembelian::get();

        $query->when(Auth::user()->hasRole('AdminGallery'), function($q){
            $q->where('status_dibuat', 'DIKONFIRMASI')
            ->where('lokasi_id', Auth::user()->karyawans->lokasi_id);
        });

        $query->when(Auth::user()->hasRole('Finance'), function($q){
            $q->where('status_dibuat', 'DIKONFIRMASI');
            // ->where('lokasi_id', Auth::user()->karyawans->lokasi_id);
        });
    
    
        $query->when(Auth::user()->hasRole('Auditor'), function($q){
            $q->where('status_dibuat', 'DIKONFIRMASI')
            ->where(function($query) {
                $query->where('status_diterima', 'DIKONFIRMASI')
                ->orWhere('status_diterima', '-')
                        ->orWhere(function($subQuery) {
                            $subQuery->whereNull('status_diterima')
                                    ->whereHas('lokasi.tipe', function($lokasiQuery) {
                                        $lokasiQuery->whereIn('tipe_lokasi', [3, 4]);
                                    });
                        });
            });
        });
    
        $query->orderBy('created_at', 'desc');
        
        // Execute the query
        $pembelian = $query->get();

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
            } elseif($req->status == 'Belum Lunas') {
                $query->whereHas('invoice', function($q) {
                    $q->where('sisa', '!=', 0)
                    ->where('status_dibuat', '!=', 'BATAL');
                });
            } elseif($req->status == 'Belum Ada Tagihan') {
                $query->whereDoesntHave('invoice');
            } elseif($req->status == 'Invoice Batal') {
                $query->whereHas('invoice', function($q) {
                    $q->where('status_dibuat', 'BATAL');
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

        // Initialize $query2 with a default value to avoid undefined variable error
        $months = [
            'Januari' => '01',
            'Februari' => '02',
            'Maret' => '03',
            'April' => '04',
            'Mei' => '05',
            'Juni' => '06',
            'Juli' => '07',
            'Agustus' => '08',
            'September' => '09',
            'Oktober' => '10',
            'November' => '11',
            'Desember' => '12'
        ];
        
        $caseStatement = "CASE 
            WHEN SUBSTRING_INDEX(bulan_inden, '-', 1) = 'Januari' THEN '01'
            WHEN SUBSTRING_INDEX(bulan_inden, '-', 1) = 'Februari' THEN '02'
            WHEN SUBSTRING_INDEX(bulan_inden, '-', 1) = 'Maret' THEN '03'
            WHEN SUBSTRING_INDEX(bulan_inden, '-', 1) = 'April' THEN '04'
            WHEN SUBSTRING_INDEX(bulan_inden, '-', 1) = 'Mei' THEN '05'
            WHEN SUBSTRING_INDEX(bulan_inden, '-', 1) = 'Juni' THEN '06'
            WHEN SUBSTRING_INDEX(bulan_inden, '-', 1) = 'Juli' THEN '07'
            WHEN SUBSTRING_INDEX(bulan_inden, '-', 1) = 'Agustus' THEN '08'
            WHEN SUBSTRING_INDEX(bulan_inden, '-', 1) = 'September' THEN '09'
            WHEN SUBSTRING_INDEX(bulan_inden, '-', 1) = 'Oktober' THEN '10'
            WHEN SUBSTRING_INDEX(bulan_inden, '-', 1) = 'November' THEN '11'
            WHEN SUBSTRING_INDEX(bulan_inden, '-', 1) = 'Desember' THEN '12'
            END";
        
        $rawOrderBy = "CONCAT(RIGHT(bulan_inden, 4), '-', $caseStatement)";
        
        
        $query2 = ModelsPoinden::query();
        
        if (Auth::user()->hasRole(['Auditor', 'Finance'])) {
            $query2 = ModelsPoinden::where('status_dibuat', 'DIKONFIRMASI')->orderByRaw($rawOrderBy . ' DESC');
        } elseif (Auth::user()->hasRole('Purchasing')) {
            $query2 = ModelsPoinden::orderByRaw($rawOrderBy . ' DESC');
        }
        
        
        // $query2->orderBy('created_at', 'desc');
        
        // Execute the query
        $inden = $query2->get();
        
        if ($req->supplierInd) {
            $query2->where('supplier_id', $req->input('supplierInd'));
        }

        if ($req->statusInd) {
            if ($req->statusInd == 'Lunas') {
                $query2->whereHas('invoice', function($q) {
                    $q->where('sisa', 0);
                });
            } elseif($req->statusInd == 'Belum Lunas') {
                $query2->whereHas('invoice', function($q) {
                    $q->where('sisa', '!=', 0)
                    ->where('status_dibuat', '!=', 'BATAL');
                });
            } elseif($req->statusInd == 'Belum Ada Tagihan') {
                $query2->whereDoesntHave('invoice');
            } elseif($req->statusInd == 'Invoice Batal') {
                $query2->whereHas('invoice', function($q) {
                    $q->where('status_dibuat', 'BATAL');
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

        return view('purchase_po.index',compact('datapos','dataretur','datainv','datainden', 'supplierTrd', 'supplierInd', 'galleryTrd'));  
    }

    public function invoice(Request $req)
    {
                // Query untuk invoices tanpa poinden_id
        $query = Invoicepo::with('pembelian')->whereNull('poinden_id')->orderBy('created_at', 'desc');

        // Filter untuk user dengan role Finance
        $query->when(Auth::user()->hasRole('Finance'), function ($q) {
            $q->where('status_dibuat', 'DIKONFIRMASI');
            //   ->where('lokasi_id', Auth::user()->karyawans->lokasi_id);
        });

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

        $query2 = Invoicepo::with('pembelian')->whereNotNull('poinden_id')->orderBy('tgl_inv', 'desc');

        $query2->when(Auth::user()->hasRole('Finance'), function ($q) {
                $q->where('status_dibuat', 'DIKONFIRMASI');
                //   ->where('lokasi_id', Auth::user()->karyawans->lokasi_id);
        });

        if ($req->supplierInd) {
            $query2->whereHas('pembelian', function($q) use($req){
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
        $pembelian = Pembelian::get();
        $dataretur = Returpembelian::get();
        $no_invpo = $this->generatebayarPONumber();

        return view('purchase_inv.indexinvoice', compact('pembelian','invoices','no_invpo','dataretur','invoiceinden', 'supplierTrd', 'supplierInd', 'galleryTrd', 'bankpens', 'invoice_bayar'));

    }

    public function index_retur(Request $req)
    {
        $query = Returpembelian::orderBy('created_at', 'desc');
        $query->when(Auth::user()->hasRole('Finance'), function ($q) {
            $q->where('status_dibuat', 'DIKONFIRMASI');
            //   ->where('lokasi_id', Auth::user()->karyawans->lokasi_id);
        });

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
        $pembelian = Pembelian::get();
        // $databayars = Pembayaran::where('invoice_purchase_id', $id_inv)->where('status_bayar','LUNAS')->get();

        return view('purchase_retur.returindex',compact('pembelian','dataretur','datainv', 'gallery'));
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

    public function generatebayarrefundNumber() {
        $date = date('Ymd');  // Tanggal hari ini dalam format YYYYMMDD
        $prefix = 'Refundpo_' . $date . '_';
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

    public function generateReturNumber() {
        $tgl_today = date('Y-m-d');
    
        // Cari urutan terakhir nomor retur pada hari ini
        $lastInv = Returpembelian::whereDate('created_at', $tgl_today)->orderBy('id', 'desc')->first();
    
        // Jika tidak ada nomor retur pada hari ini, urutan diinisialisasi dengan 1
        $urutan = 1;
        if ($lastInv) {
            // Jika ada nomor retur pada hari ini, ambil urutan berikutnya
            $lastNumber = substr($lastInv->no_retur, -3);
            $urutan = intval($lastNumber) + 1;
        }
    
        // Format nomor retur dengan pola 'RPM_tgl_urutanretur'
        $nomor_retur = 'RPM_' . date('Ymd') . '_' . str_pad($urutan, 3, '0', STR_PAD_LEFT);
        
        return $nomor_retur;
    }  
    
    public function create() {
        // Generate nomor PO
        $nomor_po = $this->generatePONumber();
    
        // Ambil data yang diperlukan
        $produks = Produk::get();
        $suppliers = Supplier::where('tipe_supplier','tradisional')->get();
        $lokasis = Lokasi::whereIn('tipe_lokasi', [1, 3, 4])->get();
        $kondisis = Kondisi::get();
    
        return view('purchase_po.create', compact('produks', 'suppliers', 'lokasis', 'kondisis', 'nomor_po'));
    }

    public function createinden()
    {
        $produks = Produk::get();
        $suppliers = Supplier::where('tipe_supplier','inden')->get();
        $nomor_poinden = $this->generatePOIndenNumber();
        return view('purchase_po.createinden', compact('produks','suppliers','nomor_poinden'));

    }

    public function createinvoice($type, $datapo)
    {
        if ($type === 'pembelian') {
            // Jika type adalah pembelian (Purchase Order)
            $beli = Pembelian::find($datapo);
            if ($beli) {
                $produkbelis = Produkbeli::where('pembelian_id', $datapo)->get();

                $produkkomplains = Produkretur::whereHas('produkbeli', function($q) use($datapo){
                    $q->where('pembelian_id', $datapo);
                })->get();

                $rekenings = Rekening::all();
                $no_invpo = $this->generatebayarPONumber();
                // $nomor_inv = $this->generateINVPONumber();

                return view('purchase_inv.createinv', compact('beli', 'produkbelis', 'rekenings', 'no_invpo', 'produkkomplains'));
            }
        } elseif ($type === 'poinden') {
            // Jika type adalah poinden (Inden Order)
            $beli = ModelsPoinden::find($datapo);
            if ($beli) {
                $produkbelis = Produkbeli::where('poinden_id', $datapo)->get();
                $rekenings = Rekening::all();
                $no_invpo = $this->generatebayarPONumber();
                // $nomor_inv = $this->generateINVPONumber();

                return view('purchase_inv.createinvinden', compact('beli', 'produkbelis', 'rekenings', 'no_invpo'));
            }
        }

        // Jika tidak ditemukan di kedua tabel
        return redirect()->back()->withErrors('ID Purchase Order atau Inden Order tidak valid.');
    }

    public function create_retur(Request $req)
    {
        $invoice = Invoicepo::with('pembelian', 'pembelian.produkbeli', 'pembelian.produkbeli.produk')->find($req->invoice);
        $lokasi = Lokasi::find(Auth::user()->karyawans->lokasi_id);
        $nomor_retur = $this->generateReturNumber();
        // return $nomor_retur;

        return view('purchase_retur.createretur', compact('nomor_retur', 'lokasi', 'invoice'));

    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store_po(Request $request) //purchase
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'nopo' => 'required',
            'id_supplier' => 'required',
            'id_lokasi' => 'required',
            'tgl_kirim' => 'required|date',
            'no_do' => 'required',
            'status' => 'required',
            'filedo' => 'required'
            // 'tgl_diterima' => 'required|date',
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
        $pembelian->no_retur = $request->no_retur ?? null;
        $pembelian->supplier_id = $request->id_supplier;
        $pembelian->lokasi_id = $request->id_lokasi;
        $pembelian->tgl_kirim = $request->tgl_kirim;
        $pembelian->tgl_diterima = $request->tgl_diterima;
        $pembelian->no_do_suplier = $request->no_do;

        // $pembelian->pemeriksa = $request->pemeriksa ?? null; // ID pengguna yang membuat pembelian
        // $pembelian->penerima = $request->penerima ?? null; // ID pengguna yang membuat pembelian

        //khusus purchasing
        $pembelian->pembuat = $request->pembuat; // ID pengguna yang membuat pembelian
        $pembelian->status_dibuat = $request->status; // Status pembuatan
        $pembelian->tgl_dibuat = $request->tgl_dibuat; // Tanggal pembuatan saat ini
        //end
        // $pembelian->status_diterima = $request->status_diterima ?? null; // Status pembuatan
        // $pembelian->status_diperiksa = $request->status_diperiksa ?? null; // Status pembuatan
       
        // $pembelian->tgl_diterima_ttd = $request->tgl_diterima_ttd ?? null; // Tanggal pembuatan saat ini
        // $pembelian->tgl_diperiksa = $request->tgl_diperiksa ?? null; // Tanggal pembuatan saat ini
        
        
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

        // Cek apakah data sudah ada
        $existingProduct = ModelsPoinden::where('supplier_id', $request->id_supplier)
            ->where('bulan_inden', $request->bulan_inden)
            ->where('status_dibuat', '!=', 'BATAL')
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

        // Pengecekan duplikasi kode indens
        $kode_indens = $request->kode_inden;
        if (count($kode_indens) !== count(array_unique($kode_indens))) {
            return redirect()->back()->withInput()->with('fail', 'kode inden tidak boleh sama');
        }

        // Simpan data pembelian
        $check1 = $pembelian->save();
        if (!$check1) {
            return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data pembelian');
        }

        // Simpan data produk beli
        $kategori = $request->kategori;
        $jumlah = $request->qty;
        $ket = $request->ket;
        $check2 = true;

        foreach ($kode_indens as $index => $kode_inden) {
            $produkBeli = new Produkbeli();
            $produkBeli->poinden_id = $pembelian->id;
            $produkBeli->kode_produk_inden = $kode_inden;
            $produkBeli->produk_id = $kategori[$index];
            $produkBeli->jumlahInden = $jumlah[$index];
            $produkBeli->keterangan = $ket[$index];
            $check2 = $check2 && $produkBeli->save();
        }

        if ($request->status_dibuat == "DIKONFIRMASI") {
            $produkbelis = Produkbeli::where('poinden_id', $pembelian->id)->get();
            foreach ($produkbelis as $produkbeli) {
                $kode = $produkbeli->produk->kode;
                $kode_inden = $produkbeli->kode_produk_inden;
                $jumlah = $produkbeli->jumlahInden;

                $inventoryInden = InventoryInden::where('kode_produk', $kode)
                    ->where('supplier_id', $pembelian->supplier_id)
                    ->where('bulan_inden', $pembelian->bulan_inden)
                    ->where('kode_produk_inden', $kode_inden)
                    ->first();

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
        }

        // Periksa keberhasilan penyimpanan data
        if (!$check2) {
            return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data produk');
        } else {
            return redirect(route('pembelian.index'))->with('success', 'Data pembelian berhasil disimpan. Nomor PO: ' . $pembelian->no_po);
        }
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
    
            $duplicate = Invoicepo::where('pembelian_id', $umum->id)
            ->where('status_dibuat', '!=', 'BATAL')
            ->first();

            if($duplicate){
                return redirect()->back()->withInput()->with('fail', 'data sudah ada');
            }

            $inv = new Invoicepo();
            $idpo = $inv->pembelian_id = $request->id_po;
            // $inv->poinden_id = $request->nopo;
            $inv->tgl_inv = $request->tgl_inv;
            $no_inv = $inv->no_inv = $request->no_inv;
    
            $inv->pembuat = $request->pembuat;
            $inv->status_dibuat = $request->status_dibuat;
            $inv->tgl_dibuat = $request->tgl_dibuat;
            $inv->pembuku = $request->pembuku ?? null;
            $inv->status_dibuku = $request->status_dibuku ?? null;
            $inv->tgl_dibukukan = $request->tgl_dibukukan ?? null;
    
            $subtot = $inv->subtotal = $request->sub_total;
                        //kumpulan produkbeli dengan id tersebut
            if ($request->persen_ppn == null ) {
                $inv->ppn = 0;
             } else { 
                 $persen_ppn =  $request->persen_ppn;
                 $inv->ppn = $persen_ppn/100 * $subtot;
             }
             
             $inv->persen_ppn = $request->persen_ppn ?? 0; 
             // $produks = Produkbeli::where('pembelian_id', $idpo)->get();
            $inv->biaya_kirim = $request->biaya_ongkir ?? 0;
            $inv->total_tagihan = $request->total_tagihan;
            $inv->dp = 0;
            $inv->sisa = $request->total_tagihan;
    
            $check1 = $inv->save();
    
    
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
                return redirect()->route('invoice.show',  ['datapo' => $idpo, 'type' => $type, 'id' => $inv->id])->with('success', 'Data pembelian berhasil disimpan. Nomor Invoice: ' . $no_inv);  
             }

       
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
            $inv->tgl_dibuat = $request->tgl_dibuat;
            $inv->pembuku = $request->pembuku;
            $inv->status_dibuku = $request->status_dibuku;
            $inv->tgl_dibukukan = $request->tgl_dibukukan;
    
            $subtot = $inv->subtotal = $request->sub_total;
            // $disk = $inv->diskon = $request->diskon_total;
            if ($request->persen_ppn == null ) {
               $inv->ppn = 0;
            } else { 
                $persen_ppn =  $request->persen_ppn;
                $inv->ppn = $persen_ppn/100 * $subtot;
            }
            
            $inv->persen_ppn = $request->persen_ppn ?? 0;
    
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
                return redirect()->route('invoice.show',  ['datapo' => $idpo, 'type' => $type, 'id' => $inv->id])->with('success', 'Data pembelian berhasil disimpan. Nomor Invoice: ' . $no_inv);  
             }

        }
        } else {
            return redirect()->back()->withErrors('Tipe tidak valid.');
        }
        

    }

    public function store_retur(Request $request)
    {
        // dd($request);

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

        $existingRetur = ReturPembelian::where('invoicepo_id', $request->invoicepo_id)->first();
        if ($existingRetur) {
            return redirect()->back()->withInput()->with('fail', 'Retur Sudah Dibuat.');
        }


        $data = $request->except(['_token', '_method', 'file']);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('bukti_retur_pembelian', $fileName, 'public');
            $data['foto'] = $filePath;
        }

        if($request->komplain == "Refund"){
            $data['sisa'] = $request->subtotal;
        }else{
            $data['sisa'] = 0;
        }
        
        $data['ongkir'] = $request->biaya_pengiriman ?? 0;
        // $data['total'] = $request->total_harga;
        $jenis = $data['komplain'];
        
        $save = ReturPembelian::create($data);

        // $data['sisa'] = $request->subtotal;
        // $save = ReturPembelian::create($data);
       

        if ($save) {        

            for ($i = 0; $i < count($data['nama_produk']); $i++) {
                $produkReturBeli = [
                    'returpembelian_id' => $save->id,
                    'produkbeli_id' => $data['nama_produk'][$i],
                    'alasan' => $data['alasan'][$i],
                    'jumlah' => $data['jumlah'][$i],
                    'harga' => $data['harga_satuan'][$i],
                    'diskon' => $data['diskon'][$i] ?? 0,
                    'totharga' => $data['harga_total'][$i]
                ];

                $produk_retur = Produkretur::create($produkReturBeli);
                
            //  if($request->status_dibuat == "DIKONFIRMASI"){

            //     $getProdukBeli = Produkbeli::where('id', $data['nama_produk'][$i])->first();
            //     if($jenis == 'Retur' || 'Refund'){
            //         if ($getProdukBeli->pembelian->lokasi->tipe_lokasi == 1 ) {
            //             $getInven = InventoryGallery::where('kode_produk', $getProdukBeli->produk->kode)->where('lokasi_id', $getProdukBeli->pembelian->lokasi_id)->where('kondisi_id', $getProdukBeli->kondisi_id)->first();
            //             $getInven->jumlah -=  $data['jumlah'][$i];
            //             $getInven->update();
            //         }elseif($getProdukBeli->pembelian->lokasi->tipe_lokasi == 3 ){
            //             $getInven = InventoryGreenHouse::where('kode_produk', $getProdukBeli->produk->kode)->where('lokasi_id', $getProdukBeli->pembelian->lokasi_id)->where('kondisi_id', $getProdukBeli->kondisi_id)->first();
            //             $getInven->jumlah -=  $data['jumlah'][$i];
            //             $getInven->update();
                    
            //         }elseif($getProdukBeli->pembelian->lokasi->tipe_lokasi == 4 ){
            //             $getInven = InventoryGudang::where('kode_produk', $getProdukBeli->produk->kode)->where('lokasi_id', $getProdukBeli->pembelian->lokasi_id)->where('kondisi_id', $getProdukBeli->kondisi_id)->first();
            //             $getInven->jumlah -=  $data['jumlah'][$i];
            //             $getInven->update();
            //         }
            //     } 

            //     $totalharga = ($getProdukBeli->jml_diterima - $data['jumlah'][$i]) * ($getProdukBeli->harga - $getProdukBeli->diskon);
            //     $updateproduk = [
            //         'type_komplain' => $jenis,
            //         'qty_komplain' => $data['jumlah'][$i],
            //         'totalharga' =>  $totalharga
            //     ];

            //     $update = Produkbeli::where('id', $data['nama_produk'][$i])->update($updateproduk);

            //     if (!$update) {
            //         return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
            //     }

            // }

            }
            
        //     if($request->status_dibuat == "DIKONFIRMASI"){

        //         $getInvoice = Invoicepo::find($data['invoicepo_id']);
        //         $getppn = $getInvoice->persen_pp ?? 0;
        //         $newSubtotal = $getInvoice->subtotal;
        //         $getretur = Returpembelian::where('id',$save->id)->first();
        //         // $getProdukbeli = Produkbeli::where('pembelian_id',  $getInvoice->pembelian_id)->get();
        //         $getProdukretur = Produkretur::where('returpembelian_id',$save->id)->get();
                

        //         if ($jenis == 'Retur') {
        //             $subretur = $newSubtotal - $getretur->subtotal;
        //             $getInvoice->subtotal = $subretur;
        //             $getInvoice->ppn = ($getppn / 100) * $subretur;
        //             $getInvoice->total_tagihan = $subretur + $getInvoice->biaya_kirim + ($getInvoice->ppn) + $getretur->ongkir;
        //             $getInvoice->sisa = $getInvoice->total_tagihan;
        //             $check = $getInvoice->update();
        //         } elseif ($jenis == 'Refund') {
        //             $subrefund = $newSubtotal - $getretur->subtotal;
        //             $getInvoice->subtotal = $subrefund;
        //             $getInvoice->ppn = ($getppn / 100) * $subrefund;
        //             $getInvoice->total_tagihan = $subrefund + $getInvoice->biaya_kirim + ($getInvoice->ppn) + $getretur->ongkir;
        //             $getInvoice->sisa = $getInvoice->total_tagihan;
                
        //             try {
        //                 $getretur->sisa = $subrefund;
        //                 $check2 = $getretur->update();
                
        //                 if (!$check2) {
        //                     return redirect()->back()->withInput()->with('fail', 'Gagal Update Retur');
        //                 }
        //             } catch (\Exception $e) {
        //                 return redirect()->back()->withInput()->with('fail', 'Gagal Update Retur: ' . $e->getMessage());
        //             }
                
        //             $check = $getInvoice->update();
        //         } else {
        //             foreach ($getProdukretur as $produkretur) {
        //                 $newSubtotal -= ($produkretur->jumlah * $produkretur->diskon);
        //             }
        //             $getInvoice->subtotal = $newSubtotal;
        //             $getInvoice->ppn = ($getppn / 100) * $newSubtotal;
        //             $getInvoice->total_tagihan = $newSubtotal + $getInvoice->biaya_kirim + ($getInvoice->ppn);
        //             $getInvoice->sisa = $getInvoice->total_tagihan;
        //             $check = $getInvoice->update();
        //         }
    
        //     if(!$check) return redirect()->back()->withInput()->with('fail', 'Gagal Update Invoice');
        // }

            // return redirect('returbeli.show',)->withInput()->with('success', 'Berhasil Menyimpan Data');
            return redirect(route('returbeli.index', ['retur_id' => $save->id]))->with('success', 'Berhasil Menyimpan Data');

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
        $penerima = Karyawan::where('user_id', $beli->penerima)->first()->nama ?? null;
        $penerimajbt = Karyawan::where('user_id', $beli->penerima)->first()->jabatan ?? null;
        $pemeriksa = Karyawan::where('user_id', $beli->pemeriksa)->first()->nama ?? null;
        $pemeriksajbt = Karyawan::where('user_id', $beli->pemeriksa)->first()->jabatan ?? null;
        $produkbelis = Produkbeli::with('produkretur')->where('pembelian_id', $datapo)->get();
        return view('purchase_po.showpo',compact('beli','produkbelis','pembuat','penerima','pemeriksa','pembuatjbt','penerimajbt','pemeriksajbt'));
        // return view('purchase.showpo',compact('beli','produkbelis','pembuat','pembuatjbt'));
       
        }elseif ($type === 'poinden') {
            $beli = ModelsPoinden::find($datapo);
            // return $beli;
            $pembuat = Karyawan::where('user_id', $beli->pembuat)->first()->nama ?? null;
            $pembuatjbt = Karyawan::where('user_id', $beli->pembuat)->first()->jabatan ?? null;
            $pemeriksa = Karyawan::where('user_id', $beli->pemeriksa)->first()->nama ?? null;
            $pemeriksajbt = Karyawan::where('user_id', $beli->pemeriksa)->first()->jabatan ?? null;
            $produkbelis = Produkbeli::where('poinden_id', $datapo)->get();
            
            return view('purchase_po.showpoinden',compact('beli','produkbelis','pembuat','pemeriksa','pembuatjbt','pemeriksajbt'));
            // return view('purchase.showpoinden',compact('beli','produkbelis','pembuat','pembuatjbt'));
           
        }
    }
    
     public function show_invoice ($datapo, Request $request)
    {

        $type = $request->query('type');
        $id = $request->query('id');

        if ($type === 'pembelian') {
            $inv_po = Invoicepo::find($id);
            $diskonTot = Produkbeli::where('pembelian_id', $datapo)->get();
            $idinv = $inv_po->id;
            $retur = Returpembelian::where('invoicepo_id', $idinv)->first();
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
            $pembuku = Karyawan::where('user_id', $inv_po->pembuku)->first()->nama ?? null;
            $pembukujbt = Karyawan::where('user_id', $inv_po->pembuku)->first()->jabatan ?? null;
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
                
                $produkkomplains = Produkretur::whereHas('produkbeli', function($q) use($datapo){
                    $q->where('pembelian_id', $datapo);
                })->get();

                $pembelian = Pembelian::get();
                
            return view('purchase_inv.showinv', compact('riwayat','pembelian','produkkomplains','retur','totalDis','inv_po', 'produkbelis', 'beli', 'rekenings', 'no_bypo', 'nomor_inv', 'databayars', 'pembuat', 'pembuku', 'pembuatjbt', 'pembukujbt'));

        } elseif ($type === 'poinden') {
            $inv_po = Invoicepo::where('id', $id)->first();
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
            $pembuku = Karyawan::where('user_id', $inv_po->pembuku)->first()->nama ?? null;
            $pembukujbt = Karyawan::where('user_id', $inv_po->pembuku)->first()->jabatan ?? null;
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

            return view('purchase_inv.showinvinden', compact('riwayat','totalDis','inv_po', 'produkbelis', 'beli', 'rekenings', 'no_bypo', 'nomor_inv', 'databayars', 'pembuat', 'pembuku', 'pembuatjbt', 'pembukujbt'));

        } else {
            return redirect()->back()->withErrors('Tipe tidak valid.');
        }
    }

     public function show_returpo(Request $req, $id)
     {
        $lokasi = Lokasi::find(Auth::user()->karyawans->lokasi_id);
        $data = Returpembelian::with('invoice', 'produkretur')->find($id);
        $rekenings = Rekening::all();
        $databayars = Pembayaran::where('retur_pembelian_id', $data->id)->get()->sortByDesc('created_at');

        $pembuat = Karyawan::where('user_id', $data->pembuat)->first()->nama ?? null;
        $pembuatjbt = Karyawan::where('user_id', $data->pembuat)->first()->jabatan ?? null;
        $pembuku = Karyawan::where('user_id', $data->pembuku)->first()->nama ?? null;
        $pembukujbt = Karyawan::where('user_id', $data->pembuku)->first()->jabatan ?? null;

        $no_byre = $this->generatebayarrefundNumber();

        return view('purchase_retur.showreturpo', compact('data','pembuat','pembuatjbt','pembuku','pembukujbt','rekenings','databayars', 'lokasi','no_byre'));
     }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Pembelian  $pembelian
     * @return \Illuminate\Http\Response
     */
    
     public function po_edit ($datapo, Request $request) //admin
    {

        $type = $request->query('type');
        
        if ($type === 'pembelian') {
            
            $produks = Produk::get();
            $suppliers = Supplier::where('tipe_supplier','tradisional')->get();
            $lokasis = Lokasi::whereIn('tipe_lokasi', [1, 3, 4])->get();
            $kondisis = Kondisi::get();
            $beli = Pembelian::find($datapo);

            // return $beli;
            $pembuat = Karyawan::where('user_id', $beli->pembuat)->first()->nama;
            $pembuatjbt = Karyawan::where('user_id', $beli->pembuat)->first()->jabatan;
            $penerima = Karyawan::where('user_id', $beli->penerima)->first()->nama ?? null;
            $penerimajbt = Karyawan::where('user_id', $beli->penerima)->first()->jabatan ?? null;
            $pemeriksa = Karyawan::where('user_id', $beli->pemeriksa)->first()->nama ?? null;
            $pemeriksajbt = Karyawan::where('user_id', $beli->pemeriksa)->first()->jabatan ?? null;
            $produkbelis = Produkbeli::where('pembelian_id', $datapo)->get();
            
            return view('purchase_po.editpo',compact('produks','suppliers','lokasis','kondisis','beli','produkbelis','pembuat','penerima','pemeriksa','pembuatjbt','penerimajbt','pemeriksajbt'));
        
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
            $pemeriksa = Karyawan::where('user_id', $beli->pemeriksa)->first()->nama ?? null;
            $pemeriksajbt = Karyawan::where('user_id', $beli->pemeriksa)->first()->jabatan ?? null;
            $produkbelis = Produkbeli::where('poinden_id', $datapo)->get();
            
            return view('purchase_po.editpoinden',compact('produks','suppliers','lokasis','kondisis','beli','produkbelis','pembuat','pemeriksa','pembuatjbt','pemeriksajbt'));

        } else {
            return redirect()->back()->withErrors('Tipe tidak valid.');
        }


    }

    public function po_editpurchase ($datapo, Request $request)
    {

        $type = $request->query('type');
        
        if ($type === 'pembelian') {
            
            $produks = Produk::get();
            $suppliers = Supplier::where('tipe_supplier','tradisional')->get();
            $lokasis = Lokasi::whereIn('tipe_lokasi', [1, 3, 4])->get();
            $kondisis = Kondisi::get();
            $beli = Pembelian::find($datapo);

            // return $beli;
            $pembuat = Karyawan::where('user_id', $beli->pembuat)->first()->nama;
            $pembuatjbt = Karyawan::where('user_id', $beli->pembuat)->first()->jabatan;
            $penerima = Karyawan::where('user_id', $beli->penerima)->first()->nama ?? null;;
            $penerimajbt = Karyawan::where('user_id', $beli->penerima)->first()->jabatan ?? null;;
            $pemeriksa = Karyawan::where('user_id', $beli->pemeriksa)->first()->nama ?? null;;
            $pemeriksajbt = Karyawan::where('user_id', $beli->pemeriksa)->first()->jabatan ?? null;
            $produkbelis = Produkbeli::where('pembelian_id', $datapo)->get();
            
            return view('purchase_po.editpopurchase',compact('produks','suppliers','lokasis','kondisis','beli','produkbelis','pembuat','penerima','pemeriksa','pembuatjbt','penerimajbt','pemeriksajbt'));
        
        } 


    }

    public function po_edit_audit ($datapo, Request $request)
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
            $penerima = Karyawan::where('user_id', $beli->penerima)->first()->nama ?? null;;
            $penerimajbt = Karyawan::where('user_id', $beli->penerima)->first()->jabatan ?? null;;
            $pemeriksa = Karyawan::where('user_id', $beli->pemeriksa)->first()->nama ?? null;;
            $pemeriksajbt = Karyawan::where('user_id', $beli->pemeriksa)->first()->jabatan ?? null;
            $produkbelis = Produkbeli::where('pembelian_id', $datapo)->get();
            
            return view('purchase_po.editpoaudit',compact('produks','suppliers','lokasis','kondisis','beli','produkbelis','pembuat','penerima','pemeriksa','pembuatjbt','penerimajbt','pemeriksajbt'));
        
        } 


    }

    public function edit_invoice ($datapo, Request $request) //purchasing
    {

        $type = $request->query('type');
        $id = $request->query('id');

        if ($type === 'pembelian') {
            $inv_po = Invoicepo::find($id);
            $diskonTot = Produkbeli::where('pembelian_id', $datapo)->get();
            $retur = Returpembelian::where('invoicepo_id', $inv_po->id)->first();
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
            $pembuku = Karyawan::where('user_id', $inv_po->pembuku)->first()->nama ?? null;
            $pembukujbt = Karyawan::where('user_id', $inv_po->pembuku)->first()->jabatan ?? null;
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
            $produkkomplains = Produkretur::whereHas('produkbeli', function($q) use($datapo){
                $q->where('pembelian_id', $datapo);
            })->get();

            return view('purchase_inv.editinv', compact('riwayat','retur','produkkomplains','totalDis','inv_po', 'produkbelis', 'beli', 'rekenings', 'no_bypo', 'nomor_inv', 'databayars', 'pembuat', 'pembuku', 'pembuatjbt', 'pembukujbt'));

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
            $pembuku = Karyawan::where('user_id', $inv_po->pembuku)->first()->nama ?? null;
            $pembukujbt = Karyawan::where('user_id', $inv_po->pembuku)->first()->jabatan ?? null;
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

            return view('purchase_inv.editinvinden', compact('riwayat','totalDis','inv_po', 'produkbelis', 'beli', 'rekenings', 'no_bypo', 'nomor_inv', 'databayars', 'pembuat', 'pembuku', 'pembuatjbt', 'pembukujbt'));

        } else {
            return redirect()->back()->withErrors('Tipe tidak valid.');
        }


    }

    public function edit_invoice_purchase ($datapo, Request $request) //purchasing
    {

        $type = $request->query('type');
        $id = $request->query('id');

        if ($type === 'pembelian') {
            $inv_po = Invoicepo::find($id);
            $diskonTot = Produkbeli::where('pembelian_id', $datapo)->get();
            $retur = Returpembelian::where('invoicepo_id', $inv_po->id)->first();
            // return $diskonTot;

            $ppn = $inv_po->ppn / $inv_po->subtotal * 100;

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
            $pembuku = Karyawan::where('user_id', $inv_po->pembuku)->first()->nama ?? null;
            $pembukujbt = Karyawan::where('user_id', $inv_po->pembuku)->first()->jabatan ?? null;
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
            $produkkomplains = Produkretur::whereHas('produkbeli', function($q) use($datapo){
                $q->where('pembelian_id', $datapo);
            })->get();

            return view('purchase_inv.editinv_purchasefinance', compact('ppn','riwayat','retur','produkkomplains','totalDis','inv_po', 'produkbelis', 'beli', 'rekenings', 'no_bypo', 'nomor_inv', 'databayars', 'pembuat', 'pembuku', 'pembuatjbt', 'pembukujbt'));

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
            $pembuku = Karyawan::where('user_id', $inv_po->pembuku)->first()->nama ?? null;
            $pembukujbt = Karyawan::where('user_id', $inv_po->pembuku)->first()->jabatan ?? null;
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

            return view('purchase_inv.editinvinden', compact('riwayat','totalDis','inv_po', 'produkbelis', 'beli', 'rekenings', 'no_bypo', 'nomor_inv', 'databayars', 'pembuat', 'pembuku', 'pembuatjbt', 'pembukujbt'));

        } else {
            return redirect()->back()->withErrors('Tipe tidak valid.');
        }


    }

    public function editinvoice($datapo, Request $request) //atala edit invoice inden
    {

        //show
        $no_po = $request->input('no_po');
        $type = $request->query('type');

        if ($type === 'pembelian') {
            $inv_po = Invoicepo::where('pembelian_id', $datapo)->first();
            $diskonTot = Produkbeli::where('pembelian_id', $datapo)->get();
            $idinv = $inv_po->id;
            $retur = Returpembelian::where('invoicepo_id', $idinv)->first();
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
            $pembuku = Karyawan::where('user_id', $inv_po->pembuku)->first()->nama ?? null;
            $pembukujbt = Karyawan::where('user_id', $inv_po->pembuku)->first()->jabatan ?? null;
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
                
                $produkkomplains = Produkretur::whereHas('produkbeli', function($q) use($datapo){
                    $q->where('pembelian_id', $datapo);
                })->get();

            return view('purchase_inv.showinv', compact('riwayat','produkkomplains','retur','totalDis','inv_po', 'produkbelis', 'beli', 'rekenings', 'no_bypo', 'nomor_inv', 'databayars', 'pembuat', 'pembuku', 'pembuatjbt', 'pembukujbt'));

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
            $pembuku = Karyawan::where('user_id', $inv_po->pembuku)->first()->nama ?? null;
            $pembukujbt = Karyawan::where('user_id', $inv_po->pembuku)->first()->jabatan ?? null;
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

            return view('purchase_inv.editinvoice', compact('riwayat','totalDis','inv_po', 'produkbelis', 'beli', 'rekenings', 'no_bypo', 'nomor_inv', 'databayars', 'pembuat', 'pembuku', 'pembuatjbt', 'pembukujbt'));

        } else {
            return redirect()->back()->withErrors('Tipe tidak valid.');
        }
    }

    public function edit_returpo($id)
    {
        $data = Returpembelian::with('invoice', 'produkretur')->where('id', $id)->first();
        foreach($data->produkretur as $produk) {
            $beli = Produkbeli::where('id', $produk->produkbeli_id)->first();
        }
        // dd($beli);
        
        if (!$data) {
            return redirect()->route('returbeli.index')->with('error', 'Data retur pembelian tidak ditemukan.');
        }
    
        $lokasi = Lokasi::find(Auth::user()->karyawans->lokasi_id ?? '');
        $rekenings = Rekening::all();
        $databayars = Pembayaran::where('retur_pembelian_id', $data->id)->get()->sortByDesc('created_at');
    
        // Mengambil data pembuat dan pembuku menggunakan satu query
        $pembuat = Karyawan::where('user_id', $data->pembuat)->first()->nama ?? null;
        $pembuatjbt = Karyawan::where('user_id', $data->pembuat)->first()->jabatan ?? null;
        $pembuku = Karyawan::where('user_id', $data->pembuku)->first()->nama ?? null;
        $pembukujbt = Karyawan::where('user_id', $data->pembuku)->first()->jabatan ?? null;

        $nomor_retur = $this->generateReturNumber();

        $invoice = Invoicepo::with('pembelian', 'pembelian.produkbeli', 'pembelian.produkbeli.produk')->find($data->invoicepo_id);
        // return $nomor_retur;

    
        return view('purchase_retur.editretur', compact('beli','nomor_retur','invoice','data', 'pembuat','pembuatjbt', 'pembuku','pembukujbt', 'rekenings', 'databayars', 'lokasi'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Pembelian  $pembelian
     * @return \Illuminate\Http\Response
     */

    public function po_update(Request $request, $datapo) //admin
    {
         // dd($request);
         // Validasi input
 
         $type = $request->type;
         // return $type;
 
         if ($type === 'pembelian') {
 
             $validator = Validator::make($request->all(), [
                 'tgl_diterima' => 'required|date',
                 'status' => 'required',
 
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
           
         
         $pembelian->penerima = $request->penerima;
         $pembelian->tgl_diterima = $request->tgl_diterima;
         $pembelian->status_diterima = $request->status;
         $pembelian->tgl_diterima_ttd = $request->tgl_diterima_ttd;
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
         
                 if ($request->status == 'DIKONFIRMASI') {
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
 
                         }else{
                             return redirect()->back()->withInput()->with('fail', 'Bukan Inventory Galery');
 
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

            $user = Auth::user();
            
            
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
            
            if($user->hasRole(['Purchasing'])){
                
                $existingProduct = ModelsPoinden::where('supplier_id', $request->id_supplier)
                    ->where('bulan_inden', $request->bulan_inden)
                    ->where('status_dibuat', '!=', 'BATAL')
                    ->where('id', '!=', $datapo) // Tambahkan kondisi ini untuk mengecualikan $datapo
                    ->first();
                
                if ($existingProduct) {
                    return redirect()->back()->withInput()->with('fail', 'Data sudah ada.');
                }
                // $pembelian->supplier_id = $request->id_supplier;
                $pembelian->bulan_inden = $request->bulan_inden;
                $pembelian->supplier_id = $request->id_supplier;
                $pembelian->pembuat = Auth::user()->id;
                $pembelian->status_dibuat = $request->status_dibuat;
                $pembelian->tgl_dibuat = $request->tgl_dibuat;

                // $pembelian->pemeriksa = $request->pemeriksa;
                // $pembelian->status_diperiksa = $request->status_diperiksa;
                // $pembelian->tgl_diperiksa = $request->tgl_diperiksa;
    
                if ($request->status_dibuat == "BATAL") {
                   $pembelian->status_diperiksa = "BATAL";
               } 
    
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
    
                if ($request->status_dibuat == "DIKONFIRMASI") {
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
                   
                }

            } elseif($user->hasRole(['Auditor'])){

                $pembelian->pemeriksa = $request->pemeriksa;
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
                            // Update inventory for the old condition and amount
                            $inventoryIndenLama = InventoryInden::where('kode_produk', $produkBeli->produk->kode)
                                ->where('supplier_id', $pembelian->supplier_id)
                                ->where('bulan_inden', $pembelian->bulan_inden)
                                ->where('kode_produk_inden', $produkBeli->kode_produk_inden)
                                ->first();
                
                            if ($inventoryIndenLama) {
                                $inventoryIndenLama->jumlah -= $produkBeli->jumlahInden;
                                $inventoryIndenLama->save();
                            }
                
                            // Update produkBeli with the new data
                            $produkBeli->update($produkBeliData);
                
                            // Update inventory for the new condition and amount
                            $inventoryIndenBaru = InventoryInden::where('kode_produk', $produkBeli->produk->kode)
                                ->where('supplier_id', $pembelian->supplier_id)
                                ->where('bulan_inden', $pembelian->bulan_inden)
                                ->where('kode_produk_inden', $kodeInden[$index])
                                ->first();
                
                            if ($inventoryIndenBaru) {
                                $inventoryIndenBaru->jumlah += $jumlah[$index];
                                $inventoryIndenBaru->save();
                            } else {
                                InventoryInden::create([
                                    'supplier_id' => $pembelian->supplier_id,
                                    'kode_produk_inden' => $kodeInden[$index],
                                    'bulan_inden' => $pembelian->bulan_inden,
                                    'kode_produk' => $produkBeli->produk->kode,
                                    'jumlah' => $jumlah[$index],
                                ]);
                            }
                        }
                    } 
                }

                // if ($request->status_dibuat == "DIKONFIRMASI") {
                //    $produkbelis = Produkbeli::where('poinden_id', $datapo )->get();
                //    foreach ($produkbelis as $index => $produkbeli) {
                //        $kode = $produkbeli->produk->kode;
                //        $kode_inden = $produkbeli->kode_produk_inden;
                //        $jumlah = $produkbeli->jumlahInden;
                //        // return $kode;
       
       
                //         $inventoryInden = InventoryInden::where('kode_produk',$kode )
                //                        ->where('supplier_id', $pembelian->supplier_id)
                //                        ->where('bulan_inden', $pembelian->bulan_inden)
                //                        ->where('kode_produk_inden', $kode_inden)
                //                        ->first();
       
                //        // return $inventoryInden;
       
                //        if ($inventoryInden) {
                //                // Update existing InventoryInden
                //                $inventoryInden->jumlah += $jumlah;
                //                $inventoryInden->save();
                //        } else {
                //        // Create new InventoryInden
                //            InventoryInden::create([
                //                    'supplier_id' => $pembelian->supplier_id,
                //                    'kode_produk_inden' => $kode_inden,
                //                    'bulan_inden' => $pembelian->bulan_inden,
                //                    'kode_produk' => $kode,
                //                    'jumlah' => $jumlah,
                //            ]);
                //        }
                //    }
                   
                // }

            }
             


             
             return redirect()->route('pembelian.show', ['datapo' => $datapo, 'type'=>'poinden'])->with('success', 'Data pembelian berhasil diupdate');
         }
 
    }
 
    public function po_update_audit(Request $request, $datapo) //audit
    {
        $type = $request->type;

        if ($type === 'pembelian') {
            $validator = Validator::make($request->all(), [
                // 'tgl_diterima' => 'required|date',
                'status' => 'required',

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

        

        if ($pembelian->tgl_diterima == null && in_array($pembelian->lokasi->tipe_lokasi, [3, 4])) {      
            $pembelian->tgl_diterima = $request->tgl_diterima;
            
            $pembelian->pemeriksa = $request->pemeriksa;
            $pembelian->status_diperiksa = $request->status;
            $pembelian->tgl_diperiksa= $request->tgl_diperiksa;

            $pembelian->penerima = $request->pemeriksa;;
            $pembelian->status_diterima = $request->status;
            $pembelian->tgl_diterima_ttd= $request->tgl_diperiksa;
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
            
                    if ($request->status == 'DIKONFIRMASI') {
                        if ($lokasi && $produk) {
                            if ($lokasi->tipe_lokasi == 4) {
                                $checkInven = InventoryGudang::where('kode_produk', $produk->kode)
                                    ->where('kondisi_id', $kondisiIds[$index])
                                    ->where('lokasi_id', $lokasi->id)
                                    ->first();
                                if ($checkInven) {
                                    $checkInven->jumlah += $qtyTerima[$index];
                                    $checkInven->update();
                                } else {
                                    $createProduk = new InventoryGudang();
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
                    }
    
                } else {
                    $check2 = false;
                }
            }


         } else {
            $pembelian->pemeriksa = $request->pemeriksa;
            $pembelian->status_diperiksa = $request->status;
            $pembelian->tgl_diperiksa= $request->tgl_diperiksa;
            $check1 = $pembelian->save();

            $produkIds = $request->id;
            $kode = $request->kode;
            $qtyTerima = $request->qtytrm;
            $kondisiIds = $request->kondisi;
            $check2 = true;
            
            foreach ($produkIds as $index => $produkId) {
                $produkBeli = Produkbeli::find($produkId);
            
                if ($produkBeli) {
                    $lokasi = Lokasi::find($pembelian->lokasi_id);
                    $produk = Produk::where('kode', $kode[$index])->first();
            
                    if ($request->status == 'DIKONFIRMASI') {
                        if ($lokasi && $produk) {
                            if ($lokasi->tipe_lokasi == 1) {
                                // Cari inventory dengan kondisi lama
                                $checkInvenLama = InventoryGallery::where('kode_produk', $produk->kode)
                                    ->where('kondisi_id', $produkBeli->kondisi_id)
                                    ->where('lokasi_id', $lokasi->id)
                                    ->first();
                                
                                // Jika ada inventory dengan kondisi lama, kurangi jumlahnya
                                if ($checkInvenLama) {
                                    $checkInvenLama->jumlah -= $produkBeli->jml_diterima;
                                    $checkInvenLama->update();
                                }
            
                                // Cari inventory dengan kondisi baru
                                $checkInvenBaru = InventoryGallery::where('kode_produk', $produk->kode)
                                    ->where('kondisi_id', $kondisiIds[$index])
                                    ->where('lokasi_id', $lokasi->id)
                                    ->first();
                                
                                // Jika ada inventory dengan kondisi baru, tambahkan jumlahnya
                                if ($checkInvenBaru) {
                                    $checkInvenBaru->jumlah += $qtyTerima[$index];
                                    $checkInvenBaru->update();
                                } else {
                                    // Jika tidak ada, buat entry inventory baru
                                    $checkInvenBaru = new InventoryGallery();
                                    $checkInvenBaru->kode_produk = $produk->kode;
                                    $checkInvenBaru->kondisi_id = $kondisiIds[$index];
                                    $checkInvenBaru->lokasi_id = $lokasi->id;
                                    $checkInvenBaru->jumlah = $qtyTerima[$index];
                                    $checkInvenBaru->save();
                                }
                            } else {
                                return redirect()->back()->withInput()->with('fail', 'tipe lokasi bukan gallery');
                            }
                        }
                    }
                    // Update produkBeli dengan jumlah dan kondisi baru
                    $produkBeli->jml_diterima = $qtyTerima[$index];
                    $produkBeli->kondisi_id = $kondisiIds[$index];
                    $check2 = $produkBeli->update();

                } else {
                    $check2 = false;
                }
            }


         }
            
        
            
        }
        
        if (!$check1 || !$check2) {
            return redirect()->back()->withInput()->with('fail', 'Gagal mengupdate data');
        } else {
            return redirect(route('pembelian.show', ['datapo' => $datapo, 'type'=>'pembelian']))->with('success', 'Data pembelian berhasil diupdate. Nomor PO: ' . $pembelian->no_po);
        }
    }

    public function po_update_purchase(Request $request, $datapo)
    {
        $type = $request->type;

        if ($type === 'pembelian') {

        $validator = Validator::make($request->all(), [
            'nopo' => 'required',
            'id_supplier' => 'required',
            'id_lokasi' => 'required',
            'tgl_kirim' => 'required|date',
            'no_do' => 'required',
            'status' => 'required',
            // 'filedo' => 'required'
        ]);
    
        // Periksa apakah validasi gagal
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return redirect()->back()->withInput()->with('fail', $errors);
        }
    
        // Simpan data pembelian
        // Muat data pembelian yang sudah ada
        $pembelian = Pembelian::findOrFail($datapo); // atau gunakan find jika Anda ingin menangani data yang tidak ditemukan sendiri
        
        // Perbarui atribut model dengan data dari request
        $pembelian->no_po = $request->nopo;
        $pembelian->no_retur = $request->no_retur ?? null;
        $pembelian->supplier_id = $request->id_supplier;
        $pembelian->lokasi_id = $request->id_lokasi;
        $pembelian->tgl_kirim = $request->tgl_kirim;
        $pembelian->tgl_diterima = $request->tgl_diterima ?? null;
        $pembelian->no_do_suplier = $request->no_do;
        
        // Khusus purchasing
        $pembelian->pembuat = Auth::user()->id; // ID pengguna yang membuat pembelian
        $pembelian->status_dibuat = $request->status;
        $pembelian->tgl_dibuat = $request->tgl_dibuat;

        if ($request->status == "BATAL") {
            $pembelian->status_diterima = "BATAL";
            $pembelian->status_diperiksa = "BATAL";
        } 
        // Status pembuatan
        // $pembelian->tgl_dibuat = $request->tgl_dibuat; // Tanggal pembuatan saat ini

        if ($request->hasFile('filedo')) {
            $file = $request->file('filedo');
            $fileName = $request->nopo . date('YmdHis') . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('bukti_do_supplier', $fileName, 'public');
            $pembelian->file_do_suplier = $filePath; // Simpan path file ke dalam model jika ada
        }

        // Simpan perubahan ke database
        $pembelian->save();

        $dataprodukbeli = ProdukBeli::where('pembelian_id', $pembelian->id)->get();

        foreach ($dataprodukbeli as $produk) {
            $produk->delete();
        }
        // Loop through each existing ProdukBeli entry and update it
        // if ($request->has('id')) {
        //     foreach ($request->id as $index => $id) {
        //         $produkBeli = ProdukBeli::find($id);
        //         if ($produkBeli) {
        //             $produkBeli->produk_id = $request->produk[$index];
        //             $produkBeli->jml_dikirim = $request->qtykrm[$index];
        //             $produkBeli->save();
        //         }
        //     }
        // }

        // Handle new entries
        if ($request->has('produk')) {
            foreach ($request->produk as $index => $produkId) {
                // if (!isset($request->id[$index])) {
                    $produkBeli = new ProdukBeli();
                    $produkBeli->pembelian_id = $datapo;
                    $produkBeli->produk_id = $produkId;
                    $produkBeli->jml_dikirim = $request->qtykrm[$index];
                    $produkBeli->save();
                // }
            }
        }


        return redirect()->route('pembelian.index')->with('success', 'Data PO berhasil diupdate');
     }
    }


    public function update_invoice(Request $request, Pembelian $pembelian, $idinv)
    {
        $validator = Validator::make($request->all(), [
            'status_dibuku' => 'required',
            'tgl_dibukukan' => 'required|date',
        ]);
    
        // Periksa apakah validasi gagal
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return redirect()->back()->withInput()->with('fail', $errors);
        }
        
        $data = $request->only(['status_dibuku', 'tgl_dibukukan']);

        $getInvoice = Invoicepo::find($idinv);

        if ($getInvoice->sisa !== 0 && $request->status_dibuku == 'DIKONFIRMASI')  {
            return redirect()->back()->withInput()->with('fail', 'Tagihan belum Lunas');

        } elseif ($getInvoice->sisa == 0) {
            $getInvoice->pembuku = Auth::user()->id;
            $getInvoice->status_dibuku = $data['status_dibuku'];
            $getInvoice->tgl_dibukukan = $data['tgl_dibukukan'];
            $check = $getInvoice->update();
        }
        

        if($getInvoice->pembelian()->exists()){
            $tipe = 'pembelian';
            $datapo = $getInvoice->pembelian_id;
        } else if($getInvoice->poinden()->exists()){
            $tipe = 'poinden';
            $datapo = $getInvoice->poinden_id;
        }

        if(!$check) return redirect()->back()->withInput()->with('fail', 'Gagal update pembukuan');
        return redirect(route('invoice.show', ['datapo' => $datapo, 'type' => $tipe, 'id' => $idinv]))->with('success', 'Berhasil update pembuku');
    }

    public function update_purchase_invoice(Request $request, Pembelian $pembelian, $idinv)
    {
        $validator = Validator::make($request->all(), [
            'id_po' => 'required',
            'no_inv' => 'required',
            'tgl_inv' => 'required',
            'sub_total' => 'required',
            'total_tagihan' => 'required',
            // 'status_dibuat' => 'required',
            // 'tgl_dibuat' => 'required|date',
        ]);
    
        // Periksa apakah validasi gagal
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return redirect()->back()->withInput()->with('fail', $errors);
        }

        $user = Auth::user();
        $inv = Invoicepo::find($idinv);

        // $idpo = $inv->pembelian_id = $request->id_po;
        // $inv->poinden_id = $request->nopo;

        $inv->tgl_inv = $request->tgl_inv;
        $no_inv = $inv->no_inv = $request->no_inv;

        if($user->hasRole(['Purchasing'])){
        $inv->pembuat = Auth::user()->id;
        $inv->status_dibuat = $request->status_dibuat;
        $inv->tgl_dibuat = $request->tgl_dibuat;

        }

        $subtot = $inv->subtotal = $request->sub_total;

        if ($request->persen_ppn == null ) {
            $inv->ppn = 0;
         } else { 
             $persen_ppn =  $request->persen_ppn;
             $inv->ppn = $persen_ppn/100 * $subtot;
         }
         
        $inv->persen_ppn = $request->persen_ppn ?? 0;

        $inv->biaya_kirim = $request->biaya_ongkir ?? 0;
        $inv->total_tagihan = $request->total_tagihan;
        $inv->sisa = $request->total_tagihan;
        // $inv->dp = 0;

        if($user->hasRole(['Finance'])){
            $inv->pembuku = $request->pembuku;
            $inv->status_dibuku = $request->status_dibuku;
            $inv->tgl_dibukukan = $request->tgl_dibukukan;
        }

        $check1 = $inv->update();
        
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
            $check2 = $produkbeli->update();
        }


        if($inv->pembelian()->exists()){
            $tipe = 'pembelian';
            $datapo = $inv->pembelian_id;
        } else if($inv->poinden()->exists()){
            $tipe = 'poinden';
            $datapo = $inv->poinden_id;
        }

        if (!$check1 || !$check2) {
            return redirect()->back()->withInput()->with('fail', 'Gagal update data');
        } else {
            return redirect(route('invoice.show', ['datapo' => $datapo, 'type' => $tipe, 'id' => $idinv]))->with('success', 'Berhasil update data');
        }
    }

    public function editinvoiceupdate($datapo, Request $request) //atala edit invoice inden
    {
        // dd($request);
        $no_po = $request->input('no_po');
        $type = $request->input('type');
        $id_po = $request->input('id_po');

        $user = Auth::user();
    
        if ($type === 'poinden') {
            $inden = ModelsPoinden::where('no_po', $no_po)->first();
            if ($inden) {

                $validator = Validator::make($request->all(), [
                    'id_po' => 'required',
                    'no_inv' => 'required',
                    'tgl_inv' => 'required|date',
                    'sub_total' => 'required|numeric',
                    'total_tagihan' => 'required|numeric',
                    // Tambahkan validasi lainnya sesuai kebutuhan
                ]);
    
                // Periksa apakah validasi gagal
                if ($validator->fails()) {
                    $errors = $validator->errors()->all();
                    return redirect()->back()->withInput()->with('fail', $errors);
                }                  

                if($user->hasRole(['Purchasing'])){
                    $invData = [
                        'poinden_id' => $request->id_po,
                        'tgl_inv' => $request->tgl_inv,
                        'no_inv' => $request->no_inv,
                        'pembuat' => Auth::user()->id,
                        'status_dibuat' => $request->status_dibuat,
                        'tgl_dibuat' => $request->tgl_dibuat,
                        'subtotal' => $request->sub_total,
                        'ppn' => $request->persen_ppn === null ? 0 : ($request->persen_ppn / 100 * $request->sub_total),
                        'persen_ppn' => $request->persen_ppn === null ? 0 : $request->persen_ppn,
                        'total_tagihan' => $request->total_tagihan,
                        'dp' => 0,
                        'sisa' => $request->total_tagihan,
                    ];
                }elseif($user->hasRole(['Finance'])){
                    $invData = [
                        'poinden_id' => $request->id_po,
                        'tgl_inv' => $request->tgl_inv,
                        'no_inv' => $request->no_inv,
                        'pembuku' =>  Auth::user()->id,
                        'status_dibuku' => $request->status_dibuku,
                        'tgl_dibukukan' => $request->tgl_dibuku,
                        'subtotal' => $request->sub_total,
                        'ppn' => $request->persen_ppn === null ? 0 : ($request->persen_ppn / 100 * $request->sub_total),
                        'persen_ppn' => $request->persen_ppn === null ? 0 : $request->persen_ppn,
                        'total_tagihan' => $request->total_tagihan,
                        'dp' => 0,
                        'sisa' => $request->total_tagihan,
                    ];
                }
                // Temukan Invoicepo berdasarkan id dan update
                
    
                $check1 = Invoicepo::where('id', $datapo)->update($invData);
    
                // Loop melalui setiap produk untuk update
                $produkIds = $request->input('id');
                $hargas = $request->input('harga');
                $diskons = $request->input('diskon');
                $jumlahs = $request->input('jumlah');
    
                foreach ($produkIds as $index => $produkId) {
                    $produkData = [
                        'harga' => $hargas[$index],
                        'diskon' => $diskons[$index],
                        'totalharga' => $jumlahs[$index],
                    ];
    
                    $check2 = Produkbeli::where('id', $produkId)->update($produkData);
    
                    if (!$check2) {
                        return redirect()->back()->withInput()->with('fail', 'Gagal mengupdate Data Produk');
                    }
                }
    
                // Periksa keberhasilan penyimpanan data
                if (!$check1) {
                    return redirect()->back()->withInput()->with('fail', 'Gagal mengupdate Data Invoice');
                } else {
                    return redirect()->route('invoice.show',  ['datapo' => $id_po, 'type' => $type, 'id' => $datapo])->with('success', 'Data pembelian berhasil diupdate. Nomor Invoice: ' . $invData['no_inv']);
                }
    
            } else {
                return redirect()->back()->withErrors('PO Inden tidak ditemukan.');
            }
        } else {
            return redirect()->back()->withErrors('Tipe tidak valid.');
        }
    }

    public function update_retur_finance (Request $request, Pembelian $pembelian, $idretur) // gak kepake DIKONFIRMASI sekalian di invoice
    {
        // dd($request);

        $validator = Validator::make($request->all(), [
            'pembuku' => 'required',
            'status_dibuku' => 'required',
            'tgl_dibuku' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return redirect()->back()->withInput()->with('fail', $errors);
        }

        $data = $request->only(['pembuku','status_dibuku', 'tgl_dibuku']);

        $getretur = Returpembelian::find($idretur);

     
            if ($getretur->sisa !== 0 && $request->status_dibuku == 'DIKONFIRMASI')  {
                return redirect()->back()->withInput()->with('fail', 'Refund belum Lunas');
    
            } elseif ($getretur->sisa == 0) {
                $getretur->pembuku = Auth::user()->id;
                $getretur->status_dibuku = $data['status_dibuku'];
                $getretur->tgl_dibuku = $data['tgl_dibuku'];
                $check = $getretur->update();
            }

        if ($check) {         
            return redirect(route('returbeli.show', ['retur_id' =>  $getretur->id]))->with('success', 'Berhasil Menyimpan Data');
        }
            return redirect()->back()->withInput()->with('fail', 'Gagal Update Retur');
    }

    function update_retur_purchase($retur, Request $request) {
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

        $data = $request->except(['_token', '_method', 'file', 'supplier_id', 'lokasi_id', 'tanggal_po', 'tanggal_invoice', 'no_po', 'no_invoice', 'kode_produk', 'nama_produk', 'alasan', 'jumlah', 'diskon', 'harga_satuan', 'harga_total', 'DataTables_Table_0_length','biaya_pengiriman','total_harga']);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('bukti_retur_pembelian', $fileName, 'public');
            $data['foto'] = $filePath;
        }

        if($request->komplain == "Refund"){
            $data['sisa'] = $request->subtotal;
        }else{
            $data['sisa'] = 0;
        }

        if($request->komplain == "Refund" || $request->komplain == "Retur" ){
        $data['ongkir'] = $request->biaya_pengiriman ?? 0;
        }elseif($request->komplain == "Diskon"){
        $data['ongkir'] = 0;
        }

        // $data['total'] = $request->total_harga;
        $jenis = $data['komplain'];
        
        $save = ReturPembelian::where('id', $retur)->update($data);

        // $data['sisa'] = $request->subtotal;
        // $save = ReturPembelian::create($data);
        $returpem = ReturPembelian::where('id', $retur)->first();
        $komponen = ProdukRetur::where('returpembelian_id', $returpem->id)->get();
        if($komponen) {
            $komponen->each->forceDelete();
        }

        if ($save) {        

            for ($i = 0; $i < count($request->nama_produk); $i++) {
            
                $diskon = 0;

                if ($request->komplain == "Refund" || $request->komplain == "Retur") {
                    $diskon = 0;
                } elseif ($request->komplain == "Diskon") {
                    $diskon = $request->diskon[$i];
                }

                $produkReturBeli = [
                    'returpembelian_id' => $returpem->id,
                    'produkbeli_id' => $request->nama_produk[$i],
                    'alasan' => $request->alasan[$i],
                    'jumlah' => $request->jumlah[$i],
                    'harga' => $request->harga_satuan[$i],
                    'diskon' => $diskon,
                    'totharga' => $request->harga_total[$i]
                ];

                // $produkret = Produkretur::where('returpembelian_id', $returpem->id)->where('produkbeli_id', $request->nama_produk[$i])->first();
                // if(!empty($produkret)) {
                //     $produk_retur = Produkretur::where('returpembelian_id', $returpem->id)->where('produkbeli_id', $request->nama_produk[$i])->update($produkReturBeli);
                // }elseif(empty($produkret)){
                    $produk_retur = Produkretur::create($produkReturBeli);
                // }
                
                if($request->status_dibuku == "DIKONFIRMASI"){

                    $getProdukBeli = Produkbeli::where('id', $request->nama_produk[$i])->first();
                    if($jenis == 'Retur' || 'Refund'){
                        if ($getProdukBeli->pembelian->lokasi->tipe_lokasi == 1 ) {
                            $getInven = InventoryGallery::where('kode_produk', $getProdukBeli->produk->kode)->where('lokasi_id', $getProdukBeli->pembelian->lokasi_id)->where('kondisi_id', $getProdukBeli->kondisi_id)->first();
                            $getInven->jumlah -=  $request->jumlah[$i];
                            $getInven->update();
                        }elseif($getProdukBeli->pembelian->lokasi->tipe_lokasi == 3 ){
                            $getInven = InventoryGreenHouse::where('kode_produk', $getProdukBeli->produk->kode)->where('lokasi_id', $getProdukBeli->pembelian->lokasi_id)->where('kondisi_id', $getProdukBeli->kondisi_id)->first();
                            $getInven->jumlah -=  $request->jumlah[$i];
                            $getInven->update();
                        
                        }elseif($getProdukBeli->pembelian->lokasi->tipe_lokasi == 4 ){
                            $getInven = InventoryGudang::where('kode_produk', $getProdukBeli->produk->kode)->where('lokasi_id', $getProdukBeli->pembelian->lokasi_id)->where('kondisi_id', $getProdukBeli->kondisi_id)->first();
                            $getInven->jumlah -=  $request->jumlah[$i];
                            $getInven->update();
                        }
                    } 

                    $totalharga = ($getProdukBeli->jml_diterima - $request->jumlah[$i]) * ($getProdukBeli->harga - $getProdukBeli->diskon);
                    $updateproduk = [
                        'type_komplain' => $jenis,
                        'qty_komplain' => $request->jumlah[$i],
                        'totalharga' =>  $totalharga
                    ];

                    $update = Produkbeli::where('id', $request->nama_produk[$i])->update($updateproduk);

                    if (!$update) {
                        return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
                    }

                }

            }
            
            if($request->status_dibuku == "DIKONFIRMASI"){

            $getInvoice = Invoicepo::find($request->invoicepo_id);
            $getppn = $getInvoice->persen_ppn ?? 0;
            $persen = $getppn / 100;
            $newSubtotal = $getInvoice->subtotal;
            $getretur = Returpembelian::where('id',$retur)->first();
            // $getProdukbeli = Produkbeli::where('pembelian_id',  $getInvoice->pembelian_id)->get();
            $getProdukretur = Produkretur::where('returpembelian_id',$getretur->id)->get();
            
            if ($jenis == 'Retur') {
                $subretur = $newSubtotal - $getretur->subtotal;
                $getInvoice->subtotal = $subretur;
                $getInvoice->ppn = $persen * $subretur;
                $getInvoice->total_tagihan = $persen * $subretur + $subretur + $getInvoice->biaya_kirim + $getretur->ongkir;
                $getInvoice->sisa = $getInvoice->total_tagihan;
                $check = $getInvoice->update();
            } elseif ($jenis == 'Refund') {
                $subrefund = $newSubtotal - $getretur->subtotal;
                $getInvoice->subtotal = $subrefund;
                // $getInvoice->ppn = $persen * $subrefund;
                // $getInvoice->total_tagihan = $persen * $subrefund + $subrefund + $getInvoice->biaya_kirim + $getretur->ongkir;
                $getInvoice->total_tagihan =  $subrefund + $getInvoice->biaya_kirim +  $getInvoice->ppn  + $getretur->ongkir;
                $getInvoice->sisa = $getretur->ongkir;
            
                try {
                    $getretur->sisa = $subrefund;
                    $check2 = $getretur->update();
            
                    if (!$check2) {
                        return redirect()->back()->withInput()->with('fail', 'Gagal Update Retur');
                    }
                } catch (\Exception $e) {
                    return redirect()->back()->withInput()->with('fail', 'Gagal Update Retur: ' . $e->getMessage());
                }
            
                $check = $getInvoice->update();
            } else {
                foreach ($getProdukretur as $produkretur) {
                    $newSubtotal -= ($produkretur->jumlah * $produkretur->diskon);
                }
                $getInvoice->subtotal = $newSubtotal;
                $getInvoice->ppn =  $persen * $newSubtotal;
                $getInvoice->total_tagihan = $persen * $newSubtotal + $newSubtotal + $getInvoice->biaya_kirim;
                $getInvoice->sisa = $getInvoice->total_tagihan;
                $check = $getInvoice->update();
            }
            
                   
            if(!$check) return redirect()->back()->withInput()->with('fail', 'Gagal Update Invoice');
        }

            // return redirect('returbeli.show',)->withInput()->with('success', 'Berhasil Menyimpan Data');
            return redirect(route('returbeli.index', ['retur_id' => $returpem->id]))->with('success', 'Berhasil Menyimpan Data');

        }
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

    


}
