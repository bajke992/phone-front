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

			.actionBtns {
				text-align: center;
			}

			.btn {
				color: white;
				width: 50%;
				height: 28px;
				font-size: 16px;
				border: none;
				border-radius: 15px;
			}

			.btn.answer {
				background-color: #8BFC19;
			}

			.btn.decline {
				background-color: #FC1919;
			}

			.btn.endCall {
				background-color: #FC1919;
			}

			.dialpad{
				float: left;
				width: 400px;
			}

			.dialpad #number {
			    width: 79%;

			    height: 38px;
			    font-size: 24px;

			    border-top: none;
			    border-left: none;
			    border-right: none;
			}

			.dialpad .call {
				width: 19%;
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

			var callData = null;
			var ext = "<?php echo $_GET['ext']; ?>";

			var pusher = new Pusher('29752e0e135325d56ed5', {
				cluster: 'eu',
				encrypted: true
			});

			var channel = pusher.subscribe(ext);

			channel.bind("App\\Events\\IncomingCallEvent", function(data) {
				callData = data;
				console.log(data);
				createCallNotification(data.data);
			});

			// channel.bind("App\\Events\\ActiveCallEvent", function(data) {
			// 	console.log(data);
			// 	createActiveCallNotification(data.data);
			// });

			$(document).ready(function () {
				performAction('phone/active-call?ext=' + ext, function (data) {
					callData = data;
					if(data.hasOwnProperty('OutgoingCallEvent')) {
						createActiveOutboundCallNotification(data.OutgoingCallEvent);
					} else if (data.hasOwnProperty('IncomingCallEvent')) {
						createActiveCallNotification(data.IncomingCallEvent);
					}
				});
			});

			function createCallNotification(data) {
				$('#notifications').append(
					$('<div />').attr({ id: data.MACAddress, 'class': 'notification' }).css({ display: 'none' }).append(
						$('<div />').attr({ 'class' : 'callerInfo' }).text(data.CallingPartyName)
					).append(
						$('<div />').attr({ 'class' : 'callerInfo' }).text(data.CallingPartyNumber)
					).append(
						$('<div />').attr({ 'class' : 'actionBtns'}).append(
							$('<button />').attr({ 'class' : 'btn answer', onclick : 'answerCall("' + data.MACAddress + '");' }).text('Answer')
						)
					)
				);

				$('#' + data.MACAddress).fadeIn(500);
			}

			function createActiveCallNotification(data) {
				$('#notifications').append(
					$('<div />').attr({ id: data.MACAddress, 'class': 'notification' }).css({ display: 'none' }).append(
						$('<div />').attr({ 'class' : 'callerInfo' }).text(data.CallingPartyName)
					).append(
						$('<div />').attr({ 'class' : 'callerInfo' }).text(data.CallingPartyNumber)
					).append(
						$('<div />').attr({ 'class' : 'actionBtns'}).append(
							$('<button />').attr({ 'class' : 'btn endCall', onclick : 'endCall("' + data.MACAddress + '");' }).text('End Call')
						)
					)
				);

				$('#' + data.MACAddress).fadeIn(500);
			}

			function createActiveOutboundCallNotification(data) {
				$('#notifications').append(
					$('<div />').attr({ id: data.MACAddress, 'class': 'notification' }).css({ display: 'none' }).append(
						$('<div />').attr({ 'class' : 'callerInfo' }).text(data.CalledPartyName)
					).append(
						$('<div />').attr({ 'class' : 'callerInfo' }).text(data.CalledPartyNumber)
					).append(
						$('<div />').attr({ 'class' : 'actionBtns'}).append(
							$('<button />').attr({ 'class' : 'btn endCall', onclick : 'endCall("' + data.MACAddress + '");' }).text('End Call')
						)
					)
				);

				$('#' + data.MACAddress).fadeIn(500);
			}

			function answerCall (callId) {
				// $('#' + callId).fadeOut(500, function () { this.remove(); });
				$('#' + callId + ' .actionBtns').children().remove()
				$('#' + callId + ' .actionBtns').append(
					$('<button />').attr({ 'class' : 'btn endCall', onclick : 'endCall("' + callId + '");' }).text('End Call')
				);

				performAction('phone/answer-call?ext=' + ext);
			}

			function endCall (callId) {
				$('#' + callId).fadeOut(500, function () { this.remove(); });
				performAction('phone/end-call?ext=' + ext);
			}

			function doClick(item){
				$('#number').val($('#number').val() + item);
			}

			function placeCall () {
				var number = $('#number').val();
				performAction('phone/place-call?number=' + number + '&ext=' + ext, function () {
					var data = {
						CalledPartyName : 'N/A',
						CalledPartyNumber : number,
						MACAddress: Math.floor(Math.random()*9000) + 1000
					}

					createActiveOutboundCallNotification(data);
				});
			}

			function performAction(action, callback) {
				callback = (typeof callback === 'undefined') ? function(data){ console.log(data); } : callback;
				$.ajax({
					type: "GET",
					url: 'http://159.203.102.189:3000/' + action,
					success: callback
				});
			}
		</script>
	</body>
</html>