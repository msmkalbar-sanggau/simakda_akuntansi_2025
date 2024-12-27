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
                $(".periode").hide();
            } else {
                $(".periode").show();
                $('#periode').val(null).change();
                $(".bulan").hide();
            }
        }).change();

        // kd skpd
        $('#kd_skpd').on('select2:select', function() {
            let nama = $(this).find(':selected').data('nama');
            $('#nm_skpd').val(nama);
        });
        // cetak
        $('.cetak').on('click', function() {
            let url = new URL("{{ route('rekon.cetak') }}");
            let kd_skpd = $("#kd_skpd").val();
            let periode = $("#periode").val();
            let bulan = $("#bulan").val();
            let jns_rekon = $("#jns_rekon").val();

            if (!kd_skpd) {
                return alert('Kode SKPD Belum Dipilih')
            }

            const jenis_periode = $('input[name="jenis_periode"]:checked').val()
            if (jenis_periode == 'periode') {
                if (!periode) {
                    return alert('Periode Belum Dipilih')
                }
            } else {
                if (!bulan) {
                    return alert('Bulan Belum Dipilih')
                }
            }


            if (!jns_rekon) {
                return alert('Jenis Rekon Belum Dipilih')
            }

            let searchParams = url.searchParams;
            searchParams.append('kd_skpd', kd_skpd);
            if (jenis_periode == 'periode') {
                searchParams.append('periode', periode);
            } else {
                searchParams.append('bulan', bulan);
            }
            searchParams.append('jns_rekon', jns_rekon);
            searchParams.append('jenis', $(this).data('jenis'));
            window.open(url.toString(), "_blank");
        });
    });
</script>
