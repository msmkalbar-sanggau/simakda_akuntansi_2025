@extends('layouts.template')
@section('title', 'Input Jurnal | SIMAKDA')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title">Input Jurnal</h4>
                <p class="sub-header">
                    Input Jurnal.
                </p>
                <div class="row">
                    {{-- kd skpd --}}
                    <div class="col-lg-6">
                        <div class="mb-2">
                            <label for="kd_skpd" class="form-label">Kode SKPD</label>
                            <select class="form-control select2" id="kd_skpd" name="kd_skpd">
                                <option value=""></option>
                                @foreach ($skpd as $value)
                                    <option value="{{ $value->kd_skpd }}" data-nama="{{ $value->nm_skpd }}">
                                        {{ $value->kd_skpd }} - {{ $value->nm_skpd }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{-- nama skpd --}}
                    <div class="col-lg-6">
                        <div class="mb-2">
                            <label for="nm_skpd" class="form-label">Nama SKPD</label>
                            <input type="text" id="nm_skpd" name="nm_skpd" class="form-control" readonly>
                        </div>
                    </div>
                </div>
                <div class="row">
                    {{-- no voucher --}}
                    <div class="col-lg-6">
                        <div class="mb-2">
                            <label for="no_voucher" class="form-label">No Voucher</label>
                            <input type="text" class="form-control" id="no_voucher" name="no_voucher">
                        </div>
                    </div>
                    {{-- tgl voucher --}}
                    <div class="col-lg-6">
                        <div class="mb-2">
                            <label for="tgl_voucher" class="form-label">Tanggal Voucher</label>
                            <input type="date" id="tgl_voucher" name="tgl_voucher" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="mb-2">
                            <label for="ket" class="form-label">Keterangan</label>
                            <textarea class="form-control" name="ket" id="ket" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="row"> 
                    {{-- Jenis Jurnal --}}
                    <div class="col-lg-6">
                        <div class="mb-2">
                            <label for="reev" class="form-label">Jenis Jurnal</label>
                            <select class="form-control select2" id="reev" name="reev">
                                <option value=""></option>
                                <option value="0">Umum</option>
                                <option value="2">Koreksi Persediaan</option>
                                <option value="1">Revaluasi</option>
                                <option value="3">Lain - Lain</option>
                            </select>
                        </div>
                    </div>

                    {{-- tgl real --}}
                    <div class="col-lg-6" id="div1">
                        <div class="mb-2">
                            <label for="tgl_real1" class="form-label">Umum</label>
                            <select class="form-control select2" id="tgl_real1" name="tgl_real1">
                                <option value=""></option>
                                <option value="0">Jurnal Umum</option>
                                <option value="20">Extracomptable</option>
                                <option value="21">Penghapusan</option>
                                <option value="22">Hibah Pemerintah Lainnya</option>
                                <option value="25">Koreksi Lain-Lain</option>
                                <option value="29">Reklas Aset Lain-Lain</option>
                            </select>
                        </div>
                    </div>

                    {{-- tgl real2 --}}
                    <div class="col-lg-6" id="div2">
                        <div class="mb-2">
                            <label for="tgl_real2" class="form-label">Lain-lain</label>
                            <select class="form-control select2" id="tgl_real2" name="tgl_real2">
                                <option value=""></option>
                                <option value="99">Saldo Awal Neraca</option>
                                <option value="89">Saldo Awal LO</option>
                                <option value="1">Penyisihan Piutang</option>
                                <option value="2">Koreksi Penyusutan</option>
                                <option value="3">Hibah Keluar</option>
                                <option value="4">Mutasi Masuk antar OPD</option>
                                <option value="5">Mutasi Keluar antar OPD</option>
                                <option value="6">Penghapusan TPTGR</option>
                                <option value="7">Perubahan Kode Rekening</option>
                                <option value="8">Koreksi Tanah</option>
                                <option value="9">Koreksi Utang Belanja</option>
                                <option value="10">Reklass Antar Akun</option>
                                <option value="11">Tagihan Penjualan Angsuran</option>
                                <option value="12">Penyertaan Modal</option>
                                <option value="13">Persediaan APBN yang belum Tercatat TA 2023</option>
                                <option value="15">Koreksi Dana Transfer Pemerintah Pusat</option>
                                <option value="16">Koreksi Gedung dan Bangunan</option>
                                <option value="17">Koreksi Persediaan</option>
                                <option value="18">Koreksi Kas</option>
                                <option value="19">Extracomptable</option>
                                <option value="23">Koreksi Peralatan dan Mesin</option>
                                <option value="24">Koreksi Jaringan Irigasi Jembatan</option>
                                <option value="26">Koreksi Aset Tetap Lainnya</option>
                                <option value="27">Koreksi Piutang</option>
                                <option value="28">Koreksi Aset Lain Lain</option>
                                <option value="30">Pelimpahan Masuk</option>
                                <option value="31">Pelimpahan Keluar</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="mb-3"  style="float: right;">
                            <button id="simpan" class="btn btn-primary">Simpan</button>
                            <a href="{{ route('input_jurnal.index') }}"
                                class="btn btn-warning width-md waves-effect waves-light">Kembali</a>
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
                <p style="color: red">*Untuk penginputan JURNAL MUTASI MASUK / MUTASI KELUAR hanya perlu menginputkan Nomor BAST dan Tanggal BAST. Terima kasih.</p>
                <h4 class="header-title mb-4">Rekening
                    <button id="tambah" type="button" class="btn btn-success" style="float: right;">Tambah Kegiatan +</button> 
                </h4>
                <table id="dataKegiatan" class="table table-bordered dt-responsive table-responsive">
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
                            <th>Aksi</th>
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
                            class="form-control" id="total_debet" name="total_debet">
                    </div>
                    <label for="total_kredit" class="col-md-2 col-form-label" style="text-align: right">Total
                        Kredit</label>
                    <div class="col-md-4">
                        <input type="text" style="text-align: right;background-color:white;border:none;" readonly
                            class="form-control" id="total_kredit" name="total_kredit">
                    </div>
                </div>
            </div>
        </div> <!-- end card-body -->
    </div> <!-- end card -->
