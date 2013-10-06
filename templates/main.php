<?php
\OCP\Util::addScript('chat', 'main');
\OCP\Util::addStyle('chat', 'main');
\OCP\Util::addScript('chat', 'date');
\OCP\Util::addScript('3rdparty', 'md5/md5.min');
\OCP\Util::addScript('chat', 'wjl');
?>
<div id="app">
	<div id="undo-container">
	</div>
	<div id="app-navigation" >
		<ul id="conversations">
			<!--  All active conversation for this user are displayed here as a list-->
		</ul>

		<div id="app-settings" >
			<!-- The user can join or create a conversation here -->
			<fieldset>
				<input type="text" id="user" placeholder="User Name"><br>
				<button type="submit" id="createConverstation"><?php p($l->t("Create Conversation")); ?></button>
			</fieldset>
			<ul id="status">

			</ul>
		</div>

	</div>

	<div id="app-content" >
		<div id="chats">
		</div>
	</div>
</div>
