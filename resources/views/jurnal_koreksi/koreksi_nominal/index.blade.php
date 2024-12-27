@extends('layouts.template')
@section('title', 'Koreksi Nominal | SIMAKDA')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header" style="font-size: 20px; color: black">
                    Koreksi Nominal
                    <a href="{{ route('koreksi_nominal.create') }}" class="btn btn-primary" style="float: right;">Tambah +</a>
                </div>
                <div class="card-body">
                    <table id="dataKoreksiNominal" class="table table-bordered dt-responsive table-responsive">
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
    @include('jurnal_koreksi.koreksi_nominal.js.index')
@endsection
