@extends('layouts.template')
@section('title', 'Lihat Input Jurnal | SIMAKDA')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Lihat Input Jurnal</h4>
                    <p class="sub-header">
                        Lihat Input Jurnal.
                    </p>
                    <div class="row">
                        {{-- kd skpd --}}
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="kd_skpd" class="form-label">Kode SKPD</label>
                                <input type="text" class="form-control" name="kd_skpd" id="kd_skpd" value="{{ $dataJurnal->kd_skpd }}" readonly>
                            </div>
                        </div>
                        
                        {{-- nm skpd --}}
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="nm_skpd" class="form-label">Nama SKPD</label>
                                <input type="text" class="form-control" value="{{ $dataJurnal->nm_skpd }}" readonly>
                            </div>
                        </div>

                        {{-- no voucher --}}
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="no_voucher" class="form-label">No Voucher</label>
                                <input type="text" name="no_voucher" id="no_voucher" class="form-control" value="{{ $dataJurnal->no_voucher }}" readonly>
                            </div>
                        </div>

                        {{-- tgl voucher --}}
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="tgl_voucher" class="form-label">Tanggal Voucher</label>
                                <input type="text" class="form-control" value="{{ tanggal($dataJurnal->tgl_voucher) }}" readonly>
                            </div>
                        </div>
                        
                        {{-- jenis jurnal --}}
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="jns_jurnal" class="form-label">Jenis Jurnal</label>
                                <input type="text" class="form-control" value="{{ dataReev($dataJurnal->reev) }}" readonly>
                            </div>
                        </div>

                        {{-- tgl real --}}
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="tgl_real" class="form-label">-</label>
                                <input type="text" class="form-control" value="{{ dataReal($dataJurnal->tgl_real) }}" readonly>
                            </div>
                        </div>

                        {{-- keterangan --}}
                        <div class="col-lg-12">
                            <div class="mb-3">
                                <label for="ket" class="form-label">Keterangan</label>
                                <textarea class="form-control" rows="2" readonly>{{ $dataJurnal->ket }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="mb-3">
                                <a href="{{ route('input_jurnal.index') }}" class="btn btn-warning width-md waves-effect waves-light" style="float: right;">Kembali</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- end card-body -->
        </div> <!-- end card -->
    </div><!-- end col -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Daftar Rekening Tujuan</h4>
                    <table id="datainputJurnal" class="table table-bordered dt-responsive table-responsive">
                        <thead>
                        <tr>
                            <th>Kode Kegiatan</th>
                            <th>Nama Kegiatan</th>
                            <th>Kode Rekening</th>
                            <th>Nama Rekening</th>
                            <th>Debet</th>
                            <th>Kredit</th>
                            <th>D/K</th>
                            <th>Posting</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    <div class="mb-2 mt-2 row">
                        <label for="total_debet" class="col-md-2 col-form-label" style="text-align: right">Total
                            Debet</label>
                        <div class="col-md-4">
                            <input type="text" style="text-align: right;background-color:white;border:none;" readonly
                                class="form-control" id="total_debet" name="total_debet"
                                value="{{ rupiah($dataJurnal->total_d) }}">
                        </div>
                        <label for="total_kredit" class="col-md-2 col-form-label" style="text-align: right">Total
                            Kredit</label>
                        <div class="col-md-4">
                            <input type="text" style="text-align: right;background-color:white;border:none;" readonly
                                class="form-control" id="total_kredit" name="total_kredit"
                                value="{{ rupiah($dataJurnal->total_k) }}">
                        </div>
                    </div>
                </div>
            </div> <!-- end card-body -->
        </div> <!-- end card -->
    </div><!-- end col -->
@endsection

@section('js')
    @include('jurnal.input_jurnal.js.show')
@endsection
