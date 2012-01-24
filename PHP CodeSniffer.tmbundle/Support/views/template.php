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
	<h2 class="summary">PHP Lint Results</h2>
<?php echo $lintContent, PHP_EOL; ?>

	<h2 class="summary">PHP CodeSniffer Results<?php echo $standard; ?></h2>
<?php echo $phpcsContent, PHP_EOL; ?>
</body>
</html>