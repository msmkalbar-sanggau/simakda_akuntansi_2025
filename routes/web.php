<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KelolaAkses\PenggunaController;
use App\Http\Controllers\KelolaAkses\PeranController;
use App\Http\Controllers\KelolaAkses\HakAksesController;
use App\Http\Controllers\LaporanPemda\LRAHBTS\LRAHBTSController;
use App\Http\Controllers\LaporanPemda\LRAPROGRAM\LRAPROGRAMController;
use App\Http\Controllers\LaporanPemda\LRAKESELARASAN\LRAKESELARASANController;
use App\Http\Controllers\LaporanPemda\LRAURUSAN\LRAURUSANController;
use App\Http\Controllers\LaporanKeuangan\LRA\LRAController;
use App\Http\Controllers\LaporanKeuangan\LPSAL\LPSALController;
use App\Http\Controllers\LaporanKeuangan\LPE\LPEController;
use App\Http\Controllers\LaporanKeuangan\LO\LOController;
use App\Http\Controllers\LaporanKeuangan\LAK\LAKController;
use App\Http\Controllers\LaporanKeuangan\NERACA\NERACAController;
use App\Http\Controllers\BukuBesar\BUKUBESARController;
use App\Http\Controllers\Rekal\RekalController;
use App\Http\Controllers\RekapBukuBesar\REKAPBUKUBESARController;
use App\Http\Controllers\NeracaSaldo\NERACASALDOController;
use App\Http\Controllers\LaporanMonitoring\LaporanKONSNeraca\LAPKONSOLNERACAController;
use App\Http\Controllers\LaporanMonitoring\LaporanSisaBank\LAPSISABANKController;
use App\Http\Controllers\LaporanMonitoring\Rekon\REKONController;
use App\Http\Controllers\LaporanMonitoring\RekapTransaksiCMS\REKAPTRANSAKSICMSController;
use App\Http\Controllers\LaporanMonitoring\RekapSP2DSKPD\REKAPSP2DSKPDController;
use App\Http\Controllers\LaporanMonitoring\RekapSTS\REKAPSTSController;
use App\Http\Controllers\LaporanMonitoring\RegisterSP2DBPK\REGISTERSP2DBPKController;
use App\Http\Controllers\LaporanMonitoring\RegisterSP2DBPK\REGISTERSP2DBPKTController;
use App\Http\Controllers\Jurnal\Cetakjurnal\CETAKJURNALController;
use App\Http\Controllers\Jurnal\Inputjurnal\INPUTJURNALController;
use App\Http\Controllers\JurnalKoreksi\KoreksiRekening\KOREKSIREKENINGController;
use App\Http\Controllers\JurnalKoreksi\KoreksiNominal\KOREKSINOMINALController;
use App\Http\Controllers\Perda\LampiranI1\LampiranI1Controller;
use App\Http\Controllers\Perda\LampiranI2\LampiranI2Controller;
use App\Http\Controllers\Perda\LampiranI3\LampiranI3Controller;
use App\Http\Controllers\Perda\LampiranI4\LampiranI4Controller;
use App\Http\Controllers\Perda\LampiranII\LampiranIIController;
use App\Http\Controllers\Perda\LampiranIII\LampiranIIIController;
use App\Http\Controllers\Perda\LampiranIV\LampiranIVController;
use App\Http\Controllers\Perda\LampiranV\LampiranVController;
use App\Http\Controllers\Perda\LampiranVI\LampiranVIController;
use App\Http\Controllers\Perda\LampiranVIII\LampiranVIIIController;
use App\Http\Controllers\Perda\LampiranXII\LampiranXIIController;
use App\Http\Controllers\Perda\LampiranXIII\LampiranXIIIController;
use App\Http\Controllers\Perda\LampiranXV\LampiranXVController;
use App\Http\Controllers\Perda\LampiranXVII\LampiranXVIIController;
use App\Http\Controllers\Perda\LampiranXVIII\LampiranXVIIIController;
use App\Http\Controllers\Perbup\LampiranpI\LampiranpIController;
use App\Http\Controllers\Perbup\LampiranpI1\LampiranpI1Controller;
use App\Http\Controllers\InformasiLainnya\Lampirand1\Lampirand1Controller;
use App\Http\Controllers\InformasiLainnya\Lampirand3\Lampirand3Controller;
use App\Http\Controllers\InformasiLainnya\Lampirand4\Lampirand4Controller;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('', [LoginController::class, 'index'])->name('login');
Route::post('login', [LoginController::class, 'authenticate'])->name('login.index')->middleware(['throttle:3,1']);
Route::get('logout', [LoginController::class, 'logout'])->name('logout');

