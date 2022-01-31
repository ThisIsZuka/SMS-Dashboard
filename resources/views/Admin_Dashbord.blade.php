<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="{{ asset('images/icon.jpg') }}" rel="icon" type="image/gif">
    <title>Admin</title>

    <!-- Font Awesome -->
    <link href="{{ asset('assets/fontawesome/css/all.min.css') }}" rel="stylesheet" />

    {{-- bootstrap --}}
    <link href="{{ asset('assets/bootstrap-5.1.3/css/bootstrap.min.css') }}" rel="stylesheet">
    <script src="{{ asset('assets/bootstrap-5.1.3/js/bootstrap.min.js') }}"></script>


    {{-- JQuery --}}
    <script src="{{ asset('assets/jquery-3.5.1.min.js') }}"></script>

    {{-- axios --}}
    <script src="{{ asset('assets/axios.min.js') }}"></script>

    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script> --}}

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
    <script src={{ asset('monster-html/js/pages/dashboards/dashboard1.js') }}></script>
    <!--flot chart-->
    <script src={{ asset('monster-html/plugins/flot/jquery.flot.js') }}></script>
    <script src={{ asset('monster-html/plugins/flot.tooltip/js/jquery.flot.tooltip.min.js') }}></script>

</head>

<body>

    @include('Template.Loading')
    {{-- <div class="loading" style="display: none">Loading&#8230;</div> --}}

    <!-- ============================================================== -->
    <!-- Preloader - style you can find in spinners.css -->
    <!-- ============================================================== -->

    <div id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="absolute" data-header-position="absolute" data-boxed-layout="full">
        <!-- ============================================================== -->
        <!-- End Topbar header -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->

        @include('Template.Left_Navbar')

        <!-- ============================================================== -->
        <!-- End Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Page wrapper  -->
        <!-- ============================================================== -->
        <div class="page-wrapper">
            <!-- ============================================================== -->
            <!-- Bread crumb and right sidebar toggle -->
            <!-- ============================================================== -->
            <div class="page-breadcrumb">
                <div class="row align-items-center">
                    <div class="col-md-6 col-8 align-self-center">
                        <h3 class="page-title mb-0 p-0">Dashboard</h3>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ url('/') }}">home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ============================================================== -->
            <!-- End Bread crumb and right sidebar toggle -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- Container fluid  -->
            <!-- ============================================================== -->
            <div class="container-fluid">
                <!-- ============================================================== -->
                <!-- Sales chart -->
                <!-- ============================================================== -->
                <div class="row">
                    <!-- Column -->
                    <div class="col-sm-6">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">SMS Credit ที่เหลือ</h4>
                                <div class="text-center">
                                    <h2 class="font-light mb-0" id="txt_sms_credit"> </h2>
                                    <span class="text-muted">Credit </span>
                                </div>
                                {{-- <span class="text-success">80%</span>
                                <div class="progress">
                                    <div class="progress-bar bg-success" role="progressbar"
                                        style="width: 80%; height: 6px;" aria-valuenow="25" aria-valuemin="0"
                                        aria-valuemax="100"></div>
                                </div> --}}
                            </div>
                        </div>
                    </div>
                    <!-- Column -->
                    <!-- Column -->
                    <div class="col-sm-6">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">SMS Credit ที่ใช้ไป</h4>
                                <div class="text-center">
                                    <h2 class="font-light mb-0" id="txt_sms_sender"></h2>
                                    <span class="text-muted">Credit </span>
                                </div>
                                {{-- <span class="text-info">30%</span>
                                <div class="progress">
                                    <div class="progress-bar bg-info" role="progressbar"
                                        style="width: 30%; height: 6px;" aria-valuenow="25" aria-valuemin="0"
                                        aria-valuemax="100"></div>
                                </div> --}}
                            </div>
                        </div>
                    </div>
                    <!-- Column -->
                </div>

                <div class="row">
                    <!-- Column -->
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">SMS ที่ส่ง <span class="text-muted">(ทั้งหมด)</span></h4>
                                <div class="text-center">
                                    <h2 class="font-light mb-0" id="txt_sms_sum_sender"> </h2>
                                    <span class="text-muted">SMS</span>
                                </div>
                                {{-- <span class="text-success">80%</span>
                                <div class="progress">
                                    <div class="progress-bar bg-success" role="progressbar"
                                        style="width: 80%; height: 6px;" aria-valuenow="25" aria-valuemin="0"
                                        aria-valuemax="100"></div>
                                </div> --}}
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ============================================================== -->
                <!-- Sales chart -->
                <!-- ============================================================== -->
                {{-- <div class="row">
                    <!-- column -->
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Revenue Statistics</h4>
                                <div class="flot-chart">
                                    <div class="flot-chart-content " id="flot-line-chart"
                                        style="padding: 0px; position: relative;">
                                        <canvas class="flot-base w-100" height="400"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- column -->
                </div> --}}
                <!-- ============================================================== -->
                <!-- Table -->
                <!-- ============================================================== -->
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-md-flex">
                                    <h4 class="card-title col-sm-12 col-md-8 mb-md-0 mb-3 align-self-center">SMS Detail
                                    </h4>
                                    <div class="col-sm-6 col-md-2 ms-auto">
                                        <select class="form-select shadow-none col-md-2 ml-auto" id="year_list">
                                        </select>
                                    </div>
                                    <div class="col-sm-6 col-md-2">
                                        <select class="form-select shadow-none col-md-2 ml-auto" id="month_list">
                                        </select>
                                    </div>
                                </div>
                                <div class="table-responsive mt-5">
                                    <table class="table stylish-table no-wrap">
                                        <thead>
                                            <tr>
                                                <th class="border-top-0" colspan="2">Type</th>
                                                <th class="border-top-0">จำนวน (SMS)</th>
                                            </tr>
                                        </thead>
                                        <tbody id="list_count_sms_type">
                                            <tr>
                                                <td style="width:50px;"><span class="round round-danger">Inv</span></td>
                                                <td class="align-middle">
                                                    <h6>SMS INVOICE</h6>
                                                </td>
                                                <td class="align-middle">350</td>
                                            </tr>
                                            <tr class="active">
                                                <td style="width:50px;"><span class="round round-success">Rec</span>
                                                </td>
                                                <td class="align-middle">
                                                    <h6>SMS RECEIPT</h6>
                                                </td>
                                                <td class="align-middle">220</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ============================================================== -->
                <!-- Table -->
                <!-- ============================================================== -->
                <!-- ============================================================== -->
                <!-- Recent blogss -->
                <!-- ============================================================== -->
                {{-- <div class="row justify-content-center">
                    <!-- Column -->
                    <div class="col-lg-4 col-md-6">
                        <div class="card">
                            <div class="text-center">
                                <img class="card-img-top img-responsive w-50 p-3"
                                    src="{{ asset('images/QR_Code.png') }}" alt="Card">
                            </div>
                            <div class="card-body">
                                <ul class="list-inline d-flex align-items-center">
                                    <li class="ps-0">20 May 2021</li>
                                    <li class="ms-auto"><a href="javascript:void(0)" class="link">3
                                            Comment</a></li>
                                </ul>
                                <h3 class="font-normal">Featured Hydroflora Pots Garden &amp; Outdoors</h3>
                            </div>
                        </div>
                    </div>
                    <!-- Column -->
                    <!-- Column -->
                    <div class="col-lg-4 col-md-6">
                        <div class="card">
                            <div class="text-center">
                                <img class="card-img-top img-responsive w-50 p-3"
                                    src="{{ asset('images/UFUND.png') }}" alt="Card">
                            </div>
                            <div class="card-body">
                                <ul class="list-inline d-flex align-items-center">
                                    <li class="ps-0">20 May 2021</li>
                                    <li class="ms-auto"><a href="javascript:void(0)" class="link">3
                                            Comment</a></li>
                                </ul>
                                <h3 class="font-normal">Featured Hydroflora Pots Garden &amp; Outdoors</h3>
                            </div>
                        </div>
                    </div>
                    <!-- Column -->
                    <!-- Column -->
                    <div class="col-lg-4 col-md-6">
                        <div class="card">
                            <div class="text-center">
                                <img class="card-img-top img-responsive w-50 p-3"
                                    src="{{ asset('images/QR_Code.png') }}" alt="Card">
                            </div>
                            <div class="card-body">
                                <ul class="list-inline d-flex align-items-center">
                                    <li class="ps-0">20 May 2021</li>
                                    <li class="ms-auto"><a href="javascript:void(0)" class="link">3
                                            Comment</a></li>
                                </ul>
                                <h3 class="font-normal">Featured Hydroflora Pots Garden &amp; Outdoors</h3>
                            </div>
                        </div>
                    </div>
                    <!-- Column -->
                </div>
                <!-- ============================================================== -->
                <!-- Recent blogss -->
                <!-- ============================================================== -->
            </div> --}}
                <!-- ============================================================== -->
                <!-- End Container fluid  -->
                <!-- ============================================================== -->
                <!-- ============================================================== -->
                <!-- footer -->
                <!-- ============================================================== -->
                {{-- <footer class="footer text-center">
                © 2022 SMS Dashboard by <a href="">MIS-FINTECH</a>
            </footer> --}}
                @include('Template.footer')
                <!-- ============================================================== -->
                <!-- End footer -->
                <!-- ============================================================== -->
            </div>
            <!-- ============================================================== -->
            <!-- End Page wrapper  -->
            <!-- ============================================================== -->
        </div>
        <!-- End main-wrapper  -->



        <div class="modal fade" tabindex="-1" id="Modal_alert" data-bs-backdrop="static" data-bs-keyboard="false"
            tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">แจ้งเตือน [Code : <span id="txt_head_code"></span>] </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p id="text_alert"> </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="btn_alert_reload" class="btn btn-secondary"
                            data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>


