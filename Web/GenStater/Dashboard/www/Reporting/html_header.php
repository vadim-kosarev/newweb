<html>
<head>
<title>Reporting</title>

<style>
/* style.css */
		<?php readfile("style.css")?>
/* style.css end */
</style>

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
		<div id="progressDiv" style="font-family:Tahoma;width:200px;height:100px;position:absolute;left:50%;top:50%;margin-left:-100px;margin-top:-50px;"><img src="progress.gif" /> Please wait...</div>
<?php 

function mflush(){
    echo(str_repeat(' ',256));
    // check that buffer is actually set before flushing
    if (ob_get_length()){            
        @ob_flush();
        @flush();
        @ob_end_flush();
    }    
    @ob_start();
}

mflush(); 
?>

