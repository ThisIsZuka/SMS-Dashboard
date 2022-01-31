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
                {{-- <button id="testbrn">Test SMS</button> --}}
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
                axios({
                        method: 'GET',
                        url: 'test_send_SMS',
                        params: {
                            PHONE: '66xxxxx',
                            APP_ID: '123456',
                            QUOTATION_ID: '123456',
                            INVOICE_ID: '123456',
                            CONTRACT_ID: '123456',
                            TYPE: 'INVOICE',
                            DUE_DATE: '2022-01-01',
                            INV_DATE: '2022-01-01',
                            TRANSECTION_ID: '124564'
                        }
                    }).then(function(response) {
                        console.log(response);
                    })
                    .catch(function(error) {
                        console.log(error);
                    });
            })


        $('#Get_cookie').click(function() {
            axios({
                    method: 'POST',
                    url: 'get_cookie',
                    data: {
                        cookie: ['SMS_Username_Permission', 'SMS_Username_server'],
                    }
                }).then(function(response) {
                    console.log(response);
                })
                .catch(function(error) {
                    console.log(error);
                });
        });
    })
</script>
