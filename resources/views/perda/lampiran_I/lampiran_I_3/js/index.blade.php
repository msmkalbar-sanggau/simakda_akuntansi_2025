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

        $('#kd_skpd').on('select2:select', function() {
            let nama = $(this).find(':selected').data('nama');
            $('#nm_skpd').val(nama);
        });

         // cetak
         $('.cetak').on('click', function() {
            let url = new URL("{{ route('lampiran_I.lampiran_I_3.cetak') }}");
            let tgl_ttd = $("#tgl_ttd").val();
            let ttd = $("#ttd").val();
            let kd_skpd = $("#kd_skpd").val();

            if (!kd_skpd) {
                return alert('Kode SKPD Belum Dipilih')
            }

            if (!tgl_ttd) {
                return alert('Tanggal TTD Belum Dipilih')
            }

            if (!ttd) {
                return alert('Penandatangan Belum Dipilih')
            }

            let searchParams = url.searchParams;
            searchParams.append('kd_skpd', kd_skpd);
            searchParams.append('tgl_ttd', tgl_ttd);
            searchParams.append('ttd', ttd);
            searchParams.append('jenis', $(this).data('jenis'));
            window.open(url.toString(), "_blank");
        });
    });
</script>