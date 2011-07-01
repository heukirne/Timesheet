<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<title>Timesheet - Relatório</title>
		<?php echo HTML::style('css/screen-rel.css'); ?>
		<?php echo HTML::script('js/jquery-1.4.2.min.js'); ?>
	</head>
	<body>
		<?php echo Form::open('report/do', array('method' => 'post', 'target' => 'report'));  ?>
			<div>
				Clientes<br>
				<select id="client" multiple="true" class="multi" name="client[]">
					<option value="x">Qualquer cliente</option>
					<?php foreach ($clients as $id => $name) { ?>
						<option value="<?php echo $name?>"><?php echo $name?></option>
					<?php } ?>
				</select> 
			</div>
			<div>
				ou Usuario</br>
				<select id="user" multiple="true" class="multi" name="user[]">
					<option value="x">Qualquer usuario</option>
					<?php foreach ($users as $id => $name) { ?>
						<option value="<?php echo $name?>"><?php echo $name?></option>
					<?php } ?>
				</select>
			</div>
			<div>
				Periodo<br>
				<?php echo Form::input('dateIni', date('d/m/Y'),array('size'=>'10')); ?> a
				<?php echo Form::input('dateEnd', date('d/m/Y'),array('size'=>'10')); ?>
			</div>
			<div>
				Campos<br>
				<select multiple="true" class="multi" name="fields[]" id="fields">
					<option value="p.CAMPANHA">Campanha</option>
					<option value="p.PRODUTO">Produto</option>
					<option value="p.TITULO">Titulo</option>
					<option value="p.ESPECIE">Especie</option>
					<option value="p.FORMATO">Formato</option>
					<option value="p.VEICULO">Veiculo</option>
				</select>
			</div>
			<div>
				<br/>
				<?php echo Form::submit(NULL, 'GERAR',array('class'=>'bt'));  ?>
				<br/><br/><br/>
				<input class="bt" type="button" value="IMPRIMIR" onclick="window.parent.report.print();"/>
			</div>
		<?php echo Form::close();  ?>
		<script>
		/*
			$('#user').change(function () { 
				if(!$("#user option:selected").val()) $('#client').attr('disabled',''); 
				else $('#client').attr('disabled','disabled');
				if ($("#client option:selected").val() == 'x') $('#fields').attr('disabled','disabled'); 
				else $('#fields').attr('disabled',''); 
			});
			$('#client').change(function () { 
				if(!$("#client option:selected").val()) $('#user').attr('disabled',''); 
				else $('#user').attr('disabled','disabled'); 
				if ($("#client option:selected").val() == 'x') $('#fields').attr('disabled','disabled'); 
				else $('#fields').attr('disabled',''); 
			});			
		*/
		</script>
	</body>
</html>