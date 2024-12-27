@extends('layouts.template')
@section('title', 'Hak Akses | SIMAKDA')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header" style="font-size: 18px; color: black">
                List Data
            </div>
            <div class="card-body">
                <table id="hakAksesTable" class="table table-bordered dt-responsive table-responsive nowrap">
                    <thead>
                        <tr>
                            <th style="width: 5px">No</th>
                            <th>Nama Peran</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
@include('kelola_akses.hak_akses.js.index')
@endsection
