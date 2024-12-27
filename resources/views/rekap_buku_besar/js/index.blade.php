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

        // kd skpd && rekening
        $('#kd_skpd').on('select2:select', function() {
            let nama = $(this).find(':selected').data('nama');
            $('#nm_skpd').val(nama);
        });

        // cetak
        $('.cetak').on('click', function() {
            let url = new URL("{{ route('rekap_buku_besar.cetak') }}");
            let kd_skpd = $("#kd_skpd").val();
            let tgl_awal = $("#tgl_awal").val();
            let tgl_akhir = $("#tgl_akhir").val();
            let rekening = $("#rekening").val();
            let jns_rekening = $("#jns_rekening").val();

            if (!kd_skpd) {
                return alert('Kode SKPD Belum Dipilih')
            }

            if (!tgl_awal) {
                return alert('Tanggal Awal Belum Dipilih')
            }

            if (!tgl_akhir) {
                return alert('Tanggal Akhir Belum Dipilih')
            }

            if (!jns_rekening) {
                return alert('Jenis Rekening Belum Dipilih')
            }

            if (!rekening) {
                return alert('Rekening Belum Dipilih')
            }

            let searchParams = url.searchParams;
            searchParams.append('kd_skpd', kd_skpd);
            searchParams.append('jns_rekening', jns_rekening);
            searchParams.append('tgl_awal', tgl_awal);
            searchParams.append('tgl_akhir', tgl_akhir);
            searchParams.append('rekening', rekening);
            searchParams.append('jenis', $(this).data('jenis'));
            window.open(url.toString(), "_blank");
        });
    });
</script>
