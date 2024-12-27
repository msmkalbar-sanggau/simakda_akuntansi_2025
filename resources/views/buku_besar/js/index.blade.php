<script type="text/javascript">
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

        // kd skpd && rekening
        $('#rekening').prop('disabled', true);
        $('#penandatangan').prop('disabled', true);
        $('#kd_skpd').on('select2:select', function() {
            let nama = $(this).find(':selected').data('nama');
            $('#nm_skpd').val(nama);
            $('#penandatangan').val(null).change();
            $('#rekening').val(null).change();

            $.ajax({
                url: "{{ route('buku_besar.rekening') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    kd_skpd: this.value,
                },
                success: function(data) {
                    $('#rekening').prop('disabled', false);
                    $('#rekening').empty();
                    $('#rekening').append(
                        `<option value="" disabled selected>Silahkan Pilih</option>`);
                    $.each(data, function(index, data) {
                        $('#rekening').append(
                            `<option value="${data.kd_rek6}">${data.kd_rek6} - ${data.nm_rek6}</option>`
                        );
                    })
                }
            });
            $.ajax({
                url: "{{ route('buku_besar.penandatangan') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    kd_skpd: this.value,
                },
                success: function(data) {
                    $('#penandatangan').prop('disabled', false);
                    $('#penandatangan').empty();
                    $('#penandatangan').append(
                        `<option value="" disabled selected>Silahkan Pilih</option>`);
                    $.each(data, function(index, data) {
                        $('#penandatangan').append(
                            `<option value="${data.nip}">${data.nip} - ${data.nama}</option>`
                        );
                    })
                }
            })
        });

        // cetak
        $('.cetak').on('click', function() {
            let url = new URL("{{ route('buku_besar.cetak') }}");
            let kd_skpd = $("#kd_skpd").val();
            let penandatangan = $("#penandatangan").val();
            let tgl_awal = $("#tgl_awal").val();
            let tgl_akhir = $("#tgl_akhir").val();
            let rekening = $("#rekening").val();

            if (!kd_skpd) {
                return alert('Kode SKPD Belum Dipilih')
            }

            if (!penandatangan) {
                return alert('Penandatangan Belum Dipilih')
            }

            if (!tgl_awal) {
                return alert('Tanggal Awal Belum Dipilih')
            }

            if (!tgl_akhir) {
                return alert('Tanggal Akhir Belum Dipilih')
            }

            if (!rekening) {
                return alert('Rekening Belum Dipilih')
            }

            let searchParams = url.searchParams;
            searchParams.append('kd_skpd', kd_skpd);    
            searchParams.append('penandatangan', penandatangan);
            searchParams.append('tgl_awal', tgl_awal);
            searchParams.append('tgl_akhir', tgl_akhir);
            searchParams.append('rekening', rekening);
            searchParams.append('jenis', $(this).data('jenis'));
            window.open(url.toString(), "_blank");
        });
    });
</script>
