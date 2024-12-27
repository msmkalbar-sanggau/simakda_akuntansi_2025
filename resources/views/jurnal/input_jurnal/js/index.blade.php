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

        let table = $('#datainputJurnal').DataTable({
            responsive: true,
            ordering: false,
            serverSide: true,
            processing: true,
            lengthMenu: [10, 50],
            ajax: {
                url: "{{ route('input_jurnal.load_data') }}",
                type: "post",
                data: function(d) {
                    d.kd_skpd = document.getElementById('kd_skpd').value;
                },
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
                    data: 'no_voucher',
                    name: 'no_voucher',
                },
                {
                    data: 'tgl_voucher',
                    name: 'tgl_voucher',
                    render: function(data, type, row, meta) {
                    return new Intl.DateTimeFormat('id-ID', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric',
                    }).format(new Date(data))
                }
                },
                {
                    data: 'nm_skpd',
                    name: 'nm_skpd',
                },
                {
                    data: 'ket',
                    name: 'ket',
                },
            ],
        });
    });

    function hapusJurnal(no_voucher, kd_skpd) {
        let tanya = confirm('Apakah anda yakin untuk menghapus data dengan Nomor Voucher : ' + no_voucher);

        if (tanya == true) {
            $.ajax({
                url: "{{ route('input_jurnal.hapus') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    no_voucher: no_voucher,
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
