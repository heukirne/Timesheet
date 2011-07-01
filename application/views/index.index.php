<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<title>Login</title>
		<?php echo HTML::style('css/screen.css'); ?>
		<!--[if IE 8]>
			<?php echo HTML::style('css/screen-ie8.css'); ?>
		<![endif]--> 
		<?php echo HTML::script('js/jquery-1.4.2.min.js'); ?>
	</head>
	<body onload="">
		<div id="wrap" class="index">
			<br/><br/><br/><br/>
			<a href="#">Reloginho</a>
			<br/><br/>
			<a href="<?php echo URL::site('timesheet/index'); ?>" class="peq">Reloginho na mesma janela</a>
		</div>
	</body>
	<script>
	$("a").first().click(function () {
			var ret = window.open('<?php echo URL::site('timesheet/index'); ?>','timesheet','height=480,width=320,resizable=no,status=no,titlebar=no,toolbar=no,location=no,directories=no,channelmode=no');
		});
	</script>
</html>