<?php
	/**
	* @author Jhon Erick Marroquin Cardenas <jhon3rick@gmail.com> <twitter @jhon3rick>
	* @license MIT
	*/

	$arrayJson = json_decode($_GET['txtJson'],true);

	$html = '';
	foreach ($arrayJson as $arrayRow) {
		$html .= '<tr>';
		foreach ($arrayRow as $col) { $html .= '<td>'.$col.'</td>'; }
		$html .= '</tr>';
	}

	echo '<table>'.$html.'</table>';

?>