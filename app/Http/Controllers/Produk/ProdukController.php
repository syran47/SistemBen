<?php

namespace App\Http\Controllers\Produk;

use App\Http\Controllers\Controller;
use App\Models\BahanBaku;
use App\Models\HistoryBahanBaku;
use App\Models\HistoryManagementProduk;
use App\Models\HistoryProduk;
use App\Models\Produk;
use App\Models\Resep;
use App\Rules\ProdukValidationRule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;

class ProdukController extends Controller
{
    public function index()
    {
        $produks = Produk::with('resep')->orderByDesc('id')->get();
        $reseps = Resep::with('produk', 'bahanbaku')->get();
        $bahanbakus = BahanBaku::all();

        return view('dashboard.pages.manajemen.produk', compact('produks', 'reseps', 'bahanbakus'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'nama'          => ['required', Rule::unique('produks', 'nama')],
            ]
        );

        if ($validator->fails()) {
            return back()->with('toast_error', '<center> Nama produk sudah digunakan <br> gagal menambah data produk </center>');
        }

        if ($request['satuantambah'] == 'select') {
            $satuan = $request->get('satuanSelect');
        } else if ($request['satuantambah'] == 'text') {
            $request->validate(
                [
                    'satuanText'    => ['required']
                ]
            );
            $satuan = $request->get('satuanText');
        }

        $bahanBaku = array_unique($request->get('bahanbaku'));

        DB::transaction(function () use ($request, $satuan, $bahanBaku) {
            $produk = Produk::create(
                [
                    'nama'      => $request->get('nama'),
                    'satuan'    => $satuan,
                    'jumlah'    => 0
                ]
            );

            Produk::where('id', $produk->id)->update(
                [
                    'kode'  => 'BJ' . $produk->id
                ]
            );

            $produk = Produk::orderByDesc('id')->first();

            foreach ($bahanBaku as $key => $value) {
                Resep::create(
                    [
                        'produk_id'     => $produk->id,
                        'bahan_baku_id' => $value,
                        'jumlah'        => $request->get('jumlah')[$key]
                    ]
                );
            }

            HistoryManagementProduk::create(
                [
                    'kode'      => $produk->kode,
                    'nama'      => $produk->nama,
                    'user_id'   => auth()->user()->id,
                    'aksi'      => 'Tambah',
                    'tanggal'       => Carbon::now()->setTimezone('Asia/Jakarta')
                ]
            );
        });

        return redirect()->route('produk.index')->with('toast_success', 'Sukses menambah data produk ' . $request->get('nama'));
    }

    public function update(Request $request, $id)
    {
        if ($request['radioKeterangan'] == 'select') {
            $keterangan = $request->get('keteranganSelect');
        } else if ($request['radioKeterangan'] == 'text') {
            $validator = Validator::make(
                $request->all(),
                [
                    'keteranganText'    => ['required']
                ]
            );

            if ($validator->fails()) {
                return back()->with('toast_error', '<center> Jenis keterangan harus diisi <br> gagal merubah stok</center>');
            }
            $keterangan = $request->get('keteranganText');
        }
        $pesan = $request->input('update');
        switch ($request->input('update')) {
            case 'tambah':
                $pesan = "menambah";

                $validator = Validator::make(
                    $request->all(),
                    [
                        'jumlah' => ['numeric', 'min:1', 'required', new ProdukValidationRule($id, $request->get('jumlah'))],
                    ]
                );

                if ($validator->fails()) {
                    return back()->with('toast_error', '<center> Stok bahan baku tidak mencukupi<br> gagal menambah stok</center>');
                }

                DB::transaction(function () use ($request, $keterangan, $id) {
                    $produk = Produk::where('id', $id)->first();

                    Produk::where('id', $id)->update(
                        [
                            'jumlah'    => $produk->jumlah + $request->get('jumlah')
                        ]
                    );

                    $reseps = Resep::where('produk_id', $id)->pluck('bahan_baku_id')->toArray();
                    $bahanBakus = BahanBaku::whereIn('id', $reseps)->get();

                    foreach ($bahanBakus as $bahanBaku) {
                        $resep = Resep::where('produk_id', $id)
                            ->where('bahan_baku_id', $bahanBaku->id)
                            ->first();

                        BahanBaku::where('id', $bahanBaku->id)->update(
                            [
                                'jumlah' => $bahanBaku->jumlah - ($resep->jumlah * $request->get('jumlah'))
                            ]
                        );

                        HistoryBahanBaku::create(
                            [
                                'kode'          => $bahanBaku->kode,
                                'nama'          => $bahanBaku->nama,
                                'user_id'       => auth()->user()->id,
                                'jumlah'        => $resep->jumlah * $request->get('jumlah'),
                                'satuan'        => $request->get('satuan'),
                                'keterangan'    => 'Bahan produksi ' . $produk->nama,
                                'kategori'      => 'Keluar',
                                'tanggal'       => Carbon::now()->setTimezone('Asia/Jakarta')
                            ]
                        );
                    }

                    HistoryProduk::create(
                        [
                            'kode'          => $produk->kode,
                            'nama'          => $produk->nama,
                            'user_id'       => auth()->user()->id,
                            'jumlah'        => $request->get('jumlah'),
                            'satuan'        => $request->get('satuan'),
                            'keterangan'    => $keterangan,
                            'kategori'      => 'Masuk',
                            'tanggal'       => Carbon::now()->setTimezone('Asia/Jakarta')
                        ]
                    );
                });
                break;
            case 'kurang':
                $pesan = "mengurangi";
                $request->validate(
                    [
                        'jumlah' => ['numeric', 'min:1', 'required']
                    ]
                );

                DB::transaction(function () use ($request, $keterangan, $id) {
                    $produk = Produk::where('id', $id)->first();

                    HistoryProduk::create(
                        [
                            'kode'          => $produk->kode,
                            'nama'          => $produk->nama,
                            'user_id'       => auth()->user()->id,
                            'jumlah'        => $request->get('jumlah'),
                            'satuan'        => $produk->satuan,
                            'keterangan'    => $keterangan,
                            'kategori'      => 'Keluar',
                            'tanggal'       => Carbon::now()->setTimezone('Asia/Jakarta')
                        ]
                    );

                    Produk::where('id', $id)->update(
                        [
                            'jumlah'    => $produk->jumlah - $request->get('jumlah')
                        ]
                    );
                });
                break;
        }
        $produk = Produk::where('id', $id)->first();
        return redirect()->route('produk.index')->with('toast_success', 'Sukses ' . $pesan . ' ' . $request->get('jumlah') . ' stok ' . $produk->nama);
    }

