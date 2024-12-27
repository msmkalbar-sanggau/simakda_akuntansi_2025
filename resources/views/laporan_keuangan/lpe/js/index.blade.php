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
        let idskpd = document.getElementById('idskpd').value;
        if (idskpd > 0) {
            // kd skpd && penandatangan
            $('#penandatangan').prop('disabled', true);
            $('#kd_skpd').on('select2:select', function() {
                let nama = $(this).find(':selected').data('nama');
                $('#nm_skpd').val(nama);

                $('#penandatangan').val(null).change();
                $.ajax({
                    url: "{{ route('lpe.penandatangan') }}",
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
        } else {
            let skpd = document.getElementById('kd_skpd').value;
            $.ajax({
                url: "{{ route('lra_hbts.penandatangan') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    kd_skpd: skpd,
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
        }
        
        // cetak
        $('.cetak').on('click', function() {
            let url = new URL("{{ route('lpe.cetak') }}");
            let kd_skpd = $("#kd_skpd").val();
            let penandatangan = $("#penandatangan").val();
            let tgl_ttd = $("#tgl_ttd").val();
            let bulan = $("#bulan").val();

            if (!kd_skpd) {
                return alert('Kode SKPD Belum Dipilih')
            }

            if (!penandatangan) {
                return alert('Penandatangan Belum Dipilih')
            }

            if (!tgl_ttd) {
                return alert('Tanggal TTD Belum Dipilih')
            }
            if (!bulan) {
                return alert('Bulan Belum Dipilih')
            }

            let searchParams = url.searchParams;
            if (kd_skpd !== 'ALL') {
                searchParams.append('kd_skpd', kd_skpd);
            }

            searchParams.append('penandatangan', penandatangan);
            searchParams.append('tgl_ttd', tgl_ttd);
            searchParams.append('bulan', bulan);
            searchParams.append('jenis', $(this).data('jenis'));
            window.open(url.toString(), "_blank");
        });
    });
</script>
