<html>
<head>
<title>Reporting</title>

<style>
/* style.css */
		<?php readfile("style.css")?>
/* style.css end */
</style>

<script src="../Public/ajax/libs/jquery/1.8.0/jquery-1.8.0.min.js" type="text/javascript" charset="utf-8"></script>
<script src="../Public/ajax/libs/jquery.jeditable/jquery.jeditable.js" type="text/javascript" charset="utf-8"></script>

<script language="javascript">
<!--

var state = 'none';

function getNewState(oldState) {
	if (oldState == 'none') {
		return 'block';
	} else {
		return 'none';
	}	
}

function showhide(layer_ref) {
	
	if (document.all) { //IS IE 4 or 5 (or 6 beta)
		dispVal = "document.all." + layer_ref + ".style.display";  
		eval( dispVal + " = getNewState(" + dispVal + ")");
	}
	
	if (document.layers) { //IS NETSCAPE 4 or below
		document.layers[layer_ref].display = getNewState(document.layers[layer_ref].display);
	}
	
	if (document.getElementById && !document.all) {
		hza = document.getElementById(layer_ref);
		hza.style.display = getNewState(hza.style.display);
	}
	
}
//-->
</script>
</head>
<body>