    public function updateProduk(Request $request)
    {

        if ($request['satuanedit'] == 'select') {
            $satuan = $request->get('satuanSelect');
        } else if ($request['satuanedit'] == 'text') {
            $validator = Validator::make(
                $request->all(),
                [
                    'satuanText'    => ['required']
                ]
            );

            if ($validator->fails()) {
                return back()->with('toast_error', '<center> Jenis satuan harus diisi <br> gagal mengubah data produk</center>');
            }
            $satuan = $request->get('satuanText');
        }

        $cek_bahanbaku = $request->get('editBahanBaku');
        if ($cek_bahanbaku) {
            $bahanBaku = array_unique($request->get('editBahanBaku'));
        } else {
            return back()->with('toast_error', '<center> Minimal terdapat 1 bahan baku <br> gagal mengubah data produk</center>');
        }

        DB::transaction(function () use ($request, $satuan, $bahanBaku) {
            Resep::where('produk_id', $request->get('id'))->delete();
            Produk::where('id', $request->get('id'))->update(
                [
                    'satuan'    => $satuan,
                ]
            );

            if ($request->editNama) {
                Produk::where('id', $request->get('id'))->update(
                    [
                        'nama'    => $request->editNama,
                    ]
                );
            }

            $produk = Produk::where('id', $request->get('id'))->first();

            foreach ($bahanBaku as $key => $value) {
                Resep::create(
                    [
                        'produk_id'     => $produk->id,
                        'bahan_baku_id' => $value,
                        'jumlah'        => $request->get('jumlahEdit')[$key]
                    ]
                );
            }

            HistoryManagementProduk::create(
                [
                    'kode'      => $produk->kode,
                    'nama'      => $produk->nama,
                    'user_id'   => auth()->user()->id,
                    'aksi'      => 'Edit',
                    'tanggal'       => Carbon::now()->setTimezone('Asia/Jakarta')
                ]
            );
        });

        return redirect()->route('produk.index')->with('toast_success', 'Sukses merubah data produk');
    }

    public function destroy(Request $request)
    {
        $produk = Produk::where('id', $request->get('id'))->first();

        if ($produk->jumlah > 0) {
            return redirect()->route('produk.index')->with('status', 'Masih terdapat stok, Produk gagal dihapus');
        } else {
            HistoryManagementProduk::create(
                [
                    'kode'          => $produk->kode,
                    'nama'          => $produk->nama,
                    'user_id'       => auth()->user()->id,
                    'aksi'          => 'Hapus',
                    'tanggal'       => Carbon::now()->setTimezone('Asia/Jakarta')
                ]
            );

            Produk::where('id', $request->get('id'))->delete();

            return redirect()->route('produk.index')->with('toast_success', 'Sukses menghapus data produk ' . $produk->nama);
        }
    }

    public function masuk()
    {
        $histories = HistoryProduk::with('user')
            ->where('kategori', 'Masuk')
            ->orderByDesc('tanggal')->get();
        $kategori = 1;
        $jenis = 2;

        return view('dashboard.pages.history.stok', compact('histories', 'kategori', 'jenis'));
    }

    public function keluar()
    {
        $histories = HistoryProduk::with('user')
            ->where('kategori', 'Keluar')
            ->orderByDesc('tanggal')->get();
        $kategori = 2;
        $jenis = 2;

        return view('dashboard.pages.history.stok', compact('histories', 'kategori', 'jenis'));
    }

    public function history()
    {
        $histories = HistoryManagementProduk::orderByDesc('tanggal')->get();
        $kategori = 2;

        return view('dashboard.pages.history.data', compact('histories', 'kategori'));
    }

    public function getDataProduk(Request $request)
    {
        // $dataProduk = Produk::where('id', $request->id)->first();
        $dataProduk = Produk::with('resep')->where('id', $request->id)->first();
        return response()->json($dataProduk);
    }
}
