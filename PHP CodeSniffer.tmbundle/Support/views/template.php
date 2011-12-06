<?php

if ((bool) $standard = $cs->getStandard()) {
	$standard = ' - Coding Standard: ' . $standard;
}

?>
<!DOCTYPE html>
<html>
<head>
	<title>PHP CodeSniffer Results<?php echo $standard; ?></title>
	
	<script type="text/javascript">
<?php echo $script, PHP_EOL; ?>
	</script>
	
	<style type="text/css">
<?php echo $style, PHP_EOL; ?>
	</style>
</head>
<body onload="init();">
	<div class="summary">PHP CodeSniffer Results<?php echo $standard; ?></div>
<?php echo $content, PHP_EOL; ?>
</body>
</html>