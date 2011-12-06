<?php
	if ((bool) $valid) {
?>
		<div class="success">No coding standard violations found.</div>
<?php
	} else {
		echo implode(PHP_EOL, $cs->getViolations()), PHP_EOL;
	}
?>
	<div class="footer">
		Errors: <?php echo $cs->getErrorCount(); ?>.
		Warnings: <?php echo $cs->getWarningCount(); ?>.
	</div>