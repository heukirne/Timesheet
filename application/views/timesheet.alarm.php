<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<title>Despertador</title>
		<?php echo HTML::style('css/screen.css'); ?>
		<!--[if IE 8]>
			<?php echo HTML::style('css/screen-ie8.css'); ?>
		<![endif]--> 
		<?php echo HTML::script('js/jquery-1.4.2.min.js'); ?>
	</head>
	<body onload="">
		<div id="wrap" class="index">
			<a href="<?php echo URL::site('timesheet/index'); ?>" class="peq">voltar para TimeSheet</a>
			<br /> <br />
			<a href="#">Despertador</a>
			<br /> <br /> 
			<div id="alarm">
			<?php foreach ($pps as $pit => $validade) {
					echo "$pit: $validade dias<br>";
			} ?>
			</div>
		</div>
	</body>
</html>