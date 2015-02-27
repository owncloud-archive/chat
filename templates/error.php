<!DOCTYPE html>
	<head>
		<link rel="stylesheet" href="/apps/chat/css/error.min.css">
	</head>
	<body>
		<section id="error">
			<h1><?php p('An error occurred in the ownCloud Chat app!');?></h1><br />
			<b><?php echo $brief?></b>
			<p>
				<?php echo $info?>
			</p>
			<h2><a href="<?php echo $link;?>">More information</a></h2>
			<h2><a href="https://github.com/owncloud/chat/issues/new">Contact a developer</a></h2>
			<code>
				<i>
					<?php echo $raw;?>
				</i>
			</code>
		</section>
	</body>
</html>
