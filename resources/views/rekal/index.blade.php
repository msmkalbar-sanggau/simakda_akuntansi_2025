@extends('layouts.template')
@section('title', 'Rekal Jurnal | SIMAKDA')
@section('content')
    <!-- start page title -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                Rekal Jurnal Umum
            </div>
            <div class="card-body">
                @csrf
                <div class="mb-3 row">
                    <label for="kd_skpd" class="col-md-12 col-form-label">
                        <center>SIMAKDA</center>
                    </label>


                    <div class="text-center">
                        <button id="mapping_all" class="btn btn-success" style="float: center;">1. POSTING JURNAL
                            SEMUA SKPD</button>
                    </div>
                </div>
                {{--  <div class="mb-3 row">
                    <label for="kd_skpd" class="col-md-12 col-form-label">
                        <center>LRA</center>
                    </label>
                    <div class="text-center">
                        <button id="mapping_rekap" class="btn btn-success" style="float: center;">1. POSTING
                            JURNAL REKAP PERMEN 90</button>
                    </div>
                </div>  --}}
            </div>

        </div>
    </div>
    <div id="loading" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <img src='{{ asset('template/loading.gif') }}' width='100%' height='320px'>
            </div>
        </div>
    </div>
    @endsection
    @section('js')
        @include('rekal.js.index');
    @endsection
