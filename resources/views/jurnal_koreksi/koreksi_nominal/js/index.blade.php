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
        })

        $('#btnProses').click(function(){
            table.ajax.reload();
        });

        let table = $('#dataKoreksiNominal').DataTable({
            responsive: true,
            ordering: false,
            serverSide: true,
            processing: true,
            lengthMenu: [10, 50],
            ajax: {
                url: "{{ route('koreksi_nominal.load_data') }}",
                type: "post",
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                },
                {
                    data: 'aksi',
                    name: 'aksi',
                    width: 115,
                    className: "text-center",
                },
                {
                    data: 'no_bukti',
                    name: 'no_bukti',
                },
                {
                    data: 'tgl_bukti',
                    name: 'tgl_bukti',
                    render: function(data, type, row, meta) {
                    return new Intl.DateTimeFormat('id-ID', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric',
                    }).format(new Date(data))
                }
                },
                {
                    data: 'ket',
                    name: 'ket',
                },
                {
                    data: 'no_transaksi_awal',
                    name: 'no_transaksi_awal',
                },
            ],
        });
    });

    function hapusKoreksi(no_bukti, kd_skpd) {
        let tanya = confirm('Apakah anda yakin untuk menghapus data dengan Nomor Bukti : ' + no_bukti);

        if (tanya == true) {
            $.ajax({
                url: "{{ route('koreksi_nominal.hapus') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    no_bukti: no_bukti,
                    kd_skpd: kd_skpd,
                },
                success: function(data) {
                    if (data.message == '1') {
                        alert('Proses Hapus Berhasil');
                        window.location.reload();
                    } else {
                        alert('Proses Hapus Gagal...!!!');
                    }
                },
            })
        } else {
            return false;
        }
    }
</script>
