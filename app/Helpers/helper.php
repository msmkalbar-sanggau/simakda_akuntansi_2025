<?php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

function getBulan()
{
    return [
        '1' => 'Januari', '2' => 'Februari', '3' => 'Maret', '4' => 'April', '5' => 'Mei', '6' => 'Juni',
        '7' => 'Juli', '8' => 'Agustus', '9' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
    ];
}

function MsBulan($bulan)
{
    switch ($bulan) {
        case 1:
            $bulan = "Januari";
            break;
        case 2:
            $bulan = "Februari";
            break;
        case 3:
            $bulan = "Maret";
            break;
        case 4:
            $bulan = "April";
            break;
        case 5:
            $bulan = "Mei";
            break;
        case 6:
            $bulan = "Juni";
            break;
        case 7:
            $bulan = "Juli";
            break;
        case 8:
            $bulan = "Agustus";
            break;
        case 9:
            $bulan = "September";
            break;
        case 10:
            $bulan = "Oktober";
            break;
        case 11:
            $bulan = "November";
            break;
        case 12:
            $bulan = "Desember";
            break;
    }

    return $bulan;
}

function tahun_anggaran()
{
    $data = DB::table('config_app')->select('thn_ang')->first();
    return $data->thn_ang;
}

function tahun_anggarans()
{
    $thn = DB::table('config_app')->select('thn_ang')->first();
    $data = $thn->thn_ang - 1;
    return $data;
}

function tahun_anggarant()
{
    $thn = DB::table('config_app')->select('thn_ang')->first();
    $data = $thn->thn_ang + 1;
    return $data;
}

function rupiah($data)
{
    return number_format($data, 2, ',', '.');
}

function tanggal($tgl)
{
    return \Carbon\Carbon::parse($tgl)->locale('id')->isoFormat('DD MMMM Y');
}

function tanggal_indonesia($tgl)
{
    return \Carbon\Carbon::parse($tgl)->locale('id')->isoFormat('DD-MM-Y');
}

function TextUpperCase($text)
{
    return strtoupper($text);
}


function filter_menu()
{
    $id = Auth::user()->id;

    $hak_akses = DB::table('pengguna1 as a')->join('pengguna_peran1 as b', function ($join) {
        $join->on('a.id', '=', 'b.id_pengguna');
    })->join('peran1 as c', function ($join) {
        $join->on('c.id', '=', 'b.id_peran');
    })->join('akses_peran1 as d', function ($join) {
        $join->on('c.id', '=', 'd.id_role');
    })->join('akses1 as e', function ($join) {
        $join->on('e.id', '=', 'd.id_akses');
    })->select('e.*')
        ->where(['a.id' => $id, 'e.urutan_menu' => '1'])
        ->orderBy('e.id')
        ->get();

    return $hak_akses;
}

function sub_menu()
{
    $id = Auth::user()->id;

    $hak_akses = DB::table('pengguna1 as a')->join('pengguna_peran1 as b', function ($join) {
        $join->on('a.id', '=', 'b.id_pengguna');
    })->join('peran1 as c', function ($join) {
        $join->on('c.id', '=', 'b.id_peran');
    })->join('akses_peran1 as d', function ($join) {
        $join->on('c.id', '=', 'd.id_role');
    })->join('akses1 as e', function ($join) {
        $join->on('e.id', '=', 'd.id_akses');
    })->select('e.*')
        ->where(['a.id' => $id, 'e.urutan_menu' => '2'])
        ->orderBy('e.urut_akses')
        ->orderBy('e.urut_akses2')
        ->get();

    return $hak_akses;
}

function sub_menu1()
{
    $id = Auth::user()->id;

    $hak_akses = DB::table('pengguna1 as a')->join('pengguna_peran1 as b', function ($join) {
        $join->on('a.id', '=', 'b.id_pengguna');
    })->join('peran1 as c', function ($join) {
        $join->on('c.id', '=', 'b.id_peran');
    })->join('akses_peran1 as d', function ($join) {
        $join->on('c.id', '=', 'd.id_role');
    })->join('akses1 as e', function ($join) {
        $join->on('e.id', '=', 'd.id_akses');
    })->select('e.*')
        ->where(['a.id' => $id, 'e.urutan_menu' => '3'])
        ->orderBy('e.urut_akses2')
        ->get();

    return $hak_akses;
}

function dataReev($data) {
    switch ($data) {
        case 0:
            $data = "Umum";
            break;
        case 1:
            $data = "Revaluasi";
            break;
        case 2:
            $data = "Koreksi Persediaan";
            break;
        case 3:
            $data = "Lain - Lain";
            break;
    }

    return $data;
}

function jenis_anggaran()
{
    $jns_anggaran = DB::table('tb_status_anggaran')
        ->where(['status_aktif' => 1])->get();

    return $jns_anggaran;
}

function nama_skpd($kd_skpd)
{
    $leng_skpd = strlen($kd_skpd);
    // dd($kd_skpd);
    if ($leng_skpd == 17) {
        $data = DB::table('ms_organisasi')->select('nm_org as nm_skpd')->where(['kd_org' => $kd_skpd])->first();
    } else {
        $data = DB::table('ms_skpd')->select('nm_skpd')->where(['kd_skpd' => $kd_skpd])->first();
    }
    return $data->nm_skpd;
}

