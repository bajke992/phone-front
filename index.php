<!DOCTYPE html>
<html>
	<head>
		<title>Test</title>
		<style type="text/css">
			#notifications {
				float: right;
			}

			.notification {
				width: 220px;
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

			.dialpad{
				float: left;
				width: 400px;
			}

			.dialpad #number {
			    width: 79%;
			    /*margin: 0px;*/
			    /*padding: 0px;*/
			    /*float: left;*/

			    height: 38px;
			    font-size: 24px;

			    border-top: none;
			    border-left: none;
			    border-right: none;
			}

			.dialpad .call {
				width: 19%;
			    /*margin: 0px;*/
			    /*padding: 0px;*/
			    /*float: right;*/
			    height: 40px;
			    font-size: 20px;

			    border: none;
			    margin-left: -2px;
			}

			.dialpad .row {
				width: 100%;
				float: left;
				text-align: center;
			}

			.dialpad .row button {
				width: 50px;
				height: 50px;
				border:none;
				border-radius: 50%;
				font-size: 20px;
				margin: 10px 20px;
			}
		</style>
	</head>
	<body>
		<div class="dialpad">
			<div>
				<input type="text" id="number">
				<button class="call" onclick="placeCall();">Call</button>
			</div>
			<div class="row">
				<button onclick="doClick(1);">1</button>
				<button onclick="doClick(2);">2</button>
				<button onclick="doClick(3);">3</button>
			</div>
			<div class="row">
				<button onclick="doClick(4);">4</button>
				<button onclick="doClick(5);">5</button>
				<button onclick="doClick(6);">6</button>
			</div>
			<div class="row">
				<button onclick="doClick(7);">7</button>
				<button onclick="doClick(8);">8</button>
				<button onclick="doClick(9);">9</button>
			</div>
			<div class="row">
				<button onclick="doClick('*');">*</button>
				<button onclick="doClick(0);">0</button>
				<button onclick="doClick('#');">#</button>
			</div>
			
		</div>
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
							$('<button />').attr({ 'class' : 'btn answer', onclick : 'answerCall("' + data.MACAddress + '");' }).text('Answer')
						).append(
							$('<button />').attr({ 'class' : 'btn decline', onclick : 'declineCall("' + data.MACAddress + '");' }).text('Decline')
						)
					)
				);

				$('#' + data.MACAddress).fadeIn(500);
			}

			function answerCall (callId) {
				$('#' + callId).fadeOut(500, function () { this.remove(); });

				performAction('answer-call');
			}

			function declineCall (callId) {
				$('#' + callId).fadeOut(500, function () { this.remove(); });

				performAction('decline-call')
			}

			function endCall (callId) {
				$('#' + callId).fadeOut(500, function () { this.remove(); });
			}

			function doClick(item){
				$('#number').val($('#number').val() + item);
			}

			function placeCall () {
				performAction('place-call?number=' + $('#number').val());
			}

			function performAction(action) {
				$.ajax({
					type: "GET",
					url: 'http://159.203.102.189:3000/' + action,
					success: function (data) {
						console.log(data);
					}
				});
			}
		</script>
	</body>
</html>