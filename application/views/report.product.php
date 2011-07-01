<?php 
	function sumTotal($it) {
		return $it['pitG']+$it['ocG']+$it['piG']+$it['refGer'];
	}
?>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<title>Timesheet - Relatorio Operacional</title>
		<?php echo HTML::style('css/screen-rel.css'); ?>
	</head>
	<body>
		<br/><h2>Relatorio Operacional</h2>
		<form method="POST">
			Ano: <input name="ano" type="text" value="<?=(!empty($_POST['ano'])?$_POST['ano']:'')?>"/><br />
			Mes: <input name="mes" type="text" value="<?=(!empty($_POST['mes'])?$_POST['mes']:'')?>"/><br />
			<input name="enviar" type="submit"/>
		</form>
		<br/>
		<table>
			<tr>
				<th>Cliente</th>
				<th>Total Trab.</th>
				<th>Total Trab. Aprov.</th>
				<th>% Trab. Aprov.</th>
				<th>OCs Emitidos</th>
				<th>OCs Aprov.</th>
				<th>% OCs Aprov.</th>
				<th>Planos Emitidos</th>
				<th>Planos Aprov.</th>
				<th>% Planos Aprov.</th>
				<th>Ref. Est.</th>
				<th>Ref. Cri.</th>
				<th>Ref. Ate.</th>
				<th>Ref. Cli.</th>
				<th>Total Refacao</th>
				<th>% Refacao</th>
			</tr>
			<?php foreach($result as $item) { if (sumTotal($item)) {?>
			<tr>
				<td><?=$item['nome']?></td>
				<td><?=$item['pitG']?></td>
				<td><?=$item['pitA']?></td>
				<td><?=$item['pitG']?number_format($item['pitA']*100/$item['pitG'],2,',','.'):0?></td>
				<td><?=$item['ocG']?></td>
				<td><?=$item['ocA']?></td>
				<td><?=$item['ocG']?number_format($item['ocA']*100/$item['ocG'],2,',','.'):0?></td>
				<td><?=$item['piG']?></td>
				<td><?=$item['piA']?></td>
				<td><?=$item['piG']?number_format($item['piA']*100/$item['piG'],2,',','.'):0?></td>
				<td><?=$item['refEst']?></td>
				<td><?=$item['refCri']?></td>
				<td><?=$item['refAte']?></td>
				<td><?=$item['refCli']?></td>
				<td><?=$item['refGer']?></td>
				<td><?=$item['pitG']?number_format($item['refGer']*100/$item['pitG'],2,',','.'):0?></td>
			</tr>
			<?php } } ?>
		<table>
	</body>
</html>