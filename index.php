<?php
	ini_set('display_errors', '1');  //ACTIVA REPORTES DE ERRORES
	error_reporting(1);  //DESACTIVA REPORTES DE ERRORES
	session_start();

	$table   = '';
	$objData = '';

	$var = ($_GET['imprimeXml']=='true')? $_GET: $_POST;

	if(isset($var['jsonData']) || $_GET['imprimeXml']=='true'){

		$jsonData = ($_GET['imprimeXml']=='true')? $_SESSION['jsonData']: $var['jsonData'];
		$_SESSION['jsonData'] = $jsonData;

		$objData  = json_decode($jsonData,true);

		$url     = $var['url'];
		$metodo  = $var['metodo'];

		require_once('class/nuSoap/nusoap.php');
		$objSoap = new nusoap_client($url,true);
		$errorWs = $objSoap->getError();

	    if ($errorWs) { $response = $errorWs; }
	    $responseWs = $objSoap->call($metodo, $objData);

	    if ($objSoap->fault) { $response = $responseWs; }
	    else {
	        $errorWs = $objSoap->getError();
	        if ($errorWs) { $response = "<h2>Error</h2>".$objSoap; }
	        else {

	        	$response = $responseWs;
	        	if(gettype($responseWs[$metodo.'Result']['diffgram']['NewDataSet']['Table1']) == 'array'){
            		$response = $responseWs[$metodo.'Result']['diffgram']['NewDataSet']['Table1'];
            	}
            	//========================// RESPONCE DATA EMPY //=======================//
            	//***********************************************************************//
		        else if (gettype($responseWs[$metodo.'Result']['diffgram']['NewDataSet']['Table1']) == 'string') {
		        	echo $responseWs[$metodo.'Result']['diffgram']['NewDataSet']['Table1'];
            		$response = "// NO REPORTA DATOS EL WEB SERVICE";
            	}

            	//======================// DATOS EN TABLA "MATRIZ" //======================//
            	//************************************************************************//
		        if(gettype($response) == 'array' && gettype($response[0]) == 'array'){

					$contHead = 0;
					$head     = '';
					$body     = '';
		        	foreach ($response as $fila=> $arrayFila) {

		        		if($contHead==0){ $head .= '<td>&nbsp;</td>'; }
		        		$body .= '<tr>
		        					<td>'.$fila.'</td>';

		        		foreach ($arrayFila as $col => $value) {
		        			if($contHead==0){ $head .= '<td>'.$col.'</td>'; }
		        			$body .= '<td>'.$value.'</td>';
		        		}

		        		$body .= '</tr>';
		        		$contHead++;
		        	}

		        	$table .= '<table class="tablaXml">
		        					<thead style="background-color:#000; font-size13px; border-color:#000;">
		        						<tr>'.$head.'</tr>
		        					</thead>
		        					<tbody>
		        						'.$body.'
		        					</tbody>

		        				</table>';
		        }

		        //======================// DATOS EN TABLA "ARRAY" //======================//
            	//***********************************************************************//
		        else if(gettype($response) == 'array'){

					$contHead = 0;
					$head     = '';
					$body     = '';
		        	foreach ($response as $fila=> $value) {
		        		$head .= '<td>'.$fila.'</td>';
		        		$body .= '<td>'.$value.'</td>';
		        	}

		        	$table .= '<table class="tablaXml">
		        					<thead style="background-color:#000; font-size13px;">
		        						<tr>'.$head.'</tr>
		        					</thead>
		        					<tbody>
		        						<tr>'.$body.'</tr>
		        					</tbody>
		        				</table>';
		        }


	        }
	    }
	}
	if($var['imprimeXml']=='true'){
		header('Content-Type: text/xml');

		$ini = '<definitions xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/" xmlns:tns="urn:" xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/" xmlns="http://schemas.xmlsoap.org/wsdl/" targetNamespace="urn:">';
		$fin = '</definitions>';

		echo $documentXml = $ini.$objSoap->document.$fin;
		// echo $documentXml = $ini.$objSoap->document.$objSoap->response.$objSoap->responseData.$fin;

		exit;
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN""http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<title>Cliente Web Service</title>
	<link rel="stylesheet" href="js/highlight/styles/androidstudio.css">
	<!-- <link rel="stylesheet" href="js/highlight/styles/default.css"> -->
	<script src="js/highlight/highlight.pack.js"></script>

	<style type="text/css">

		input, textarea{
			padding : 5px;
			border  : none;
			color   : #2900FF;
		}

		html,body, body > div{
			height           : 100%;
			overflow         : hidden;
			background-color : #222;
		}

		body{
			overflow    : hidden;
			margin      : 0px;
			padding     : 0px;
			font-size   : 13px;
			font-family : arial;
		}

		.campo, .campoTop{
			overflow : hidden;
			margin   : 5px;
		}

		.campo > div, .campo input, .campoTop > div, .campoTop input{ float:left; }

		.contenedor_form{
			width    : 355px;
			padding  : 50px 10px;
			overflow : auto;
			margin   : auto;
			height   : 100%;
		}

		*{
			box-sizing : border-box;
			color      : #fff;
		}

		input{ width: 100%; }

		pre, code{
			margin  : 0px;
			padding : 0px;
			height  : 100%;
		}

		.boton{
			float       : right;
			padding-top : 5px;
			text-align  : center;
			border      : 1px solid #fff;
		}

		.boton:hover{
			border-color : red !important;
			color        : red;
			cursor       : default;
		}

		.boton:active{
			border : 2px solid red !important;
			color  : red;
			cursor : default;
		}

		.tablaXml{
			margin          : auto !important;
			border          : 1px solid #fff;
			border-collapse : collapse;
		}

		thead{
			background-color : #000;
			text-align       : center;
			border           : 1px solid #000;
		}

		.tablaXml thead td{
			padding   : 5px;
			font-size : 13px !important;
		}

		.tablaXml tbody td{
			padding    : 10px;
			font-size  : 11px;
			background : #fff;
			color      : #000 !important;
		}

		#content_ws .title{
			float         : left;
			padding       : 10px;
			float         : left;
			width         : 100%;
			text-align    : center;
			border-bottom : 1px solid #FFF;
		}

		#responseTabla{ height : calc(100% - 50px); }
		#responseArray{ height : calc(100% - 20px); }

		#responseTabla, #responseArray{
			padding  : 50px 0;
			margin   : 0;
			overflow : hidden;
			width    : 100%;
		}

		#responseTabla > *, #responseArray > *{
			margin: 0px;
		}

		#responseTabla > * > *, #responseArray > * > *{
			padding: 0px 20px;
		}

		.btnFila{
			width            : 25px;
			height           : 25px;
			font-size        : 20px;
			font-weight      : bold;
			padding-top      : 2px;
			text-align       : center;
			background-color : #999;
		}

	</style>
