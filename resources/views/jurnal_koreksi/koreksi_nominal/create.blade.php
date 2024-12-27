@extends('layouts.template')
@section('title', 'Koreksi Rekening | SIMAKDA')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title">Koreksi Rekening</h4>
                <p class="sub-header">
                    Koreksi Rekening.
                </p>
                <div class="row">
                    {{-- kd skpd --}}
                    <div class="col-lg-6">
                        <div class="mb-2">
                            <label for="kd_skpd" class="form-label">Kode SKPD</label>
                            <select class="form-control select2" id="kd_skpd" name="kd_skpd">
                                <option value=""></option>
                                @foreach ($skpd as $value)
                                    <option value="{{ $value->kd_skpd }}" data-nama="{{ $value->nm_skpd }}">
                                        {{ $value->kd_skpd }} - {{ $value->nm_skpd }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{-- nama skpd --}}
                    <div class="col-lg-6">
                        <div class="mb-2">
                            <label for="nm_skpd" class="form-label">Nama SKPD</label>
                            <input type="text" id="nm_skpd" name="nm_skpd" class="form-control" readonly>
                        </div>
                    </div>
                </div>
                <div class="row">
                    {{-- no bukti --}}
                    <div class="col-lg-6">
                        <div class="mb-2">
                            <label for="no_bukti" class="form-label">No bukti</label>
                            <select class="form-control select2" id="no_bukti" name="no_bukti"></select>
                        </div>
                    </div>
                    {{-- tgl --}}
                    <div class="col-lg-6">
                        <div class="mb-2">
                            <label for="tgl" class="form-label">Tanggal</label>
                            <input type="date" class="form-control" id="tgl" name="tgl">
                        </div>
                    </div>
                </div>
                <div class="row">
                    {{-- no sp2d --}}
                    <div class="col-lg-4">
                        <div class="mb-2">
                            <label for="no_sp2d" class="form-label">No SP2D</label>
                            <input type="text"  class="form-control" id="no_sp2d" name="no_sp2d" readonly>
                        </div>
                    </div>
                    {{-- tgl bukti --}}
                    <div class="col-lg-4">
                        <div class="mb-2">
                            <label for="tgl_bukti" class="form-label">Tanggal Bukti</label>
                            <input type="text"  class="form-control" id="tgl_bukti" name="tgl_bukti" readonly>
                        </div>
                    </div>
                    {{-- jns_spp --}}
                    <div class="col-lg-4">
                        <div class="mb-2">
                            <label for="jns_spp" class="form-label">Jenis SPP</label>
                            <input type="text"  class="form-control" id="jns_spp" name="jns_spp" readonly>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="mb-2">
                            <label for="ket" class="form-label">Keterangan</label>
                            <textarea class="form-control" name="ket" id="ket" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-lg-12">
                        <div class="mb-3"  style="float: right;">
                            <button id="simpan" class="btn btn-primary">Simpan</button>
                            <a href="{{ route('koreksi_nominal.index') }}"
                                class="btn btn-warning width-md waves-effect waves-light">Kembali</a>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- end card-body -->
    </div> <!-- end card -->
</div><!-- end col -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title mb-4">Daftar Koreksi
                    <button id="tambah" type="button" class="btn btn-success" style="float: right;">Tambah Kegiatan +</button> 
                </h4>
                <table id="dataKoreksi" class="table table-bordered dt-responsive table-responsive">
                    <thead>
                        <tr>
                            <th>id</th>
                            <th>Kode Sub Kegiatan</th>
                            <th>Nama Sub Kegiatan</th>
                            <th>Kode Rekening</th>
                            <th>Nama Rekening</th>
                            <th>Nilai Awal</th>
                            <th>Nilai Koreksi</th>
                            <th>Sumber</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div> <!-- end card-body -->
    </div> <!-- end card -->
</div><!-- end col -->
{{-- modal --}}
<div id="tampil-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal-title"></h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="card">
                            <div class="card-header" style="background-color:red;color:white">
                                Rincian Transaksi
                            </div>  
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="mb-2">
                                            <label for="kd_sub_kegiatan" class="form-label">Kode Sub Kegiatan</label>
                                            <select class="form-control select2-modal" id="kd_sub_kegiatan" name="kd_sub_kegiatan" required></select>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-2">
                                            <label for="nm_sub_kegiatan" class="form-label">Nama Sub Kegiatan</label>
                                            <input type="text" id="nm_sub_kegiatan" name="nm_sub_kegiatan" class="form-control" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="mb-2">
                                            <label for="kd_rek6" class="form-label">Kode Rekening</label>
                                            <select class="form-control select2-modal" id="kd_rek6" name="kd_rek6" required></select>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-2">
                                            <label for="nm_rek6" class="form-label">Nama Rekening</label>
                                            <input type="text" id="nm_rek6" name="nm_rek6" class="form-control" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="mb-2">
                                            <label for="nilai_awal" class="form-label">Nilai</label>
                                            <input type="text" id="nilai_awal" name="nilai_awal" class="form-control" readonly>
                                        </div>
                                    </div>
                                    <input type="hidden" id="id_trd" name="id_trd" class="form-control" readonly>
                                </div>
                            </div>
                        </div> 
                       
                        <div class="card">
                            <div class="card-header" style="background-color:green;color:white">
                                Transaksi Koreksi
                            </div>    
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="mb-2">
                                            <label for="kd_sumber_dana" class="form-label">Sumber Dana</label>
                                            <input type="text" id="kd_sumber_dana" name="kd_sumber_dana" class="form-control" readonly>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="mb-2">
                                            <label for="nilai_sumber_dana" class="form-label">Nilai Sumber Dana</label>
                                            <input type="text" id="nilai_sumber_dana" name="nilai_sumber_dana" class="form-control" readonly>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="mb-2">
                                            <label for="realisasi_sumber_dana" class="form-label">Realisasi Sumber Dana</label>
                                            <input type="text" id="realisasi_sumber_dana" name="realisasi_sumber_dana" class="form-control" readonly>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-3">
                                        <div class="mb-2">
                                            <label for="nilai_anggaran" class="form-label">Nilai Anggaran</label>
                                            <input type="text" id="nilai_anggaran" name="nilai_anggaran" class="form-control" readonly>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="mb-2">
                                            <label for="nilai_spd" class="form-label">Nilai SPD</label>
                                            <input type="text" id="nilai_spd" name="nilai_spd" class="form-control" readonly>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="mb-2">
                                            <label for="nilai_angkas" class="form-label">Nilai Angkas</label>
                                            <input type="text" id="nilai_angkas" name="nilai_angkas" class="form-control" readonly>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="mb-2">
                                            <label for="nilai_realisasi" class="form-label">Realisasi</label>
                                            <input type="text" id="nilai_realisasi" name="nilai_realisasi" class="form-control" readonly>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-3">
                                        <div class="mb-2">
                                            <label for="nilai_tersedia" class="form-label">Dana Tersedia</label>
                                            <input type="text" id="nilai_tersedia" name="nilai_tersedia" class="form-control" readonly>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="mb-2">
                                            <label for="nilai_transaksi" class="form-label">Nilai Transaksi</label>
                                            <input type="text" id="nilai_transaksi" name="nilai_transaksi" pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" data-type="currency" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> 
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="simpan-koreksi" class="btn btn-primary">Simpan</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div><!-- /.modal-content -->
        </form>
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@endsection

@section('js')
    @include('jurnal_koreksi.koreksi_nominal.js.create')
@endsection
