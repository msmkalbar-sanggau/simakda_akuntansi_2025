@extends('layouts.template')
@section('title', 'Rekap SP2D SKPD | SIMAKDA')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Rekap SP2D SKPD</h4>
                    <p class="sub-header">
                        Rekap SP2D yang belum diterima cair SKPD.
                    </p>

                    <div class="row">
                        {{-- jns_cetak --}}
                        <div class="col-lg-12">
                            <div class="mb-3">
                                <label for="jns_cetak" class="form-label">Jenis Cetak</label>
                                <select class="form-control select2" id="jns_cetak" name="jns_cetak">
                                    <option value=""></option>
                                    <option value="1">Penerimaan</option>
                                    <option value="2">Pencairan</option>
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
    @include('laporan_monitoring.rekap_sp2d_skpd.js.index')
@endsection