</body>

</html>



<script>
    $(document).ready(function() {

        const option_count_type = () => {
            let today = new Date();
            let now_year = today.getFullYear();
            let now_month = today.getMonth();
            let html_year = ''
            const list = [ now_year, now_year-1, now_year-2];
            html_year = list.map((val, index) => {
                return $('<option/>', {
                    val: val,
                    text: val,
                })
            })
            $('#year_list').html(html_year)

            let months_th = ["ทุกเดือน","มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฎาคม",
                "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"];
            let html_month = ''
            html_month = months_th.map((val, index) => {
                return $('<option/>', {
                    val: index,
                    text: val,
                    selected : index == (now_month + 1) ? true : false,
                })
            })
            $('#month_list').html(html_month)

            SMS_count_type(now_month + 1, now_year);
        }


        // function start
        option_count_type();
        SMS_Check_Credit();
        SMS_Check_Sender();


        function SMS_Check_Credit(iurl, id, type, number) {
            $(".background_loading").css("display", "block");
            axios({
                    method: 'GET',
                    url: 'SMS_Check_Credit',
                }).then(function(response) {
                    // console.log(response.data.split('#'));
                    respon = response.data.split('#');
                    if (respon[0] == 'Success') {
                        respon = response.data.split(':');
                        txt_sms_credit = '<i class="ti-arrow-up text-success"></i>' + parseFloat(respon[
                            respon.length - 1]).toFixed(2)
                        $('#txt_sms_credit').html(txt_sms_credit);
                    } else {
                        // Snackbar.show({
                        //     actionText: 'close',
                        //     pos: 'top-center',
                        //     duration: 15000,
                        //     actionTextColor: '#dc3545',
                        //     backgroundColor: '#323232',
                        //     width: 'auto',
                        //     text: 'ไม่สามารถเชื่อมต่อกับ Mailbit ได้'
                        // });
                        $('#text_alert').css('color', 'red')
                        $('#text_alert').text('ไม่สามารถเชื่อมต่อกับ Mailbit ได้ : ' + respon[1])
                        $('#txt_head_code').text(respon[0])
                        $('#Modal_alert').modal('show')

                        $('#txt_sms_credit').css('color', 'red')
                        $('#txt_sms_credit').html('Error');
                    }
                    $(".background_loading").css("display", "none");
                })
                .catch(function(error) {
                    console.log(error);
                    Snackbar.show({
                        actionText: 'close',
                        pos: 'top-center',
                        duration: 0,
                        actionTextColor: '#dc3545',
                        backgroundColor: '#323232',
                        width: 'auto',
                        text: error,
                        onClose: function() {
                            location.reload();
                        }
                    });
                });

        }


        function SMS_Check_Sender() {
            $(".background_loading").css("display", "block");
            axios({
                    method: 'POST',
                    url: 'SMS_Sender',
                }).then(function(response) {
                    // console.log(response.data);
                    // respon = response.data.split('#');
                    if (response.data.code == "999999") {
                        txt_sms_sender = '<i class="ti-arrow-down text-danger"></i>' + response.data.data.sms_credit;
                        $('#txt_sms_sender').html(txt_sms_sender);
                        txt_sms_sum_sender = '<i class="ti-arrow-down text-danger"></i>' + response.data.data.sms_sum;
                        $('#txt_sms_sum_sender').html(txt_sms_sum_sender);
                    } else {
                        $('#text_alert').css('color', 'red')
                        $('#text_alert').text('ไม่สามารถเชื่อมต่อกับระบบได้ : ' + response.data.message)
                        $('#txt_head_code').text(response.data.code)
                        $('#Modal_alert').modal('show')

                        $('#txt_sms_sender').css('color', 'red')
                        $('#txt_sms_sender').html('Error');
                    }
                    $(".background_loading").css("display", "none");
                })
                .catch(function(error) {
                    console.log(error);
                    Snackbar.show({
                        actionText: 'close',
                        pos: 'top-center',
                        duration: 15000,
                        actionTextColor: '#dc3545',
                        backgroundColor: '#323232',
                        width: 'auto',
                        text: error,
                        onClose: function() {
                            location.reload();
                        }
                    });
                });
        }


        $('#year_list').on('change',function(){
            SMS_count_type($('#month_list').val(), $('#year_list').val())
        })

        $('#month_list').on('change',function(){
            SMS_count_type($('#month_list').val(), $('#year_list').val())
        })


        function SMS_count_type(month, year) {
            $(".background_loading").css("display", "block");

            var color_round = [{
                    type: 'INVOICE',
                    class: 'round round-danger'
                },
                {
                    type: 'RECEIPT',
                    class: 'round round-success'
                },
                {
                    type: 'TAX',
                    class: 'round round-info'
                }
            ]

            axios({
                    method: 'POST',
                    url: 'SMS_Sender_type',
                    data:{
                        year : year,
                        month : month,
                    }
                }).then(function(response) {
                    // console.log(response.data);
                    html = '';
                    items = response.data.data
                    for (let i = 0; i < items.length; i++) {
                        let class_in = color_round.filter(val => val.type == items[i].type)
                        html += '<tr>' +
                            '<td style="width:50px;"><span class="' + class_in[0].class + '">' + class_in[0].type.substring(0, 3) + '</span></td>' +
                            '<td class="align-middle">' +
                            '<h6>' + items[i].txt_name + '</h6>' +
                            '</td>' +
                            '<td class="align-middle">' + items[i].sum + '</td>' +
                            '</tr>';
                    }
                    $('#list_count_sms_type').html(html);

                    $(".background_loading").css("display", "none");
                })
                .catch(function(error) {
                    console.log(error);
                    Snackbar.show({
                        actionText: 'close',
                        pos: 'top-center',
                        duration: 15000,
                        actionTextColor: '#dc3545',
                        backgroundColor: '#323232',
                        width: 'auto',
                        text: error,
                        // onClose: function() {
                        //     location.reload();
                        // }
                    });
                });
        }


        $('#Modal_alert').on('hidden.bs.modal', function() {
            location.reload();
        })



    });
</script>
