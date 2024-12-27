@extends('layouts.template')
@section('title', 'Ubah Pengguna | SIMAKDA')

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
                Form Ubah Pengguna
            </div>
            <div class="card-body">
                <form action="{{ route('pengguna.update', $data_pengguna->id) }}" method="post" enctype="multipart/form-data">
                    @method('PUT')
                    {{ csrf_field() }}
                    <div class="row mb-2">
                        <label for="username" class="col-3 col-xl-3 col-form-label">Username</label>
                        <div class="col-9 col-xl-9">
                            <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username" value="{{ $data_pengguna->username }}" placeholder="Silahkan isi dengan Username" required>
                            @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-2">
                        <label for="nama" class="col-3 col-xl-3 col-form-label">Nama</label>
                        <div class="col-9 col-xl-9">
                            <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ $data_pengguna->nama }}" placeholder="Silahkan isi dengan Nama" required>
                            @error('nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-2">
                        <label for="kd_skpd" class="col-3 col-xl-3 col-form-label">Kode SKPD</label>
                        <div class="col-9 col-xl-9">
                            <select class="form-control select2" id="kd_skpd" name="kd_skpd" required>
                                <option value=""></option>
                                @foreach ($daftar_skpd as $skpd)
                                <option value="{{ $skpd->kd_skpd }}" {{ old('kd_skpd') == $skpd->kd_skpd ? 'selected' : '' }} data-nama="{{ $skpd->nm_skpd }}" {{ $data_pengguna->kd_skpd == $skpd->kd_skpd ? 'selected' : '' }}>
                                    {{ $skpd->kd_skpd }} |
                                    {{ $skpd->nm_skpd }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <label for="peran" class="col-3 col-xl-3 col-form-label">Peran</label>
                        <div class="col-9 col-xl-9">
                            <select class="form-control select2" id="peran" name="peran" required>
                                <option value=""></option>
                                @foreach ($daftar_peran as $value)
                                <option value="{{ $value->id }}" {{ old('peran') == $value->id ? 'selected' : '' }} {{ $data_pengguna->role == $value->id ? 'selected' : '' }}>
                                    {{ $value->role }} |
                                    {{ $value->nm_role }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="justify-content-end row" style="float: right;"> 
                        <div class="col-12 col-xl-12">
                            <button class="btn btn-primary waves-effect waves-light">Simpan</button>
                            <a href="{{ route('pengguna.index') }}" class="btn btn-warning btn-md">Kembali</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
@include('kelola_akses.pengguna.js.create')
@endsection
