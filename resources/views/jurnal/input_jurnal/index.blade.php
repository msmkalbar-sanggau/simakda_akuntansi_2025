@extends('layouts.template')
@section('title', 'Input Jurnal | SIMAKDA')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header" style="font-size: 20px; color: black">
                    Input Jurnal
                    <a href="{{ route('input_jurnal.create') }}" class="btn btn-primary" style="float: right;">Tambah +</a>
                </div>
                @if($cekskpd > 1)
                <div class="card-body">
                    <div class="row">
                        <label for="kd_skpd" class="col-2 col-form-label">Kode SKPD</label>
                        <div class="col-4">
                            <select class="form-control select2" id="kd_skpd" name="kd_skpd" required>
                                <option value="all">Semua SKPD</option>
                                @foreach ($daftar_skpd as $skpd)
                                <option value="{{ $skpd->kd_skpd }}" {{ old('kd_skpd') == $skpd->kd_skpd ? 'selected' : '' }} data-nama="{{ $skpd->nm_skpd }}">
                                    {{ $skpd->kd_skpd }} |
                                    {{ $skpd->nm_skpd }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-4">
                            <button class="btn btn-md btn-success cetak" id="btnProses">Proses</button>
                        </div>
                    </div>
                </div>
                @else
                <div class="card-body">
                    <input type="text"  class="form-control" id="kd_skpd" name="kd_skpd" value={{ $daftar_skpd->kd_skpd }} hidden>
                </div>
                @endif
                <div class="card-body">
                    <table id="datainputJurnal" class="table table-bordered dt-responsive table-responsive">
                        <thead>
                        <tr>
                            <th>No</th>
                            <th>Aksi</th>
                            <th>No Voucher</th>
                            <th>Tanggal</th>
                            <th>Nama SKPD</th>
                            <th>Keterangan</th>
                        </tr>
                        </thead>


                        <tbody>
                        </tbody>
                    </table>
                </div>
                <!-- end row-->
            </div> <!-- end card-body -->
        </div> <!-- end card -->
    </div><!-- end col -->
    </div>
@endsection

@section('js')
    @include('jurnal.input_jurnal.js.index')
@endsection
