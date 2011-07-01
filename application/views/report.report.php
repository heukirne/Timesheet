<html>
<?php 
	function num2time($num) {
		$hour = floor($num);
		$minute = round(($num - floor($num))*60);
		return $hour . ':' . str_pad($minute,2,"0",STR_PAD_LEFT);
	}
?>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<title>Timesheet - Relatório</title>
		<?php echo HTML::style('css/screen-rel.css'); ?>
	</head>
	<body>
		<br/><h2>Relatorio de
				<?php if (!empty($post['user'])) { ?>
				Funcionario:
				<?php } else { ?>
				Cliente:
				<?php } ?>	
				<?php if (!empty($post['dateIni'])) { ?> Periodo <?php echo $post['dateIni']; ?> a <?php echo $post['dateEnd']; } ?>
		</h2><br/>
		<table>
			<tr>
				<th width="200">
					<?php if (!empty($post['user'])) { ?>
					Funcionario/Cliente/Pit
					<?php } else { ?>
					Cliente/Pit/Funcionario
					<?php } ?>
				</th>
				<th width="50" class="s1">Horas</th>
				<th width="100" class="s2">Valor</th>
				<?php if (!empty($post['fields'])) 
						foreach($post['fields'] as $field) { ?>
							<th><?php echo $field; ?></th>
				<?php } ?>
			</tr>
			<?php foreach ($result as $iten1 => $sec) { if (is_array($sec)) { ?>
				<tr class="c1">
					<td><?php echo $iten1; ?></td> 
					<td class="s1"><?php echo num2time($sec[0]); ?></td>
					<td class="s2">R$ <?php echo number_format($sec[1], 2, ',', '.'); ?></td>
				</tr>
				<?php foreach ($sec as $iten2 => $fields) { if (is_array($fields)) { ?>
					<tr class="c2">
						<td class="f1"><?php echo $iten2; ?></td> 
						<td class="s1"><?php echo num2time($fields[0]); ?></td>
						<td class="s2">R$ <?php echo number_format($fields[1], 2, ',', '.'); ?></td>
					</tr>
					<?php foreach ($fields as $field => $itens) { if (is_array($itens)) { ?>
						<tr class="c3"><td class="f2"><?php echo $field; ?></td>
							<?php foreach ($itens as $k => $iten) { ?>
								<td class="p<?php echo $k; ?>">&nbsp;<?php 
									if ($k==0) echo num2time($iten); 
									else echo $iten;
								?></td>
							<?php } ?>
						</tr>
					<?php } } ?>
				<?php } } ?>
			<?php } } ?>
<?php if (!empty($post['user'])) { 
	$suma1 = $suma2 = 0;
	foreach ($result as $iten1 => $sec) {
		$suma1 += $sec[0];
		$suma2 += $sec[1];

	}
?>
			<tr>
				<td>&nbsp;</td>
				<td class="s1"><?php echo num2time($suma1);?></td>
				<td class="s2"><?php echo number_format($suma2, 2, ',', '.');?></td>
			</tr>
<?php } ?>
		<table>
	</body>
</html>