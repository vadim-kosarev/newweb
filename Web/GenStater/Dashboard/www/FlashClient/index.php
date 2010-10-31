<?php
	
   $rtmpServer = isset($_GET["host"])?$_GET["host"]:"webcall-test.metr.com";
   $callee = isset($_GET["callee"])?$_GET["callee"]:"442030514876";



?>
<contentType="text/html;charset=UTF-8" language="java">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="style.css" type="text/css" rel="stylesheet" media="all" />
<title>Flash phone</title>

<script src='swfobject.js'>//</script>

<script src='debug.js'>//</script>

<script>
//swfobject functions

var flashphone;





function executeScript() {
   trace("Executing script...");
   login();
   call();
   setTimeout('hangup()', 120000);
   setTimeout('logoff()', 125000);
   setTimeout('windows.close()', 130000);
}






function notifySecurityPanelClosed(){
	trace('function notifySecurityPanelClosed()');
}

function positionStatus(e){
    trace('positionStatus(' + e.ref + ')');
	flashphone = e.ref;
    trace(dumpObj(flashphone));	
    document.getElementById('usernameTextField').focus();			
}

function notifyInit(){
	trace('function notifyInit()');
        executeScript();
}

function notifyRegistered(){
	trace('function notifyRegistered()');
}

function notifyBalance(balance){
	trace('function notifyBalance() '+balance);
}

function notify(id,state,callee,cost,isOutgoing){
	trace('notify() id='+id+' state='+state+' callee='+callee+' cost='+cost+' isOutgoing='+isOutgoing);
}

function notifyError(errorCode){
	trace('function notifyError() '+errorCode);
}

function notifyLock(locked){
	trace('function notifyLock() '+locked);
}

function notifyMicUnmuted(unmuted){
	trace('function notifyMicUnmuted() '+unmuted);
}

function sysMsg(msg){
	trace('function sysMsg() '+msg);
}

function login(){
	var username = document.getElementById('usernameTextField').value;
	var password = document.getElementById('passwordTextField').value;
	var status = flashphone.login(username,password);
	trace('function login() '+username+' '+password);	
}

function dtmf() {
    trace('function dtmf()');
    var dtmfTone = document.getElementById('dtmfTone').value;
    flashphone.dtmfToneStart(dtmfTone);
    trace('function dtmf(' + dtmfTone + ')');
}

function transcoderParameter() {
    trace('function transcoderParameter()');
    var trKey = document.getElementById('transcoderParameterKey').value;
    var trVal = document.getElementById('transcoderParameterVal').value;
    flashphone.setTranscoderParameter(trKey, trVal);
    trace('function transcoderParameter(' + trKey + ":" + trVal + ')');
}

function logoff(){
	trace('function logoff()');
	flashphone.logoff();	
}

function call(){
	var caller = document.getElementById('callerTextField').value;
	var callee = document.getElementById('calleeTextField').value;
	var visibleName = document.getElementById('visibleNameTextField').value;
	trace('function call() '+ caller+' '+callee+' '+visibleName);
	var result = flashphone.call(caller,callee,visibleName);
	trace('result of call() function: 0 - ok, 1 - caller not valid, 2 - callee not valid, 3 - visibleName not valid RESULT=='+result);
}

function hangup(){
	trace('function hangup()');	
	flashphone.hangup();
}

function answer(){	
	trace('function answer()');
	flashphone.answer();
}

function getMicVolume(){	
	var micVolume = flashphone.getMicVolume();
	trace('function getMicVolume() return '+micVolume); 
	return micVolume;
}

function setMicVolume(){
	var micVolume = parseFloat(document.getElementById('microphoneVolumeTextField').value);	
	trace('function setMicVolume() '+micVolume);
	flashphone.setMicVolume(micVolume);
}

function getVolume(){
	var volume = flashphone.getVolume();
	trace('function getVolume() return '+volume);
	return volume;
}

function setVolume(){
	var volume = parseFloat(document.getElementById('volumeTextField').value);
	trace('function setVolume() '+volume);
	flashphone.setVolume(volume);
}

function setLoopBack(){
	var loopBack = document.getElementById('loopBackCheckbox').checked;
	trace('function setLoopBack() '+loopBack);
	flashphone.setLoopBack(loopBack);
}

