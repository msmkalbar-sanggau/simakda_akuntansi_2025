@extends('layouts.template')
@section('title', 'Rekap Transaksi CMS | SIMAKDA')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Rekap Transaksi CMS</h4>
                    <p class="sub-header">
                        Rekap Transaksi CMS.
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
                        {{-- status --}}
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status Validasi</label>
                                <select class="form-control select2" id="status" name="status">
                                    <option value=""></option>
                                    <option value="1">Sudah Validasi</option>
                                    <option value="2">Belum Validasi</option>
                                </select>
                            </div>
                        </div>
                        {{-- bulan --}}
                        <div class="col-lg-6">
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
    @include('laporan_monitoring.rekap_transaksi_cms.js.index')
@endsection
