@extends('layouts.template')
@section('title', 'Koreksi Rekening | SIMAKDA')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header" style="font-size: 20px; color: black">
                    Koreksi Rekening
                    <a href="{{ route('koreksi_rekening.create') }}" class="btn btn-primary" style="float: right;">Tambah +</a>
                </div>
                <div class="card-body">
                    <table id="dataKoreksiRekening" class="table table-bordered dt-responsive table-responsive">
                        <thead>
                        <tr>
                            <th>No</th>
                            <th>Aksi</th>
                            <th>No Koreksi</th>
                            <th>Tanggal</th>
                            <th>Keterangan</th>
                            <th>No Bukti</th>
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
    @include('jurnal_koreksi.koreksi_rekening.js.index')
@endsection
