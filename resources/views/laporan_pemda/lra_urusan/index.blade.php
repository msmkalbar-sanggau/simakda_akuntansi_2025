@extends('layouts.template')
@section('title', 'LRA URUSAN | SIMAKDA')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">LRA URUSAN</h4>
                    <p class="sub-header">
                        LRA URUSAN.
                    </p>
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
                        {{-- jenis anggaran --}}
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="jns_ang" class="form-label">Jenis Anggaran</label>
                                <select class="form-control select2" id="jns_ang" name="jns_ang">
                                    <option value=""></option>
                                    @foreach ($jenis_anggaran as $value)
                                        <option value="{{ $value->jns_ang }}">{{ $value->nama }}
                                            ({{ tanggal($value->tgl_dpa) }})
                                        </option>
                                    @endforeach
                                </select>
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
                        {{-- jenis --}}
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="jenis_cetak" class="form-label">Jenis</label>
                                <select class="form-control select2" id="jenis_cetak" name="jenis_cetak">
                                    <option value=""></option>
                                    <option value="1">Global</option>
                                    <option value="2">Rinci</option>
                                </select>
                            </div>
                        </div>

                    </div>
                    <div class="row btt" hidden>
                        <div class="col-lg-12">
                            <div class="form-check mb-2 form-check-primary">
                                <input class="form-check-input" type="checkbox" id="btt" name="btt">
                                <label class="form-check-label" for="btt">BTT</label>
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
    @include('laporan_pemda.lra_urusan.js.index')
@endsection
