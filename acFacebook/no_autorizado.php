<h1>No autorizado</h1>

<form action="#" id="form_fb">
	<div>
		<label for="nombre">Nombre: </label>
		<input type="text" name="nombre" id="nombre"/>
	</div>
	<div>
		<label for="cedula">Cédula: </label>
		<input type="text" name="cedula" id="cedula"/>
	</div>
	<div>
		<label for="correo">Correo: </label>
		<input type="text" name="correo" id="correo"/>
	</div>
	<p id="mensaje"></p>
	<a href="#" id="autorizar">Autorizar y enviar</a>
</form>

<script>
$('#autorizar').click(function(){
	<?php echo $app->facebook->btn_login("fb_cancel()","fb_cancel_permisions()","fb_success()")?>
	return false;
});

function fb_cancel(){
	$('#mensaje').html('Cancelo autorización');
}
function fb_cancel_permisions(){
	$('#mensaje').html('Cancelo permisos');
}
function fb_success(){
	$('#form_fb').submit();
}

$('#form_fb').validate({
	rules:{
       nombre: {
           	required: true
        },
        correo: {
           	required: true,
        	minlength: 2,
 			email: true
        },
 	   cedula: {
          	required: true,
			number: true
       }
	},
    errorPlacement:function(error,element){
    	$('#mensaje').html('* Todos los campos son necesarios.');
    	return true;
    },
    submitHandler: function(form){
    	$.post('ajax.php',$(form).serialize()+"&session=<?php echo(session_id());?>").done(function(){
    		top.location.href = "<?php echo $config->fb_url?>";
		});
    }
});
</script>