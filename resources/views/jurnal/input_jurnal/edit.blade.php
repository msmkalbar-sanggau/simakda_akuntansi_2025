@extends('layouts.template')
@section('title', 'Ubah Input Jurnal | SIMAKDA')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title">Ubah Input Jurnal</h4>
                <p class="sub-header">
                    Ubah Input Jurnal.
                </p>
                <div class="row">
                    {{-- no voucher lama --}}
                    <div class="col-lg-6">
                        <div class="mb-2">
                            <label for="no_voucher_lama" class="form-label">No. Tersimpan</label>
                            <input type="text" class="form-control" style="font-weight: bold" name="no_voucher_lama" id="no_voucher_lama" readonly value="{{ $jurnal->no_voucher }}">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-2">
                            <label for="no_voucher_lama" class="form-label">*</label>
                            <div style="color: red">
                                <i>Tidak Perlu diisi atau di Edit</i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    {{-- kd skpd --}}
                    <div class="col-lg-6">
                        <div class="mb-2">
                            <label for="kd_skpd" class="form-label">Kode SKPD</label>
                            <select class="form-control select2" id="kd_skpd" name="kd_skpd">
                                <option value=""></option>
                                @foreach ($skpd as $value)
                                    <option value="{{ $value->kd_skpd }}" {{ $jurnal->kd_skpd == $value->kd_skpd ? 'selected' : '' }} data-nama="{{ $value->nm_skpd }}">
                                        {{ $value->kd_skpd }} - {{ $value->nm_skpd }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{-- nama skpd --}}
                    <div class="col-lg-6">
                        <div class="mb-2">
                            <label for="nm_skpd" class="form-label">Nama SKPD</label>
                            <input type="text" id="nm_skpd" name="nm_skpd" class="form-control" value="{{ $jurnal->nm_skpd }}" readonly>
                        </div>
                    </div>
                </div>
                <div class="row">
                    {{-- no voucher --}}
                    <div class="col-lg-6">
                        <div class="mb-2">
                            <label for="no_voucher" class="form-label">No Voucher</label>
                            <input type="text" class="form-control" id="no_voucher" name="no_voucher" value="{{ $jurnal->no_voucher }}">
                        </div>
                    </div>
                    {{-- tgl voucher --}}
                    <div class="col-lg-6">
                        <div class="mb-2">
                            <label for="tgl_voucher" class="form-label">Tanggal Voucher</label>
                            <input type="date" id="tgl_voucher" name="tgl_voucher" class="form-control" value="{{ $jurnal->tgl_voucher }}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="mb-2">
                            <label for="ket" class="form-label">Keterangan</label>
                            <textarea class="form-control" name="ket" id="ket" rows="2">{{ $jurnal->ket }}</textarea>
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
                                <option value="0" {{ $jurnal->reev == 0 ? 'selected' : '' }}>Umum</option>
                                <option value="2" {{ $jurnal->reev == 2 ? 'selected' : '' }}>Koreksi Persediaan</option>
                                <option value="1" {{ $jurnal->reev == 1 ? 'selected' : '' }}>Revaluasi</option>
                                <option value="3" {{ $jurnal->reev == 3 ? 'selected' : '' }}>Lain - Lain</option>
                            </select>
                        </div>
                    </div>

                    {{-- tgl real --}}
                    <div class="col-lg-6" id="div1">
                        <div class="mb-2">
                            <label for="tgl_real1" class="form-label">Umum</label>
                            <select class="form-control select2" id="tgl_real1" name="tgl_real1">
                                <option value=""></option>
                                <option value="0"  {{ $jurnal->tgl_real == 0 ? 'selected' : '' }}>Jurnal Umum</option>
                                <option value="20" {{ $jurnal->tgl_real == 20 ? 'selected' : '' }}>Extracomptable</option>
                                <option value="21" {{ $jurnal->tgl_real == 21 ? 'selected' : '' }}>Penghapusan</option>
                                <option value="22" {{ $jurnal->tgl_real == 22 ? 'selected' : '' }}>Hibah Pemerintah Lainnya</option>
                                <option value="25" {{ $jurnal->tgl_real == 25 ? 'selected' : '' }}>Koreksi Lain-Lain</option>
                                <option value="29" {{ $jurnal->tgl_real == 29 ? 'selected' : '' }}>Reklas Aset Lain-Lain</option>
                            </select>
                        </div>
                    </div>

                    {{-- tgl real2 --}}
                    <div class="col-lg-6" id="div2">
                        <div class="mb-2">
                            <label for="tgl_real2" class="form-label">Lain-lain</label>
                            <select class="form-control select2" id="tgl_real2" name="tgl_real2">
                                <option value=""></option>
                                <option value="99" {{ $jurnal->tgl_real == 99 ? 'selected' : '' }} >Saldo Awal Neraca</option>
                                <option value="89" {{ $jurnal->tgl_real == 89 ? 'selected' : '' }} >Saldo Awal LO</option>
                                <option value="1" {{ $jurnal->tgl_real == 1 ? 'selected' : '' }} >Penyisihan Piutang</option>
                                <option value="2" {{ $jurnal->tgl_real == 2 ? 'selected' : '' }} >Koreksi Penyusutan</option>
                                <option value="3" {{ $jurnal->tgl_real == 3 ? 'selected' : '' }} >Hibah Keluar</option>
                                <option value="4" {{ $jurnal->tgl_real == 4 ? 'selected' : '' }} >Mutasi Masuk antar OPD</option>
                                <option value="5" {{ $jurnal->tgl_real == 5 ? 'selected' : '' }} >Mutasi Keluar antar OPD</option>
                                <option value="6" {{ $jurnal->tgl_real == 6 ? 'selected' : '' }} >Penghapusan TPTGR</option>
                                <option value="7" {{ $jurnal->tgl_real == 7 ? 'selected' : '' }} >Perubahan Kode Rekening</option>
                                <option value="8" {{ $jurnal->tgl_real == 8 ? 'selected' : '' }} >Koreksi Tanah</option>
                                <option value="9" {{ $jurnal->tgl_real == 9 ? 'selected' : '' }} >Koreksi Utang Belanja</option>
                                <option value="10" {{ $jurnal->tgl_real == 10 ? 'selected' : '' }} >Reklass Antar Akun</option>
                                <option value="11" {{ $jurnal->tgl_real == 11 ? 'selected' : '' }} >Tagihan Penjualan Angsuran</option>
                                <option value="12" {{ $jurnal->tgl_real == 12 ? 'selected' : '' }} >Penyertaan Modal</option>
                                <option value="13" {{ $jurnal->tgl_real == 13 ? 'selected' : '' }} >Persediaan APBN yang belum Tercatat TA 2023</option>
                                <option value="15" {{ $jurnal->tgl_real == 15 ? 'selected' : '' }} >Koreksi Dana Transfer Pemerintah Pusat</option>
                                <option value="16" {{ $jurnal->tgl_real == 16 ? 'selected' : '' }} >Koreksi Gedung dan Bangunan</option>
                                <option value="17" {{ $jurnal->tgl_real == 17 ? 'selected' : '' }} >Koreksi Persediaan</option>
                                <option value="18" {{ $jurnal->tgl_real == 18 ? 'selected' : '' }} >Koreksi Kas</option>
                                <option value="19" {{ $jurnal->tgl_real == 19 ? 'selected' : '' }} >Extracomptable</option>
                                <option value="23" {{ $jurnal->tgl_real == 23 ? 'selected' : '' }} >Koreksi Peralatan dan Mesin</option>
                                <option value="24" {{ $jurnal->tgl_real == 24 ? 'selected' : '' }} >Koreksi Jaringan Irigasi Jembatan</option>
                                <option value="26" {{ $jurnal->tgl_real == 26 ? 'selected' : '' }} >Koreksi Aset Tetap Lainnya</option>
                                <option value="27" {{ $jurnal->tgl_real == 27 ? 'selected' : '' }} >Koreksi Piutang</option>
                                <option value="28" {{ $jurnal->tgl_real == 28 ? 'selected' : '' }} >Koreksi Aset Lain Lain</option>
                                <option value="30" {{ $jurnal->tgl_real == 30 ? 'selected' : '' }} >Pelimpahan Masuk</option>
                                <option value="31" {{ $jurnal->tgl_real == 31 ? 'selected' : '' }} >Pelimpahan Keluar</option>
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
                            <th>Jenis</th>
                            <th>Posting</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($detail_jurnal as $detail)
                            <tr>
                                <td>{{ $detail->kd_sub_kegiatan }}</td>
                                <td>{{ $detail->nm_sub_kegiatan }}</td>
                                <td>{{ $detail->kd_rek6 }}</td>
                                <td>{{ $detail->nm_rek6 }}</td>
                                <td>{{ rupiah($detail->debet) }}</td>
                                <td>{{ rupiah($detail->kredit) }}</td>
                                <td>{{ $detail->rk }}</td>
                                <td>{{ $detail->jns }}</td>
                                <td>{{ $detail->pos }}</td>
                                <td>
                                    <a href="javascript:void(0);"
                                        onclick="hapus('{{ $detail->kd_sub_kegiatan }}','{{ $detail->kd_rek6 }}','{{ $detail->debet }}','{{ $detail->kredit }}','{{ $detail->rk }}')"
                                        class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mb-2 mt-2 row">
                    <label for="total_debet" class="col-md-2 col-form-label" style="text-align: right">Total
                        Debet</label>
                    <div class="col-md-4">
                        <input type="text" style="text-align: right;background-color:white;border:none;" readonly
                            class="form-control" id="total_debet" name="total_debet"
                            value="{{ rupiah($jurnal->total_d) }}">
                    </div>
                    <label for="total_kredit" class="col-md-2 col-form-label" style="text-align: right">Total
                        Kredit</label>
                    <div class="col-md-4">
                        <input type="text" style="text-align: right;background-color:white;border:none;" readonly
                            class="form-control" id="total_kredit" name="total_kredit"
                            value="{{ rupiah($jurnal->total_k) }}">
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
    @include('jurnal.input_jurnal.js.edit')
@endsection
