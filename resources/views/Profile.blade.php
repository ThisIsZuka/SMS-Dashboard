<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="{{ asset('images/icon.jpg') }}" rel="icon" type="image/gif">
    <title>Profile</title>

    <!-- Font Awesome -->
    <link href="{{ asset('assets/fontawesome/css/all.min.css') }}" rel="stylesheet" />

    {{-- bootstrap --}}
    <link href="{{ asset('assets/bootstrap-5.1.3/css/bootstrap.min.css') }}" rel="stylesheet">
    <script src="{{ asset('assets/bootstrap-5.1.3/js/bootstrap.min.js') }}"></script>


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


    {{-- DASHBOARD --}}

    <link rel="canonical" href="https://www.wrappixel.com/templates/monster-admin-lite/" />
    <!-- Custom CSS -->
    <link href={{ asset('monster-html/plugins/chartist/dist/chartist.min.css') }} rel="stylesheet">
    <!-- Custom CSS -->
    <link href={{ asset('monster-html/css/style.min.css') }} rel="stylesheet">


    <script src={{ asset('monster-html/js/app-style-switcher.js') }}></script>
    <!--Wave Effects -->
    <script src={{ asset('monster-html/js/waves.js') }}></script>
    <!--Menu sidebar -->
    <script src={{ asset('monster-html/js/sidebarmenu.js') }}></script>
    <!--Custom JavaScript -->
    <script src={{ asset('monster-html/js/custom.js') }}></script>
    <!--This page JavaScript -->
    <!--flot chart-->
    <script src={{ asset('monster-html/plugins/flot/jquery.flot.js') }}></script>
    <script src={{ asset('monster-html/plugins/flot.tooltip/js/jquery.flot.tooltip.min.js') }}></script>

</head>

