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
            let url = new URL("{{ route('lpsal.cetak') }}");
            let penandatangan = $("#penandatangan").val();
            let tgl_ttd = $("#tgl_ttd").val();
            let bulan = $("#bulan").val();

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
            searchParams.append('penandatangan', penandatangan);
            searchParams.append('tgl_ttd', tgl_ttd);
            searchParams.append('bulan', bulan);
            searchParams.append('jenis', $(this).data('jenis'));
            window.open(url.toString(), "_blank");
        });
    });
</script>
