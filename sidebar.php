<?php
$common = new commonObj();
$arrModules = $common->GetModules();
$arrUserPages = $common->getuserPages($userAcess['userId']);
?>

<ul>
	<?
	$ctr = 0;
	foreach($arrModules as $valMod) {
		$modID = ($ctr==0) ? "": $ctr;
	?>
		<li>
		<a href="#"><?=$valMod['moduleName']?></a>
			<ul>
				<?
				$arrSubModule = $common->GetSubModules($valMod['moduleName']);
				foreach($arrSubModule as $valSub) {
					if (in_array($valSub['moduleId'], $arrUserPages)) {
				?>
					<li><a href="#" onclick="menu('<?=$valSub['page']?>','<?=strtoupper($valSub['label'])?>')"><?=$valSub['label']?>
					</a></li>
				<?  } 
				} 
                ?>
			</ul>  
		</li>
	<? 
		$ctr++;
	}
	?>
	<li><a href="index.php"><font color="red">Logout</font></a></li>
</ul>