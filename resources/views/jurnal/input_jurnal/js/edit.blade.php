<script type="text/javascript">
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    
        let dataKegiatan = $('#dataKegiatan').DataTable({
            responsive: true,
            ordering: false,
            columns: [{
                    data: 'kd_sub_kegiatan',
                    name: 'kd_sub_kegiatan',
                },
                {
                    data: 'nm_sub_kegiatan',
                    name: 'nm_sub_kegiatan',
                    visible: false
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
                    data: 'debet',
                    name: 'debet',
                },
                {
                    data: 'kredit',
                    name: 'kredit',
                },
                {
                    data: 'rk',
                    name: 'rk',
                },
                {
                    data: 'jns',
                    name: 'jns',
                    visible: false
                },
                {
                    data: 'pos',
                    name: 'pos',
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
        
        // kd skpd
        $('#kd_skpd').on('select2:select', function() {
            let nama = $(this).find(':selected').data('nama');
            $('#nm_skpd').val(nama);
        });

        
        // inisiasi div 
        $("#div1").hide();
        $("#div2").hide();
        let jenis_jurnal_lalu = "{{ $jurnal->reev }}";
        
        if(jenis_jurnal_lalu == '0') {
            $("#div1").show();
            $("#div2").hide();
        } else if (jenis_jurnal_lalu == '3') {
            $("#div1").hide();
            $("#div2").show();
        }

        $("#reev").change(function() {
            let jns = document.getElementById('reev').value;
            if (jns == '0') {
                $("#div1").show();
                $("#div2").hide();
            } else if (jns == '3') {
                $("#div1").hide();
                $("#div2").show();
            } else {
                $("#div1").hide();
                $("#div2").hide();
            }
        });
    
        // kode kegiatan
        $('#kd_kegiatan').prop('disabled', true);
        $('#kd_rek6').prop('disabled', true);
        $("#jns").change(function(e) {
            let kd_skpd = document.getElementById('kd_skpd').value;
            let kegiatan = $(this).find(':selected').data('kegiatan');
            $('#nm_kegiatan').val(kegiatan);
            let nm_rek = $(this).find(':selected').data('rek');
            $('#nm_rek6').val(nm_rek);
            $.ajax({
                url: "{{ route('input_jurnal.load_kd_kegiatan') }}",
                type: "post",
                data: {
                    jns: this.value,
                    kd_skpd: kd_skpd,
                },
                success: function(response) {
                    if ($.trim(response) != '') {   
                        $('#kd_kegiatan').prop('disabled', false);
                        $('#kd_kegiatan').empty();
                        $('#kd_kegiatan').append(`<option value="" disabled selected>Pilih</option>`);
                        $.each(response, function(key, value) {
                            $('#kd_kegiatan').append('<option value="' + value.kd_sub_kegiatan + '" data-kegiatan="' + value.nm_sub_kegiatan + '">' + value.kd_sub_kegiatan + ' | ' + value.nm_sub_kegiatan + '</option>');
                        });
                    } else {
                        $('#kd_kegiatan').empty();
                        $('#kd_kegiatan').prop('disabled', true);
                    }
                },
            });
    
            $.ajax({
                url: "{{ route('input_jurnal.load_kd_rekening') }}",
                type: "post",
                data: {
                    jns: this.value,
                    kd_skpd: kd_skpd,
                },
                success: function(response) {
                    if ($.trim(response) != '') {   
                        $('#kd_rek6').prop('disabled', false);
                        $('#kd_rek6').empty();
                        $('#kd_rek6').append(`<option value="" disabled selected>Pilih</option>`);
                        $.each(response, function(key, value) {
                            $('#kd_rek6').append('<option value="' + value.kd_rek6 + '" data-rek="' + value.nm_rek6 + '">' + value.kd_rek6 + ' | ' + value.nm_rek6 + '</option>');
                        });
                    } else {
                        $('#kd_rek6').empty();
                        $('#kd_rek6').prop('disabled', true);
                    }
                },
            });
        });
    
        $("#kd_kegiatan").change(function(e) {
            let kd_rek = document.getElementById('kd_rek6').value;
            let jenis = document.getElementById('jns').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            $('#kd_rek6').prop('disabled', true);
            let nm_rek = $(this).find(':selected').data('rek');
            $('#nm_rek6').val(nm_rek);
            $.ajax({
                url: "{{ route('input_jurnal.load_kd_rekening') }}",
                type: "post",
                data: {
                    kd_kegiatan: this.value,
                    kd_rek: kd_rek,
                    kd_skpd: kd_skpd,
                    jns: jenis,
                },
                success: function(response) {
                    if ($.trim(response) != '') {   
                        $('#kd_rek6').prop('disabled', false);
                        $('#kd_rek6').empty();
                        $('#kd_rek6').append(`<option value="" disabled selected>Pilih</option>`);
                        $.each(response, function(key, value) {
                            $('#kd_rek6').append('<option value="' + value.kd_rek6 + '" data-rek="' + value.nm_rek6 + '">' + value.kd_rek6 + ' | ' + value.nm_rek6 + '</option>');
                        });
                    } else {
                        $('#kd_rek6').empty();
                        $('#kd_rek6').prop('disabled', true);
                    }
                },
            });
        });
    
        $('#tambah').on('click', function() {
            let no_voucher = document.getElementById('no_voucher').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
    
            if (no_voucher != '' && kd_skpd != '') {
                $('#modal-title').html('Tambah Data');
                $('#tampil-modal').modal('show');
                $('#kd_kegiatan').prop('disabled', true);
                $('#kd_rek6').prop('disabled', true);
            }  else {
                alert("Silahkan Isi Kode SKPD & Nomor Voucher");
                return
            }
        });
    
        //simpan kegiatan
        $('#simpan-kegiatan').on('click', function() {
            let jns = document.getElementById('jns').value;
            let rk = document.getElementById('rk').value;
            let kd_kegiatan = document.getElementById('kd_kegiatan').value;
            let kd_rek6 = document.getElementById('kd_rek6').value;
            let nilai = angka(document.getElementById('nilai').value);
            let total_debet = rupiah(document.getElementById('total_debet').value);
            let total_kredit = rupiah(document.getElementById('total_kredit').value);
            let posting = document.getElementById('posting').checked;
            let nm_kegiatan = document.getElementById('nm_kegiatan').value;
            let rekening = $('#kd_rek6').find('option:selected');
            let nm_rek6 = rekening.data('rek');
    
            if (jns == '') {
                alert("Jenis tidak boleh kosong")
                return
            } 
    
            if (rk == '') {
                alert("Debet / Kredit tidak boleh kosong")
                return
            }
    
            if (nilai == '') {
                alert("Nilai tidak boleh kosong")
                return
            }
    
            let pos = '';
            let nilai_debet = '';
            let nilai_kredit = '';
    
            if (posting == true) {
                pos = '1';
            } else {
                pos = '0';
            }
    
            if (rk == 'D') {
                nilai_debet = nilai;
                nilai_kredit = 0;
                total_debet += nilai;
                $('#total_debet').val(new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 2
                }).format(total_debet));
            } else {
                nilai_debet = 0;
                nilai_kredit = nilai;
                total_kredit += nilai;
                $('#total_kredit').val(new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 2
                }).format(total_kredit));
            }
    
            dataKegiatan.row.add({
                'kd_sub_kegiatan': kd_kegiatan,
                'nm_sub_kegiatan': nm_kegiatan,
                'kd_rek6': kd_rek6,
                'nm_rek6': nm_rek6,
                'debet': new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 2
                }).format(nilai_debet),
                'kredit': new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 2
                }).format(nilai_kredit),
                'rk': rk,
                'jns': jns,
                'pos': pos,
                'aksi': `<a href="javascript:void(0);" onclick="hapus('${kd_kegiatan}','${kd_rek6}','${nilai_debet}','${nilai_kredit}','${rk}')" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></a>`,
            }).draw();
    
            $('#jns').val(null).change();
            $('#rk').val(null).change();
            $('#kd_kegiatan').val(null).change();
            $('#kd_rek6').val(null).change();
            $('#nilai').val(null);
            $('#tampil-modal').modal('hide');
        });
    
        // simpan data 
        $('#simpan').on('click', function() {
            let kd_skpd = document.getElementById('kd_skpd').value;
            let nm_skpd = document.getElementById('nm_skpd').value;
            let no_voucher = document.getElementById('no_voucher').value;
            let no_voucher_lama = document.getElementById('no_voucher_lama').value;
            let tgl_voucher = document.getElementById('tgl_voucher').value;
            let ket = document.getElementById('ket').value;
            let reev = document.getElementById('reev').value;
            let tgl_real1 = document.getElementById('tgl_real1').value;
            let tgl_real2 = document.getElementById('tgl_real2').value;
            if (!kd_skpd) return alert('Kode SKPD Belum Diisi')
            if (!no_voucher) return alert('No Voucher Belum Diisi')
            if (!tgl_voucher) return alert('Tanggal Voucher Belum Dipilih')
            if (!ket) return alert('Keterangan Belum Diisi')
            if (!reev) return alert('Jenis Jurnal Belum Diisi')
    
            let tgl_real = '';
            if (reev == '0') {
                tgl_real = tgl_real1;
                if (!tgl_real) return alert('Umum Belum Diisi')
            }
            if (reev == '3') {
                tgl_real = tgl_real2;
                if (!tgl_real) return alert('Lain-lain Belum Dipilih')
            }   
            
            let total_debet = rupiah(document.getElementById('total_debet').value);
            let total_kredit = rupiah(document.getElementById('total_kredit').value);
    
            if ((total_debet < 0) || (total_kredit < 0)) {
                alert('Rincian tidak boleh Minus');
                return
            }
    
            if (total_debet != total_kredit) {
                alert('Kredit dan Debet harus sama!');
                return
            }
    
            let tampungan = dataKegiatan.rows().data().toArray().map((value) => {
                let data = {
                    kd_sub_kegiatan: value.kd_sub_kegiatan,
                    nm_sub_kegiatan: value.nm_sub_kegiatan,
                    kd_rek6: value.kd_rek6,
                    nm_rek6: value.nm_rek6,
                    debet: rupiah(value.debet),
                    kredit: rupiah(value.kredit),
                    rk: value.rk,
                    jns: value.jns,
                    pos: value.pos,
                };
                return data;
            });
    
            let tampungan_data = JSON.stringify(tampungan);
    
            if (tampungan.length == 0) {
                alert('Rekening tidak boleh kosong!');
                return;
            }
    
            let data = {
                kd_skpd,
                nm_skpd,
                no_voucher_lama,
                no_voucher,
                tgl_voucher,
                ket,
                total_debet,
                total_kredit,
                reev,
                tgl_real,
                tampungan_data
            };
    
            $('#simpan').prop('disabled', true);
            $.ajax({
                url: "{{ route('input_jurnal.update') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    data: data
                },
                success: function(response) {
                    if (response.message == '1') {
                        alert('Data berhasil diupdate!');
                        window.location.href = "{{ route('input_jurnal.index') }}";
                    } else if (response.message == '2') {
                        alert('Nomor Telah Digunakan!');
                        $('#simpan').prop('disabled', false);
                        return;
                    } else {
                        alert('Data tidak berhasil diupdate!');
                        $('#simpan').prop('disabled', false);
                        return;
                    }
                }
            })
        });
        
        $("input[data-type='currency']").on({
            keyup: function() {
                formatCurrency($(this));
            },
            blur: function() {
                formatCurrency($(this), "blur");
            }
        });
    
    });
    
    function hapus(kd_kegiatan, kd_rek6, nilai_debet, nilai_kredit, rk) {
        let nilai_dk = '';
        if (rk == 'K') {
            nilai_dk = nilai_kredit;
        } else {
            nilai_dk = nilai_debet;
        }
    
        let hapus = confirm('Yakin Ingin Menghapus Data, Rekening : ' + kd_rek6);
    
        let total_debet = rupiah(document.getElementById('total_debet').value);
        let total_kredit = rupiah(document.getElementById('total_kredit').value);
    
        let tabel = $('#dataKegiatan').DataTable();
    
        if (hapus == true) {
            if (rk == 'D') {
                tabel.rows(function(idx, data, node) {
                    return data.kd_sub_kegiatan == kd_kegiatan && data.kd_rek6 == kd_rek6 && rupiah(data
                        .debet) == parseFloat(nilai_dk)
                }).remove().draw();
    
                $('#total_debet').val(new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 2
                }).format(total_debet - parseFloat(nilai_dk)));
            } else {
                tabel.rows(function(idx, data, node) {
                    return data.kd_sub_kegiatan == kd_kegiatan && data.kd_rek6 == kd_rek6 && rupiah(data
                        .kredit) == parseFloat(nilai_dk)
                }).remove().draw();
                
                $('#total_kredit').val(new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 2
                }).format(total_kredit - parseFloat(nilai_dk)));
            }
        }
    }
    
    function formatNumber(n) {
        // format number 1000000 to 1,234,567
        return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",")
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
        if (input_val.indexOf(".") >= 0) {
    
            // get position of first decimal
            // this prevents multiple decimals from
            // being entered
            var decimal_pos = input_val.indexOf(".");
    
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
            input_val = left_side + "." + right_side;
    
        } else {
            // no decimal entered
            // add commas to number
            // remove all non-digits
            input_val = formatNumber(input_val);
            input_val = input_val;
    
            // final formatting
            if (blur === "blur") {
                input_val += ".00";
            }
        }
    
        // send updated string to input
        input.val(input_val);
    
        // put caret back in the right position
        var updated_len = input_val.length;
        caret_pos = updated_len - original_len + caret_pos;
        input[0].setSelectionRange(caret_pos, caret_pos);
    }
    
    function angka(n) {
        let nilai = n.split(',').join('');
        return parseFloat(nilai) || 0;
    }
    
    function rupiah(n) {
        let n1 = n.split('.').join('');
        let rupiah = n1.split(',').join('.');
        return parseFloat(rupiah) || 0;
    }
    
    </script>
    