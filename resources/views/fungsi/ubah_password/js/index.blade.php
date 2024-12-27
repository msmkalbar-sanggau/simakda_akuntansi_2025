<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('.select2-multiple').select2({
            placeholder: "Silahkan Pilih",
            theme: 'bootstrap-5'
        });

        $('#simpan').on('click', function() {
            let id          = document.getElementById('id').value;
            let username    = document.getElementById('username').value;
            let password    = document.getElementById('password').value;
            let password2   = document.getElementById('password2').value;

            if (password.length <= 8) {
                alert('Minimal 8 Karakter');
                return;
            }

            if (password2.length <= 8) {
                alert('Minimal 8 Karakter');
                return;
            }

            let data = {
                id,
                username,
                password,
                password2
            };


            //$('#simpan').prop('disabled', true);
            $.ajax({
                url: "{{ route('ubah_password.simpan') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    data: data
                },
                success: function(response) {
                    if (response.message == '1') {
                        alert('Password Berhasil Diubah');
                        window.location.href =
                            "{{ route('home') }}";
                    } else if (response.message == '2') {
                        alert('Password tidak sama!');
                        $('#simpan').prop('disabled', false);
                        return;
                    }else {
                        alert('Password Gagal Diubah!');
                        $('#simpan').prop('disabled', false);
                        return;
                    }
                }
            })
        });

    });
</script>
