@extends('layouts.template')
@section('title', 'Lampiran I.3 | SIMAKDA')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title">Lampiran I.3</h4>
                <p class="sub-header">
                Rincian APBD menurut urusan pemerintahan daerah, organisasi, program, kegiatan, sub kegiatan, kelompok, dan jenis pendapatan, belanja, dan pembiayaan.
                </p>

                <div class="row">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="kd_skpd" class="form-label">Kode SKPD</label>
                                <select class="form-control select2" id="kd_skpd" name="kd_skpd">
                                    <option value=""></option>
                                    @foreach ($skpd as $value)
                                        <option value="{{ $value->kd_skpd }}" data-nama="{{ $value->nm_skpd }}">{{ $value->kd_skpd }} - {{ $value->nm_skpd}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="nm_skpd" class="form-label">Nama SKPD</label>
                                <input type="text" id="nm_skpd" name="nm_skpd" class="form-control" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="tgl_ttd" class="form-label">Tanggal TTD</label>
                                <input type="date" id="tgl_ttd" class="form-control">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="ttd" class="form-label">Penandatangan</label>
                                <select class="form-control select2" id="ttd" name="ttd">
                                    <option value=""></option>
                                    @foreach ($ttd as $value)
                                        <option value="{{ $value->nama }}">{{ $value->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="mb-3">
                                <center>
                                    <button class="btn btn-md btn-dark cetak" data-jenis="layar">Layar <i class="fa fa-print"></i></button>
                                    <button class="btn btn-md btn-danger cetak" data-jenis="pdf">PDF <i class="fas fa-file-pdf"></i></button>
                                    <button class="btn btn-md btn-success cetak" data-jenis="excel">Excel <i class="far fa-file-excel"></i></button>
                                </center>
                            </div>
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
@include('perda.lampiran_I.lampiran_I_3.js.index')
@endsection