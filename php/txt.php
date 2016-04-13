<?php
	/**
	* @author Jhon Erick Marroquin Cardenas <jhon3rick@gmail.com> <twitter @jhon3rick>
	* @license MIT
	*/

	$datos = '';
	foreach ($objData as $key => $value) { $datos .= "\n\t\t\t'$key' => '$value',"; }
	$datos = substr($datos, 0, -1);

	echo htmlspecialchars('
<?php
	require_once($_SERVER[\'DOCUMENT_ROOT\'].\'/misc/nuSoap/nusoap.php\');

	$url      = "'.$url.'";
	$metodo   = "'.$metodo.'";
	$arrayVar = array('.$datos.');

	$objSoap = new nusoap_client($url,true);
	$errorWs = $objSoap->getError();

	if ($errorWs) { echo "<h2>Constructor error</h2><pre>".$errorWs."</pre>"; exit; }
	$responseWs = $objSoap->call($metodo, $arrayVar);

	if ($objSoap->fault) {
	    echo "<h2>Fault</h2><pre>";
	    print_r($responseWs);
	    echo "</pre>";
	}
	else {
	    $errorWs = $objSoap->getError();
	    if ($errorWs) { echo "<h2>Error</h2><pre>".$objSoap."</pre>"; }
	    else {

	    	if(stripos(\'?wsdl\', $url)){
	    		$responseWs = $responseWs[$metodo.\'Result\'][\'diffgram\'][\'NewDataSet\'][\'Table1\'];
	    	}

	    	//===============// VIEW RESPONSE //===============//
	    	/***************************************************/
	    	echo \'<pre>\';
	    	print_r($responseWs);
	    	echo \'<pre>\';

	    	//===============// ARRAY RESPONSE //===============//
	    	/****************************************************/
	    	foreach ($responseWs as $fila=> $arrayData) {
	    		// print_r($arrayData);
	    	}
	    }
	}
?>');
?>