function setAdv(){	
	var enable = document.getElementById('setAdvCheckbox').checked;
	trace('function setAdv() '+enable);
	flashphone.setAdv(enable);
}

function setAdvParams(){
	var advPath = document.getElementById('advPathTextField').value;
	var advTargetUrl = document.getElementById('advTargetUrlTextField').value;
	flashphone.setAdvParams(advPath,advTargetUrl);
}

function getInfo(){
	var info = flashphone.getInfo();
	trace('function getInfo() '+info);
}

function showSecurityPanel(){
	flashphone.showSecurityPanel();
	trace('function showSecurityPanel()');
}

function trace(str){
	document.getElementById('consoleTextArea').innerHTML = document.getElementById('consoleTextArea').innerHTML +'\n'+ str;	
}


var getParams = location.search;
getParams = getParams.substring(1);
getParams = getParams.split('&');
var flashvars = {};
flashvars.username = 'flashphone';
flashvars.password='ca5f09d029df37b45903047650d553cc';
if (getParams!=null){
	for(var i=0; i < getParams.length; i++) 
	{ 
		tmp2 = getParams[i].split('=');
		flashvars[tmp2[0]] = tmp2[1];
	}
}

flashvars.RTMP_SERVER_URL ='rtmp://<?=$rtmpServer?>:1935';
flashvars.TRY_OTHER_PROTOCOLS ='true';
flashvars.USE_LOADBALANCER='false';
flashvars.LOAD_BALANCER_URL='http://10.0.2.2:1936';

flashvars.LOAD_BALANCER_DATA_LOADING_TIMEOUT='1000';
flashvars.LOAD_BALANCER_NODE_CONNECTION_TIMEOUT='5000';
flashvars.LOAD_BALANCER_CHANGE_NODE_CONNECTION_INTERVAL='1000';
flashvars.INCOMING_CALL_SOUND_URL = 'assets/incoming.mp3';
flashvars.OUTGOING_CALL_SOUND_URL = 'assets/outgoing.mp3';
flashvars.BUSY_SOUND_URL = 'assets/busy.mp3';
flashvars.REGISTERED_SOUND_URL = 'assets/registered.mp3';
flashvars.CALL_FINISHED_SOUND_URL = 'assets/finished.mp3';
flashvars.IMAGE_URL = 'assets/cat.jpg';
flashvars.REGMODE='true';
flashvars.BANNER_URL = 'http://metr.com';
flashvars.BG_COLOR = '#FFFFFF';
flashvars.REPORT_URL = 'http://10.0.2.2:1937';
flashvars.REPORT_CROSSDOMAIN = 'http://10.0.2.2:1935/crossdomain.xml';
flashvars.KEEP_MOUSE_MOVE_LISTENER='false';

// LOG_LEVEL: NONE, ERROR, INFO, DEBUG, TRACE, FULL
flashvars.LOG_LEVEL = 'INFO';

