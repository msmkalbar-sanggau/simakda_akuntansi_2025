<?php

namespace App\Http\Controllers\KelolaAkses;

use App\Http\Controllers\Controller;
use App\Http\Requests\PenggunaRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

class PenggunaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('kelola_akses.pengguna.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = [
            'daftar_skpd' => DB::table('ms_skpd')->get(),
            'daftar_peran'  => DB::table('peran1')->get(),
        ];

        return view('kelola_akses.pengguna.create')->with($data);
    }

    public function load_data()
    {
        $data = DB::table('pengguna1 as a')->Select('a.*',
            DB::raw("(select nm_skpd from ms_skpd b where a.kd_skpd = b.kd_skpd ) as nama_skpd"),
            DB::raw("(select nm_role from peran1 b where a.role = b.id) as jabatan")
        )->get();

        return DataTables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
            $btn = '<a href="' . route("pengguna.edit", Crypt::encryptString($row->id)) . '" class="btn btn-success btn-xs" style="margin-right:4px" title="Edit Data"><i class="fas fa-edit"></i></a>';
            $btn .= '<a href="javascript:void(0);" onclick="hapusPengguna(\'' . $row->id . '\', \'' . Auth::user()->id . '\');" data-id="\'' . $row->id . '\'" class="btn btn-danger btn-xs" style="margin-right:4px" title="Hapus Data"><i class="fas fa-trash-alt"></i></a>';
            return $btn;
        })->rawColumns(['aksi'])->make(true);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PenggunaRequest $request)
    {
        $input = array_map('htmlentities', $request->validated());
        try {
            DB::beginTransaction();
            $id = DB::table('pengguna1')->insertGetId([
                'username' => $input['username'],
                'password' => Hash::make($input['password']),
                'nama' => $input['nama'],
                'kd_skpd' => $request['kd_skpd'],
                'role' => $request['peran'],
            ]);
            DB::table('pengguna_peran1')->insert([
                'id_pengguna' => $id,
                'id_peran' => $request['peran'],
            ]);

            DB::commit();
            return redirect()->route('pengguna.index')->withStatus('Data Berhasil Disimpan');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $id = Crypt::decryptString($id);
        $pengguna = DB::table('pengguna1')->where(['id' => $id])->first();
        $data = [
            'data_pengguna' => $pengguna,
            'daftar_peran' => DB::table('peran1')->get(),
            'daftar_skpd' => DB::table('ms_skpd')->get(),
        ];

        return view('kelola_akses.pengguna.edit')->with($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PenggunaRequest $request, $id)
    {
        $input = array_map('htmlentities', $request->validated());
        try {
            DB::beginTransaction();
            DB::table('pengguna1')->where(['id' => $id])->update([
                'username' => $input['username'],
                'nama' => $input['nama'],
                'kd_skpd' => $request['kd_skpd'],
                'role' => $request['peran'],
            ]);
            DB::table('pengguna_peran1')->where(['id_pengguna' => $id])->update([
                'id_peran' => $request['peran'],
            ]);
            DB::commit();
            return redirect()->route('pengguna.index')->withStatus('Data Berhasil Disimpan');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            DB::table('pengguna1')->where(['id' => $id])->delete();
            DB::table('pengguna_peran1')->where(['id_pengguna' => $id])->delete();
            DB::commit();
            return response()->json([
                'message' => '1'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => '0'
            ]);
        }
    }
}
