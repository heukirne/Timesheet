<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<title>Login</title>
		<?php echo HTML::style('css/screen.css'); ?>
		<!--[if IE 8]>
			<?php echo HTML::style('css/screen-ie8.css'); ?>
		<![endif]--> 
	</head>
	<body>
		<div id="wrap" class="login">
			<?php echo Form::open('user/login', array('method' => 'post'));  ?>
			<h1 style="font-size:22px;">Reloginho Escala</h1>
			Usuario e senha:
			<?php echo Form::input('tsuser', $post['tsuser'],array('class'=>'tx')); ?><br/>
			<?php echo Form::password('tspass','',array('class'=>'tx')); ?><br/>
			<?php echo Form::submit(NULL, 'ENTRAR',array('class'=>'bt'));  ?>
			<?php if ($errors): ?>
			<p class="message">Houve algum erro no login:</p>
				<ul class="errors">
				<?php foreach ($errors as $message): ?>
					<li><?php echo $message ?></li>
				<?php endforeach ?>
				</ul>
			<?php endif ?>
			<?php echo Form::close();  ?>
		</div>
	</body>
</html>