var params = {};
params.menu = "true";
params.swliveconnect = "true";
params.allowfullscreen = "true";
params.allowscriptaccess = "always";
var attributes = {};
if (swfobject.hasFlashPlayerVersion("10.0.12")) {
	swfobject.embedSWF("PhoneJS.swf?v=160.4", "PHONE", "215", "138", "10.0.12", "expressInstall.swf", flashvars, params, attributes, positionStatus);
} else if (swfobject.hasFlashPlayerVersion("6.0")) {
	alert('Пожалуйста, скачайте последнюю версию Adobe Flash Player');
} else {
	alert('Пожалуйста, скачайте последнюю версию Adobe Flash Player');
}
</script>		
</head>

  <body bgcolor="#ffffff">
    <table class="main">
      <tr>
        <td class="left_column">
        
          
          <div id="PHONE">
	         <a href="http://www.adobe.com/go/getflashplayer"><img border="0" src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" /></a>
          </div>

          <h2>JS &rArr; Flash</h2>
          
          <ol>
          
          <li> <a href="#" onClick="transcoderParameter();return false;">transcoderParameter</a> - set transcoder parameter<br/>
          key:<input class="input" id="transcoderParameterKey" type="text" value="2" size="4"/>
          val:<input class="input" id="transcoderParameterVal" type="text" value="0" size="4"/>
          <br/>
          <br/>
          </li>
          
          <li> <a href="#" onClick="dtmf();return false;">dtmf</a> - send DTMF signal<br/>
          <input class="input" id="dtmfTone" type="text" value="1"/> dtmF<br/>
          <br/>
          </li>
          
          <li> 
          <a href="#" onClick="login();return false;">login</a> - Login to server<br/>
          <input class="input" id="usernameTextField" type="text" value="flashphone"/> Login<br/>
          <input id="passwordTextField" type="text" value="ca5f09d029df37b45903047650d553cc"/> Password<br/><br/>
  		  </li>
  			
          <li>
          <a href="#" onClick="call();return false;">call</a> - Make call<br/> 
          <input id="callerTextField" type="text" value="caller"/> Caller<br/>
          <input id="calleeTextField" type="text" value="<?=$callee?>"/> Callee<br/>
          <input id="visibleNameTextField" type="text" value="visibleName"/> VisibleName<br/><br/>	
          </li>
          
          <li><a href="#" onClick="hangup();return false;">hangup</a> - Hangup call<br/><br/></li>
          
          <li><a href="#" onClick="answer();return false;">answer</a> - Answer call<br/><br/></li>
          
          <li><a href="#" onClick="getVolume();return false;">getVolume</a> - Get the current volume: Number [0,1]<br/><br/></li>
          
          <li><a href="#" onClick="setVolume();return false;">setVolume</a> - Set volume<br/>
          <input id="volumeTextField" type="text" value="0.5"/> Number [0,1] <br/><br/>
          </li>
          
          <li>
          <a href="#" onClick="getMicVolume();return false;">getMicVolume</a> - Get microphone volume: Number [0,100]<br/><br/>
          </li>
          
          <li><a href="#" onClick="setMicVolume();return false;">setMicVolume</a> - Set microphone volume<br/>
          <input id="microphoneVolumeTextField" type="text" value="50"/> Number [0,100] <br/><br/>
          </li>
          
          <li><a href="#" onClick="setLoopBack();return false;">setLoopBack</a> <input class="checkbox" id="loopBackCheckbox" type="checkbox" value="true"/> Route 
          microphone to sound device<br/><br/>
          </li>
          
          <li><a href="#" onClick="setAdv();return false;">setAdv</a> <input class="checkbox" id="setAdvCheckbox" type="checkbox" value="true"/> Show advert image<br/><br/>
          </li>
          
          <li><a href="#" onClick="logoff();return false;">logoff</a> - Log off from server<br/><br/></li>
          
          <li><a href="#" onClick="getInfo();return false;">getInfo</a> - Show flash client info <br/><br/></li>
          
          <li><a href="#" onClick="showSecurityPanel();return false;">showSecurityPanel</a> - Show security panel<br/><br/></li>
          
          <li><a href="#" onClick="setAdvParams();return false;">setAdvParams</a> - Set advert parameters<br/> 
          &nbsp;&nbsp;<input id="advPathTextField" type="text" value="assets/dog.jpg"/> Path to image, like <label class="help_link">assets/dog.jpg</label> <br/>
          &nbsp;&nbsp;<input id="advTargetUrlTextField" type="text" value="http://youtube.com"/> Navigate URL, like <label class="help_link">http://site.com</label> <br/>
          </li> 
          
          </ol>

          <h2>Js <b>&lArr;</b> Flash</h2>
                    
	         1. <label class="func_name">notifyRegistered():void</label><br/><br/>
	         2. <label class="func_name">notifyError(string):void</label><br/>
           Error notification. Errors={AUTH_FAIL, USER_NOT_FOUND, RTMP_CONNECTION_FAIL, RTMP_CONNECTION_REJECTED}<br/><br/>
	         3. <label class="func_name">notifyLock(boolean):void</label><br/>
           Notification about other user at the same account outgoing calling or talking now<br/><br/>
	         4. <label class="func_name">notifyBalance(number):void</label><br/>
           User balance<br/><br/>
	         5. <label class="func_name">notify(id,state,callee,cost,isOutgoing):void</label><br/>
           Notification about incoming/outgoing call state<br/><br/>	
          
      </td>
      
      <td class="right_column" valign="top" >
        <textarea id="consoleTextArea" cols="130" rows="65" readonly></textarea>
      </td>
      
    </tr>
</table>


</body>
</html>