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

        $('input[name="jenis_periode"]').change(function() {
            if ($('input[name="jenis_periode"]:checked').val() == "bulan") {
                $(".bulan").show();
                $('#bulan').val(null).change();
                $(".tanggal").hide();
            } else {
                $(".tanggal").show();
                $('#tgl_awal').val(null).change();
                $('#tgl_akhir').val(null).change();
                $(".bulan").hide();
            }
        }).change();

        // kd skpd && penandatangan
        $('#penandatangan').prop('disabled', true);
        $('#kd_skpd').on('select2:select', function() {
            let nama = $(this).find(':selected').data('nama');
            $('#nm_skpd').val(nama);

            $('#penandatangan').val(null).change();
            $.ajax({
                url: "{{ route('lra_program.penandatangan') }}",
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
            let url = new URL("{{ route('lra_program.cetak') }}");
            let kd_skpd = $("#kd_skpd").val();
            let penandatangan = $("#penandatangan").val();
            let tgl_ttd = $("#tgl_ttd").val();
            let bulan = $("#bulan").val();
            let tgl_awal = $("#tgl_awal").val();
            let tgl_akhir = $("#tgl_akhir").val();
            let kode = $("#kode").val();
            let jns_ang = $("#jns_ang").val();
            let ttd = $("#ttd").val();

            if (!kd_skpd) {
                return alert('Kode SKPD Belum Dipilih')
            }

            if (!penandatangan) {
                return alert('Penandatangan Belum Dipilih')
            }

            if (!tgl_ttd) {
                return alert('Tanggal TTD Belum Dipilih')
            }

            const jenis_periode = $('input[name="jenis_periode"]:checked').val()
            if (jenis_periode == 'tanggal') {
                if (!tgl_awal) {
                    return alert('Tanggal Awal Belum Dipilih')
                }
                if (!tgl_akhir) {
                    return alert('Tanggal Akhir Belum Dipilih')
                }
            } else {
                if (!bulan) {
                    return alert('Bulan Belum Dipilih')
                }
            }

            if (!kode) {
                return alert('Kode Belum Dipilih')
            }

            if (!jns_ang) {
                return alert('Jenis Anggaran Belum Dipilih')
            }

            if (!ttd) {
                return alert('TTD Belum Dipilih')
            }

            let searchParams = url.searchParams;
            searchParams.append('kd_skpd', kd_skpd);
            searchParams.append('penandatangan', penandatangan);
            searchParams.append('tgl_ttd', tgl_ttd);
            if (jenis_periode == 'tanggal') {
                searchParams.append('tgl_awal', tgl_awal);
                searchParams.append('tgl_akhir', tgl_akhir);
            } else {
                searchParams.append('bulan', bulan);
            }
            searchParams.append('kode', kode);
            searchParams.append('jns_ang', jns_ang);
            searchParams.append('ttd', ttd);
            searchParams.append('jenis', $(this).data('jenis'));
            window.open(url.toString(), "_blank");
        });
    });
</script>
