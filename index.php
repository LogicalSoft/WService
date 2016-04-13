<?php

	/**
	* @author Jhon Erick Marroquin Cardenas <jhon3rick@gmail.com> <twitter @jhon3rick>
	* @license MIT
	*/

	ini_set('display_errors', '1');
	error_reporting(1);
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

		require_once('php/class/nuSoap/nusoap.php');
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
	<link rel="stylesheet" href="css/style.css">
	<script src="js/highlight/highlight.pack.js"></script>
</head>
<body>
	<div id="content_ws">
		<div class="title">CLIENTE WEB SERVICE</div>

		<!-- BODY WEBSERVICE -->
		<div class="bodyWs">

			<!-- ASIDE FORM -->
			<div class="aside">

				<!-- DIV TITLE -->
				<div class="divTitle">
					<div class="title">Formulario Cliente</div>
					<div onclick="modalXml(true)" class="btnClose" title="XML Cliente"><input type="button" value="XML"/></div>
				</div>
				<div class="contenedor_form">
					<form action="index.php" method="post" id="form_service" name="form_service"  onsubmit="return false;">
						<div id="camposData">
							<div class="filaVar">
								<div>Url</div>
								<div style="width:255px;"><textarea id="url" name="url" style="width:100%; height:80px;"><?php echo $url; ?></textarea></div>
							</div>
							<div class="filaVar">
								<div>Method</div>
								<div style="width:255px;"><input type="text" id="metodo" name="metodo" value="<?php echo $metodo; ?>"/></div>
							</div>

							<?php
								$cont  = 1;
								if($objData != ''){

									foreach ($objData as $campo => $valor) {
										echo'<div class="filaVar" id="'.$cont.'">
												<div>Field '.$cont.'</div>
												<div><input type="text" id="var_'.$cont.'" placeholder="Nombre" value="'.$campo.'"/></div>
												<div><input type="text" id="val_'.$cont.'" placeholder="Valor" value="'.$valor.'"/></div>
												<div class="btnFila" id="menos_'.$cont.'" onclick="btnMenos('.$cont.')">-</div>
											</div>';
										$cont++;
									}
								}
								echo'<div class="filaVar" id="'.$cont.'">
										<div>Field '.$cont.'</div>
										<div><input type="text" id="var_'.$cont.'" placeholder="Nombre" onchange="eventBtnMas(this,'.$cont.');"/></div>
										<div><input type="text" id="val_'.$cont.'" placeholder="Valor" onchange="eventBtnMas(this,'.$cont.');"/></div>
										<div class="btnFila" id="mas_'.$cont.'" onclick="btnMas('.$cont.')">+</div>
										<div class="btnFila" id="menos_'.$cont.'" style="display:none;" onclick="btnMenos('.$cont.')">-</div>
									</div>';
							?>
						</div>
						<input type="hidden" id="jsonData" name="jsonData"/>
						<input type="button" onclick="validate()" value="Enviar Datos"/>
					</form>
				</div>
			</div>

			<!-- DIV RESPONSE -->
			<div class="response">

				<!-- DIV TITLE -->
				<div class="divTitle">
					<div class="title">Response</div>
					<div onclick="codePhp(true)" class="btnClose" title="Response PHP"><input type="button" value="PHP"/></div>
					<div onclick="responseXml(true)" class="btnClose" title="Response XML"><input type="button" value="XML"/></div>
					<div id="btnTableXml" onclick="tableXml()" class="btnClose"><input type="button" value="TABLA"/></div>
				</div>
				<div id="responseTabla" style="display:none;">
					<div class="parentTable"><?php echo $table; ?></div>
					<div class="actionTable">
						<a target="_blank" download="wService.xls" title="Download Excel">
							<div></div>
						</a>
						<div data-edit="false" onclick="edit(this)"></div>
					</div>
				</div>
				<div id="responseArray" style="display:block;">
					<?php
						echo '<pre><code id="result">';
						print_r($response);
						echo '<code></pre>';
					?>
				</div>
			</div>
			<div id="divXml">
				<div style="overflow:hidden;">
					<div onclick="responseXml(false)" class="btnClose" style="float:right; margin-top:0;"><input type="button" value="X"/></div>
					<div style="float:left; padding:3px; color:red; font-size:15px;">Response XML</div>
				</div>
				<div style="overflow:auto; width:100%; height:calc(100% - 59px);">

					<?php

						$ini = '<definitions xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/" xmlns:tns="urn:" xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/" xmlns="http://schemas.xmlsoap.org/wsdl/" targetNamespace="urn:">';
						$fin = '</definitions>';
						$xml = $ini.$objSoap->document.$fin;

						require_once('php/class/beauty_xml/beauty_xml.php');
						$objXml      = new beauty_xml();
						$documentXml = $objXml->format_xml($xml);

						echo '<pre><code id="xmlResponse">'.htmlspecialchars($documentXml).'<code></pre>';
					?>
				</div>
			</div>
			<div id="divPhp">
				<div style="overflow:hidden;">
					<div onclick="codePhp(false)" class="btnClose" style="float:right; margin-top:0;"><input type="button" value="X"/></div>
					<div style="float:left; padding:3px; color:red; font-size:15px;">Code Php</div>
				</div>
				<div style="overflow:auto; width:100%; height:calc(100% - 59px);">
					<?php
						echo '<pre><code id="codePhp">';
						require_once('php/txt.php');
						echo '<code></pre>';
					?>
				</div>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		var styleCodePhp  = false
		var styleResponse = false
		,	varTableXml   = false;

		hljs.highlightBlock(document.querySelector('#result'));


		var tableXml = function(){

			if(varTableXml){
				varTableXml = false;
				document.querySelector("#btnTableXml input").value  = 'TABLA';
				document.querySelector("#responseTabla").style.display = 'none';
				document.querySelector("#responseArray").style.display = 'block';
			}
			else{
				varTableXml = true;
				document.querySelector("#btnTableXml input").value  = 'ARRAY';
				document.querySelector("#responseTabla").style.display = 'block';
				document.querySelector("#responseArray").style.display = 'none';
			}
		}

		var validate = function(){
			var url         = document.getElementById("url").value
			,	objData     = {}
			,	arrayCampos = document.querySelectorAll(".filaVar");

			[].forEach.call(arrayCampos,function(input,indice,documento){

				if(input.id == ''){ return; }

				inputVar = document.querySelector("#var_"+input.id);
				inputVal = document.querySelector("#val_"+input.id);

				if(inputVar.value!='' && inputVal.value!=''){ objData[inputVar.value] = inputVal.value; }
			});

			document.querySelector("#jsonData").value=JSON.stringify(objData);

			if( url == null || url.length == 0 || /^\s+$/.test(url) ) {
				alert('Field url obligatorio');
				return false;
			}

			document.form_service.submit();
		}

		var modalXml = function(){ window.open("<?php echo $url; ?>"); }
		var responseXml = function(estado){

			if(!styleResponse){
				styleResponse = true;
				hljs.highlightBlock(document.querySelector('#xmlResponse'));
			}

			if(estado){ document.getElementById("divXml").style.display='block'; }
			else{ document.getElementById("divXml").style.display='none'; }
		}

		var codePhp = function(estado){

			if(!styleCodePhp){
				styleCodePhp = true;
				hljs.highlightBlock(document.querySelector('#codePhp'));
			}

			if(estado){ document.getElementById("divPhp").style.display='block'; }
			else{ document.getElementById("divPhp").style.display='none'; }
		}

		var btnMenos = function(cont){ document.getElementById(cont).parentNode.removeChild(document.getElementById(cont)); }
		var btnMas = function(cont){
			var newCont = cont+1
			,	div     = document.createElement('div')
			,	content = '<div>Field '+newCont+'</div>'
							+'<div><input type="text" id="var_'+newCont+'" placeholder="Nombre" onchange="eventBtnMas(this,'+newCont+');"/></div>'
							+'<div><input type="text" id="val_'+newCont+'" placeholder="Valor" onchange="eventBtnMas(this,'+newCont+');"/></div>'
							+'<div class="btnFila" id="mas_'+newCont+'" onclick="btnMas('+newCont+')">+</div>'
							+'<div class="btnFila" id="menos_'+newCont+'" style="display:none;" onclick="btnMenos('+newCont+')">-</div>';

			div.setAttribute('id',newCont);
			div.setAttribute('class','filaVar');
            div.innerHTML = content;

            document.getElementById('camposData').appendChild(div);
			document.getElementById("mas_"+cont).style.display   = "none";
			document.getElementById("menos_"+cont).style.display = "block";
			document.getElementById("var_"+newCont).focus();

		}

		var eventBtnMas = function(input,cont){
			var metodo = document.getElementById('var_'+cont).value
			,	valor  = document.getElementById('val_'+cont).value
			,	style  = document.getElementById('menos_'+cont).style.display;

			if(metodo != '' && valor!='' && style=='none'){ btnMas(cont) }
		}

		var objJson = function(){
			var cols     = []
			,	arrayCol = []
			,	contCol  = 0
			,	arrayRow = []
			,	contRow  = 0
			,	rows     = document.querySelectorAll(".tablaXml tr");

			[].forEach.call(rows,function(tr,indice,documento){
				contCol = 0;
				contRow++;

				arrayCol = [];
				cols = tr.querySelectorAll("td");

				[].forEach.call(cols,function(td,indice,documento){

					contCol++;
					if(contCol==1){ return; }

					arrayCol.push(td.innerHTML);
				});

				arrayRow.push(arrayCol);
			});

			txtJson = JSON.stringify(arrayRow);

			document.querySelector(".actionTable a").href = "php/excel.php?txtJson="+txtJson;
		}
		objJson();

		function edit(edit){
			var state = edit.getAttribute("data-edit");
			if(state == 'false'){
				edit.setAttribute("data-edit","true");
				document.querySelector(".tablaXml").contentEditable = true;
			}
			else{
				edit.setAttribute("data-edit","false");
				document.querySelector(".tablaXml").contentEditable = false;
			}
			objJson();
		}

	</script>
</body>
</html>