<script type="text/javascript">
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        let dataKoreksi = $('#dataKoreksi').DataTable({
            responsive: true,
            ordering: false,
            columns: [{
                    data: 'id',
                    name: 'id',
                    visible: false,
                },
                {
                    data: 'kd_sub_kegiatan',
                    name: 'kd_sub_kegiatan',
                },
                {
                    data: 'nm_sub_kegiatan',
                    name: 'nm_sub_kegiatan',
                },
                {
                    data: 'kd_rek6',
                    name: 'kd_rek6',
                },
                {
                    data: 'nm_rek6',
                    name: 'nm_rek6',
                },
                {
                    data: 'nilai',
                    name: 'nilai',
                },
                {
                    data: 'nilai_koreksi',
                    name: 'nilai_koreksi',
                },
                {
                    data: 'kd_sumber_dana',
                    name: 'kd_sumber_dana',
                },
                {
                    data: 'aksi',
                    name: 'aksi',
                }
            ]
        });

        $('.select2').select2({
            placeholder: 'Silahkan Pilih',
            width: 'resolve',
            theme: 'bootstrap-5'
        });

        $('.select2-modal').select2({
            dropdownParent: $('#tampil-modal .modal-content'),
            placeholder: 'Silahkan Pilih',
            width: 'resolve',
            theme: 'bootstrap-5'
        });

        $("#tgl").change(function(e) {
            //transaksi koreksi
            $('#kd_sub_kegiatan').empty();
            $('#nm_sub_kegiatan').val(null);
            $('#kd_rek6').empty();
            $('#nm_rek6').val(null);
            $('#kd_rek6').prop('disabled', true);
            $('#nilai_awal').val(null);
            $('#kd_sumber_dana').val(null);
            $('#nilai_sumber_dana').val(null);
            $('#realisasi_sumber_dana').val(null);
            $('#nilai_anggaran').val(null);
            $('#nilai_spd').val(null);
            $('#nilai_angkas').val(null);
            $('#nilai_realisasi').val(null);
            $('#nilai_tersedia').val(null);
            $('#nilai_transaksi').val(null);

            // kd skpd dan no bukti
            let kd_skpd = document.getElementById('kd_skpd').value;
            let no_bukti = document.getElementById('no_bukti').value;
            load_daftar_transaksi(kd_skpd, no_bukti);
        });

        // kd skpd
        $('#no_bukti').prop('disabled', true);
        $('#kd_skpd').on('select2:select', function() {
            $('#no_bukti').prop('disabled', true);
            let nama = $(this).find(':selected').data('nama');
            $('#nm_skpd').val(nama);
            //rincian transaksi
            $('#kd_sub_kegiatan').empty();
            $('#nm_sub_kegiatan').val(null);
            $('#kd_rek6').empty();
            $('#nm_rek6').val(null);
            $('#nilai_transaksi').val(null);
            $('#nilai_awal').val(null);
            $('#id_trd').val(null);
            
            $.ajax({
                url: "{{ route('koreksi_nominal.load_daftar_transaksi') }}",
                type: "post",
                data: {
                    kd_skpd: this.value,
                },
                success: function(response) {
                    if ($.trim(response) != '') {
                        $('#no_bukti').prop('disabled', false);
                        $('#no_bukti').empty();
                        $('#no_bukti').append(
                            `<option value="" disabled selected>Pilih</option>`);
                        $.each(response, function(key, value) {
                            $('#no_bukti').append('<option value="' + value
                                .no_bukti + '" data-spd="' + value.no_sp2d +
                                '" data-tgl="' + value.tgl_bukti +
                                '" data-spp="' + value.jns_spp + '">' + value
                                .no_bukti + '</option>');
                        });
                    } else {
                        $('#no_bukti').empty();
                        $('#no_bukti').prop('disabled', true);
                    }
                },
            });
        });

        // no bukti
        $('#tgl').prop('disabled', true);
        $('#no_bukti').on('select2:select', function() {
            let sp2d = $(this).find(':selected').data('spd');
            $('#no_sp2d').val(sp2d);
            let tgl_bukti = $(this).find(':selected').data('tgl');
            $('#tgl_bukti').val(new Intl.DateTimeFormat('id-ID', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
            }).format(new Date(tgl_bukti)));
            let jns_spp = $(this).find(':selected').data('spp');
            if (jns_spp == '1') {
                $('#jns_spp').val('UP');
            } else if (jns_spp == '2') {
                $('#jns_spp').val('GU');
            } else if (jns_spp == '3') {
                $('#jns_spp').val('TU');
            } else if (jns_spp == '4') {
                $('#jns_spp').val('LS Gaji');
            } else if (jns_spp == '5') {
                $('#jns_spp').val('LS Pihak Ketiga');
            } else {
                $('#jns_spp').val('LS Barang & Jasa');
            }
            
            // kd skpd dan no bukti
            let kd_skpd = document.getElementById('kd_skpd').value;
            let no_bukti = this.value;
            $('#nm_sub_kegiatan').val(null);
            $('#kd_rek6').empty();
            $('#nm_rek6').val(null);
            $('#nilai_transaksi').val(null);
            $('#nilai_awal').val(null);
            $('#id_trd').val(null);
            $('#kd_rek6').prop('disabled', true);
            load_daftar_transaksi(kd_skpd, no_bukti);
        });

        $('#kd_rek6').prop('disabled', true);
        $('#kd_sub_kegiatan').on('select2:select', function() {
            let nama = $(this).find(':selected').data('nama');
            $('#nm_sub_kegiatan').val(nama);
            // kd skpd dan no bukti
            let kd_skpd = document.getElementById('kd_skpd').value;
            let no_bukti = document.getElementById('no_bukti').value;
            let kd_sub_kegiatan = this.value;
            $('#nm_rek6').val(null);
            $('#nilai_transaksi').val(null);
            $('#nilai_awal').val(null);
            $('#id_trd').val(null);
            load_daftar_rekening(kd_skpd, no_bukti, kd_sub_kegiatan);
        });

        $('#kd_rek6').on('select2:select', function() {
            let nama = $(this).find(':selected').data('nama');
            $('#nm_rek6').val(nama);

            let nilai = $(this).find(':selected').data('nilai');
            $('#nilai_transaksi').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(nilai));
            $('#nilai_awal').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(nilai));

            let id_trd = $(this).find(':selected').data('id');
            $('#id_trd').val(id_trd);

            let kd_sumber_dana = $(this).find(':selected').data('sumber');
            $('#kd_sumber_dana').val(kd_sumber_dana);

            // kd skpd dan no bukti
            let kd_skpd = document.getElementById('kd_skpd').value;
            let kd_sub_kegiatan = document.getElementById('kd_sub_kegiatan').value;
            let kd_sumberdana = document.getElementById('kd_sumber_dana').value;
            let bulan = document.getElementById('tgl').value;
            let kd_rek6 = this.value;
            load_daftar_nilai(kd_skpd, kd_sub_kegiatan, kd_sumberdana, bulan, kd_rek6);
        });

        $('#simpan-koreksi').on('click', function() {
            let nilai_tersedia = rupiah(document.getElementById('nilai_tersedia').value);
            let nilai_transaksi = rupiah(document.getElementById('nilai_transaksi').value);
            if (nilai_tersedia == '') {
                alert("Nilai Tersedia boleh kosong")
                return
            }
            if (nilai_transaksi == '') {
                alert("Nilai Transaksi boleh kosong")
                return
            }

            if (nilai_tersedia >= nilai_transaksi) {
                let kd_sub_kegiatan = document.getElementById('kd_sub_kegiatan').value;
                let nm_sub_kegiatan = document.getElementById('nm_sub_kegiatan').value;
                let kd_rek6 = document.getElementById('kd_rek6').value;
                let nm_rek6 = document.getElementById('nm_rek6').value;
                let nilai_awal = rupiah(document.getElementById('nilai_awal').value);
                let nilai_transaksi = rupiah(document.getElementById('nilai_transaksi').value);
                let kd_sumber_dana = document.getElementById('kd_sumber_dana').value;
                let id_trd = document.getElementById('id_trd').value;

                dataKoreksi.row.add({
                    'id': id_trd,
                    'kd_sub_kegiatan': kd_sub_kegiatan,
                    'nm_sub_kegiatan': nm_sub_kegiatan,
                    'kd_rek6': kd_rek6,
                    'nm_rek6': nm_rek6,
                    'nilai': new Intl.NumberFormat('id-ID', {
                                minimumFractionDigits: 2
                            }).format(nilai_awal),
                    'nilai_koreksi': new Intl.NumberFormat('id-ID', {
                            minimumFractionDigits: 2
                        }).format(nilai_transaksi),
                    'kd_sumber_dana': kd_sumber_dana,
                    'aksi': `<a href="javascript:void(0);" onclick="hapus('${kd_sub_kegiatan}','${kd_rek6}','${nilai_transaksi}')" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></a>`,
                }).draw();

                $('#kd_sub_kegiatan').val(null).change();
                $('#nm_sub_kegiatan').val(null);
                $('#kd_rek6').val(null).change();
                $('#nm_rek6').val(null);
                $('#kd_rek6').prop('disabled', true);
                $('#kd_sumber_dana').val(null);
                $('#nilai_awal').val(null);
                $('#id_trd').val(null);
                $('#nilai_sumber_dana').val(null);
                $('#realisasi_sumber_dana').val(null);
                $('#nilai_anggaran').val(null);
                $('#nilai_spd').val(null);
                $('#nilai_angkas').val(null);
                $('#nilai_realisasi').val(null);
                $('#nilai_tersedia').val(null);
                $('#nilai_transaksi').val(null);
                
                $('#tampil-modal').modal('hide');
            }  else {
                alert("Dana Tersedia Tidak cukup");
                return
            }
        });

        $('#tambah').on('click', function() {
            let no_bukti = document.getElementById('no_bukti').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let tgl = document.getElementById('tgl').value;

            if (no_bukti != '' && kd_skpd != '' && tgl) {
                $('#modal-title').html('Tambah Data');
                $('#tampil-modal').modal('show');
            }  else {
                alert("Silahkan Isi Kode SKPD, tanggal & Nomor Bukti");
                return
            }
        });

        // simpan data 
        $('#simpan').on('click', function() {
            let kd_skpd = document.getElementById('kd_skpd').value;
            let nm_skpd = document.getElementById('nm_skpd').value;
            let no_bukti = document.getElementById('no_bukti').value;
            let tgl = document.getElementById('tgl').value;
            let ket = document.getElementById('ket').value;

            if (!kd_skpd) return alert('Kode SKPD Belum Diisi')
            if (!no_bukti) return alert('No Bukti Belum Diisi')
            if (!tgl) return alert('Tanggal Belum Dipilih')
            if (!ket) return alert('Keterangan Belum Diisi')

            let tampungan = dataKoreksi.rows().data().toArray().map((value) => {
                let data = {
                    id: value.id,
                    kd_sub_kegiatan: value.kd_sub_kegiatan,
                    nm_sub_kegiatan: value.nm_sub_kegiatan,
                    kd_rek6: value.kd_rek6,
                    nm_rek6: value.nm_rek6,
                    nilai: rupiah(value.nilai),
                    nilai_koreksi: rupiah(value.nilai_koreksi),
                    kd_sumber_dana: value.kd_sumber_dana,
                };
                return data;
            });

            let tampungan_data = JSON.stringify(tampungan);

            if (tampungan.length == 0) {
                alert('Daftar Koreksi tidak boleh kosong!');
                return;
            }

            let data = {
                kd_skpd,
                nm_skpd,
                no_bukti,
                tgl,
                ket,
                tampungan_data
            };

            $('#simpan').prop('disabled', true);
            $.ajax({
                url: "{{ route('koreksi_nominal.simpan') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    data: data
                },
                success: function(response) {
                    if (response.message == '1') {
                        alert('Data berhasil ditambahkan!');
                        window.location.href = "{{ route('koreksi_nominal.index') }}";
                    } else {
                        alert('Data tidak berhasil ditambahkan!');
                        $('#simpan').prop('disabled', false);
                        return;
                    }
                }
            })
        });

        function load_daftar_transaksi(kd_skpd, no_bukti) {
            $.ajax({
                url: "{{ route('koreksi_nominal.load_rincian_transaksi') }}",
                type: "post",
                data: {
                    no_bukti: no_bukti,
                    kd_skpd: kd_skpd,
                },
                success: function(response) {
                    if ($.trim(response) != '') {   
                        $('#tgl').prop('disabled', false);
                        $('#kd_sub_kegiatan').empty();
                        $('#kd_sub_kegiatan').append(`<option value="" disabled selected>Pilih</option>`);
                        $.each(response, function(key, value) {
                            $('#kd_sub_kegiatan').append('<option value="' + value.kd_sub_kegiatan + '" data-nama="' + value.nm_sub_kegiatan + '">' + value.kd_sub_kegiatan + ' | ' + value.nm_sub_kegiatan + '</option>');
                        });
                    } else {
                        $('#kd_sub_kegiatan').empty();
                        $('#kd_sub_kegiatan').prop('disabled', true);
                        $('#tgl').prop('disabled', true);
                    }
                },
            });
        }
            
        function load_daftar_rekening(kd_skpd, no_bukti, kd_sub_kegiatan) {
            $.ajax({
                url: "{{ route('koreksi_nominal.load_rincian_rekening') }}",
                type: "post",
                data: {
                    no_bukti: no_bukti,
                    kd_skpd: kd_skpd,
                    kd_sub_kegiatan: kd_sub_kegiatan,
                },
                success: function(response) {
                    if ($.trim(response) != '') {   
                        $('#kd_rek6').prop('disabled', false);
                        $('#kd_rek6').empty();
                        $('#kd_rek6').append(`<option value="" disabled selected>Pilih</option>`);
                        $.each(response, function(key, value) {
                            $('#kd_rek6').append('<option value="' + value.kd_rek6 + '" data-nama="' + value.nm_rek6 + '" data-nilai="' + value.nilai + '" data-id="' + value.id + '" data-sumber="' + value.sumber + '">' + value.kd_rek6 + ' | ' + value.nm_rek6 + '</option>');
                        });
                    } else {
                        $('#kd_rek6').empty();
                        $('#kd_rek6').prop('disabled', true);
                    }
                },  
            });
        }

        function load_daftar_nilai(kd_skpd, kd_sub_kegiatan, kd_sumberdana, bulan, kd_rek6) {
            $.ajax({
                url: "{{ route('koreksi_nominal.load_daftar_nilai') }}",
                type: "post",
                dataType: 'json',
                data: {
                    kd_skpd: kd_skpd,
                    kd_sub_kegiatan: kd_sub_kegiatan,
                    kd_rek6: kd_rek6,
                    kd_sumber_dana: kd_sumberdana,
                    bulan: bulan,
                },
                success: function(response) {
                    $('#nilai_sumber_dana').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(response.nilai_sumber_dana));
                    $('#realisasi_sumber_dana').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(response.realisasi_sumber_dana));
                    $('#nilai_anggaran').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(response.nilai_anggaran));
                    $('#nilai_spd').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(response.nilai_spd));
                    $('#nilai_angkas').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(response.nilai_angkas));
                    $('#nilai_realisasi').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(response.nilai_realisasi));
                    let nilai_awal = rupiah(document.getElementById('nilai_awal').value);
                    let total_angkas = parseFloat(response.nilai_angkas);
                    let total_realisasi = parseFloat(response.nilai_realisasi);
                    $('#nilai_tersedia').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(total_angkas - total_realisasi + nilai_awal));
                },  
            });
        }

        $("input[data-type='currency']").on({
            keyup: function() {
                formatCurrency($(this));
            },
            blur: function() {
                formatCurrency($(this), "blur");
            }
        });
    });

    function angka(n) {
        let nilai = n.split(',').join('');
        return parseFloat(nilai) || 0;
    }

    function rupiah(n) {
        let n1 = n.split('.').join('');
        let rupiah = n1.split(',').join('.');
        return parseFloat(rupiah) || 0;
    }

    function formatNumber(n) {
        // format number 1000000 to 1,234,567
        return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".")
    }

    function formatCurrency(input, blur) {
        // appends $ to value, validates decimal side
        // and puts cursor back in right position.

        // get input value
        var input_val = input.val();

        // don't validate empty input
        if (input_val === "") {
            return;
        }

        // original length
        var original_len = input_val.length;

        // initial caret position
        var caret_pos = input.prop("selectionStart");

        // check for decimal
        if (input_val.indexOf(",") >= 0) {

            // get position of first decimal
            // this prevents multiple decimals from
            // being entered
            var decimal_pos = input_val.indexOf(",");

            // split number by decimal point
            var left_side = input_val.substring(0, decimal_pos);
            var right_side = input_val.substring(decimal_pos);

            // add commas to left side of number
            left_side = formatNumber(left_side);

            // validate right side
            right_side = formatNumber(right_side);

            // On blur make sure 2 numbers after decimal
            if (blur === "blur") {
                right_side += "00";
            }

            // Limit decimal to only 2 digits
            right_side = right_side.substring(0, 2);

            // join number by .
            input_val = left_side + "," + right_side;

        } else {
            // no decimal entered
            // add commas to number
            // remove all non-digits
            input_val = formatNumber(input_val);
            input_val = input_val;

            // final formatting
            if (blur === "blur") {
                input_val += ",00";
            }
        }

        // send updated string to input
        input.val(input_val);

        // put caret back in the right position
        var updated_len = input_val.length;
        caret_pos = updated_len - original_len + caret_pos;
        input[0].setSelectionRange(caret_pos, caret_pos);
    }

    function hapus(kd_sub_kegiatan, kd_rek6, nilai_transaksi) {
        let hapus = confirm('Yakin Ingin Menghapus Data, Rekening : ' + kd_rek6);

        let tabel = $('#dataKoreksi').DataTable();

        if (hapus == true) {
            tabel.rows(function(idx, data, node) {
                return data.kd_sub_kegiatan == kd_sub_kegiatan && data.kd_rek6 == kd_rek6 &&
                    rupiah(data
                        .nilai) == parseFloat(nilai_transaksi)
            }).remove().draw();
        }
    }
</script>
