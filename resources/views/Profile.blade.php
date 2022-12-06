<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="{{ asset('public/images/icon.jpg') }}" rel="icon" type="image/gif">
    <title>Profile</title>

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


    {{-- DASHBOARD --}}

    <link rel="canonical" href="https://www.wrappixel.com/templates/monster-admin-lite/" />
    <!-- Custom CSS -->
    <link href={{ asset('public/monster-html/plugins/chartist/dist/chartist.min.css') }} rel="stylesheet">
    <!-- Custom CSS -->
    <link href={{ asset('public/monster-html/css/style.min.css') }} rel="stylesheet">


    <script src={{ asset('public/monster-html/js/app-style-switcher.js') }}></script>
    <!--Wave Effects -->
    <script src={{ asset('public/monster-html/js/waves.js') }}></script>
    <!--Menu sidebar -->
    <script src={{ asset('public/monster-html/js/sidebarmenu.js') }}></script>
    <!--Custom JavaScript -->
    <script src={{ asset('public/monster-html/js/custom.js') }}></script>
    <!--This page JavaScript -->
    <!--flot chart-->
    <script src={{ asset('public/monster-html/plugins/flot/jquery.flot.js') }}></script>
    <script src={{ asset('public/monster-html/plugins/flot.tooltip/js/jquery.flot.tooltip.min.js') }}></script>

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
                <button id="Test_SCB">Test_SCB</button>

                <table class="table" id="tb_select">
                    <thead>
                        <tr>
                            <th scope="col"><input type="checkbox" id="allCheck" name="allCheck" /></th>
                            <th scope="col">First</th>
                            <th scope="col">Last</th>
                            <th scope="col">Handle</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="checkbox" id="cell_check" /></td>
                            <td>Mark</td>
                            <td>Otto</td>
                            <td>@mdo</td>
                        </tr>
                        <tr>
                            <td><input type="checkbox" id="cell_check" /></td>
                            <td>Jacob</td>
                            <td>Thornton</td>
                            <td>@fat</td>
                        </tr>
                        <tr>
                            <td><input type="checkbox" id="cell_check" /></td>
                            <td>Larry</td>
                            <td>the Bird</td>
                            <td>@twitter</td>
                        </tr>
                    </tbody>
                </table>


                <button id="Get_table">Get_table</button>

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

        var token = document.head.querySelector('meta[name="csrf-token"]');
        window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;


        $('#allCheck').change(function() {
            if ($(this).prop('checked')) {
                $('tbody tr td input[type="checkbox"]').each(function() {
                    $(this).prop('checked', true);
                    $(this).val('checked')
                });
            } else {
                $('tbody tr td input[type="checkbox"]').each(function() {
                    $(this).prop('checked', false);
                    $(this).val('')
                });
            }
        });

        $('#Get_table').on('click', function() {
            var tbl = $('#tb_select tr:has(td)').map(function(index, cell) {
                var $td = $('td', this);
                if ($('td input', this).prop('checked')) {
                    return {
                        id: ++index,
                        name: $td.eq(1).text(),
                        age: $td.eq(2).text(),
                        grade: $td.eq(3).text()
                    }
                }
            }).get();

            console.log(tbl)
        })

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


        $('#Test_SCB').click(function() {
            axios({
                    method: 'POST',
                    url: 'api/SCBbillPayment',
                    data: {
                        "request": "verify",
                        "user": "",
                        "password": "",
                        "tranID": "1507211824590420",
                        "tranDate": "2015-05-30T18:00:00",
                        "channel": "TELL",
                        "account": "1113060335",
                        "amount": "1947.91",
                        "reference1": "2122631",
                        "reference2": "03",
                        "reference3": "",
                        "branchCode": "0111",
                        "terminalID": "2"
                    },
                    // data : formData,
                }).then(function(response) {
                    console.log(response);

                })
                .catch(function(error) {
                    console.log(error);
                });
        });
    })
</script>
