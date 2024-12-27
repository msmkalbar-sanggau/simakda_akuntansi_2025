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
            let url = new URL("{{ route('rekap_sts.cetak') }}");
            let bulan = $("#bulan").val();
            let kd_skpd = $("#kd_skpd").val();
            
            if (!bulan) {
                return alert('Bulan Belum Dipilih')
            }

            if (!kd_skpd) {
                return alert('Kode SKPD Belum Dipilih')
            }

            let searchParams = url.searchParams;            
            searchParams.append('bulan', bulan);
            searchParams.append('kd_skpd', kd_skpd);
            searchParams.append('jenis', $(this).data('jenis'));
            window.open(url.toString(), "_blank");
        });
    });
</script>
