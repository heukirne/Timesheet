<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<title>TimeSheet</title>
		<?php echo HTML::style('css/screen.css'); ?>
		<!--[if IE 8]>
			<?php echo HTML::style('css/screen-ie8.css'); ?>
		<![endif]--> 
		<?php echo HTML::script('js/jquery-1.4.2.min.js'); ?>
		<?php echo HTML::script('js/jquery.countdown.min.js'); ?>
	</head>
	<body>
		<div id="wrap">
			<div id="header">
				<span><?php echo $depto ?></span>
			</div>
			<span id="user-name"><?php echo $nome?></span>
			<?php if (substr($depto,0,8) == "COORDENA") { ?>
				(<a href="<?php echo URL::site('report/index'); ?>" class="peq" target="relatorio" >Relat</a>&nbsp;|
				<a href="<?php echo URL::site('admin/task'); ?>" class="peq" target="relatorio" >Fixas</a>&nbsp;|
				<a href="<?php echo URL::site('admin/timesheet'); ?>" class="peq" target="relatorio" >Timesheet</a>)
			<?php } ?>
			<?php if (strpos(" ".$depto,"PG")!=0) { ?>
				<a href="<?php echo URL::site('timesheet/alarm'); ?>" class="peq" >Despertador</a>
			<?php } ?>
			<?php if ($user == 328) { ?>
				<a href="<?php echo URL::site('report/product'); ?>" class="peq" target="relatorio" >Produtiv.</a>
			<?php } ?>
			<br/>
			<select id="clients">
				<option value="">Selecione um cliente</option>
				<?php foreach ($clients as $id => $name) { ?>
					<option value="<?php echo $id?>"><?php echo $name?></option>
				<?php } ?>
			</select>
			<input type="button" value="T" id="change_cli" class="bt_mini" title="Todos os clientes"/>
			<span id="status"></span>
			<br/>
			<select id="pits"></select> <input type="button" value="ADICIONAR" id="add" class="bt"/>
			<br/><br/>
			<div id="wrap-work">
				<table id="work"></table>
			</div>
			<span id="saveTime"></span><br/>
			<input type="button" value="SALVAR" id="save" class="bt"/>
			<input type="button" value="ENVIAR" id="send" class="bt"/>
			<input type="button" value="SAIR" id="logout" class="bt_out"/>
			<br/><br/>
			<div id="task-now">
				<hr color="#2890d2"/>
				<span>&nbsp;</span>
			</div>
			<div id="timer-now">
				00:00:00
			</div>
		</div>
		<script>
		$("#change_cli").toggle(function () { 
			$("#clients").empty();
			$("<option/>").attr("value", "").text("Selecione um cliente").appendTo("#clients");
			<?php foreach ($clients_all as $id => $name) { ?>
				$("<option/>").attr("value", "<?php echo $id?>").text("<?php echo $name?>").appendTo("#clients");
			<?php } ?>
			$("#change_cli").attr("value", "P").attr("title", "Clientes da pauta");
		},function () { 
			$("#clients").empty();
			$("<option/>").attr("value", "").text("Selecione um cliente").appendTo("#clients");
			<?php foreach ($clients as $id => $name) { ?>
				$("<option/>").attr("value", "<?php echo $id?>").text("<?php echo $name?>").appendTo("#clients");
			<?php } ?>
			$("#change_cli").attr("value", "T").attr("title", "Todos os clientes");
		});
		
		
			$("#logout").click(function () { window.location = "<?php echo URL::site('user/logout'); ?>" });
			/*######## Funções de Tarefas ########*/
			$("#clients").change(function () {
				var id = $("#clients option:selected").val();
				if (id) {
					$("#pits").attr('disabled','disabled');
					$("#add").attr('disabled','disabled');
					$("#status").text('...');
					$.getJSON("<?php echo URL::site('timesheet/pit'); ?>/"+id,
					function(data){
						$("#pits").empty();
						if (data) {
							$("<option/>").attr("value", "").text("Selecione um PIT").appendTo("#pits");
							<?php foreach ($pitDefault as $id => $nome) { ?>
								$("<option/>").attr("value", id+"_<?php echo $id?>").text("<?php echo $nome?>").appendTo("#pits");
							<?php } ?>
							$.each(data, function(i,item){
								$("<option/>").attr("value", i).text(item).appendTo("#pits");
							});
						} else {
							$("<option/>").attr("value", "").text("Selecione um PIT").appendTo("#pits");
							<?php foreach ($pitDefault as $id => $nome) { ?>
								$("<option/>").attr("value", id+"_<?php echo $id?>").text("<?php echo $nome?>").appendTo("#pits");
							<?php } ?>
						}
						$("#pits").attr('disabled','');
						$("#add").attr('disabled','');
						$("#status").text('');
					});
				} else {
					$("#pits").attr('disabled','disabled');
					$("#add").attr('disabled','disabled');
				}
		    })		
			
			$('#add').click(function() {
				if ($("#pits option:selected").val()) {
					add($("#pits option:selected").val(), $("#clients option:selected").text(),0);
				}
			});
			
			function exist(pit) {
				var ret = true;
				if (pit) {
					$('#work tr').each(function(i, item) {
						if (item.id == pit) { ret = false; }
					});
					return ret;
				} else return false;
			}
			function add(pit,client,time) {
				if (exist(pit)) {
					$("<tr/>")
					.attr("id", pit)
					.append($("<td/>").attr("class", "td-ie8-hack").html(client.substr(0,18)+":<br>"+pit))
					.append($("<td/>").attr("class", "timer").text(time))
					.append($("<td/>").attr("class", "bt_td")
								.append($("<input/>").attr("type", "button").attr("id", "play"))
								.append($("<input/>").attr("type", "button").attr("id", "del"))
							)
					.hide().prependTo("#work");
					var dTime = new Date();
					dTime.setTime(dTime.getTime()-time*1000);
					$('#'+pit+' .timer').countdown({since: dTime, compact: true, format: 'HMS', description: ''}).countdown('pause');
					$('#'+pit).addClass('pause');
					$('#'+pit).slideDown("fast");
				}
			}	
			
			/*######## Funções de Fluxo ########*/
			function hackSafari(text) {
				if (navigator.vendor) 
					if (navigator.vendor.indexOf('Apple') > -1) {
						$('#task-now span').text('');
						setTimeout("$('#task-now span').html('&nbsp;"+text+"')",50);
					}
			}
			
			var timerInt;
			function updateTimer(id) {
				$('#timer-now').text($('#'+id+' .timer').text());
			}
			$('#play').live('click', function() {
				var idObj = $(this).parent().parent()[0].id;
				if ($('#'+idObj).hasClass('pause')) {
					$('#work tr').each(function(i, item) { 
						$('#'+item.id+' .timer').countdown('pause'); 
						$('#'+item.id).removeClass('play').addClass('pause');						
					});
					$('#'+idObj+' .timer').countdown('resume'); 
					$('#'+idObj).removeClass('pause').addClass('play');
					$('#task-now span').text($('#'+idObj).children().first().text());
					hackSafari($('#'+idObj).children().first().text());
					clearInterval(timerInt);
					timerInt = setInterval("updateTimer('"+idObj+"')",50);
			    } else {
					$('#'+idObj+' .timer').countdown('pause'); 
					$('#'+idObj).removeClass('play').addClass('pause');
					clearInterval(timerInt);
				}
			});		
			
			/*######## Funções de Salvar em Banco ########*/
			
			$('#del').live('click', function(event) {
				ask = confirm("Deseja remover tarefa?");
				if (ask) {
					$.post("<?php echo URL::site('timesheet/delete'); ?>", 
						{
							pit: $(this).parent().parent()[0].id,
							user: <?php echo $user?>
						},
						function(data){
							if (data.success) {
								$('#'+event.target.parentNode.parentNode.id).slideUp("fast",function () {$('#'+this.id).remove();});
							} else {
								alert(data.msg);
							}
						}, "json"
					);
				}
			});	
			
			setInterval("$('#save').click()",5*60*1000); //salva de 5 em 5 minutos
			$('#save').click(function() {
				$('#work tr').each(function(i, item) { 
					$('#saveTime').text('salvando...');
					$.post("<?php echo URL::site('timesheet/save'); ?>", 
						{
							pit: item.id,
							time: $('#'+item.id+' .timer').text(),
							user: <?php echo $user?>
						},
						function(data){
							if (data.success) {
								var today=new Date();
								var h=today.getHours();
								var m=$.strPad(today.getMinutes(), 2);
								$('#saveTime').text("salvo as "+h+"h"+m);
							} else {
								$('#saveTime').text('Erro:'+data.msg);
							}
						}, "json"
					);
				});
				/* código de controle de sucesso */
			});
			
			$('#send').click(function() {
				if ($('#work .play').length) { 
					alert("Pause todas as tarefas antes de enviar para o TimeSheet do PubliManager.");
				} else {
					ask = confirm("Deseja salvar as tarefas no TimeSheet do PubliManager?\nIsso ira remove-las deste aplicativo.");
					if (ask) {
						$('#save').click();
						$.get("<?php echo URL::site('timesheet/send'); ?>", 
							function(data) {
								if (data.success) {
									alert(data.msg);
									$('#work tr').each(function(i, item) { 
										$('#'+item.id).remove();
									});
								}
							},"json"
						);
					}
				}				
			});
			
			$(window).load(function () {
			<?php foreach ($tasks as $task) { ?>
				add('<?php echo $task->pit_id?>','<?php echo $task->cliente?>',<?php echo $task->tempo?>);
			<?php } ?>
				$("#pits").attr('disabled','disabled');
				$("#add").attr('disabled','disabled');
			});
			
			//window.onbeforeunload = function (){ return "Deseja sair?"; }
		</script>
		<?php echo HTML::script('js/functions.js'); ?>
	</body>
</html>