<body>

    @include('Template.Loading')
    {{-- <div class="loading" style="display: none">Loading&#8230;</div> --}}

    <div id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="absolute" data-header-position="absolute" data-boxed-layout="full">

        @include('Template.Left_Navbar')

        <div class="page-wrapper">
            <!-- ============================================================== -->
            <!-- Bread crumb and right sidebar toggle -->
            <!-- ============================================================== -->
            <div class="page-breadcrumb">
                <div class="row align-items-center">
                    <div class="col-md-6 col-8 align-self-center">
                        <h3 class="page-title mb-0 p-0">Profile</h3>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Profile</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container-fluid">
                <button id="testbrn">Test Mail</button>
                <button id="Get_cookie">Get_cookie</button>

                <div style="height:30px;overflow:hidden;margin-right:15px;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">SMS_ID</th>
                                <th scope="col">DATE</th>
                                <th scope="col">CONTRACT_ID</th>
                                <th scope="col">QUOTATION_ID</th>
                                <th scope="col">APP_ID</th>
                                <th scope="col">PHONE</th>
                                <th scope="col">TRANSECTION_TYPE</th>
                                <th scope="col">TRANSECTION_ID</th>
                                <th scope="col">DUE_DATE</th>
                                <th scope="col">SMS_RESPONSE_MESSAGE</th>
                                <th scope="col">SMS_RESPONSE_JOB_ID</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th scope="col">SMS_ID</th>
                                <th scope="col">DATE</th>
                                <th scope="col">CONTRACT_ID</th>
                                <th scope="col">QUOTATION_ID</th>
                                <th scope="col">APP_ID</th>
                                <th scope="col">PHONE</th>
                                <th scope="col">TRANSECTION_TYPE</th>
                                <th scope="col">TRANSECTION_ID</th>
                                <th scope="col">DUE_DATE</th>
                                <th scope="col">SMS_RESPONSE_MESSAGE</th>
                                <th scope="col">SMS_RESPONSE_JOB_ID</th>
                            </tr>
                            <tr>
                                <th scope="col">SMS_ID</th>
                                <th scope="col">DATE</th>
                                <th scope="col">CONTRACT_ID</th>
                                <th scope="col">QUOTATION_ID</th>
                                <th scope="col">APP_ID</th>
                                <th scope="col">PHONE</th>
                                <th scope="col">TRANSECTION_TYPE</th>
                                <th scope="col">TRANSECTION_ID</th>
                                <th scope="col">DUE_DATE</th>
                                <th scope="col">SMS_RESPONSE_MESSAGE</th>
                                <th scope="col">SMS_RESPONSE_JOB_ID</th>
                            </tr>
                            <tr>
                                <td>Cel 3,1</td>
                                <td>Cel 3,2</td>
                                <td>Cel 3,3</td>
                            </tr>


                        </tbody>
                    </table>
                </div>
                <div style="height:100px;overflow-y:scroll;;">
                    <table class="table">
                        <thead>

                        </thead>
                        <tbody>
                            <tr>
                                <th scope="col">SMS_ID</th>
                                <th scope="col">DATE</th>
                                <th scope="col">CONTRACT_ID</th>
                                <th scope="col">QUOTATION_ID</th>
                                <th scope="col">APP_ID</th>
                                <th scope="col">PHONE</th>
                                <th scope="col">TRANSECTION_TYPE</th>
                                <th scope="col">TRANSECTION_ID</th>
                                <th scope="col">DUE_DATE</th>
                                <th scope="col">SMS_RESPONSE_MESSAGE</th>
                                <th scope="col">SMS_RESPONSE_JOB_ID</th>
                            </tr>
                            <tr>
                                <th scope="col">SMS_ID</th>
                                <th scope="col">DATE</th>
                                <th scope="col">CONTRACT_ID</th>
                                <th scope="col">QUOTATION_ID</th>
                                <th scope="col">APP_ID</th>
                                <th scope="col">PHONE</th>
                                <th scope="col">TRANSECTION_TYPE</th>
                                <th scope="col">TRANSECTION_ID</th>
                                <th scope="col">DUE_DATE</th>
                                <th scope="col">SMS_RESPONSE_MESSAGE</th>
                                <th scope="col">SMS_RESPONSE_JOB_ID</th>
                            </tr>
                            <tr>
                                <td>Cel 3,1</td>
                                <td>Cel 3,2</td>
                                <td>Cel 3,3</td>
                            </tr>
                            <tr style="color:white">
                                <th>Col 1</th>
                                <th>Col 2</th>
                                <th>Col 3</th>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>

            <footer class="footer text-center">
                © 2021 SMS Admin by <a href="https://www.wrappixel.com/">wrappixel.com</a>
            </footer>

        </div>
    </div>

</body>

</html>

<script>
    $(document).ready(function() {
        
        // http: //ufund-portal.webhop.biz:9090/SMS-Dashboard/send_SMS_Invoice?PHONE=..&QUOTATION_ID=..&APP_ID=..&INVOICE_ID=..&CONTRACT_ID=..&DUE_DATE=..

        $('#testbrn').on('click', function() {
            // $(".background_loading").css('display', 'block');
            var data = new FormData();
            data.append("from_name", "Thaksinai Kondee");
            data.append("from_email", "thaksinai@hotmail.com");
            data.append("to", "thaksinai@ispio.com");
            data.append("subject",
                "ร่วมฉลอง ครบรอบการก่อตั้ง บริษัท Nipa technology วันนี้ เวลา 18:00 น.");
            data.append("message", "content1");
            data.append("reply_email", "se55660159@gmail.com");
            data.append("reply_name", "Thakweb.com");

            var xhr = new XMLHttpRequest();
            xhr.withCredentials = true;

            xhr.addEventListener("readystatechange", function() {
                if (this.readyState === 4) {
                    console.log(this.responseText);
                }
            });

            xhr.open("POST",
                "https://app-x.nipamail.com/v1.0/transactional/post?accept_token=NPAPP-IGBmfucf0Np567WzwAH1KuRovnszt9r4HJPJ2U4SRhPk1pCeBXEi7nUkixLby8qwwwymbXwtV3gjOMeodNKlcAChYrZ0D8TQv60208fxXIZlJ7www"
                );
            xhr.setRequestHeader("cache-control", "no-cache");

            xhr.send(data);
        })


        $('#Get_cookie').click(function() {
            // image = 'data:image/png;base64,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'
            // const formData = new FormData();
            // formData.append("pad_1", image);
            // formData.append("pad_2", image);
            axios({
                method: 'POST',
                url: 'http://ufund-portal.webhop.biz:9090/API-Corelease/api/new_customer',
                // data: {
                //     // APP_ID : APP_ID,
                //     // pad_1: $('#e807d126-8ea3-cc8d-62fb-b0ed2942a86b_2ab444e5-926d-436c-86b8-cbfb41edf98e_Pad_output').val(),
                //     // pad_2: $('#e807d126-8ea3-cc8d-62fb-b0ed2942a86b_498b5cd7-3071-4da9-bfdc-39706d192d6f_Pad_output').val(),
                //     // pad_3: $('#454f0c3e-ae68-9568-15c7-6864a470847b_82579654-5ce7-58e2-42c9-c28bf570c9c2_Pad_output').val(),
                //     // pad_4: $('#c469e695-5111-c55e-60a9-cb315122d88e_356e0513-1b6b-4c93-8645-fa478187df8e_Pad_output').val(),
                //     // pad_5: $('#1c5c57a2-f000-7ab8-86b5-b04498bcb8d0_722fe589-5a37-4b2b-9cf9-f9fd9b246be5_Pad_output').val(),
                // },
                // data : formData,
            }).then(function (response) {
                console.log(response);
            })
            .catch(function (error) {
                console.log(error);
            });
        });
    })
</script>
