@extends('layouts.template')
@section('title', 'LRA Program | SIMAKDA')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">LRA Program</h4>
                    <p class="sub-header">
                        LRA Program.
                    </p>
                    <div class="row">
                        {{-- kd skpd --}}
                        <div class="col-lg-6">
                            <div class="mb-3">
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
                            <div class="mb-3">
                                <label for="nm_skpd" class="form-label">Nama SKPD</label>
                                <input type="text" id="nm_skpd" name="nm_skpd" class="form-control" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        {{-- penandatangan --}}
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="penandatangan" class="form-label">Penandatangan</label>
                                <select class="form-control select2" id="penandatangan" name="penandatangan">
                                    <option value=""></option>
                                </select>
                            </div>
                        </div>
                        {{-- tanggal ttd --}}
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="tgl_ttd" class="form-label">Tanggal TTD</label>
                                <input type="date" id="tgl_ttd" name="tgl_ttd" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-1">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="jenis_periode" id="jenis_bulan"
                                    value="bulan" checked>
                                <label class="form-check-label" for="jenis_bulan">Bulan</label>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-check mb-2 form-check-success">
                                <input class="form-check-input" type="radio" name="jenis_periode" id="jenis_tanggal"
                                    value="tanggal">
                                <label class="form-check-label" for="jenis_tanggal">Tangal</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        {{-- bulan --}}
                        <div class="col-lg-6 bulan">
                            <div class="mb-3">
                                <label for="bulan" class="form-label">Bulan</label>
                                <select class="form-control select2" id="bulan" name="bulan">
                                    <option value=""></option>
                                    @foreach (getBulan() as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        {{-- tanggal awal --}}
                        <div class="col-lg-3 tanggal">
                            <div class="mb-3">
                                <label for="tgl_awal" class="form-label">Tanggal Awal</label>
                                <input type="date" id="tgl_awal" name="tgl_awal" class="form-control">
                            </div>
                        </div>
                        {{-- tanggal akhir --}}
                        <div class="col-lg-3 tanggal">
                            <div class="mb-3">
                                <label for="tgl_akhir" class="form-label">Tanggal Akhir</label>
                                <input type="date" id="tgl_akhir" name="tgl_akhir" class="form-control">
                            </div>
                        </div>
                        {{-- pilih kode --}}
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="kode" class="form-label">Pilih Kode</label>
                                <select class="form-control select2" id="kode" name="kode">
                                    <option value=""></option>
                                    <option value="4">Jenis</option>
                                    <option value="6">Objek</option>
                                    <option value="8">Rincian Objek</option>
                                    <option value="12">Sub Rincian Objek</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        {{-- jenis anggaran --}}
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="jns_ang" class="form-label">Jenis Anggaran</label>
                                <select class="form-control select2" id="jns_ang" name="jns_ang">
                                    <option value=""></option>
                                    @foreach ($jenis_anggaran as $value)
                                        <option value="{{ $value->jns_ang }}">{{ $value->nama }}
                                            ({{ tanggal($value->tgl_dpa) }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        {{-- ttd --}}
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="ttd" class="form-label">TTD</label>
                                <select class="form-control select2" id="ttd" name="ttd">
                                    <option value=""></option>
                                    <option value="1">Dengan TTD</option>
                                    <option value="2">Tanpa TTD</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="mb-3">
                            <center>
                                <button class="btn btn-md btn-dark cetak" data-jenis="layar">Layar <i
                                        class="fa fa-print"></i></button>
                                <button class="btn btn-md btn-danger cetak" data-jenis="pdf">PDF <i
                                        class="fas fa-file-pdf"></i></button>
                                <button class="btn btn-md btn-success cetak" data-jenis="excel">Excel <i
                                        class="far fa-file-excel"></i></button>
                            </center>
                        </div>
                    </div>
                </div>
                <!-- end row-->
            </div> <!-- end card-body -->
        </div> <!-- end card -->
    </div><!-- end col -->
    </div>
@endsection

@section('js')
    @include('laporan_pemda.lra_program.js.index')
@endsection
