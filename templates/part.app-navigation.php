<ul>
	<li
		ng-class="{heightInvite: view.elements.inviteInput, 'conv-list-active' : conv.id === active.conv}"
		ng-click="view.makeActive(conv.id, $event, 'invite-button');"
		ng-repeat="conv in convs | orderObjectBy:'order':true""
		class="conv-list-item"
		id="conv-list-{{ conv.id }}"
		ng-if="conv.archived === false"
		auto-height
		data-item-count="{{ conv.users.length -1 }}"
		data-item-height="30"
		data-min-height="60"
		data-conv-id="{{ conv.id }}"
	>
		<?php print_unescaped($this->inc('part.avatar')) ?>
		<div class="conv-list-item-buttons">
			<div ng-click="view.inviteClick();" ng-if="conv.id == active.conv && conv.users.length === 2" class="icon-add right icon-20 invite-button">
				&nbsp;
			</div>
			<div ng-if="conv.id == active.conv" ng-click="toggleArchive(conv.id)" class="icon-close right icon-20" >
				&nbsp;
			</div>
		</div>
	</li>
	<li
		ng-if="(view.elements.archived === true) && (conv.archived === true)"
		ng-class="{heightInvite: view.elements.inviteInput, 'conv-list-active' : conv.id === active.conv}"
		ng-click="view.makeActive(conv.id, $event, 'invite-button');"
		ng-repeat="conv in convs"
		class="conv-list-item archived"
		id="conv-list-{{ conv.id }}"
		auto-height
		data-item-count="{{ conv.users.length -1 }}"
		data-item-height="30"
		data-min-height="60"
		data-conv-id="{{ conv.id }}""
		>
		<?php print_unescaped($this->inc('part.avatar')) ?>
		<div class="conv-list-item-buttons">
			<div ng-click="view.toggle('invite');view.toggle('chat');" ng-if="conv.id == active.conv && conv.users.length === 2" class="icon-add right icon-20 invite-button">
				&nbsp;
			</div>
			<div ng-if="conv.id == active.conv" ng-click="toggleArchive(conv.id)" class="icon-close right icon-20" >
				&nbsp;
			</div>
		</div>
	</li>
	<li
		ng-if="view.elements.archived === true"
		ng-click="view.toggle('archived');"
		id="archived-button"
		ng-class="{bold : view.elements.showArchived.bold === true}"
		show-archived
		>
		<?php p($l->t('Hide Archived Conversations')); ?>
	</li>
	<li
		ng-if="view.elements.archived === false"
		ng-click="view.toggle('archived');"
		id="archived-button"
		ng-class="{bold : view.elements.showArchived.bold === true}"
		show-archived
		>
		<?php p($l->t('Show Archived Conversations')); ?>
	</li>
</ul>
