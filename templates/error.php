<!DOCTYPE html>
	<head data-requesttoken="<?php echo $requesttoken; ?>">
		<link rel="stylesheet" href="/apps/chat/css/error.min.css">
		<link rel="stylesheet" href="/core/css/styles.css">
		<link rel="stylesheet" href="/core/css/header.css">
		<script src="/core/vendor/jquery/jquery.min.js"></script>
		<script src="/apps/chat/js/error.min.js"></script>
	</head>
	<body id="body-login">
		<div class="wrapper"><!-- for sticky footer -->
			<div class="v-align"><!-- vertically centred box -->
				<header>
					<div id="header">
						<div class="logo svg">
							<h1 class="hidden-visually">ownCloud</h1>
						</div>
						<div id="logo-claim" style="display:none;"></div>
					</div>
				</header>
				<section id="error">
					<h1><?php p('An error occurred in the ownCloud Chat app!');?></h1><br />
					<b><?php echo $brief?></b>
					<p>
						<?php echo $info?>
					</p>
					<code>
						This is the raw error which you should include in any bug report
						<i>
							<br><br>
							<?php echo $version;?>
							<br><br>
							<?php echo $raw;?>
						</i>
					</code>
					<br><br>
					<a href="/index.php/" >Try again</a> |
					<a href="<?php echo $link;?>">More information</a> |
					<a href="https://github.com/owncloud/chat/issues/new">Contact a developer</a> |
					<button id="disable">Disable the Chat app</button>
				</section>
				<div class="push"></div><!-- for sticky footer -->
			</div>
		</div>
	</body>
</html>
