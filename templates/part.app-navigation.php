<ul>
	<li ng-class="{ 'conv-list-active' : view.elements.newConv }" ng-click="view.unActive();view.show('newConv');view.hide('chat');" id="conv-list-new-conv">
		<div class="icon-add icon-32 left">&nbsp;</div>
		<div class="left">New Conversation</div>
	</li>
	<li
		ng-class="{heightInvite: view.elements.inviteInput, 'conv-list-active' : conv.id === active.conv}"
		ng-click="view.makeActive(conv.id, $event, 'invite-button');"
		ng-repeat="conv in convs"
		class="conv-list-item"
		id="conv-list-{{ conv.id }}"
		ng-if="conv.archived === false"
		>
		<div class="conv-list-item-avatar">
			<div class="avatar-list-container" >
				<div
					tipsy
					title="{{ user.displayname }}"
					ng-if="key < 4"
					ng-repeat="(key, user) in conv.users | userFilter"
					class="avatar-list-avatar"
					avatar
					data-size="20"
					data-id="{{ user.id }}"
					data-displayname="{{ user.displayname }}"
					data-addressbook-backend="{{ user.address_book_backend }}"
					data-addressbook-id="{{ user.address_book_id  }}"
					>
				</div>
			</div>
			<span displayname data-users="{{ conv.users }}" class="left avatar-list-displayname" ng-class="{bold : conv.new_msg === true}" >
			</span>
		</div>
		<div class="conv-list-item-buttons">
			<div ng-click="view.toggle('invite');view.toggle('chat');" ng-if="conv.id == active.conv" class="icon-add right icon-20 invite-button">
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
		>
		<div class="conv-list-item-avatar">
			<div class="avatar-list-container" >
				<div
					tipsy
					title="{{ user.displayname }}"
					ng-if="key < 4"
					ng-repeat="(key, user) in conv.users | userFilter"
					class="avatar-list-avatar"
					avatar
					data-size="20"
					data-id="{{ user.id }}"
					data-displayname="{{ user.displayname }}"
					data-addressbook-backend="{{ user.address_book_backend }}"
					data-addressbook-id="{{ user.address_book_id  }}"
					>
				</div>
			</div>
			<span displayname data-users="{{ conv.users }}" class="left avatar-list-displayname" ng-class="{bold : conv.new_msg === true}" >
			</span>
		</div>
		<div class="conv-list-item-buttons">
			<div ng-click="view.toggle('invite');view.toggle('chat');" ng-if="conv.id == active.conv" class="icon-add right icon-20 invite-button">
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
		>
		Hide Archived Conversations
	</li>
	<li
		ng-if="view.elements.archived === false"
		ng-click="view.toggle('archived');"
		id="archived-button"
		ng-class="{bold : view.elements.showArchived.bold === true}"
		>
		Show Archived Conversations
	</li>
</ul>
