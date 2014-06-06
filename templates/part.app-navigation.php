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
			<div class="avatar-list-container" tipsy title="{{ user.displayname }}" ng-if="key < 3" ng-repeat="(key, user) in conv.users | userFilter">
				<div
					class="left"
					avatar
					data-onlinesize="5"
					data-size="32"
					data-id="{{ user.id }}"
					data-displayname="{{ user.displayname }}"
					data-addressbook-backend="{{ user.address_book_backend }}"
					data-addressbook-id="{{ user.address_book_id  }}"
					>
				</div>
			</div>
							<span ng-if="conv.users.length === 2" ng-repeat="user in conv.users | userFilter" class="left" >
								{{ user.displayname }}
							</span>
			<div title="" more-users users="{{ conv.users }}"  tipsy ng-if="conv.users.length > 5" class="avatar-list-more">
				+ {{ conv.users.length - 4 }}
			</div>
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
			<div class="avatar-list-container" tipsy title="{{ user.displayname }}" ng-if="key < 3" ng-repeat="(key, user) in conv.users | userFilter">
				<div
					class="left"
					avatar
					data-onlinesize="5"
					data-size="32"
					data-id="{{ user.id }}"
					data-displayname="{{ user.displayname }}"
					data-addressbook-backend="{{ user.address_book_backend }}"
					data-addressbook-id="{{ user.address_book_id  }}"
					>
				</div>
			</div>
							<span ng-if="conv.users.length === 2" ng-repeat="user in conv.users | userFilter" class="left" >
								{{ user.displayname }}
							</span>
			<div title="" more-users users="{{ conv.users }}"  tipsy ng-if="conv.users.length > 5" class="avatar-list-more">
				+ {{ conv.users.length - 4 }}
			</div>
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
		>
		Hide Archived Conversations
	</li>
	<li
		ng-if="view.elements.archived === false"
		ng-click="view.toggle('archived');"
		id="archived-button"
		>
		Show Archived Conversations
	</li>
</ul>
