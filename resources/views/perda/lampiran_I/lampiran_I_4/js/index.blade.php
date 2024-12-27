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
            let url = new URL("{{ route('lampiran_I.lampiran_I_4.cetak') }}");
            let tgl_ttd = $("#tgl_ttd").val();
            let ttd = $("#ttd").val();
            let bulan = $("#bulan").val();
            let jns_ang = $("#jns_ang").val();

            if (!bulan) {
                return alert('Bulan Belum Dipilih')
            }

            if (!jns_ang) {
                return alert('Jenis Anggaran Belum Dipilih')
            }

            if (!tgl_ttd) {
                return alert('Tanggal TTD Belum Dipilih')
            }

            if (!ttd) {
                return alert('Penandatangan Belum Dipilih')
            }

            let searchParams = url.searchParams;
            searchParams.append('bulan', bulan);
            searchParams.append('jns_ang', jns_ang);
            searchParams.append('tgl_ttd', tgl_ttd);
            searchParams.append('ttd', ttd);
            searchParams.append('jenis', $(this).data('jenis'));
            window.open(url.toString(), "_blank");
        });
    });
</script>
