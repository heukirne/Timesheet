<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<title>Timesheet - Administrativo</title>
		<?php echo HTML::style('css/screen-rel.css'); ?>
		<?php echo HTML::script('js/jquery-1.4.2.min.js'); ?>
	</head>
	<body>
		<?php echo Form::open('admin/timesheet', array('method' => 'post', 'target' => '_self'));  ?>
			<div>
				Dia<br>
				<?php echo Form::input('date', $date,array('size'=>'10')); ?>
			</div>
			<div>
				Selecione o Usuario</br>
				<select id="user" name="user">
					<?php foreach ($users as $id => $name) { ?>
						<option value="<?php echo $name?>" <?php echo ($_POST['user']==$name?'selected':'')?> ><?php echo $name?></option>
					<?php } ?>
				</select>
			</div>
			<div>
				<br/>
				<?php echo Form::submit(NULL, 'BUSCAR',array('class'=>'bt'));  ?>
			</div>
		<?php echo Form::close();  ?>
		
		<?php if (!empty($_POST['user'])) { ?>
			<?php echo Form::open('admin/timesheet/1', array('method' => 'post', 'target' => '_self'));  ?>
			<br class="clear"/>
			<br class="clear"/>
			<br class="clear"/>
			<table border="1">
				<tr>
					<th>Nome</th>
					<th>Pit</th>
					<th>Data</th>
					<th>Horas</th>
				</tr>
			<?php foreach($ts as $row) { ?>
				<tr>
					<td><?php echo $row->USUARIO?></td>
					<td><?php echo $row->PIT?></td>
					<td><?php echo substr($row->DATA,0,10)?></td>
					<td><input type="text" name="item[<?php echo $row->RECNO?>]" value="<?php echo $row->HORAS?>"></td>
				</tr>				
			<?php } ?>
			</table>
			<br/>
			<?php echo Form::hidden('user',$_POST['user']); ?>
			<?php echo Form::hidden('date',$date); ?>
			<?php echo Form::submit(NULL, 'SALVAR',array('class'=>'bt'));  ?>
			<?php echo Form::close();  ?>
		<?php } ?>
	</body>
</html>