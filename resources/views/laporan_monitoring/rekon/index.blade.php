@extends('layouts.template')
@section('title', 'REKON | SIMAKDA')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">REKON</h4>
                    <p class="sub-header">
                        Laporan REKON.
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
                                    value="periode">
                                <label class="form-check-label" for="jenis_tanggal">Periode</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        {{-- periode --}}
                        <div class="col-lg-6 periode">
                            <div class="mb-3">
                                <label for="periode" class="form-label">Periode</label>
                                <select class="form-control select2" id="periode" name="periode">
                                    <option value=""></option>
                                    <option value="1">Januari - Maret (Triwulan I)</option>
                                    <option value="2">April - Juni (Triwulan II)</option>
                                    <option value="3">Juli - September (Triwulan III)</option>
                                    <option value="4">Oktober - Desember (Triwulan IV)</option>
                                </select>
                            </div>
                        </div>
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
                        {{-- jenis --}}
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="jns_rekon" class="form-label">Jenis Rekon</label>
                                <select class="form-control select2" id="jns_rekon" name="jns_rekon">
                                    <option value=""></option>
                                    <option value="1">SPJ</option>
                                    <option value="2">SP2D</option>
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
    @include('laporan_monitoring.rekon.js.index')
@endsection
