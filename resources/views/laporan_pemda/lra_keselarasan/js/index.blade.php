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

        $.ajax({
            url: "{{ route('lra_keselarasan.penandatangan') }}",
            type: "POST",
            dataType: 'json',
            success: function(data) {
                $('#penandatangan').empty();
                $('#penandatangan').append(
                    `<option value="" disabled selected>Silahkan Pilih</option>`);
                $.each(data, function(index, data) {
                    $('#penandatangan').append(
                        `<option value="${data.nip}">${data.nip} - ${data.nama}</option>`
                    );
                })
            }
        });

        // cetak
        $('.cetak').on('click', function() {
            let url = new URL("{{ route('lra_keselarasan.cetak') }}");
            let penandatangan = $("#penandatangan").val();
            let tgl_ttd = $("#tgl_ttd").val();
            let bulan = $("#bulan").val();
            let jns_ang = $("#jns_ang").val();

            if (!bulan) {
                return alert('Bulan Belum Dipilih')
            }

            if (!jns_ang) {
                return alert('Jenis Anggaran Belum Dipilih')
            }

            if (!penandatangan) {
                return alert('Penandatangan Belum Dipilih')
            }

            if (!tgl_ttd) {
                return alert('Tanggal TTD Belum Dipilih')
            }


            let searchParams = url.searchParams;
            searchParams.append('penandatangan', penandatangan);
            searchParams.append('tgl_ttd', tgl_ttd);
            searchParams.append('bulan', bulan);
            searchParams.append('jns_ang', jns_ang);
            searchParams.append('jenis', $(this).data('jenis'));
            window.open(url.toString(), "_blank");
        });
    });
</script>
