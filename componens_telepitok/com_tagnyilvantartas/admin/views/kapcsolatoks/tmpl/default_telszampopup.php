<?php
// telefonszám caller popup ablak
// 2017.04.05 teszámpopup close után képernyő frissités
$session = JFactory::getSession();
$kampany_id = substr(JRequest::getVar(funkcio),8,10);
$token = md5($kampany_id.time());
$session->set('telpopup',$token);
$db1 = JFactory::getDBO();
if (substr(JRequest::getVar('funkcio'),0,7) == 'kampany') {
	$width = '1000';
	$height = '800';
} else {
	$width = '600';
	$height = '300';
}
?>

<script type="text/javascript">
    var telszamPopupWin = false;
	function telszamClick(kapcs_id, telSzimp=true, telHirlevel=true, telHivhato=true) {
		if (telszamPopupWin) telszamPopupWin.close();
		var ifrm = document.getElementById("telszamPopupIfrm");
		var url = "<?php echo JURI::base(); ?>index.php?option=com_tagnyilvantartas&task=telpopup.popupform&tmpl=component"+
		"&<?php echo $token; ?>=1"+
		"&kapcs_id="+kapcs_id+"&kampany_id=<?php echo substr(JRequest::getVar('funkcio'),8,10); ?>";
		telszamPopupWin = window.open(url,'Call center','left:10,top=10,width=<?php echo $width; ?>,height=<?php echo $height; ?>');
	}
</script>

