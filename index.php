<!DOCTYPE html>
<html>
	<head>
		<title>Test</title>
		<style type="text/css">
			#notifications {
				float: right;
			}

			.notification {
				width: 160px;
				height: auto;
				background: #0085CC;
				border-radius: 15px;
				padding: 10px;
				margin-bottom: 10px;
			}

			.callerInfo {
				color: white;
				font-size: 17px;
				text-align: center;
				padding: 3px;
			}

			.btn {
				color: white;
				width: 44%;
				height: 28px;
				font-size: 16px;
				border: none;
				border-radius: 15px;
			}

			.btn.answer {
				margin-right: 5%;
				background-color: #8BFC19;
			}

			.btn.decline {
				margin-left: 5%;
				background-color: #FC1919;
			}
		</style>
	</head>
	<body>
		<div id="notifications"></div>

		<script src="//js.pusher.com/3.1/pusher.min.js"></script>
		<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
		<script>
			Pusher.logToConsole = true

			var pusher = new Pusher('29752e0e135325d56ed5', {
				cluster: 'eu',
				encrypted: true
			});

			var channel = pusher.subscribe('test');

			channel.bind("App\\Events\\IncomingCallEvent", function(data) {
				// console.log(data.data);
				createCallNotification(data.data);
			});

			channel.bind("", function (data) {

			});

			function createCallNotification (data) {
				$('#notifications').append(
					$('<div />').attr({ id: data.MACAddress, 'class': 'notification' }).css({ display: 'none' }).append(
						$('<div />').attr({ 'class' : 'callerInfo' }).text(data.CallingPartyName)
					).append(
						$('<div />').attr({ 'class' : 'callerInfo' }).text(data.CallingPartyNumber)
					).append(
						$('<div />').append(
							$('<button />').attr({ 'class' : 'btn answer', onclick : 'answerCall(' + data.MACAddress + ');' }).text('Answer')
						).append(
							$('<button />').attr({ 'class' : 'btn decline', onclick : 'declineCall(' + data.MACAddress + ');' }).text('Decline')
						)
					)
				);

				$('#' + data.MACAddress).fadeIn(500);
			}

			function answerCall (callId) {
				$('#' + callId).fadeOut(500, function () { this.remove(); });

			}

			function declineCall (callId) {
				$('#' + callId).fadeOut(500, function () { this.remove(); });
			}

			function endCall (callId) {
				$('#' + callId).fadeOut(500, function () { this.remove(); });
			}
		</script>
	</body>
</html>