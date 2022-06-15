<!DOCTYPE html>
<head>
  <title>Pusher Test</title>
  <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
  <script>

    // Enable pusher logging - don't include this in production
    Pusher.logToConsole = true;

    var pusher = new Pusher('54952d201ebbc6d5bcab', {
      cluster: 'ap1'
    });

    var channel = pusher.subscribe('realtimeData-channel');
    channel.bind('realtimeData-event', function(data) {
      // alert(JSON.stringify(data));
      console.log(data.data)
    });
    
  </script>
</head>
<body>
  <h1>Pusher Test</h1>
  <p>
    Try publishing an event to channel <code>my-channel</code>
    with event name <code>my-event</code>.
  </p>
</body>