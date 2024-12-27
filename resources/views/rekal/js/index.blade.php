
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#mapping_all').on('click', function() {
            $.ajax({
                url: "{{ route('rekal.proses_mapping_all') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    nomor: 1,
                },
                beforeSend: function() {
                    // Show image container
                    $("#loading").modal('show');
                },
                success: function(data) {
                    if (data.message == '1') {
                        alert('Berhasil Rekal');
                    } else {
                        alert('Gagal Rekal');
                    }
                },
                complete: function(data) {
                    // Hide image container
                    $("#loading").modal('hide');
                }
            });
        });


    });
</script>
