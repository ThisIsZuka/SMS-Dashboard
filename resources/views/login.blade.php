<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="{{ asset('images/icon.jpg') }}" rel="icon" type="image/gif">
    <title>Login SMS Management</title>

    <!-- Font Awesome -->
    <link href="{{ asset('assets/fontawesome/css/all.min.css') }}" rel="stylesheet" />

    {{-- bootstrap --}}
    <link href="{{ asset('assets/bootstrap-5.0.2/css/bootstrap.min.css') }}" rel="stylesheet">
    <script src="{{ asset('assets/bootstrap-5.0.2/js/bootstrap.min.js') }}"></script>


    {{-- JQuery --}}
    <script src="{{ asset('assets/jquery-3.5.1.min.js') }}"></script>

    {{-- axios --}}
    <script src="{{ asset('assets/axios.min.js') }}"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>

    {{-- SnackBar --}}
    <link href="{{ asset('assets/SnackBar-master/dist/snackbar.min.css') }}" rel="stylesheet">
    <script src="{{ asset('assets/SnackBar-master/dist/snackbar.min.js') }}"></script>


    {{-- Cookie --}}
    <script src="{{ asset('assets/js.cookie.js') }}"></script>

    {{-- CSS --}}
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Main.css') }}">


</head>

<body>

    @include('Template.Loading')
    {{-- <div class="loading" style="display: none">Loading&#8230;</div> --}}

    <div class="container mt-3">

        <div class="loading" style="display: none">Loading&#8230;</div>

        <div>
            <img src="{{ asset('images/UFUND.png') }}" alt="" style="width: 15%;" id="icon_ufond">
        </div>

        <form class="centered">
            {{-- <div class="mb-3">
                <label for="id_card" class="form-label collectes-ville text-center"> กรุณากรอกเลขที่บัตรประชาชน </label>
                <input type="email" class="form-control" id="id_card" aria-describedby="idHelp" placeholder="Search"
                    maxlength="13">
            </div> --}}

            <div class="mb-3">
                <label for="username" class="form-label collectes-ville text-center"> username </label>
                <input type="tel" class="form-control" id="username" aria-describedby="idHelp"
                    placeholder="username" maxlength="13">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">password</label>
                <input type="tel" class="form-control" id="password" placeholder="password">
            </div>

            <div class="text-center mt-3">
                <button type="button" id="btn_submit" class="btn btn-outline-dark"
                    style="width: 80%; border-radius: 2rem;"> เข้าสู่ระบบ </button>
            </div>
        </form>

    </div>

</body>

</html>

<script>
    $(document).ready(function() {

        $('#testbrn').on('click', function() {
            $(".background_loading").css('display', 'block');
        })


        $('#btn_submit').click(function() {
            $(".background_loading").css("display", "block");
            // var id_card = $('#id_card').val().replace(/[^0-9 ]/g, "");
            axios({
                    method: 'POST',
                    url: 'Login_user',
                    data: {
                        username: $('#username').val(),
                        password: $('#password').val(),
                    }
                }).then(function(response) {
                    console.log(response);
                    $(".background_loading").css("display", "none");
                    window.location = '{{ url('/') }}';
                })
                .catch(function(error) {
                    $(".background_loading").css("display", "none");
                    Snackbar.show({
                        actionText: 'close',
                        pos: 'top-center',
                        actionTextColor: '#dc3545',
                        backgroundColor: '#323232',
                        width: 'auto',
                        text: 'SYSTEM ERROR'
                    });
                    // $(".loading").css("display", "none");
                    console.log(error);
                });
        });
    })
</script>