function nama_org($org)
{
    $data = DB::table('ms_organisasi')
        ->select('nm_org')
        ->where(['kd_org' => $org])
        ->first();

    return $data->nm_org;
}

function dataSpp($data) {
    switch ($data) {
        case 1:
            $data = "UP";
            break;
        case 2:
            $data = "GU";
            break;
        case 3:
            $data = "TU";
            break;
        case 4:
            $data = "LS Gaji";
            break;
        case 5:
            $data = "LS Pihak Ketiga";
            break;
        case 6:
            $data = "LS Barang & Jasa";
            break;
    }

    return $data;
}

function dataReal($data) {
    switch ($data) {
        case 0:
            $data = "Jurnal Umum";
            break;
        case 1:
            $data = "Penyisihan Piutang";
            break;
        case 2:
            $data = "Koreksi Penyusutan";
            break;
        case 3:
            $data = "Hibah Keluar";
            break;
        case 4:
            $data = "Mutasi Masuk antar OPD";
            break;
        case 5:
            $data = "Mutasi Keluar antar OPD";
            break;
        case 6:
            $data = "Penghapusan TPTGR";
            break;
        case 7:
            $data = "Perubahan Kode Rekening";
            break;
        case 8:
            $data = "Koreksi Tanah";
            break;
        case 9:
            $data = "Koreksi Utang Belanja";
            break;
        case 10:
            $data = "Reklass Antar Akun";
            break;
        case 11:
            $data = "Tagihan Penjualan Angsuran";
            break;
        case 12:
            $data = "Penyertaan Modal";
            break;
        case 13:
            $data = "Persediaan APBN yang belum Tercatat TA 2023";
            break;
        case 15:
            $data = "Koreksi Dana Transfer Pemerintah Pusat";
            break;
        case 16:
            $data = "Koreksi Gedung dan Bangunan";
            break;
        case 17:
            $data = "Koreksi Persediaan";
            break;
        case 18:
            $data = "Koreksi Kas";
            break;
        case 19:
            $data = "Extracomptable";
            break;
        case 20:
            $data = "Extracomptable";
            break;
        case 23:
            $data = "Koreksi Peralatan dan Mesin";
            break;
        case 24:
            $data = "Koreksi Jaringan Irigasi Jembatan";
            break;
        case 26:
            $data = "Koreksi Aset Tetap Lainnya";
            break;
        case 27:
            $data = "Koreksi Piutang";
            break;
        case 28:
            $data = "Koreksi Aset Lain Lain";
            break;
        case 29:
            $data = "Reklas Aset Lain-Lain";
            break;
        case 30:
            $data = "Pelimpahan Masuk";
            break;
        case 31:
            $data = "Pelimpahan Keluar";
            break;
        case 89:
            $data = "Saldo Awal LO";
            break;
        case 99:
            $data = "Saldo Awal Neraca";
            break;
    }

    return $data;
}

function dotrek($rek)
{
    $nrek = strlen($rek);
    switch ($nrek) {
        case 1:
            $rek = substr($rek,0, 1);
            break;
        case 2:
            $rek = substr($rek,0, 1) . '.' . substr($rek, 1, 1);
            break;
        case 3:
            $rek = substr($rek,0, 1) . '.' . substr($rek, 1, 1) . '.' . substr($rek, 2, 1);
            break;
        case 4:
            $rek = substr($rek,0, 1) . '.' . substr($rek, 1, 1) . '.' . substr($rek, 2, 2);
            break;
        case 5:
            $rek = substr($rek,0, 1) . '.' . substr($rek, 1, 1) . '.' . substr($rek, 2, 1) . '.' . substr($rek, 3, 2);
            break;
        case 6:
            $rek = substr($rek,0, 1) . '.' . substr($rek, 1, 1) . '.' . substr($rek, 2, 2) . '.' . substr($rek, 4, 2);
            break;
        case 7:
            $rek = substr($rek,0, 1) . '.' . substr($rek, 1, 1) . '.' . substr($rek, 2, 1) . '.' . substr($rek, 3, 2) . '.' . substr($rek, 5, 2);
            break;
        case 8:
            $rek = substr($rek,0, 1) . '.' . substr($rek, 1, 1) . '.' . substr($rek, 2, 2) . '.' . substr($rek, 4, 2) . '.' . substr($rek, 6, 2);
            break;
        case 11:
            $rek = substr($rek,0, 1) . '.' . substr($rek, 1, 1) . '.' . substr($rek, 2, 2) . '.' . substr($rek, 4, 2) . '.' . substr($rek, 6, 2) . '.' . substr($rek, 8, 2) . '.' . substr($rek, 10, 1);
            break;
        case 12:
            $rek = substr($rek,0, 1) . '.' . substr($rek, 1, 1) . '.' . substr($rek, 2, 2) . '.' . substr($rek, 4, 2) . '.' . substr($rek, 6, 2) . '.' . substr($rek, 8, 2) . '.' . substr($rek, 10, 2);
            break;
        default:
            $rek = "";
    }
    return $rek;
}


