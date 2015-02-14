<div id="filter-container">
	<!-- Field to start an instant chat via XMPP -->
	<form ng-submit="view.startInstantChat()">
		<input
			type="text"
			placeholder="<?php p($l->t('Start a chat with ...'));?>"
			ng-model="fields.jid"
			ng-model-options="{ updateOn: 'default', debounce: {'default': 500} }"
			>
	</form>
	<!-- Field to search in the conversations -->
	<input class="filter-field" type="text" placeholder="<?php p($l->t('Search in conversations'));?>" ng-model="search.name">
</div>
<ul>
	<!-- App-navigation entry representing a conversation -->
	<li
		ng-repeat="conv in convs | orderObjectBy:'order':true | filter:search "
		ng-class="{heightInvite: view.elements.inviteInput, 'conv-list-active' : conv.id === $session.conv}"
		ng-click="view.makeActive(conv.id, $event, 'invite-button');"
		class="conv-list-item"
		id="conv-list-{{ conv.id }}"
		auto-height
		data-item-count="{{ conv.users.length -1 }}"
		data-item-height="50"
		data-min-height="60"
		data-conv-id="{{ conv.id }}"
		>
		<?php print_unescaped($this->inc('part.avatar')) ?>
		<!-- These buttons are shown on the right side of the enty -->
		<div class="conv-list-item-buttons">
			<div
				ng-click="view.inviteClick();"
				ng-if="conv.id == $session.conv && conv.users.length === 2"
				class="icon-add right icon-20 invite-button"
				>
				&nbsp;
			</div>
			<!-- The following 3 buttons are buttons used onyl by the XMPP backend -->
			<div
				title="Add Contact to roster"
				ng-click="view.addContactToRoster(conv.id);"
				ng-if="conv.id == $session.conv && conv.backend.id === 'xmpp' && !contactInRoster(conv.id)"
				class="icon-download right icon-20 "
				>
				&nbsp;
			</div>
			<div
				title="Add Contact to contacts"
				ng-click="view.saveContact(conv.id);"
				ng-if="conv.id == $session.conv && conv.backend.id === 'xmpp' && !contactInContacts(conv.id)"
				class="icon-download right icon-20 "
				>
				&nbsp;
			</div>
			<div
				title="Remove Contact from roster"
				ng-click="view.removeContactFromRoster(conv.id);"
				ng-if="conv.id == $session.conv && conv.backend.id === 'xmpp' && contactInRoster(conv.id)"
				class="icon-delete right icon-20 "
				>
				&nbsp;
			</div>
			<!-- END Specific for XMPP-->
			<div title="Show attached files" ng-click="view.showFiles();" ng-if="conv.id == $session.conv && conv.users.length === 2" class="icon-file right icon-20 files-button">
				&nbsp;
			</div>
		</div>
	</li>
</ul>
<?php print_unescaped($this->inc('part.settings')) ?>
