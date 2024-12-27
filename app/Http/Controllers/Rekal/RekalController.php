<?php

namespace App\Http\Controllers\Rekal;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Types\Static_;
use PhpParser\ErrorHandler\Collecting;
use PDF;
use Knp\Snappy\Pdf as SnappyPdf;
use Yajra\DataTables\Facades\DataTables;


class RekalController extends Controller
{
    public function index()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $data = [
            'bendahara' => DB::table('ms_ttd')
                ->whereIn('kode', ['1'])
                ->orderBy('nip')
                ->orderBy('nama')
                ->get(),
            'data_skpd' => DB::table('ms_skpd')->select('kd_skpd', 'nm_skpd', 'bank', 'rekening', 'npwp')->where('kd_skpd', $kd_skpd)->first(),
            // 'jns_anggaran' => jenis_anggaran(),
            // 'jns_anggaran2' => jenis_anggaran()
        ];

        return view('rekal.index')->with($data);
    }

    public function proses_mapping_all()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $nama = Auth::user()->nama;
        $thn = tahun_anggaran();

        DB::beginTransaction();
        try {
            DB::update("exec jurnal_brewok_all ?,?",[$kd_skpd,$thn]);

            DB::commit();
            return response()->json([
                'message' => '1'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => '0'
            ]);
        }
    }
}
