<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- JQuery --}}
    <script src="{{ asset('public/assets/jquery-3.5.1.min.js') }}"></script>

    {{-- axios --}}
    <script src="{{ asset('public/assets/axios.min.js') }}"></script>

</head>

<body>



</body>

</html>

<script>
    // setTimeout(() => { disp_prompt(); }, 3000); 
    $(".background_loading").css("display", "block");
    $(document).ready(function() {
        disp_prompt();
    });


    function disp_prompt() {
        var USER = prompt("Please enter your Username", "")
        var PW = prompt("Please enter your Password", "")
        if (USER != null && USER != "") {
            if (PW != null && PW != "") {
                send_submit(USER, PW)
            }
        }
    }

    function send_submit(USER, PW) {

        const queryString = window.location.search;
        console.log(queryString);

        axios({
            method: 'POST',
            url: 'send_SMS_Invoice' + queryString,
            data: {
                USER: USER,
                PW: PW,
            }
        }).then(function(response) {

            // console.log(response);
            if (response.data.Code) {
                var str = JSON.stringify(response.data, undefined, 4);

                output(str);

            } else {
                location.reload();
            }
        }).catch(function(error) {
            console.log(error);
        });

        $(".background_loading").css("display", "none");
    }

    function output(inp) {
        document.body.appendChild(document.createElement('pre')).innerHTML = inp;
    }

    function syntaxHighlight(json) {
        if (typeof json != 'string') {
            json = JSON.stringify(json, undefined, 2);
        }
        json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
        return json.replace(
            /("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g,
            function(match) {
                var cls = 'number';
                if (/^"/.test(match)) {
                    if (/:$/.test(match)) {
                        cls = 'key';
                    } else {
                        cls = 'string';
                    }
                } else if (/true|false/.test(match)) {
                    cls = 'boolean';
                } else if (/null/.test(match)) {
                    cls = 'null';
                }
                return '<span class="' + cls + '">' + match + '</span>';
            });
    }
</script>
