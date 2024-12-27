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
            let url = new URL("{{ route('register_sp2d_bpk_t.cetak') }}");
            let akumulasi = $("#akumulasi").val();
            let kd_skpd = $("#kd_skpd").val();
            let bulan = $("#bulan").val();

            if (!kd_skpd) {
                return alert('Kode SKPD Belum Dipilih')
            }

            if (!bulan) {
                return alert('Bulan Belum Dipilih')
            }

            if (!akumulasi) {
                return alert('Akumulasi Belum Dipilih')
            }

            let searchParams = url.searchParams;
            if (kd_skpd !== 'ALL') {
                searchParams.append('kd_skpd', kd_skpd);
            }
            searchParams.append('bulan', bulan);
            searchParams.append('akumulasi', akumulasi);
            searchParams.append('jenis', $(this).data('jenis'));
            window.open(url.toString(), "_blank");
        });
    });
</script>
