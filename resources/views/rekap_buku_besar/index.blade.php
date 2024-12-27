@extends('layouts.template')
@section('title', 'REKAP BUKU BESAR | SIMAKDA')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">REKAP BUKU BESAR</h4>
                    <p class="sub-header">
                        REKAP BUKU BESAR.
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
                        {{-- tanggal awal --}}
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="tgl_awal" class="form-label">Tanggal Awal</label>
                                <input type="date" id="tgl_awal" name="tgl_awal" class="form-control">
                            </div>
                        </div>
                        {{-- tanggal akhir --}}
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="tgl_akhir" class="form-label">Tanggal Akhir</label>
                                <input type="date" id="tgl_akhir" name="tgl_akhir" class="form-control">
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        {{-- jenis rekening --}}
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="jns_rekening" class="form-label">Jenis Rekening</label>
                                <select class="form-control select2" id="jns_rekening" name="jns_rekening">
                                    <option value=""></option>
                                    <option value="1">Jenis</option>
                                    <option value="2">Objek</option>
                                    <option value="3">Rincian Objek</option>
                                </select>
                            </div>
                        </div>
                        {{-- Rekening --}}
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="rekening" class="form-label">Rekening</label>
                                <select class="form-control select2" id="rekening" name="rekening">
                                    <option value=""></option>
                                    <option value="0">0</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                    <option value="7">7</option>
                                    <option value="8">8</option>
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
    @include('rekap_buku_besar.js.index')
@endsection
