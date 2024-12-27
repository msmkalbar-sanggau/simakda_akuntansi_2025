@extends('layouts.template')
@section('title', 'LRA H/B/T/S | SIMAKDA')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">LRA H/B/T/S</h4>
                    <p class="sub-header">
                        LRA HARIAN/BULANAN/TRIWULAN/SEMESTER.
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
                        {{-- jenis_data --}}
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="jenis_data" class="form-label">Jenis Data</label>
                                <select class="form-control select2" id="jenis_data" name="jenis_data">
                                    <option value=""></option>
                                    <option value="SPJ">SPJ</option>
                                    {{-- <option value="SP2D Terbit">SP2D Terbit</option> --}}
                                    {{-- <option value="SP2D Lunas">SP2D Lunas</option> --}}
                                    {{-- <option value="SP2D Advice">SP2D Advice</option> --}}
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        {{-- pilih kode --}}
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="kode" class="form-label">Pilih Kode</label>
                                <select class="form-control select2" id="kode" name="kode">
                                    <option value=""></option>
                                    <option value="map_lra_rinci_jenis">Jenis</option>
                                    <option value="map_lra_rinci_objek">Objek</option>
                                    <option value="map_lra_rinci_rincian_objek">Rincian Objek</option>
                                    <option value="map_lra_rinci_sub_rincian_objek">Sub Rincian Objek</option>
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
                        {{-- akumulasi --}}
                        <div class="col-lg-6 akumulasi">
                            <div class="mb-3">
                                <label for="akumulasi" class="form-label">Akumulasi</label>
                                <select class="form-control select2" id="akumulasi" name="akumulasi">
                                    <option value=""></option>
                                    <option value="1">Dengan Akumulasi</option>
                                    <option value="2">Tanpa Akumulasi</option>
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
                    {{--  <div class="row btt">
                        <div class="col-lg-12">
                            <div class="form-check mb-2 form-check-primary">
                                <input class="form-check-input" type="checkbox" id="btt" name="btt">
                                <label class="form-check-label" for="btt">BTT</label>
                            </div>
                        </div>
                    </div>  --}}
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
    @include('laporan_pemda.lra_hbts.js.index')
@endsection
