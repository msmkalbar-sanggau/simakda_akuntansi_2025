@extends('layouts.template')
@section('title', 'Lihat Peran | SIMAKDA')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header" style="font-size: 18px; color: black">
                List Data
                <a href="{{ route('peran.index') }}" class="btn btn-warning width-md waves-effect waves-light" style="float: right;">Kembali</a>
            </div>
            <div class="card-body">
                <table id="datatable" class="table table-bordered dt-responsive table-responsive nowrap">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Peran</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($daftar_hak_akses as $value)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $value->display_name }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection