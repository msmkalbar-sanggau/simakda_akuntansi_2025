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

        $.ajax({
            url: "{{ route('lra_urusan.penandatangan') }}",
            type: "POST",
            dataType: 'json',
            success: function(data) {
                $('#penandatangan').empty();
                $('#penandatangan').append(
                    `<option value="" disabled selected>Silahkan Pilih</option>`);
                $.each(data, function(index, data) {
                    $('#penandatangan').append(
                        `<option value="${data.nip}">${data.nip} - ${data.nama}</option>`
                    );
                })
            }
        });


        // cetak
        $('.cetak').on('click', function() {
            let url = new URL("{{ route('lra_urusan.cetak') }}");
            let bulan = $("#bulan").val();
            let tgl_awal = $("#tgl_awal").val();
            let tgl_akhir = $("#tgl_akhir").val();
            let jns_ang = $("#jns_ang").val();
            let penandatangan = $("#penandatangan").val();
            let tgl_ttd = $("#tgl_ttd").val();
            let jenis_cetak = $("#jenis_cetak").val();
            let cekbtt = document.getElementById("btt").checked == true;
            
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

            if (!jns_ang) {
                return alert('Jenis Anggaran Belum Dipilih')
            }

            if (!penandatangan) {
                return alert('Penandatangan Belum Dipilih')
            }

            if (!tgl_ttd) {
                return alert('Tanggal TTD Belum Dipilih')
            }

            if (!jenis_cetak) {
                return alert('Jenis Belum Dipilih')
            }

            let searchParams = url.searchParams;
            if (jenis_periode == 'tanggal') {
                searchParams.append('tgl_awal', tgl_awal);
                searchParams.append('tgl_akhir', tgl_akhir);
            } else {
                searchParams.append('bulan', bulan);
            }
            searchParams.append('jns_ang', jns_ang);
            searchParams.append('penandatangan', penandatangan);
            searchParams.append('tgl_ttd', tgl_ttd);
            
            let btt = '';
            searchParams.append('jenis_cetak', jenis_cetak);
            if (cekbtt == true) {
                btt = '1';
            } else {
                btt = '0';
            }
            searchParams.append('btt', btt);
            searchParams.append('jenis', $(this).data('jenis'));
            window.open(url.toString(), "_blank");
        });
    });
</script>