</head>
<body>
	<div id="content_ws">
		<div class="title">CLIENTE WEB SERVICE</div>
		<div style="float:left; width:370px; overflow:auto;">
			<div class="title">Formulario Cliente</div>
			<div style="padding:7px; text-align:center; float:left; overflow:hidden; margin-left:-60px; font-size:8px;">
				<div style="width:30px; height:20px; margin-right:5px;" onclick="modalXml(true)" class="boton" title="XML Proveedor">XML</div>
			</div>
			<div class="contenedor_form">
				<form action="index.php" method="post" id="form_service" name="form_service">
					<div id="camposData">
						<div class="campoTop">
							<div style="width:70px;">Url</div>
							<div style="width:255px;"><textarea id="url" name="url" style="width:100%; height:80px;"><?php echo $url; ?></textarea></div>
						</div>
						<div class="campoTop">
							<div style="width:70px;">Metodo</div>
							<div style="width:255px;"><input type="text" id="metodo" name="metodo" value="<?php echo $metodo; ?>"/></div>
						</div>

						<?php
							$cont  = 1;
							if($objData != ''){

								foreach ($objData as $campo => $valor) {
									echo'<div class="campo" id="'.$cont.'">
											<div style="width:70px;">Campo '.$cont.'</div>
											<div style="width:100px;"><input type="text" id="var_'.$cont.'" placeholder="Nombre" value="'.$campo.'"/></div>
											<div style="width:130px;"><input type="text" id="val_'.$cont.'" placeholder="Valor" value="'.$valor.'"/></div>
											<div class="btnFila" id="menos_'.$cont.'" onclick="btnMenos('.$cont.')">-</div>
										</div>';
									$cont++;
								}
							}
							echo'<div class="campo" id="'.$cont.'">
									<div style="width:70px;">Campo '.$cont.'</div>
									<div style="width:100px;"><input type="text" id="var_'.$cont.'" placeholder="Nombre" onchange="eventBtnMas(this,'.$cont.');"/></div>
									<div style="width:130px;"><input type="text" id="val_'.$cont.'" placeholder="Valor" onchange="eventBtnMas(this,'.$cont.');"/></div>
									<div class="btnFila" id="mas_'.$cont.'" onclick="btnMas('.$cont.')">+</div>
									<div class="btnFila" id="menos_'.$cont.'" style="display:none;" onclick="btnMenos('.$cont.')">-</div>
								</div>';
						?>
					</div>
					<input type="hidden" id="jsonData" name="jsonData"/>
					<input type="button" style="font-weight:bold;" onclick="validacion()" value="Enviar Datos"/>

				</form>
			</div>
		</div>
		<div style="float:left; width:calc(100% - 370px); height:100%; border-left: 1px solid #FFF;">
			<div class="title">Response</div>
			<div style="padding:7px; text-align:center; float:left; overflow:hidden; margin-left:-130px; font-size:8px;">
				<div style="width:30px; height:20px; margin-right:5px;" onclick="codePhp(true)" class="boton" title="Response PHP">PHP</div>
				<div style="width:30px; height:20px; margin-right:5px;" onclick="responseXml(true)" class="boton" title="Response XML">XML</div>
				<div id="btnTableXml" style="width:35px; height:20px; margin-right:5px;" onclick="tableXml()" class="boton">TABLA</div>
			</div>
			<div id="responseTabla" style="display:none; background-color: #282B2E;"><?php echo $table; ?></div>
			<div id="responseArray" style="display:block; background-color: #282B2E;">
				<?php
	            	echo '<pre><code id="result">';
			        print_r($response);
			        echo '<code></pre>';
				?>
			</div>
		</div>
		<div id="divXml" style="display:none; position:absolute; width:100%; height:100%; background-color:#222;">
			<div style="overflow:hidden;">
				<div style="margin:10px; padding-top:2px; width:20px; height:20px;" onclick="responseXml(false)" class="boton">X</div>
				<div style="float:left; margin:10px; padding:5px; color:red; font-size:15px;">Response XML</div>
			</div>
			<div style="overflow:auto; width:100%; height:calc(100% - 47px);">

				<?php

					$ini = '<definitions xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/" xmlns:tns="urn:" xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/" xmlns="http://schemas.xmlsoap.org/wsdl/" targetNamespace="urn:">';
					$fin = '</definitions>';
					$xml = $ini.$objSoap->document.$fin;

					require_once('class/php/beauty_xml.php');
					$objXml      = new beauty_xml();
					$documentXml = $objXml->format_xml($xml);

					echo '<pre><code id="xmlResponse">'.htmlspecialchars($documentXml).'<code></pre>';
				?>
			</div>
		</div>
		<div id="divPhp" style="display:none; position:absolute; width:100%; height:100%; background-color:#222;">
			<div style="overflow:hidden;">
				<div style="margin:10px; padding-top:2px; width:20px; height:20px;" onclick="codePhp(false)" class="boton">X</div>
				<div style="float:left; margin:10px; padding:5px; color:red; font-size:15px;">Code Php</div>
			</div>
			<div style="overflow:auto; width:100%; height:calc(100% - 47px);">
				<?php
					echo '<pre><code id="codePhp">';
					require_once('text.php');
					echo '<code></pre>';
				?>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		var styleCodePhp  = false
		var styleResponse = false
		,	varTableXml   = false;

		hljs.highlightBlock(document.querySelector('#result'));

		// var tagCode     = document.querySelectorAll('pre code');
		// [].forEach.call(tagCode,function(tag,indice,documento){
		// 	hljs.highlightBlock(tag);
		// 	console.log(tag);
		// });

		function tableXml(){

			if(varTableXml){
				varTableXml = false;
				document.querySelector("#btnTableXml").innerHTML  = 'TABLA';
				document.querySelector("#responseTabla").style.display = 'none';
				document.querySelector("#responseArray").style.display = 'block';
			}
			else{
				varTableXml = true;
				document.querySelector("#btnTableXml").innerHTML  = 'ARRAY';
				document.querySelector("#responseTabla").style.display = 'block';
				document.querySelector("#responseArray").style.display = 'none';
			}
		}

		function validacion(){
			var url         = document.getElementById("url").value
			,	objData     = {}
			,	arrayCampos = document.querySelectorAll(".campo");

			[].forEach.call(arrayCampos,function(input,indice,documento){

				inputVar = document.querySelector("#var_"+input.id);
				inputVal = document.querySelector("#val_"+input.id);
				if(inputVar.value!='' && inputVal.value!=''){
					objData[inputVar.value] = inputVal.value;

				}
			});
			document.querySelector("#jsonData").value=JSON.stringify(objData);

			if( url == null || url.length == 0 || /^\s+$/.test(url) ) {
				alert('Campo url obligatorio');
			  	return false;
			}

			document.form_service.submit();
		}

		function modalXml(){
			window.open("<?php echo $url; ?>");
		}

		function responseXml(estado){

			if(!styleResponse){
				styleResponse = true;
				hljs.highlightBlock(document.querySelector('#xmlResponse'));
			}

			if(estado){ document.getElementById("divXml").style.display='block'; }
			else{ document.getElementById("divXml").style.display='none'; }
		}

		function codePhp(estado){

			if(!styleCodePhp){
				styleCodePhp = true;
				hljs.highlightBlock(document.querySelector('#codePhp'));
			}

			if(estado){ document.getElementById("divPhp").style.display='block'; }
			else{ document.getElementById("divPhp").style.display='none'; }
		}

		function btnMas(cont){
			var newCont = cont+1;
			var div     = document.createElement('div');
			var content = '<div style="width:70px;">Campo '+newCont+'</div>'
							+'<div style="width:100px;"><input type="text" id="var_'+newCont+'" placeholder="Nombre" onchange="eventBtnMas(this,'+cont+');"/></div>'
							+'<div style="width:130px;"><input type="text" id="val_'+newCont+'" placeholder="Valor" onchange="eventBtnMas(this,'+cont+');"/></div>'
							+'<div class="btnFila" id="mas_'+newCont+'" onclick="btnMas('+newCont+')">+</div>'
							+'<div class="btnFila" id="menos_'+newCont+'" style="display:none;" onclick="btnMenos('+newCont+')">-</div>';

			div.setAttribute('id',newCont);
            div.setAttribute('class','campo');
            div.innerHTML = content;
            document.getElementById('camposData').appendChild(div);

			document.querySelector("#mas_"+cont).style.display   = "none";
			document.querySelector("#menos_"+cont).style.display = "block";
		}

		function btnMenos(cont){
			document.getElementById(cont).parentNode.removeChild(document.getElementById(cont));
		}

		function eventBtnMas(input,cont){
			var metodo = document.getElementById('var_'+cont).value
			,	valor  = document.getElementById('val_'+cont).value
			,	style  = document.getElementById('menos_'+cont).style.display;

			if(metodo != '' && style=='none' && valor!=''){ btnMas(cont) }
		}

	</script>


</body>
</html>