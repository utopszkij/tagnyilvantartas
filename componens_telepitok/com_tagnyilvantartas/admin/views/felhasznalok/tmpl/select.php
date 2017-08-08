<?php
?>
<h2>Válasszd ki; melyik felhasználó csoport jogaival akarsz dolgozni!</h2>
<form method="post" action="#" class="lmpForm" name="adminForm"> 
<p> </p>
<?php foreach ($this->csoportok as $csoport) {
    if ($csoport->fcsop_id == $this->csoportok[0]->fcsop_id)
        echo '<p><input type="radio" name="csoport" checked="checked" value="'.$csoport->fcsop_id.'"> ('.$csoport->kod.') '.$csoport->nev.'</p>';  
    else                          
        echo '<p><input type="radio" name="csoport" value="'.$csoport->fcsop_id.'"> ('.$csoport->kod.') '.$csoport->nev.'</p>';  
}
?>
<p> </p>
</form>
<div class="lmptoolbar">
    <div id="toolbar">
        <button type="button" onclick="document.forms.adminForm.submit();">Rendben</button>
    </div>
</div>

