@extends('layouts.template')
@section('title', 'Lihat Koreksi Rekening | SIMAKDA')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title">Lihat Koreksi Rekening</h4>
                <p class="sub-header">
                    Lihat Koreksi Rekening.
                </p>
                <div class="row">
                    {{-- kd skpd --}}
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="kd_skpd" class="form-label">Kode SKPD</label>
                            <input type="text" class="form-control" name="kd_skpd" id="kd_skpd" value="{{ $dataKoreksiRekening->kd_skpd }}" readonly>
                        </div>
                    </div>
                    
                    {{-- nm skpd --}}
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="nm_skpd" class="form-label">Nama SKPD</label>
                            <input type="text" class="form-control" value="{{ $dataKoreksiRekening->nm_skpd }}" readonly>
                        </div>
                    </div>
                </div>
                <div class="row">
                    {{-- no koreksi --}}
                    <div class="col-lg-3">
                        <div class="mb-2">
                            <label for="no_bukti" class="form-label">No. Koreksi</label>
                            <input type="text" class="form-control" name="no_bukti" id="no_bukti" value="{{ $dataKoreksiRekening->no_bukti }}" readonly>
                        </div>
                    </div>
                    {{-- tgl --}}
                    <div class="col-lg-3">
                        <div class="mb-2">
                            <label for="tgl" class="form-label">Tanggal Koreksi</label>
                            <input type="text" class="form-control" value="{{ tanggal($dataKoreksiRekening->tgl_bukti) }}" readonly>
                        </div>
                    </div>

                    {{-- No. Transaksi Awal --}}
                    <div class="col-lg-3">
                        <div class="mb-2">
                            <label for="no_bukti" class="form-label">No. Transaksi Awal</label>
                            <input type="text" class="form-control" value="{{ $dataKoreksiRekening->no_transaksi_awal }}" readonly>
                        </div>
                    </div>
                    {{-- tgl --}}
                    <div class="col-lg-3">
                        <div class="mb-2">
                            <label for="tgl" class="form-label">Tanggal Koreksi</label>
                            <input type="text" class="form-control" value="{{ tanggal($dataKoreksiRekening->tgl_kas_trh) }}" readonly>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="mb-2">
                            <label for="ket" class="form-label">Keterangan</label>
                            <textarea class="form-control" rows="2">{{ $dataKoreksiRekening->ket }}</textarea>
                        </div>
                    </div>
                    {{-- no sp2d --}}
                    <div class="col-lg-3">
                        <div class="mb-2">
                            <label for="no_sp2d" class="form-label">No SP2D</label>
                            <input type="text" class="form-control" value="{{ $dataKoreksiRekening->no_sp2d }}" readonly>
                        </div>
                    </div>
                    {{-- jns_spp --}}
                    <div class="col-lg-3">
                        <div class="mb-2">
                            <label for="jns_spp" class="form-label">Jenis SPP</label>
                            <input type="text" class="form-control" value="{{ dataSpp($dataKoreksiRekening->jns_spp) }}" readonly>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-lg-12">
                        <div class="mb-3"  style="float: right;">
                            <a href="{{ route('koreksi_rekening.index') }}"
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
                <h4 class="header-title mb-4">Daftar Koreksi</h4>
                <table id="dataKoreksi" class="table table-bordered dt-responsive table-responsive">
                    <thead>
                        <tr>
                            <th>Kode Sub Kegiatan</th>
                            <th>Nama Sub Kegiatan</th>
                            <th>Kode Rekening</th>
                            <th>Nama Rekening</th>
                            <th>Nilai</th>
                            <th>Sumber</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div> <!-- end card-body -->
    </div> <!-- end card -->
</div><!-- end col -->
@endsection

@section('js')
    @include('jurnal_koreksi.koreksi_rekening.js.show')
@endsection
