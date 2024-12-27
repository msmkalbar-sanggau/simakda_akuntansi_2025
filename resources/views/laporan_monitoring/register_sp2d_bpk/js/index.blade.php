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

        $('#kd_belanja').on('select2:select', function() {
            let nama = $(this).find(':selected').data('nama');
            $('#nm_rek4').val(nama);
        });

        $(".kd_bl").hide();
        $('#rekening').on('select2:select', function() {
            var kb = document.getElementById('rekening').value;
            if (kb == '5') {
                $(".kd_bl").show();
            } else {
                $(".kd_bl").hide();
            }
        }).trigger('select2:select');

        // cetak
        $('.cetak').on('click', function() {
            let url = new URL("{{ route('register_sp2d_bpk.cetak') }}");
            let akumulasi = $("#akumulasi").val();
            let kd_skpd = $("#kd_skpd").val();
            let bulan = $("#bulan").val();
            let rekening = $("#rekening").val();
            let kd_belanja = $("#kd_belanja").val();

            if (!kd_skpd) {
                return alert('Kode SKPD Belum Dipilih')
            }

            if (!bulan) {
                return alert('Bulan Belum Dipilih')
            }
            
            if (!rekening) {
                return alert('Rekening Belum Dipilih')
            }

            if (kd_belanja == '5') {
                return alert('Kode Belanja Belum Dipilih')
            }

            if (!akumulasi) {
                return alert('Akumulasi Belum Dipilih')
            }

            let searchParams = url.searchParams;
            if (kd_skpd !== 'ALL') {
                searchParams.append('kd_skpd', kd_skpd);
            }
            searchParams.append('bulan', bulan);
            if (rekening !== 'ALL'){
                searchParams.append('rekening', rekening);
            }
            if (kd_belanja !== 'ALL') {
                searchParams.append('kd_belanja', kd_belanja);
            }
            searchParams.append('akumulasi', akumulasi);
            searchParams.append('jenis', $(this).data('jenis'));
            window.open(url.toString(), "_blank");
        });
    });
</script>
