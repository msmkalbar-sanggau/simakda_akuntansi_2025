@extends('layouts.template')
@section('title', 'LPE | SIMAKDA')
@section('content')


    <div class="card">
        <div class="card-header">
            <h5 class="header-title"><label>LPE</label></h5>
        </div>
        <div class="card-body">
            <div class="mb-3 row">
                <div class="col-md-6">
                    <label for="kd_skpd_ns" class="form-label">Pilih</label><br>
                    @if (Auth::user()->is_admin == '1')
                        <div class=" form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="inlineRadioOptions" id="pilihan0"
                                value="keseluruhan">
                            <label class="form-check-label" for="pilihan">Keseluruhan</label>
                        </div>
                    @else
                    @endif
                    <div class=" form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="inlineRadioOptions" id="pilihan1"
                            value="skpd">
                        <label class="form-check-label" for="pilihan">SKPD</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="inlineRadioOptions" id="pilihan2"
                            value="unit">
                        <label class="form-check-label" for="pilihan">Unit</label>
                    </div>
                </div>
            </div>

            <div class="mb-3 row">
                <div id="baris_bulan" class="col-md-6">
                    <label for="bulan" class="form-label">Bulan</label>
                    <select name="bulan" class="form-control select2" id="bulan">
                        <option value="">Silahkan Pilih</option>
                        <option value="1">Januari</option>
                        <option value="2">Februari</option>
                        <option value="3">Maret</option>
                        <option value="4">April</option>
                        <option value="5">Mei</option>
                        <option value="6">Juni</option>
                        <option value="7">Juli</option>
                        <option value="8">Agustus</option>
                        <option value="9">September</option>
                        <option value="10">Oktober</option>
                        <option value="11">November</option>
                        <option value="12">Desember</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <div id="baris_skpd">
                        <label for="kd_skpd" class="form-label">Kode SKPD</label>
                        <select class="form-control select2  @error('kd_skpd') is-invalid @enderror" style=" width: 100%;"
                            id="kd_skpd" name="kd_skpd">
                            <option value="" disabled selected>Silahkan Pilih</option>
                        </select>
                        @error('kd_skpd')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
            </div>

            <div class="mb-3 row">
                {{-- penandatangan --}}
                <div class="col-lg-6">
                    <div class="mb-3">
                        <label for="penandatangan" class="form-label">Penandatangan</label>
                        <select class="form-control select2" id="penandatangan" name="penandatangan">
                            <option value=""></option>
                            @foreach ($ttd as $value)
                                <option value="{{ $value->nip }}">{{ $value->nip }} - {{ $value->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                {{-- tanggal ttd --}}
                <div class="col-lg-6">
                    <div class="mb-3">
                        <label for="tgl_ttd" class="form-label">Tanggal TTD</label>
                        <input type="date" id="tgl_ttd" name="tgl_ttd" class="form-control">
                    </div>
                </div>

                <div class="mb-3 row">
                    <div class="col-md-12 text-center">
                        <button type="button" class="btn btn-danger btn-md bku_pdf" data-jenis="pdf" name="bku_pdf">
                            PDF</button>
                        <button type="button" class="btn btn-dark btn-md bku_layar" data-jenis="layar"
                            name="bku_layar">Layar</button>
                        <button type="button" class="btn btn-success btn-md bku_excel" data-jenis="excel"
                            name="bku_excel">Excel</button>
                        <button type="button" class="btn btn-md btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $('.select2').select2({
                placeholder: 'Silahkan Pilih',
                width: 'resolve',
                theme: 'bootstrap-5'
            });

            //hidden
            document.getElementById('baris_skpd').hidden = true; // Hide

        });


        $('input:radio[name="inlineRadioOptions"]').change(function() {
            let kd_skpd = "{{ $data_skpd->kd_skpd }}";
            if ($(this).val() == 'keseluruhan') {
                document.getElementById('baris_skpd').hidden = true; // Hide
            } else if ($(this).val() == 'skpd') {
                cari_skpd(kd_skpd, 'skpd')
                document.getElementById('baris_skpd').hidden = false; // show
            } else {
                cari_skpd(kd_skpd, 'unit')
                document.getElementById('baris_skpd').hidden = false; // show
            }
        });

        function cari_skpd(kd_skpd, jenis) {
            $.ajax({
                url: "{{ route('lpe.cari_skpd') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    kd_skpd: kd_skpd,
                    jenis: jenis
                },
                success: function(data) {
                    $('#kd_skpd').empty();
                    $('#kd_skpd').append(
                        `<option value="" disabled selected>Pilih SKPD</option>`);
                    $.each(data, function(index, data) {
                        $('#kd_skpd').append(
                            `<option value="${data.kd_skpd}" data-nama="${data.nm_skpd}">${data.kd_skpd} | ${data.nm_skpd}</option>`
                        );
                    })
                }
            })
        }


        $('.bku_layar').on('click', function() {
            Cetak(1)
        });
        $('.bku_pdf').on('click', function() {
            Cetak(2)
        });
        $('.bku_excel').on('click', function() {
            Cetak(3)
        });

        function Cetak(jns_cetak) {

            let kd_skpd = document.getElementById('kd_skpd').value;
            let bulan = document.getElementById('bulan').value;
            let tgl_ttd = $("#tgl_ttd").val();
            let penandatangan = $("#penandatangan").val();
            let skpdunit = $('input:radio[name="inlineRadioOptions"]:checked').val();

            if (!bulan) {
                alert('Bulan tidak boleh kosong!');
                return;
            }

            if (!penandatangan) {
                return alert('Penandatangan Belum Dipilih')
            }

            if (!tgl_ttd) {
                return alert('Tanggal TTD Belum Dipilih')
            }

            let url = new URL("{{ route('lpe.cetak') }}");
            let searchParams = url.searchParams;
            searchParams.append('penandatangan', penandatangan);
            searchParams.append('tgl_ttd', tgl_ttd);
            searchParams.append("bulan", bulan);
            searchParams.append("kd_skpd", kd_skpd);
            searchParams.append("skpdunit", skpdunit);
            searchParams.append("cetak", jns_cetak);
            window.open(url.toString(), "_blank");

        }
    </script>
@endsection
