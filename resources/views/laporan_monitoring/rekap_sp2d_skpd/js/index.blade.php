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
            let url = new URL("{{ route('rekap_sp2d_skpd.cetak') }}");
            let jns_cetak = $("#jns_cetak").val();

            if (!jns_cetak) {
                return alert('Jenis Cetak Belum Dipilih')
            }

            let searchParams = url.searchParams;            
            searchParams.append('jns_cetak', jns_cetak);
            searchParams.append('jenis', $(this).data('jenis'));
            window.open(url.toString(), "_blank");
        });
    });
</script>