</div><!-- end col -->
{{-- modal --}}
<div id="tampil-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal-title"></h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row"> 
                        {{-- Jenis --}}
                        <div class="col-lg-6">
                            <div class="mb-2">
                                <label for="jns" class="form-label">Jenis</label>
                                <select class="form-control select2-modal" id="jns" name="jns">
                                    <option value=""></option>
                                    <option value="0">0 || Perubahan SAL</option>
                                    <option value="1">1 || Aset</option>
                                    <option value="2">2 || Hutang</option>
                                    <option value="3">3 || Ekuitas</option>
                                    <option value="4">4 || Pendapatan</option>
                                    <option value="5">5 || Belanja</option>
                                    <option value="6">6 || Pembiayaan</option>
                                    <option value="7">7 || Pendapatan LO</option>
                                    <option value="8">8 || Beban LO</option>
                                </select>
                            </div>
                        </div>
                        {{-- rk --}}
                        <div class="col-lg-6">
                            <div class="mb-2">
                                <label for="rk" class="form-label">Debet / Kredit</label>
                                <select class="form-control select2-modal" id="rk" name="rk">
                                    <option value=""></option>
                                    <option value="D">Debet</option>
                                    <option value="K">Kredit</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row"> 
                        {{-- kd kegiatan --}}
                        <div class="col-lg-6">
                            <div class="mb-2">
                                <label for="kd_kegiatan" class="form-label">Kode Kegiatan</label>
                                <select class="form-control select2-modal" id="kd_kegiatan" name="kd_kegiatan" required></select>
                            </div>
                        </div>
                        <input type="hidden" name="nm_kegiatan" id="nm_kegiatan" readonly>

                        {{-- kd rekening --}}
                        <div class="col-lg-6">
                            <div class="mb-2">
                                <label for="kd_rek6" class="form-label">Kode Rekening</label>
                                <select class="form-control select2-modal" id="kd_rek6" name="kd_rek6" required></select>
                            </div>
                        </div>
                        <input type="hidden" name="nm_rek6" id="nm_rek6" readonly>
                    </div>
                    <div class="row"> 
                        {{-- nilai --}}
                        <div class="col-lg-12">
                            <div class="mb-2">
                                <label for="nilai" class="form-label">Nilai</label>
                                <input type="text" class="form-control" id="nilai" name="nilai" pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" data-type="currency" style="text-align: right" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        {{-- posting --}}
                        <div class="col-lg-6">
                            <div class="mb-2">
                                <input type="checkbox" class="form-check-input" id="posting" name="posting">
                                <label class="form-check-label" for="posting">Un-posting</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="simpan-kegiatan" class="btn btn-primary">Simpan</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div><!-- /.modal-content -->
        </form>
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@endsection

@section('js')
    @include('jurnal.input_jurnal.js.create')
@endsection
