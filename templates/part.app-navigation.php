<div id="filter-container">
	<input class="filter-field" type="text" placeholder="<?php p($l->t('Search in conversations'));?>" ng-model="search.name">
</div>
<ul>
	<li
		ng-class="{heightInvite: view.elements.inviteInput, 'conv-list-active' : conv.id === $session.conv}"
		ng-click="view.makeActive(conv.id, $event, 'invite-button');"
		ng-repeat="conv in convs | orderObjectBy:'order':true | filter:search "
		class="conv-list-item"
		id="conv-list-{{ conv.id }}"
		auto-height
		data-item-count="{{ conv.users.length -1 }}"
		data-item-height="50"
		data-min-height="60"
		data-conv-id="{{ conv.id }}"
	>
		<?php print_unescaped($this->inc('part.avatar')) ?>
		<div class="conv-list-item-buttons">
			<div ng-click="view.inviteClick();" ng-if="conv.id == $session.conv && conv.users.length === 2" class="icon-add right icon-20 invite-button">
				&nbsp;
			</div>
			<div title="Show attached files" ng-click="view.showFiles();" ng-if="conv.id == $session.conv && conv.users.length === 2" class="icon-file right icon-20 files-button">
				&nbsp;
			</div>
	</li>
	<?php print_unescaped($this->inc('part.settings')) ?>
</ul>