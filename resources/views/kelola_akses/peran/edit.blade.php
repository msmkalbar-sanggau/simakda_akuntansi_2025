@extends('layouts.template')
@section('title', 'Ubah Peran | SIMAKDA')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            @if (session('status'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                {{ session('status') }}
            </div>
            @endif
            <div class="card-header" style="font-size: 18px; color: black">
                Form Ubah Peran
            </div>
            <div class="card-body">
                <form action="{{ route('peran.update', $data_peran->id) }}" method="post" enctype="multipart/form-data">
                    @method('PUT')
                    {{ csrf_field() }}
                    <div class="row mb-2">
                        <label for="role" class="col-3 col-xl-3 col-form-label">Kode Peran</label>
                        <div class="col-9 col-xl-9">
                            <input type="text" class="form-control @error('role') is-invalid @enderror" id="role" name="role" value="{{ $data_peran->role }}" placeholder="Silahkan isi dengan Kode Peran" required>
                            @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-2">
                        <label for="nm_role" class="col-3 col-xl-3 col-form-label">Nama Peran</label>
                        <div class="col-9 col-xl-9">
                            <input type="text" class="form-control @error('nm_role') is-invalid @enderror" id="nm_role" name="nm_role" value="{{ $data_peran->nm_role }}" placeholder="Silahkan isi dengan Nama Peran" required>
                            @error('nm_role')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <label for="nm_role" class="col-md-12 col-form-label" style="text-align: center; font-size:15px;">Hak Akses</label>
                        <div class="col-md-12">
                            @foreach ($daftar_hak_akses as $value)
                            <div class="card">
                                <div class="card-header" style="text-align: center">{{ $value->display_name }}</div>
                                <div class="card-body">
                                    <div class="row">
                                        @foreach ($daftar_hak_akses1 as $value1)
                                        @if ($value->id == $value1->urut_akses)
                                        <div class="col-md-4 mb-2">
                                            <div class="form-check form-switch">
                                                <input type="checkbox" class="form-check-input" id="{{ $value1->id }}" name="hak_akses[]" {{ collect($hak_akses1)->contains($value1->id) ? 'checked' : '' }} value="{{ $value1->id }}">
                                                <label class="form-check-label" for="{{ $value1->id }}">{{ $value1->display_name }}</label>
                                            </div>
                                        </div>
                                        @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
    
                    <div class="justify-content-end row" style="float: right;"> 
                        <div class="col-12 col-xl-12">
                            <button class="btn btn-primary waves-effect waves-light">Simpan</button>
                            <a href="{{ route('peran.index') }}" class="btn btn-warning btn-md">Kembali</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