Route::group(['middleware' => 'auth', 'auth.session'], function () {
    Route::get('beranda', [HomeController::class, 'index'])->name('home');
    Route::get('ubah_password/{id?}', [HomeController::class, 'ubahPassword'])->where('id', '(.*)')->name('ubah_password');
    Route::post('ubah_password/simpan', [HomeController::class, 'simpanUbahPassword'])->name('ubah_password.simpan');

    Route::group(['prefix' => 'kelola-akses'], function () {
        // pengguna
        Route::resource('pengguna', PenggunaController::class);
        Route::post('pengguna/load_data', [PenggunaController::class, 'load_data'])->name('pengguna.load_data');

        // peran
        Route::resource('peran', PeranController::class);
        Route::post('peran/load_data', [PeranController::class, 'load_data'])->name('peran.load_data');

        // hak akses
        Route::resource('hak_akses', HakAksesController::class);
        Route::post('hak_akses/load_data', [HakAksesController::class, 'load_data'])->name('hak_akses.load_data');
    });

    Route::group(['prefix' => 'laporan_pemda'], function () {
        Route::group(['prefix' => 'lra_hbts'], function () {
            Route::get('', [LRAHBTSController::class, 'index'])->name('lra_hbts.index');
            Route::post('penandatangan', [LRAHBTSController::class, 'penandatangan'])->name('lra_hbts.penandatangan');
            Route::get('cetak', [LRAHBTSController::class, 'show'])->name('lra_hbts.cetak');
        });

        Route::group(['prefix' => 'lra_program'], function () {
            Route::get('', [LRAPROGRAMController::class, 'index'])->name('lra_program.index');
            Route::post('penandatangan', [LRAPROGRAMController::class, 'penandatangan'])->name('lra_program.penandatangan');
            Route::get('cetak', [LRAPROGRAMController::class, 'show'])->name('lra_program.cetak');
        });

        Route::group(['prefix' => 'lra_keselarasan'], function () {
            Route::get('', [LRAKESELARASANController::class, 'index'])->name('lra_keselarasan.index');
            Route::post('penandatangan', [LRAKESELARASANController::class, 'penandatangan'])->name('lra_keselarasan.penandatangan');
            Route::get('cetak', [LRAKESELARASANController::class, 'show'])->name('lra_keselarasan.cetak');
        });

        Route::group(['prefix' => 'lra_urusan'], function () {
            Route::get('', [LRAURUSANController::class, 'index'])->name('lra_urusan.index');
            Route::post('penandatangan', [LRAURUSANController::class, 'penandatangan'])->name('lra_urusan.penandatangan');
            Route::get('cetak', [LRAURUSANController::class, 'show'])->name('lra_urusan.cetak');
        });
    });

    Route::group(['prefix' => 'laporan_keuangan'], function () {
        Route::group(['prefix' => 'lra'], function () {
            Route::get('', [LRAController::class, 'index'])->name('lra.index');
            Route::post('penandatangan', [LRAController::class, 'penandatangan'])->name('lra.penandatangan');
            Route::get('cetak', [LRAController::class, 'show'])->name('lra.cetak');
        });

        Route::group(['prefix' => 'lpsal'], function () {
            Route::get('', [LPSALController::class, 'index'])->name('lpsal.index');
            Route::get('cetak', [LPSALController::class, 'show'])->name('lpsal.cetak');
        });

        Route::group(['prefix' => 'lpe'], function () {
            Route::get('', [LPEController::class, 'index'])->name('lpe.index');
            Route::post('penandatangan', [LPEController::class, 'penandatangan'])->name('lpe.penandatangan');
            Route::post('cari_skpd_lpe', [lpeController::class, 'cariSkpd'])->name('lpe.cari_skpd');
            Route::get('cetak', [LPEController::class, 'show'])->name('lpe.cetak');
        });

        Route::group(['prefix' => 'lo'], function () {
            Route::get('', [LOController::class, 'index'])->name('lo.index');
            Route::post('penandatangan', [LOController::class, 'penandatangan'])->name('lo.penandatangan');
            Route::get('cetak', [LOController::class, 'show'])->name('lo.cetak');
        });

        Route::group(['prefix' => 'neraca'], function () {
            Route::get('', [NERACAController::class, 'index'])->name('neraca.index');
            Route::post('penandatangan', [NERACAController::class, 'penandatangan'])->name('neraca.penandatangan');
            Route::get('cetak', [NERACAController::class, 'show'])->name('neraca.cetak');
        });

        Route::group(['prefix' => 'lak'], function () {
            Route::get('', [LAKController::class, 'index'])->name('lak.index');
            Route::post('penandatangan', [LAKController::class, 'penandatangan'])->name('lak.penandatangan');
            Route::get('cetak', [LAKController::class, 'show'])->name('lak.cetak');
        });
    });

    Route::group(['prefix' => 'buku_besar'], function () {
        Route::get('', [BUKUBESARController::class, 'index'])->name('buku_besar.index');
        Route::post('rekening', [BUKUBESARController::class, 'rekening'])->name('buku_besar.rekening');
        Route::post('penandatangan', [BUKUBESARController::class, 'penandatangan'])->name('buku_besar.penandatangan');
        Route::get('cetak', [BUKUBESARController::class, 'show'])->name('buku_besar.cetak');
    });

    Route::group(['prefix' => 'rekap_buku_besar'], function () {
        Route::get('', [REKAPBUKUBESARController::class, 'index'])->name('rekap_buku_besar.index');
        Route::get('cetak', [REKAPBUKUBESARController::class, 'show'])->name('rekap_buku_besar.cetak');
    });

    Route::group(['prefix' => 'neraca_saldo'], function () {
        Route::get('', [NERACASALDOController::class, 'index'])->name('neraca_saldo.index');
        Route::get('cetak', [NERACASALDOController::class, 'show'])->name('neraca_saldo.cetak');
    });

    Route::group(['prefix' => 'laporan_monitoring'], function () {
        Route::group(['prefix' => 'laporan_konsolidasi_neraca'], function () {
            Route::get('', [LAPKONSOLNERACAController::class, 'index'])->name('laporan_konsolidasi_neraca.index');
            Route::get('cetak', [LAPKONSOLNERACAController::class, 'show'])->name('laporan_konsolidasi_neraca.cetak');
        });

        Route::group(['prefix' => 'rekon'], function () {
            Route::get('', [REKONController::class, 'index'])->name('rekon.index');
            Route::get('cetak', [REKONController::class, 'show'])->name('rekon.cetak');
        });

        Route::group(['prefix' => 'rekap_transaksi_cms'], function () {
            Route::get('', [REKAPTRANSAKSICMSController::class, 'index'])->name('rekap_transaksi_cms.index');
            Route::get('cetak', [REKAPTRANSAKSICMSController::class, 'show'])->name('rekap_transaksi_cms.cetak');
        });

        Route::group(['prefix' => 'rekap_sp2d_skpd'], function () {
            Route::get('', [REKAPSP2DSKPDController::class, 'index'])->name('rekap_sp2d_skpd.index');
            Route::get('cetak', [REKAPSP2DSKPDController::class, 'show'])->name('rekap_sp2d_skpd.cetak');
        });

        Route::group(['prefix' => 'rekap_sts'], function () {
            Route::get('', [REKAPSTSController::class, 'index'])->name('rekap_sts.index');
            Route::get('cetak', [REKAPSTSController::class, 'show'])->name('rekap_sts.cetak');
        });

        Route::group(['prefix' => 'register_sp2d_bpk'], function () {
            Route::get('', [REGISTERSP2DBPKController::class, 'index'])->name('register_sp2d_bpk.index');
            Route::get('cetak', [REGISTERSP2DBPKController::class, 'show'])->name('register_sp2d_bpk.cetak');
        });

        Route::group(['prefix' => 'laporan_sisa_bank'], function () {
            Route::get('', [LAPSISABANKController::class, 'index'])->name('laporan_sisa_bank.index');
            Route::get('cetak', [LAPSISABANKController::class, 'show'])->name('laporan_sisa_bank.cetak');
        });

        Route::group(['prefix' => 'register_sp2d_bpk_t'], function () {
            Route::get('', [REGISTERSP2DBPKTController::class, 'index'])->name('register_sp2d_bpk_t.index');
            Route::get('cetak', [REGISTERSP2DBPKTController::class, 'show'])->name('register_sp2d_bpk_t.cetak');
        });
    });

    Route::group(['prefix' => 'jurnal'], function () {
        Route::group(['prefix' => 'cetak_jurnal'], function () {
            Route::get('', [CETAKJURNALController::class, 'index'])->name('cetak_jurnal.index');
            Route::get('cetak', [CETAKJURNALController::class, 'show'])->name('cetak_jurnal.cetak');
        });
        Route::group(['prefix' => 'input_jurnal'], function () {
            Route::get('', [INPUTJURNALController::class, 'index'])->name('input_jurnal.index');
            Route::get('create', [INPUTJURNALController::class, 'create'])->name('input_jurnal.create');
            Route::post('load_kd_kegiatan', [INPUTJURNALController::class, 'load_kd_kegiatan'])->name('input_jurnal.load_kd_kegiatan');
            Route::post('load_kd_rekening', [INPUTJURNALController::class, 'load_kd_rekening'])->name('input_jurnal.load_kd_rekening');
            Route::post('simpan', [INPUTJURNALController::class, 'store'])->name('input_jurnal.simpan');
            Route::get('show/{no_voucher?}/{kd_skpd?}', [INPUTJURNALController::class, 'show'])->name('input_jurnal.show');
            Route::get('edit/{no_voucher?}/{kd_skpd?}', [INPUTJURNALController::class, 'edit'])->name('input_jurnal.edit');
            Route::post('load_data', [INPUTJURNALController::class, 'load_data'])->name('input_jurnal.load_data');
            Route::post('update', [INPUTJURNALController::class, 'update'])->name('input_jurnal.update');
            Route::post('show_load_data', [INPUTJURNALController::class, 'show_load_data'])->name('input_jurnal.show_load_data');
            Route::post('hapus', [INPUTJURNALController::class, 'destroy'])->name('input_jurnal.hapus');
        });
    });


    Route::group(['prefix' => 'jurnal_koreksi'], function () {
        Route::group(['prefix' => 'koreksi_rekening'], function () {
            Route::get('', [KOREKSIREKENINGController::class, 'index'])->name('koreksi_rekening.index');
            Route::post('load_data', [KOREKSIREKENINGController::class, 'load_data'])->name('koreksi_rekening.load_data');
            Route::get('create', [KOREKSIREKENINGController::class, 'create'])->name('koreksi_rekening.create');
            Route::post('load_daftar_transaksi', [KOREKSIREKENINGController::class, 'load_daftar_transaksi'])->name('koreksi_rekening.load_daftar_transaksi');
            Route::post('load_rincian_transaksi', [KOREKSIREKENINGController::class, 'load_rincian_transaksi'])->name('koreksi_rekening.load_rincian_transaksi');
            Route::post('load_rincian_rekening', [KOREKSIREKENINGController::class, 'load_rincian_rekening'])->name('koreksi_rekening.load_rincian_rekening');
            Route::post('load_daftar_transaksi_koreksi', [KOREKSIREKENINGController::class, 'load_daftar_transaksi_koreksi'])->name('koreksi_rekening.load_daftar_transaksi_koreksi');
            Route::post('load_rincian_rekening_koreksi', [KOREKSIREKENINGController::class, 'load_rincian_rekening_koreksi'])->name('koreksi_rekening.load_rincian_rekening_koreksi');
            Route::post('load_sumber_dana', [KOREKSIREKENINGController::class, 'load_sumber_dana'])->name('koreksi_rekening.load_sumber_dana');
            Route::post('load_realisasi_sumber_dana', [KOREKSIREKENINGController::class, 'load_realisasi_sumber_dana'])->name('koreksi_rekening.load_realisasi_sumber_dana');
            Route::post('load_daftar_nilai', [KOREKSIREKENINGController::class, 'load_daftar_nilai'])->name('koreksi_rekening.load_daftar_nilai');
            Route::post('simpan', [KOREKSIREKENINGController::class, 'store'])->name('koreksi_rekening.simpan');
            Route::get('show/{no_bukti?}/{kd_skpd?}', [KOREKSIREKENINGController::class, 'show'])->name('koreksi_rekening.show');
            Route::post('show_load_data', [KOREKSIREKENINGController::class, 'show_load_data'])->name('koreksi_rekening.show_load_data');
            Route::post('hapus', [KOREKSIREKENINGController::class, 'destroy'])->name('koreksi_rekening.hapus');
        });


        Route::group(['prefix' => 'koreksi_nominal'], function () {
            Route::get('', [KOREKSINOMINALController::class, 'index'])->name('koreksi_nominal.index');
            Route::post('load_data', [KOREKSINOMINALController::class, 'load_data'])->name('koreksi_nominal.load_data');
            Route::get('create', [KOREKSINOMINALController::class, 'create'])->name('koreksi_nominal.create');
            Route::post('load_daftar_transaksi', [KOREKSINOMINALController::class, 'load_daftar_transaksi'])->name('koreksi_nominal.load_daftar_transaksi');
            Route::post('load_rincian_transaksi', [KOREKSINOMINALController::class, 'load_rincian_transaksi'])->name('koreksi_nominal.load_rincian_transaksi');
            Route::post('load_rincian_rekening', [KOREKSINOMINALController::class, 'load_rincian_rekening'])->name('koreksi_nominal.load_rincian_rekening');
            Route::post('load_daftar_nilai', [KOREKSINOMINALController::class, 'load_daftar_nilai'])->name('koreksi_nominal.load_daftar_nilai');
            Route::post('simpan', [KOREKSINOMINALController::class, 'store'])->name('koreksi_nominal.simpan');
            Route::get('show/{no_bukti?}/{kd_skpd?}', [KOREKSINOMINALController::class, 'show'])->name('koreksi_nominal.show');
            Route::post('show_load_data', [KOREKSINOMINALController::class, 'show_load_data'])->name('koreksi_nominal.show_load_data');
            Route::post('hapus', [KOREKSINOMINALController::class, 'destroy'])->name('koreksi_nominal.hapus');
        });
    });

    Route::group(['prefix' => 'perda'], function () {
        Route::group(['prefix' => 'lampiran_I'], function () {
            Route::group(['prefix' => 'lampiran_I_1'], function () {
                Route::get('', [LampiranI1Controller::class, 'index'])->name('lampiran_I_1.index');
                Route::get('cetak-lampiran_I_1', [LampiranI1Controller::class, 'show'])->name('lampiran_I.lampiran_I_1.cetak');
            });

            Route::group(['prefix' => 'lampiran_I_2'], function () {
                Route::get('', [LampiranI2Controller::class, 'index'])->name('lampiran_I_2.index');
                Route::get('cetak-lampiran_I_2', [LampiranI2Controller::class, 'show'])->name('lampiran_I.lampiran_I_2.cetak');
            });

            Route::group(['prefix' => 'lampiran_I_3'], function () {
                Route::get('', [LampiranI3Controller::class, 'index'])->name('lampiran_I_3.index');
                Route::get('cetak-lampiran_I_3', [LampiranI3Controller::class, 'show'])->name('lampiran_I.lampiran_I_3.cetak');
            });

            Route::group(['prefix' => 'lampiran_I_4'], function () {
                Route::get('', [LampiranI4Controller::class, 'index'])->name('lampiran_I_4.index');
                Route::get('cetak-lampiran_I_4', [LampiranI4Controller::class, 'show'])->name('lampiran_I.lampiran_I_4.cetak');
            });
        });
        Route::group(['prefix' => 'lampiran_II'], function () {
            Route::get('', [LampiranIIController::class, 'index'])->name('lampiran_II.index');
            Route::get('cetak-lampiran_II', [LampiranIIController::class, 'show'])->name('lampiran_II.cetak');
        });
        Route::group(['prefix' => 'lampiran_III'], function () {
            Route::get('', [LampiranIIIController::class, 'index'])->name('lampiran_III.index');
            Route::get('cetak-lampiran_III', [LampiranIIIController::class, 'show'])->name('lampiran_III.cetak');
        });
        Route::group(['prefix' => 'lampiran_IV'], function () {
            Route::get('', [LampiranIVController::class, 'index'])->name('lampiran_IV.index');
            Route::get('cetak-lampiran_IV', [LampiranIVController::class, 'show'])->name('lampiran_IV.cetak');
        });
        Route::group(['prefix' => 'lampiran_V'], function () {
            Route::get('', [LampiranVController::class, 'index'])->name('lampiran_V.index');
            Route::get('cetak-lampiran_V', [LampiranVController::class, 'show'])->name('lampiran_V.cetak');
        });
        Route::group(['prefix' => 'lampiran_VI'], function () {
            Route::get('', [LampiranVIController::class, 'index'])->name('lampiran_VI.index');
            Route::get('cetak-lampiran_VI', [LampiranVIController::class, 'show'])->name('lampiran_VI.cetak');
        });
        Route::group(['prefix' => 'lampiran_VIII'], function () {
            Route::get('', [LampiranVIIIController::class, 'index'])->name('lampiran_VIII.index');
            Route::get('cetak-lampiran_VIII', [LampiranVIIIController::class, 'show'])->name('lampiran_VIII.cetak');
        });
        Route::group(['prefix' => 'lampiran_XII'], function () {
            Route::get('', [LampiranXIIController::class, 'index'])->name('lampiran_XII.index');
            Route::get('cetak-lampiran_XII', [LampiranXIIController::class, 'show'])->name('lampiran_XII.cetak');
        });
        Route::group(['prefix' => 'lampiran_XIII'], function () {
            Route::get('', [LampiranXIIIController::class, 'index'])->name('lampiran_XIII.index');
            Route::get('cetak-lampiran_XIII', [LampiranXIIIController::class, 'show'])->name('lampiran_XIII.cetak');
        });
        Route::group(['prefix' => 'lampiran_XV'], function () {
            Route::get('', [LampiranXVController::class, 'index'])->name('lampiran_XV.index');
            Route::get('cetak-lampiran_XV', [LampiranXVController::class, 'show'])->name('lampiran_XV.cetak');
        });
        Route::group(['prefix' => 'lampiran_XVII'], function () {
            Route::get('', [LampiranXVIIController::class, 'index'])->name('lampiran_XVII.index');
            Route::get('cetak-lampiran_XVII', [LampiranXVIIController::class, 'show'])->name('lampiran_XVII.cetak');
        });
        Route::group(['prefix' => 'lampiran_XVIII'], function () {
            Route::get('', [LampiranXVIIIController::class, 'index'])->name('lampiran_XVIII.index');
            Route::get('cetak-lampiran_XVIII', [LampiranXVIIIController::class, 'show'])->name('lampiran_XVIII.cetak');
        });
    });

    Route::group(['prefix' => 'perbup'], function () {
        Route::group(['prefix' => 'lampiran_I'], function () {
            Route::get('', [LampiranpIController::class, 'index'])->name('lampiran_pI.index');
            Route::get('cetak-lampiran_PI', [LampiranpIController::class, 'show'])->name('lampiran_pI.cetak');
        });
        Route::group(['prefix' => 'lampiran_I_1'], function () {
            Route::get('', [LampiranpI1Controller::class, 'index'])->name('lampiran_pI1.index');
            Route::get('cetak-lampiran_PI1', [LampiranpI1Controller::class, 'show'])->name('lampiran_pI1.cetak');
        });
    });
    Route::group(['prefix' => 'informasi_lainnya'], function () {
        Route::group(['prefix' => 'lampiran_d_1'], function () {
            Route::get('', [Lampirand1Controller::class, 'index'])->name('lampiran_d1.index');
            Route::get('cetak-lampiran_d1', [Lampirand1Controller::class, 'show'])->name('lampiran_d1.cetak');
        });
        Route::group(['prefix' => 'lampiran_d_3'], function () {
            Route::get('', [Lampirand3Controller::class, 'index'])->name('lampiran_d3.index');
            Route::get('cetak-lampiran_d3', [Lampirand3Controller::class, 'show'])->name('lampiran_d3.cetak');
        });
        Route::group(['prefix' => 'lampiran_d_4'], function () {
            Route::get('', [Lampirand4Controller::class, 'index'])->name('lampiran_d4.index');
            Route::get('cetak-lampiran_d4', [Lampirand4Controller::class, 'show'])->name('lampiran_d4.cetak');
        });
    });

    Route::group(['prefix' => 'rekal'], function () {
        Route::get('', [RekalController::class, 'index'])->name('rekal.index');
        Route::post('simpan', [RekalController::class, 'proses_mapping_all'])->name('rekal.proses_mapping_all');
    });

});

Route::get('403', function () {
    return abort(401);
})->name('403');

Route::get('/{any}', function () {
    return abort(404);
})->where('any', '.*');
