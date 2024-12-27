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

        // cetak
        $('.cetak').on('click', function() {
            let url = new URL("{{ route('lak.cetak') }}");
            let bulan = $("#bulan").val();
            let penandatangan = $("#penandatangan").val();
            let tgl_ttd = $("#tgl_ttd").val();

            if (!bulan) {
                return alert('Bulan Belum Dipilih')
            }

            if (!penandatangan) {
                return alert('Bulan Belum Dipilih')
            }

            if (!tgl_ttd) {
                return alert('Bulan Belum Dipilih')
            }

            let searchParams = url.searchParams;
            searchParams.append('penandatangan', penandatangan);
            searchParams.append('tgl_ttd', tgl_ttd);
            searchParams.append('bulan', bulan);
            searchParams.append('jenis', $(this).data('jenis'));
            window.open(url.toString(), "_blank");
        });

    });
</script>
