<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="{{ asset('public/images/icon.jpg') }}" rel="icon" type="image/gif">
    <title>Login SMS Management</title>

    <!-- Font Awesome -->
    <link href="{{ asset('public/assets/fontawesome/css/all.min.css') }}" rel="stylesheet" />

    {{-- bootstrap --}}
    <link href="{{ asset('public/assets/bootstrap-5.1.3/css/bootstrap.min.css') }}" rel="stylesheet">
    <script src="{{ asset('public/assets/bootstrap-5.1.3/js/bootstrap.min.js') }}"></script>


    {{-- JQuery --}}
    <script src="{{ asset('public/assets/jquery-3.5.1.min.js') }}"></script>

    {{-- axios --}}
    <script src="{{ asset('public/assets/axios.min.js') }}"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>

    {{-- SnackBar --}}
    <link href="{{ asset('public/assets/SnackBar-master/dist/snackbar.min.css') }}" rel="stylesheet">
    <script src="{{ asset('public/assets/SnackBar-master/dist/snackbar.min.js') }}"></script>


    {{-- Cookie --}}
    <script src="{{ asset('public/assets/js.cookie.js') }}"></script>

    {{-- CSS --}}
    <link rel="stylesheet" href="{{ asset('public/css/header.css') }}">
    <link rel="stylesheet" href="{{ asset('public/css/Main.css') }}">
    <link rel="stylesheet" href="{{ asset('public/css/login.css') }}">

    <link rel="stylesheet" href="{{ asset('public/css/bg_star.css') }}">


</head>

<body>

    @include('Template.Loading')
    {{-- <div class="loading" style="display: none">Loading&#8230;</div> --}}

    <div id="bg"></div>

    <link href='https://fonts.googleapis.com/css?family=Lato:300,400,700' rel='stylesheet' type='text/css'>
    <div id='stars'></div>
    <div id='stars2'></div>
    <div id='stars3'></div>


    <div class="container mt-3">

        <div class="loading" style="display: none">Loading&#8230;</div>

        <div class="container">

            <form class="centered card_bg">
                <div class="container text-center">
                    <img src="{{ asset('public/images/UFUND.png') }}" alt="" style="width: 50%;" id="icon_ufond_login">

                    <h3 class="text-color-gradian">SMS Management</h3>
                </div>

                <div class="mb-3">
                    <div class="form__group field">
                        <input type="input" class="form__field" placeholder="Username" id='Username' required />
                        <label for="Username" class="form__label">Username</label>
                    </div>
                </div>
                <div class="mb-3">
                    {{-- <label for="password" class="form-label">Password</label>l
                    <input type="text" class="form-control" id="password" placeholder="Password"> --}}
                    <div class="form__group field">
                        <input type="password" class="form__field" placeholder="Password" id='Password' required />
                        <label for="Password" class="form__label">Password</label>
                    </div>
                </div>

                <div class="text-center mt-5">
                    <button type="button" id="btn_submit" class="btn btn-outline-light"
                        style="width: 60%; border-radius: 2rem;"> เข้าสู่ระบบ </button>
                </div>
            </form>
        </div>

    </div>

</body>

</html>

<script>
    $(document).ready(function() {

        var token = document.head.querySelector('meta[name="csrf-token"]');
        window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;

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
                        username: $('#Username').val(),
                        password: $('#Password').val(),
                    }
                }).then(function(response) {
                    console.log(response);
                    // $(".background_loading").css("display", "none");
                    if (response.data.code == '999999') {
                        // $.cookie("SMS_Username_server", "Zg8>%z!!8DH~.AY% PG,b5(*KvP{mB%)_");
                        window.location = '{{ url('/') }}';
                    } else {
                        $.cookie("SMS_Username_server", null);
                        Snackbar.show({
                            actionText: 'close',
                            pos: 'top-center',
                            actionTextColor: '#dc3545',
                            backgroundColor: '#323232',
                            width: 'auto',
                            text: 'Login Fail'
                        });
                        $(".background_loading").css("display", "none");
                    }

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
