	<div id="<?php echo $id; ?>" class="<?php echo $type; ?>" txmt="<?php echo $txmt; ?>">
		<span class="type"><?php echo ucfirst($type); ?></span>
		<span class="line">(line <?php echo $line; ?>)</span>
		<div class="error-msg"><?php echo htmlentities((string) $ele); ?></div>
	</div>