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
            let url = new URL("{{ route('laporan_sisa_bank.cetak') }}");
            let bulan = $("#bulan").val();
            
            if (!bulan) {
                return alert('Bulan Belum Dipilih')
            }

            let searchParams = url.searchParams;            
            searchParams.append('bulan', bulan);
            searchParams.append('jenis', $(this).data('jenis'));
            window.open(url.toString(), "_blank");
        });
    });
</script>
