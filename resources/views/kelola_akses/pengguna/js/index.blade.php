<script type="text/javascript">
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#penggunaTable').DataTable({
            responsive: true,
            ordering: false,
            serverSide: true,
            processing: true,
            lengthMenu: [10, 50],
            ajax: {
                "url": "{{ route('pengguna.load_data') }}",
                "type": "POST",
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    className: "text-center",
                },
                {
                    data: 'aksi',
                    name: 'aksi',
                    width: 100,
                    className: "text-center",
                },
                {
                    data: 'username',
                    name: 'username',
                },
                {
                    data: 'nama',
                    name: 'nama',
                },
                {
                    data: 'nama_skpd',
                    name: 'nama_skpd',
                },
                {
                    data: 'jabatan',
                    name: 'jabatan',
                },
            ],
        });
    });

    function hapusPengguna(id, user_id) {
        if (id == user_id) {
            alert('Dilarang menghapus data diri sendiri!!!');
            return;
        }
        var r = confirm("Hapus?");
        if (r == true) {
        let url = '{{ route("pengguna.destroy", ":id") }}';
            url = url.replace(':id', id);
            $.ajax({
                url: url,
                type: 'DELETE',
                data: {
                    id: id,
                },
                success: function(data) {
                    if (data.message == '1') {
                        alert('Data berhasil dihapus!');
                        window.location.reload();
                    } else {
                        alert('Data gagal dihapus!');
                    }
                }
            });
        } else {
            return false;
        }
    }
</script>