<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<title>Tarefas Fixas - Administrativo</title>
		<?php echo HTML::style('css/screen-rel.css'); ?>
		<?php echo HTML::script('js/jquery-1.4.2.min.js'); ?>
	</head>
	<body>
		<?php echo Form::open('admin/task', array('method' => 'post', 'target' => '_self'));  ?>
			<div>
				Selecione o Departamento</br>
				<select id="depto" name="depto">
					<?php foreach ($deptos as $id => $name) { ?>
						<option value="<?php echo $name?>" <?php echo ($_POST['depto']==$name?'selected':'')?> ><?php echo $name?></option>
					<?php } ?>
				</select>
			</div>
			<div>
				<br/>
				<?php echo Form::submit(NULL, 'BUSCAR',array('class'=>'bt'));  ?>
			</div>
		<?php echo Form::close();  ?>
		
		<?php if (!empty($_POST['depto'])) { ?>
			<?php echo Form::open('admin/task/1', array('method' => 'post', 'target' => '_self'));  ?>
			<br class="clear"/>
			<br class="clear"/>
			<br class="clear"/>
			<table border="1" id="lista">
				<tr>
					<th width="70">Acoes</th>
					<th width="70">Identificador</th>
					<th width="200">Tarefa Fixa</th>
				</tr>
			<?php foreach($fx as $row) { ?>
				<tr id="<?php echo $row->idfixa?>">
					<td><a id="delTask" href="#rem">remover</a></td>
					<td><input type="text" size="10" maxlength="8" name="item[<?php echo $row->idfixa?>][nick]" value="<?php echo $row->nicktask?>"></td>
					<td><input type="text" size="50" maxlength="30" name="item[<?php echo $row->idfixa?>][task]" value="<?php echo $row->task?>"></td>
				</tr>				
			<?php } ?>
			</table>
			<a id="addTask" href="#add">+ registro</a>
			<br/><br/>
			<?php echo Form::hidden('depto',$_POST['depto']); ?>
			<?php echo Form::submit(NULL, 'SALVAR',array('class'=>'bt'));  ?>
			<?php echo Form::close();  ?>
		<?php } ?>
		<script>
			var contAdd=0;
			var itemP;
			$('#addTask').click(function() {
				contAdd++;
				$("<tr/>").attr("id", "a"+contAdd)
						  .append($("<td/>").append($("<a/>").attr("id", "delTask").attr("href", "#rem").text('remover')))
						  .append($("<td/>").append($("<input/>").attr("name", "item[a"+contAdd+"][nick]").attr("type", "text").attr("size", "10").attr("maxlength", "8")))
						  .append($("<td/>").append($("<input/>").attr("name", "item[a"+contAdd+"][task]").attr("type", "text").attr("size", "50").attr("maxlength", "30")))
						  .appendTo("#lista");
			});
			$('#delTask').live('click', function(event) {
				ask = confirm("Deseja remover tarefa?");
				if (ask) {
					$('#'+$(this).parent().parent()[0].id).hide();
					$(this).parent().siblings()[0].childNodes[0].value = "";
					$(this).parent().siblings()[1].childNodes[0].value = "";
				}
			});
		</script>
	</body>
</html>