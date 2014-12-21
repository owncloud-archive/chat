<?php
script('chat', 'admin.min');
//style('chat', 'admin.min');
?>

<div class="section" id="news">
	<h2>Chat</h2>
	<div class="form-line">
		<?php foreach ($_['backends'] as $backend) { ?>
			<p>
				<input class="backend-checkbox" type="checkbox" id="<?php p($backend->getId())?>" name="<?php p($backend->getId())?>"
					<?php if ($backend->isEnabled()) p('checked'); ?>>
				<label class="backend-label" for="<?php p($backend->getId())?>">
					<?php p($backend->getDisplayName())?>
				</label>
			</p>
			<p>
				<em><?php p($backend->getHelp())?></em>
			</p>
		<?php }?>
	</div>
</div>