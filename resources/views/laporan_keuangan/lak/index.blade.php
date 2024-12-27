@extends('layouts.template')
@section('title', 'LAK | SIMAKDA')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">LAK</h4>
                    <p class="sub-header">
                        Laporan Arus Kas.
                    </p>

                    <div class="row">
                        {{-- bulan --}}
                        <div class="col-lg-12">
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
                    <div class="row">
                        {{-- Penandatangan --}}
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="bulan" class="form-label">Penandatangan</label>
                                <select class="form-control select2" id="penandatangan" name="penandatangan">
                                    <option value=""></option>
                                    @foreach ($ttd as $value)
                                        <option value="{{ $value->nip }}">{{ $value->nip }} - {{ $value->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            {{-- Tanggal Tandatangan --}}
                            <div class="mb-3">
                                <label for="bulan" class="form-label">Tangal TTD</label>
                                <input type="date" id="tgl_ttd" name="tgl_ttd" class="form-control">
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
    @include('laporan_keuangan.lak.js.index')
@endsection
