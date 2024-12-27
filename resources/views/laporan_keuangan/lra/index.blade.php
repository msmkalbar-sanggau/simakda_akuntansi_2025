@extends('layouts.template')
@section('title', 'LRA SAP | SIMAKDA')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">LRA SAP</h4>
                    <p class="sub-header">
                        LRA SAP.
                    </p>
                    <input type="hidden" id="idskpd" name="idskpd" value="{{ $cekSKPD }}">
                    @if($cekSKPD > 0)
                        <div class="row">
                            {{-- kd skpd --}}
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="kd_skpd" class="form-label">Kode SKPD</label>
                                    <select class="form-control select2" id="kd_skpd" name="kd_skpd">
                                        <option value=""></option>
                                        <option value="ALL" data-nama="Semua SKPD">ALL SKPD</option>
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
                    @else 
                        <div class="row">
                        {{-- kd skpd --}}
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="kd_skpd" class="form-label">Kode SKPD</label>
                                    <input type="text" id="kd_skpd" name="kd_skpd" class="form-control" value="{{ $skpdL->kd_skpd }}" disabled>
                                </div>
                            </div> 

                            {{-- nm skpd --}}
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="nm_skpd" class="form-label">Nama SKPD</label>
                                    <input type="text" id="nm_skpd" name="nm_skpd" class="form-control" value="{{ $skpdL->nm_skpd }}" disabled>
                                </div>
                            </div> 
                        </div> 
                    @endif
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
                        {{-- jenis anggaran --}}
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="jns_ang" class="form-label">Jenis Anggaran</label>
                                <select class="form-control select2" id="jns_ang" name="jns_ang">
                                    <option value=""></option>
                                    @foreach ($jenis_anggaran as $value)
                                        <option value="{{ $value->jns_ang }}">{{ $value->nama }} ({{ tanggal($value->tgl_dpa) }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        {{-- label --}}
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="label" class="form-label">Label Audited</label>
                                <select class="form-control select2" id="label" name="label">
                                    <option value=""></option>
                                    <option value="1">Unaudited</option>
                                    <option value="2">Audited</option>
                                    <option value="0">Kosong</option>
                                </select>
                            </div>
                        </div>
                        {{-- permen --}}
                        <div class="col-lg-3">
                            <div class="mb-3">
                                <label for="permen" class="form-label">Permen</label>
                                <select class="form-control select2" id="permen" name="permen">
                                    <option value=""></option>
                                    <option value="map_lra_permen_90">Permen 90</option>
                                    <option value="map_lra_permen_90_objek">Permen 90 Objek</option>
                                    <option value="map_lra_permen_90_rincian_objek">Permen 90 RINCIAN OBJEK(RO)</option>
                                </select>
                            </div>
                        </div>
                        {{-- ttd --}}
                        <div class="col-lg-3">
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
    @include('laporan_keuangan.lra.js.index')
@endsection
