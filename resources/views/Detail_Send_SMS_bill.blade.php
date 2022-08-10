<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="{{ asset('public/images/icon.jpg') }}" rel="icon" type="image/gif">
    <title>Detail Send SMS</title>

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
    <script src="{{ asset('public/assets/SnackBar-master/dist/snackbar.js') }}"></script>


    {{-- Cookie --}}
    <script src="{{ asset('public/assets/js.cookie.js') }}"></script>

    {{-- CSS --}}
    <link rel="stylesheet" href="{{ asset('public/css/header.css') }}">
    <link rel="stylesheet" href="{{ asset('public/css/Main.css') }}">
    <link rel="stylesheet" href="{{ asset('public/css/Detail_send_sms.css') }}">


    {{-- DASHBOARD --}}

    <!-- Custom CSS -->
    <link href={{ asset('public/monster-html/plugins/chartist/dist/chartist.min.css') }} rel="stylesheet">
    <!-- Custom CSS -->
    <link href={{ asset('public/monster-html/css/style.css') }} rel="stylesheet">


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

    {{-- Date Picker --}}
    <link rel="stylesheet"
        href="{{ asset('public/assets/Zebra_Datepicker-master/dist/css/bootstrap/zebra_datepicker.min.css') }}">
    <script src={{ asset('public/assets/Zebra_Datepicker-master/dist/zebra_datepicker_V2_custom.min.js') }}></script>

    <link href={{ asset('public/css/date-picker.css') }} rel="stylesheet">


    {{-- Pusher Broadcast --}}
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
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
                        <h3 class="page-title mb-0 p-0">Detail Send SMS</h3>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Detail</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container-fluid">
                <div>
                    <p>
                    <h4>ค้นหา</h4>
                    </p>
                    <div class="row g-3 ">
                        <div class="col-sm-auto col-md-6 col-lg-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="SwitchCheck_date">
                                <label class="form-check-label" for="SwitchCheck_date">ช่วงวัน (DATE_Create)</label>
                            </div>
                            <div>
                                <input type="datepicker" class="datepicker form-control form-control-sm"
                                    id="datepicker_start" readonly="readonly" placeholder="วันที่ต้องการค้นหา">
                            </div>
                            <div id="div_datepicker_end" class="mt-2">
                                <input type="datepicker" class="datepicker form-control form-control-sm"
                                    id="datepicker_end" readonly="readonly" placeholder="วันสิ้นสุด">
                            </div>
                        </div>
                        <div class="col-sm-auto col-md-6 col-lg-3">
                            <div class="form-check_custom form-switch_custom">
                                <label>DUE DATE</label>
                            </div>
                            <div>
                                <input type="datepicker" class="datepicker form-control form-control-sm"
                                    id="datepicker_due_date" readonly="readonly" placeholder="รอบ DUE DATE">
                            </div>
                        </div>
                        <div class="col-sm-auto col-md-6 col-lg-3">
                            <div class="text-left">
                                <label>TRANSECTION TYPE</label>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <select class="form-select form-select-sm" aria-label="Default select example"
                                        id="select_type">
                                        <option value="" selected>ทั้งหมด</option>
                                        <option value="INVOICE">INVOICE</option>
                                        {{-- <option value="RECEIPT">RECEIPT</option>
                                        <option value="TAX">TAX</option> --}}
                                        <option value="Other">อื่น</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <input type="email" class="form-control form-control-sm mt-2"
                                        id="input_type_txt_search" aria-describedby="search" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-auto col-md-6 col-lg-3">
                            <div class="text-left">
                                <label> Status Send SMS </label>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <select class="form-select form-select-sm" aria-label="Default select example"
                                        id="status_send">
                                        <option value="" selected>ทั้งหมด</option>
                                        <option value="000">Success</option>
                                        <option value="001">Fail</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        {{-- <div class="col-auto">
                          <span id="passwordHelpInline" class="form-text">
                            Must be 8-20 characters long.
                          </span>
                        </div> --}}
                    </div>

                    <div class="row mt-1">
                        <div class="col-sm-auto col-md-6 col-lg-3">
                            <div class="text-left">
                                <label>Quick Search</label>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <select class="form-select form-select-sm" aria-label="Default select example"
                                        id="select_search_col">
                                        <option value="" data-txt='' selected>ปิด</option>
                                        <option value="SMS_ID" data-txt='SMS_ID'>SMS_ID</option>
                                        <option value="CONTRACT_ID" data-txt='CONTRACT_ID'>CONTRACT_ID</option>
                                        <option value="QUOTATION_ID" data-txt='QUOTATION_ID'>QUOTATION_ID</option>
                                        <option value="APP_ID" data-txt='APP_ID'>APP_ID</option>
                                        <option value="SEND_PHONE" data-txt='Ex. 0801234567'>PHONE</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <input type="email" class="form-control form-control-sm mt-2"
                                        id="input_txt_search_col" aria-describedby="search" disabled>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>


                <div class="mt-4">
                    <div class="row d-flex align-items-center">
                        <div class="col-sm-12 col-md-6 col-lg-6 text-start mt-1">
                            <button type="button" id="btn_search"
                                class="btn btn-success btn-sm me-2 w-25">ค้นหา</button>
                            <button type="button" id="btn_clear_search"
                                class="btn btn-secondary btn-sm ms-2 w-25">ล้าง</button>
                        </div>

                        <div class="col-sm-12 col-md-6 col-lg-6 text-end mt-1">
                            <h6>จำนวนที่แสดง : หน้า</h6>
                            <select class="form-select form-select-sm w-25" id="num_per_page"
                                style="display: inline-block" aria-label="Default select">
                                <option value="10" selected>10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="text-start mt-3 h4 text-dark">
                    ข้อมูลทั้งหมด <span class="text-info" id="sum_count_Data"> </span>
                </div>

                <hr>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="text-center">
                            <tr>
                                <th scope="col">
                                    <label class="form-check-label label-inline-tb">
                                        {{-- <input class="form-check-input me-1" type="checkbox" value="" id="all_id"> --}}
                                        SMS_ID
                                    </label>
                                </th>
                                <th scope="col">DATE_Create</th>
                                <th scope="col">CONTRACT_ID</th>
                                <th scope="col">QUOTATION_ID</th>
                                <th scope="col">APP_ID</th>
                                <th scope="col">PHONE</th>
                                <th scope="col">TRANSECTION_TYPE</th>
                                <th scope="col">TRANSECTION_ID</th>
                                <th scope="col">DUE_DATE</th>
                                <th scope="col">SMS_MESSAGE</th>
                                <th scope="col">SMS_JOB_ID</th>
                                <th scope="col">DateTime_Send</th>
                                <th scope="col">StatusDeliver</th>
                                <th scope="col">Detail_SMS</th>
                            </tr>
                        </thead>
                        <tbody class="text-center" id="tb_data_sms">
                            {{-- @for ($i = 0; $i < 10; $i++)
                                @if ($i == 7 || $i == 4)
                                    <tr>
                                        <td scope="col">00{{ $i }}</td>
                                        <td scope="col" class="text-muted">12/03/2564 12:30:00</td>
                                        <td scope="col">51554{{ $i }}</td>
                                        <td scope="col">14541{{ $i }}</td>
                                        <td scope="col">22245{{ $i }}</td>
                                        <td scope="col">66801234{{ $i }}</td>
                                        <td scope="col">INVOICE</td>
                                        <td scope="col">2215421{{ $i }}</td>
                                        <td scope="col">01/01/2564</td>
                                        <td scope="col"><span class="text-danger"> Fail </span></td>
                                        <td scope="col">6680123456-12454</td>
                                        <td scope="col"><button type="button" id="sms_deatail"
                                                class="btn btn-info btn-sm text-white">Detail</button></td>
                                    </tr>
                                @endif
                            @endfor --}}
                        </tbody>
                    </table>
                </div>

                <hr>

                {{-- <div class="container-fluid">
                    <nav aria-label="Page">
                        <ul class="pagination justify-content-center flex-wrap" id="page_num_old">
                            <li class="page-item">
                                <a class="page-link" href="#" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                            <li class="page-item"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item">
                                <a class="page-link" href="#" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                            
                        </ul>
                    </nav>
                </div> --}}


                <div class="container-fluid">
                    <div class="text-end text-muted fs-6">
                        <span id="txt_viewof"></span>
                    </div>
                    <nav aria-label="Page">
                        <ul class="pagination justify-content-center flex-wrap" id="page_num">
                            {{-- <li class="page-item">
                                <a class="page-link" href="#" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                            <li class="page-item" id="Icon_prev">
                                <a class="page-link" href="#" aria-label="Next">
                                    <span aria-hidden="true">&lsaquo;</span>
                                </a>
                            </li>
                            <li class="page-item ms-1 me-1" style="padding: 0.375rem 0rem;">
                                <span>Page</span>
                            </li>
                            <li class="page-item ms-1 me-1">
                                <input class="input-group-text bg-white text-dark" id="page_input" type="tel"
                                    size="3">
                            </li>
                            <li class="page-item ms-1 me-1" style="padding: 0.375rem 0rem;">
                                <span>of</span> <span id="last_page">0</span>
                            </li>
                            <li class="page-item" id="Icon_next">
                                <a class="page-link" href="#" aria-label="Next">
                                    <span aria-hidden="true">&rsaquo;</span>
                                </a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="#" aria-label="Previous">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li> --}}
                        </ul>
                    </nav>
                </div>


                <div class="modal fade" tabindex="-1" id="modal_detail_sms">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">รายละเอียดการส่ง SMS </h5> <span id="count_send_sms"
                                    class="ms-1 text-muted font-weight-normal"></span>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="container-fluid">
                                    <ul class="nav nav-tabs" role="tablist" id="head_list_detail_sms">
                                        {{-- <li class="nav-item" role="presentation">
                                          <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">Home</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                          <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">Profile</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                          <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" type="button" role="tab" aria-controls="contact" aria-selected="false">Contact</button>
                                        </li> --}}
                                    </ul>
                                    <div class="tab-content" id="myTabContent">
                                        <div class="tab-pane fade show active" id="home" role="tabpanel"
                                            aria-labelledby="home-tab">
                                            <div class="row">
                                                <div class="col-3">
                                                    สถานะ
                                                </div>
                                                <div class="col-9">
                                                    <span class="text-success"> Success </span>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-3">
                                                    วันที่ส่ง
                                                </div>
                                                <div class="col-9">
                                                    02/01/2564 03.00.00
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-3">
                                                    Transection Type
                                                </div>
                                                <div class="col-9">
                                                    INVOICE
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-3">
                                                    Transection ID
                                                </div>
                                                <div class="col-9">
                                                    155424
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-3">
                                                    เบอร์ที่ส่ง
                                                </div>
                                                <div class="col-9">
                                                    6680123456
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-3">
                                                    ข้อความ
                                                </div>
                                                <div class="col-9">
                                                    UFUND จัดส่งใบแจ้งหนี้ สามารถชำระด้วย QR code บน Mobile Banking
                                                    และไม่ต้องนำหลักฐานการโอนแจ้งกลับ กรุณารอใบเสร็จในระบบภายใน 7
                                                    วันทำการ คลิ๊ก
                                                    <a href="https://ufund.comseven.com/Runtime/Runtime/Form/INVView/?INVOICE_ID=xxxx"
                                                        target="blank">https://ufund.comseven.com/Runtime/Runtime/Form/INVView/?INVOICE_ID=xxxx</a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="profile" role="tabpanel"
                                            aria-labelledby="profile-tab">
                                        </div>
                                    </div>

                                </div>
                                {{-- <span>วันที่ส่ง : 02/01/2564 03.00.00</span>
                                <br>
                                <span>Transection Type : Invoice</span>
                                <br>
                                <span>Transection ID : 155424</span>
                                <br>
                                <span>เบอร์ที่ส่ง : 6680123456</span>
                                <br>
                                <span>ข้อความ : UFUND จัดส่งใบแจ้งหนี้ สามารถชำระด้วย QR code บน Mobile Banking และไม่ต้องนำหลักฐานการโอนแจ้งกลับ กรุณารอใบเสร็จในระบบภายใน 7 วันทำการ คลิ๊ก https://ufund.comseven.com/Runtime/Runtime/Form/INVView/?INVOICE_ID=xxxx</span> --}}
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary"
                                    data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
            {{-- <footer class="footer text-center">
                © 2021 SMS Admin by <a href="https://www.wrappixel.com/">wrappixel.com</a>
            </footer> --}}
            @include('Template.footer')

        </div>
    </div>

</body>

</html>

<script>
    $(document).ready(function() {

        var token = document.head.querySelector('meta[name="csrf-token"]');
        window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;

        // constact
        function getParameterByName(name, url) {
            name = name.replace(/[\[\]]/g, '\\$&');
            let regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
                results = regex.exec(url);
            if (!results) return null;
            if (!results[2]) return '';
            return decodeURIComponent(results[2].replace(/\+/g, ' '));
        }

        function numberWithCommas(x) {
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        function format_date(date) {
            let sp_date = date.split('-');
            let new_format_date = sp_date[2] + '-' + sp_date[1] + '-' + sp_date[0]
            return new_format_date;
        }

        function urlify(text) {
            if (text != '' || text != null) {
                let urlRegex = /(https?:\/\/[^\s]+)/g;
                return text.replace(urlRegex, function(url) {
                    return '<a href="' + url + '" target="blank">' + url + '</a>';
                })
            }
            return '';
            // or alternatively
            // return text.replace(urlRegex, '<a href="$1">$1</a>')
        }

        function format_phone(phone) {
            if (phone.length > 9) {
                let format_phone = '0' + phone.substring(2, 13);
                let txt_number = format_phone.substring(0, 3) + '-' + format_phone.substring(3, 12)
                return txt_number;
            } else {
                return phone;
            }
        }


        // function start
        // set_page();
        get_list_data();

        $('input#datepicker_start').Zebra_DatePicker({
            format: 'd F Y',
            days: ['อา', 'จ', 'อ', 'พ', 'พฤ', 'ศ', 'ส'],
            months: ['ม.ค', 'ก.พ', 'มี.ค', 'เม.ย', 'พ.ค', 'มิ.ย', 'ก.ค', 'ส.ค', 'ก.ย', 'ต.ค',
                'พ.ย', 'ธ.ค'
            ],
            show_select_today: 'วันนี้',
            default_position: 'below',
            direction: false,
            pair: $('#datepicker_end'),
            // view: 'years',
            onSelect: function(view, elements) {
                var datepicker = $('#datepicker_end').data('Zebra_DatePicker');
                datepicker.clear_date();
            }
        });

        $('#datepicker_end').Zebra_DatePicker({
            format: 'd M Y',
            days: ['อา', 'จ', 'อ', 'พ', 'พฤ', 'ศ', 'ส'],
            months: ['ม.ค', 'ก.พ', 'มี.ค', 'เม.ย', 'พ.ค', 'มิ.ย', 'ก.ค', 'ส.ค', 'ก.ย', 'ต.ค',
                'พ.ย', 'ธ.ค'
            ],
            show_select_today: 'วันนี้',
            default_position: 'below',
            direction: 1
            // view: 'years',
        });

        $('#datepicker_due_date').Zebra_DatePicker({
            format: 'd M Y',
            days: ['อา', 'จ', 'อ', 'พ', 'พฤ', 'ศ', 'ส'],
            months: ['ม.ค', 'ก.พ', 'มี.ค', 'เม.ย', 'พ.ค', 'มิ.ย', 'ก.ค', 'ส.ค', 'ก.ย', 'ต.ค',
                'พ.ย', 'ธ.ค'
            ],
            show_select_today: 'วันนี้',
            default_position: 'below',
            // direction: 1,
            disabled_dates: ['2-15 1-12 *', '17-31 1-12 *'],
            // view: 'years',
        });


        $('#div_datepicker_end').hide()

        $('#SwitchCheck_date').on('change', function() {
            if ($(this).is(":checked")) {
                $('#datepicker_start').attr('placeholder', 'วันที่เริ่ม')
                $('#div_datepicker_end').show()
            } else {
                $('#datepicker_start').attr('placeholder', 'วันที่ต้องการค้นหา')
                $('#div_datepicker_end').hide()
            }
        })

        $(document).on("click", "#btn_sms_deatail", function(e) {
            e.preventDefault();
            $(".background_loading").css("display", "block");
            let $row = $(this).closest("tr");
            let sms_id = $row.find("td:nth-child(1)").text() == '-' ? null : $row.find(
                "td:nth-child(1)").text();
            let quotation_id = $row.find("td:nth-child(4)").text() == '-' ? null : $row.find(
                "td:nth-child(4)").text();
            let app_id = $row.find("td:nth-child(5)").text() == '-' ? null : $row.find(
                "td:nth-child(5)").text();
            let transection_type = $row.find("td:nth-child(7)").text() == '-' ? null : $row.find(
                "td:nth-child(7)").text();
            let transection_id = $row.find("td:nth-child(8)").text() == '-' ? null : $row.find(
                "td:nth-child(8)").text();
            let sms_job_id = $row.find("td:nth-child(11)").text() == '-' ? null : $row.find(
                "td:nth-child(11)").text();

            axios({
                    method: 'POST',
                    url: 'SMS_Detail',
                    data: {
                        transection_type: transection_type,
                        transection_id: transection_id,
                        sms_id: sms_id,
                    }
                }).then(function(response) {
                    console.log(response.data);
                    // $(".background_loading").css("display", "none");

                    $('#count_send_sms').text(' [ส่ง SMS ทั้งหมด ' + response.data.data.length +
                        ' ครั้ง]')

                    html_head_list = ''
                    response.data.data.forEach((item, index) => {
                        html_head_list += `
                        <li class="nav-item" role="presentation">
                            <button class="nav-link ${index == 0 ? 'active' : ''}" id="SMS_${index + 1}-tab" data-bs-toggle="tab" data-bs-target="#SMS_${index + 1}" type="button" role="tab" aria-controls="SMS_${index + 1}" aria-selected="true">ส่ง SMS ครั้งที่ ${index + 1}</button>
                        </li>
                        `
                    })
                    $('#head_list_detail_sms').html(html_head_list)

                    html_detail_sms = '';
                    response.data.data.forEach((item, index) => {

                        text_sms = (item.SMS_TEXT_MESSAGE == null ? '' : item
                            .SMS_TEXT_MESSAGE)
                        // if (item.Detail_SMS) {
                        //     item.Detail_SMS.forEach((sub_item, sub_index) => {
                        //         text_sms += sub_item.SMS_Text
                        //     })
                        // }

                        html_detail_sms += `
                            <div class="tab-pane fade show ${index == 0 ? 'active' : ''}" id="SMS_${index + 1}" role="tabpanel" aria-labelledby="home-tab">
                                <div class="row mt-3">
                                    <div class="col-3">
                                        สถานะ
                                    </div>
                                    <div class="col-9">
                                        <span class="${item.SMS_Status_Delivery == '#DELIVRD' ? 'text-success' : 'text-danger'}"> ${item.SMS_Status_Delivery  == null ? '-' : item.SMS_Status_Delivery} </span>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-3">
                                        วันที่ส่ง
                                    </div>
                                    <div class="col-9">
                                        ${format_date(item.SEND_DATE)}  ${item.SEND_TIME.split('.')[0]}
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-3">
                                        Transection Type
                                    </div>
                                    <div class="col-9">
                                        ${item.TRANSECTION_TYPE == null ? '-' : item.TRANSECTION_TYPE}
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-3">
                                        Transection ID
                                    </div>
                                    <div class="col-9">
                                        ${item.TRANSECTION_ID == null ? '-' : item.TRANSECTION_ID}
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-3">
                                        เบอร์ที่ส่ง
                                    </div>
                                    <div class="col-9">
                                        ${format_phone(item.SEND_Phone)}
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-3">
                                        ข้อความ
                                    </div>
                                    <div class="col-9">
                                        ${urlify(text_sms)}
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-3">
                                        Credit ที่ใช้
                                    </div>
                                    <div class="col-9">
                                        ${(item.SMS_CREDIT_USED)}
                                    </div>
                                </div>
                            </div>
                        `
                    })
                    $('#myTabContent').html(html_detail_sms)

                    $(".background_loading").css("display", "none");
                    $('#modal_detail_sms').modal('show')
                })
                .catch(function(error) {
                    // $(".background_loading").css("display", "none");
                    Snackbar.show({
                        actionText: 'close',
                        pos: 'top-center',
                        actionTextColor: '#dc3545',
                        backgroundColor: '#323232',
                        width: 'auto',
                        text: 'SYSTEM ERROR :' + error,
                        onClose: function() {
                            // location.reload();
                        }
                    });
                    // $(".loading").css("display", "none");
                    console.log(error);
                });
        });


        $('#select_type').on('change', function() {
            if ($(this).val() != '' && $(this).val() != 'Other') {
                $("#input_type_txt_search").removeAttr('disabled');
                $("#input_type_txt_search").attr('placeholder', $(this).val() + ' ID')
            } else {
                $("#input_type_txt_search").val('');
                $("#input_type_txt_search").attr('disabled', 'disabled')
                $("#input_type_txt_search").removeAttr('placeholder');
            }
        })

        $('#select_search_col').on('change', function() {
            if ($(this).val() != '') {
                $("#input_txt_search_col").removeAttr('disabled');
                $("#input_txt_search_col").attr('placeholder', $("#select_search_col option:selected")
                    .attr('data-txt'))
            } else {
                $("#input_txt_search_col").val('');
                $("#input_txt_search_col").attr('disabled', 'disabled')
                $("#input_txt_search_col").removeAttr('placeholder');
            }
        })


        // กดเลือกหน้า pagination
        $('body').on('click', '.pagination a', function(e) {
            e.preventDefault();
            let url = $(this).attr('href');
            let num_page = $('#num_per_page').find(":selected").val();
            get_form_search(url, num_page);
        });

        function changPage(page, iurl , last_page) {
            if (page != parseInt(page, 10)){
                page = 1;
            }else if(page > last_page){
                page = last_page
            }
            let url = iurl + '?page=' + page;
            let num_page = $('#num_per_page').find(":selected").val();
            get_form_search(url, num_page);
        }


        $('#num_per_page').on('change', function() {
            let num_page = $(this).val();
            let url = '?page=1';
            get_form_search(url, num_page);
        });



        $('#btn_clear_search').on('click', function() {
            let url = '?page=1';
            let num_page = 10;
            $('#select_type').val('');
            $('#input_type_txt_search').val('');
            $("#input_type_txt_search").removeAttr('placeholder');
            $("#input_type_txt_search").attr('disabled', 'disabled')
            $('#select_search_col').val('');
            $('#input_txt_search_col').val('');
            $("#input_txt_search_col").removeAttr('placeholder');
            $("#input_txt_search_col").attr('disabled', 'disabled')

            $('#status_send').val('');
            $('#num_per_page').val('10');
            // $('#datepicker_start').clear_date();
            // $('#datepicker_end').clear_date();
            $('#datepicker_start').data('Zebra_DatePicker').clear_date();
            $('#datepicker_end').data('Zebra_DatePicker').clear_date();
            $('#datepicker_due_date').data('Zebra_DatePicker').clear_date();

            $('#SwitchCheck_date').prop('checked', false);
            $('#datepicker_start').attr('placeholder', 'วันที่ต้องการค้นหา')
            $('#div_datepicker_end').hide();

            get_list_data(url, num_page, date_first = null, date_last = null, type = null, type_search =
                null, status = null, quick_select = null, quick_text = null)
        })


        $('#btn_search').on('click', function() {
            let url = '?page=1';
            let num_page = $('#num_per_page').find(":selected").val();
            get_form_search(url, num_page);
        })

        function get_form_search(url_get, num_page_get) {
            let num_page = num_page_get;
            let url = url_get;
            // get_list_data(url, num_page);
            let date_first = null;
            let date_last = null;
            let due_date = null;
            let type = $('#select_type').val() == '' ? null : $('#select_type').val();
            let type_search = null;
            let status = $('#status_send').val() == '' ? null : $('#status_send').val();
            let quick_select = $('#select_search_col').val() == '' ? null : $('#select_search_col').val();
            let quick_text = null
            if ($('#datepicker_start').val() != '') {
                let months = ['ม.ค', 'ก.พ', 'มี.ค', 'เม.ย', 'พ.ค', 'มิ.ย', 'ก.ค', 'ส.ค', 'ก.ย', 'ต.ค',
                    'พ.ย', 'ธ.ค'
                ];
                let input_date_f = $('#datepicker_start').val().split(' ')
                let _month = months.indexOf(input_date_f[1]) + 1
                date_first = input_date_f[2] + '-' + _month + '-' + input_date_f[0]
            }

            if ($('#SwitchCheck_date').is(":checked")) {
                if ($('#datepicker_end').val() != '') {
                    let months = ['ม.ค', 'ก.พ', 'มี.ค', 'เม.ย', 'พ.ค', 'มิ.ย', 'ก.ค', 'ส.ค', 'ก.ย',
                        'ต.ค', 'พ.ย', 'ธ.ค'
                    ];
                    let input_date_l = $('#datepicker_end').val().split(' ')
                    let _month = months.indexOf(input_date_l[1]) + 1
                    date_last = input_date_l[2] + '-' + _month + '-' + input_date_l[0]
                }
            }

            if ($('#datepicker_due_date').val() != '') {
                let months = ['ม.ค', 'ก.พ', 'มี.ค', 'เม.ย', 'พ.ค', 'มิ.ย', 'ก.ค', 'ส.ค', 'ก.ย', 'ต.ค',
                    'พ.ย', 'ธ.ค'
                ];
                let input_date_f = $('#datepicker_due_date').val().split(' ')
                let _month = months.indexOf(input_date_f[1]) + 1
                due_date = input_date_f[2] + '-' + _month + '-' + input_date_f[0]
            }

            if (type != null) {
                type_search = $('#input_type_txt_search').val() == '' ? null : $(
                    '#input_type_txt_search').val();
            }

            if (quick_select != null) {
                quick_text = $('#input_txt_search_col').val() == '' ? null : $(
                    '#input_txt_search_col').val();
            }

            // console.log(type_search)
            get_list_data(url, num_page, date_first, date_last, type, type_search, status, due_date,
                quick_select, quick_text)
        }


        function get_list_data(url, num_page, date_first, date_last, type, type_search, status, due_date,
            quick_select, quick_text) {
            if (typeof(num_page) == "undefined" && num_page == null) {
                num_page = 10;
            }

            const page = getParameterByName('page', url)
            $(".background_loading").css("display", "block");
            axios({
                    method: 'post',
                    url: 'list_sms',
                    params: {
                        page: page,
                    },
                    data: {
                        num_page: num_page,
                        date_first: typeof(date_first) != "undefined" || date_first != null ? date_first :
                            null,
                        date_last: typeof(date_last) != "undefined" || date_last != null ? date_last : null,
                        type: typeof(type) != "undefined" || type != null ? type : null,
                        type_search: typeof(type_search) != "undefined" || type_search != null ?
                            type_search : null,
                        status: typeof(status) != "undefined" || status != null ? status : null,
                        due_date: typeof(due_date) != "undefined" || due_date != null ? due_date : null,
                        quick_select: typeof(quick_select) != "undefined" || quick_select != null ?
                            quick_select : null,
                        quick_text: typeof(quick_text) != "undefined" || quick_text != null ? quick_text :
                            null,
                    }
                }).then(function(response) {
                    // console.log(response.data);
                    // console.log(response.data.data.links)
                    links = response.data.data.links;
                    collect = response.data;
                    prev_page_url = response.data.data.prev_page_url
                    next_page_url = response.data.data.next_page_url
                    // paging
                    $('#sum_count_Data').text(numberWithCommas(response.data.data.total))

                    // $('#Icon_prev').html(
                    //     `<a class="page-link" href="${ prev_page_url ? prev_page_url : '#'}" aria-label="Prev">
                    //         <span aria-hidden="true">&lsaquo;</span>
                    //     </a>`
                    // )
                    // $('#Icon_next').html(
                    //     `<a class="page-link" href="${next_page_url ? next_page_url : '#'}" aria-label="Next">
                    //         <span aria-hidden="true">&rsaquo;</span>
                    //     </a>`
                    // )

                    html_pageOld = '';
                    html_page = '';
                    html_page = `
                            <li class="page-item ${response.data.data.prev_page_url == null ? 'disabled' : ''}">
                                <a class="page-link" href="${response.data.data.first_page_url}" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                            <li class="page-item ${response.data.data.prev_page_url == null ? 'disabled' : ''}" id="Icon_prev">
                                <a class="page-link" href="${response.data.data.prev_page_url}" aria-label="Next">
                                    <span aria-hidden="true">&lsaquo;</span>
                                </a>
                            </li>
                            <li class="page-item ms-1 me-1" style="padding: 0.375rem 0rem;">
                                <span>Page</span>
                            </li>
                            <li class="page-item ms-1 me-1">
                                <input class="input-group-text bg-white text-dark" id="page_input" type="text" size="3" value="${response.data.data.current_page}">
                            </li>
                            <li class="page-item ms-1 me-1" style="padding: 0.375rem 0rem;">
                                <span>of</span> <span id="last_page"> ${response.data.data.last_page} </span>
                            </li>
                            <li class="page-item ${response.data.data.next_page_url == null ? 'disabled' : ''}">
                                <a class="page-link" href="${response.data.data.next_page_url}" aria-label="Next">
                                    <span aria-hidden="true">&rsaquo;</span>
                                </a>
                            </li>
                            <li class="page-item ${response.data.data.next_page_url == null ? 'disabled' : ''}">
                                <a class="page-link" href="${response.data.data.last_page_url}" aria-label="Previous">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>`;

                    // links.forEach((links, index) => {
                    //     if (links.label == "&laquo; Previous" || links.label == "Next &raquo;") {

                    //         typeIcon = links.label.includes("Previous") == true ? 'Previous' :
                    //             'Next';
                    //         Icon = links.label.includes("&laquo;") == true ? '&laquo;' : '&raquo;';

                    //         html_pageOld += `
                    //          <li class="page-item ${links.url == null ? 'disabled' : ''} ">
                    //             <a class="page-link" href="${links.url}" aria-label="${typeIcon}">
                    //                 <span aria-hidden="true">${Icon}</span>
                    //                 <span class="sr-only">${typeIcon}</span>
                    //             </a>
                    //         </li>
                    //         `
                    //     } else {
                    //         html_pageOld += `
                    //         <li class="page-item ${links.active == false ? '' : 'active'}"><a class="page-link" href="${links.url}"> ${links.label} </a></li>
                    //         `
                    //     }

                    // })
                    // $('#page_num_old').html(html_pageOld)

                    $('#txt_viewof').text(`View ${response.data.data.from} - ${response.data.data.to} of ${response.data.data.total}`)

                    $('#page_num').html(html_page)

                    document.querySelector('#page_input').addEventListener('keypress', function(e) {
                        if (e.key === 'Enter') {
                            // code for enter
                            changPage($('#page_input').val(), response.data.data.path, response.data.data.last_page)
                        }
                    });

                    html = '';
                    item = response.data.data.data;
                    item.forEach((item, index) => {
                        html += `
                        <tr>
                            <td scope="col">${item.SMS_ID != null ? item.SMS_ID : null}</td>
                            <td scope="col">${format_date(item.DATE)}</td>
                            <td scope="col">${item.CONTRACT_ID  == null ? '-' : item.CONTRACT_ID}</td>
                            <td scope="col">${item.QUOTATION_ID == null ? '-' : item.QUOTATION_ID}</td>
                            <td scope="col">${item.APP_ID  == null ? '-' : item.APP_ID}</td>
                            <td scope="col">${format_phone(item.SEND_Phone)}</td>
                            <td scope="col">${item.TRANSECTION_TYPE == null ? '-' : item.TRANSECTION_TYPE } </td>
                            <td scope="col">${item.TRANSECTION_ID  == null ? '-' : item.TRANSECTION_ID}</td>
                            <td scope="col">${item.DUE_DATE  == null ? '-' : format_date(item.DUE_DATE)}</td>
                            <td scope="col"><span class="${item.SMS_RESPONSE_CODE == '000' ? 'text-success' : 'text-danger'}"> ${item.SMS_RESPONSE_MESSAGE} </span></td>
                            <td scope="col">${item.SMS_RESPONSE_JOB_ID == null ? '-' : item.SMS_RESPONSE_JOB_ID}</td>
                            <td scope="col">${format_date(item.SEND_DATE)}  ${item.SEND_TIME.split('.')[0]}</td>
                            <td scope="col"><span class="${item.SMS_Status_Delivery == '#DELIVRD' ? 'text-success' : 'text-danger'}"> ${item.SMS_Status_Delivery  == null ? '-' : item.SMS_Status_Delivery} </span></td>
                            <td scope="col"><button type="button" id="btn_sms_deatail" class="btn btn-info btn-sm text-white">Detail</button></td>
                        </tr>
                        `
                    })

                    $('#tb_data_sms').html(html)

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


        // end script
    })
</script>
