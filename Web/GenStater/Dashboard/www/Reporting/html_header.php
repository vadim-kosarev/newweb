<html>
<head>
<title>HEAD</title>
<link href="style.css" rel="stylesheet" type="text/css">
<script language="javascript">
<!--

var state = 'none';

function getNewState(oldState) {
	if (oldState == 'block') {
		return 'none';
	} else {
		return 'block';
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