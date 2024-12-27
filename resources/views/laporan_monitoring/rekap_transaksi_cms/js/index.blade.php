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

        // kd skpd
        $('#kd_skpd').on('select2:select', function() {
            let nama = $(this).find(':selected').data('nama');
            $('#nm_skpd').val(nama);
        });

        // cetak
        $('.cetak').on('click', function() {
            let url = new URL("{{ route('rekap_transaksi_cms.cetak') }}");
            let bulan = $("#bulan").val();
            let kd_skpd = $("#kd_skpd").val();
            let status = $("#status").val();
            
            if (!bulan) {
                return alert('Bulan Belum Dipilih')
            }

            if (!kd_skpd) {
                return alert('Kode SKPD Belum Dipilih')
            }

            if (!status) {
                return alert('Status Validasi Belum Dipilih')
            }

            let searchParams = url.searchParams;            
            searchParams.append('bulan', bulan);
            searchParams.append('kd_skpd', kd_skpd);
            searchParams.append('status', status);
            searchParams.append('jenis', $(this).data('jenis'));
            window.open(url.toString(), "_blank");
        });
    });
</script>
