<?php

namespace App\Http\Controllers\BahanBaku;

use App\Http\Controllers\Controller;
use App\Models\BahanBaku;
use App\Models\History;
use App\Models\HistoryBahanBaku;
use App\Models\HistoryManagementBahanBaku;
use App\Models\Resep;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;

class BahanBakuController extends Controller
{
    public function index()
    {
        $bahanbakus = BahanBaku::all();
        $reseps = Resep::with('produk')->get();

        return view('dashboard.pages.manajemen.bahanbaku', compact('bahanbakus', 'reseps'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'nama'      => ['required', Rule::unique('bahan_bakus', 'nama')]
            ]
        );

        if ($validator->fails()) {
            return back()->with('toast_error', '<center> Nama bahan baku sudah digunakan <br> gagal menambah data bahan baku</center>');
        }

        if ($request['satuantambah'] == 'select') {
            $satuan = $request->get('satuanSelect');
        } else if ($request['satuantambah'] == 'text') {
            $validator = Validator::make(
                $request->all(),
                [
                    'satuanText'    => ['required']
                ]
            );

            if ($validator->fails()) {
                return back()->with('toast_error', '<center> Jenis satuan harus diisi <br> gagal menambah data bahan baku</center>');
            }
            $satuan = $request->get('satuanText');
        }

        $bahanBaku = BahanBaku::create(
            [
                'nama'      => $request->get('nama'),
                'jumlah'    => 0,
                'satuan'    => $satuan
            ]
        );

        BahanBaku::where('id', $bahanBaku->id)->update(
            [
                'kode'      => 'BB' . ($bahanBaku->id)
            ]
        );

        $bahanBaku = BahanBaku::orderByDesc('id')->first();

        HistoryManagementBahanBaku::create(
            [
                'kode'          => $bahanBaku->kode,
                'nama'          => $bahanBaku->nama,
                'user_id'       => auth()->user()->id,
                'aksi'          => 'Tambah',
                'tanggal'       => Carbon::now()->setTimezone('Asia/Jakarta')
            ]
        );

        return redirect()->route('bahanbaku.index')->with('toast_success', 'Sukses menambah data bahan baku ' . $request->get('nama'));
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
                $request->validate(
                    [
                        'jumlah' => ['numeric', 'min:1', 'required']
                    ]
                );

                DB::transaction(function () use ($request, $keterangan, $id) {
                    $bahanBaku = BahanBaku::where('id', $id)->first();

                    HistoryBahanBaku::create(
                        [
                            'kode'          => $bahanBaku->kode,
                            'nama'          => $bahanBaku->nama,
                            'user_id'       => auth()->user()->id,
                            'jumlah'        => $request->get('jumlah'),
                            'satuan'        => $request->get('satuan'),
                            'keterangan'    => $keterangan,
                            'kategori'      => 'Masuk',
                            'tanggal'       => Carbon::now()->setTimezone('Asia/Jakarta')
                        ]
                    );

                    BahanBaku::where('id', $id)->update(
                        [
                            'jumlah'    => $bahanBaku->jumlah + $request->get('jumlah')
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
                    $bahanBaku = BahanBaku::where('id', $id)->first();

                    HistoryBahanBaku::create(
                        [
                            'kode'          => $bahanBaku->kode,
                            'nama'          => $bahanBaku->nama,
                            'user_id'       => auth()->user()->id,
                            'jumlah'        => $request->get('jumlah'),
                            'satuan'        => $bahanBaku->satuan,
                            'keterangan'    => $keterangan,
                            'kategori'      => 'Keluar',
                            'tanggal'       => Carbon::now()->setTimezone('Asia/Jakarta')
                        ]
                    );

                    BahanBaku::where('id', $id)->update(
                        [
                            'jumlah'    => $bahanBaku->jumlah - $request->get('jumlah')
                        ]
                    );
                });
                break;
        }
        $bahanBaku = BahanBaku::where('id', $id)->first();
        return redirect()->route('bahanbaku.index')->with('toast_success', 'Sukses ' . $pesan . ' ' . $request->get('jumlah') . ' stok ' . $bahanBaku->nama);
    }

    public function updateBahanBaku(Request $request)
    {
        if ($request['ubah'] == 'select') {
            $satuan = $request->get('satuanSelect');
        } else if ($request['ubah'] == 'text') {
            $validator = Validator::make(
                $request->all(),
                [
                    'satuanText'    => ['required']
                ]
            );

            if ($validator->fails()) {
                return back()->with('toast_error', '<center> Jenis satuan harus diisi <br> gagal mengubah data bahan baku</center>');
            }
            $satuan = $request->get('satuanText');
        }

        $bahanBaku = BahanBaku::where('id', $request->get('id'))->first();
        $nama = $request->get('nama');

        if ($nama == null) {
            $nama = $bahanBaku->nama;
        } else {
            $request->validate(
                [
                    'nama' => [Rule::unique('bahan_bakus', 'nama')->ignore($bahanBaku->id)]
                ]
            );
        }

        $cekUpdate = BahanBaku::where('id', $request->get('id'))
            ->where('nama', $nama)
            ->where('satuan', $satuan)
            ->first();

        if ($cekUpdate) {
            return redirect()->route('bahanbaku.index')->with('toast_error', 'Tidak ada data bahan baku yang berubah');
        }

        DB::transaction(function () use ($request, $satuan, $nama, $bahanBaku) {

            BahanBaku::where('id', $request->get('id'))
                ->update(
                    [
                        'nama'      => $nama,
                        'satuan'    => $satuan
                    ]
                );

            HistoryManagementBahanBaku::create(
                [
                    'kode'          => $bahanBaku->kode,
                    'nama'          => $bahanBaku->nama,
                    'user_id'       => auth()->user()->id,
                    'aksi'          => 'Ubah',
                    'tanggal'       => Carbon::now()->setTimezone('Asia/Jakarta')
                ]
            );
        });

        return redirect()->route('bahanbaku.index')->with('toast_success', 'Sukses merubah data bahan baku');
    }

    public function destroy(Request $request)
    {
        $bahanBaku = BahanBaku::where('id', $request->get('id'))->first();

        if ($bahanBaku->jumlah > 0) {
            return redirect()->route('bahanbaku.index')->with('status', 'Masih terdapat stok bahan baku, Gagal menghapus bahan baku');
        } else {
            HistoryManagementBahanBaku::create(
                [
                    'kode'          => $bahanBaku->kode,
                    'nama'          => $bahanBaku->nama,
                    'user_id'       => auth()->user()->id,
                    'aksi'          => 'Hapus',
                    'tanggal'       => Carbon::now()->setTimezone('Asia/Jakarta')
                ]
            );

            BahanBaku::where('id', $request->get('id'))->delete();

            return redirect()->route('bahanbaku.index')->with('toast_success', 'Sukses menghapus data bahan baku ' . $bahanBaku->nama);
        }
    }

    public function masuk()
    {
        $histories = HistoryBahanBaku::with('user')
            ->where('kategori', 'Masuk')
            ->orderByDesc('tanggal')->get();
        $kategori = 1;
        $jenis = 1;

        return view('dashboard.pages.history.stok', compact('histories', 'kategori', 'jenis'));
    }

    public function keluar()
    {
        $histories = HistoryBahanBaku::with('user')
            ->where('kategori', 'Keluar')
            ->orderByDesc('tanggal')->get();
        $kategori = 2;
        $jenis = 1;

        return view('dashboard.pages.history.stok', compact('histories', 'kategori', 'jenis'));
    }

    public function history()
    {
        $histories = HistoryManagementBahanBaku::orderByDesc('tanggal')->get();
        $kategori = 1;

        return view('dashboard.pages.history.data', compact('histories', 'kategori'));
    }

    public function getDataBahan(Request $request)
    {
        $dataBahan = BahanBaku::with('resep')->where('id', $request->id)->first();
        // $dataResep = Resep::where('bahan_baku_id',$request->id)->get();
        return response()->json($dataBahan);
    }
}
