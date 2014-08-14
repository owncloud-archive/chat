<div ng-if="conv.id !== $parent.active.conv || ( conv.id === $parent.active.conv && conv.users.length === 2)" class="conv-list-item-avatar">
	<div ng-if="conv.users.length === 2" class="avatar-list-container" >
		<div
			tipsy
			title="{{ user.displayname }}"
			ng-if="key < 4"
			ng-repeat="(key, user) in conv.users | userFilter"
			class="avatar-list-avatar-big"
			avatar
			data-size="40"
			data-id="{{ user.id }}"
			data-displayname="{{ user.displayname }}"
			data-addressbook-backend="{{ user.address_book_backend }}"
			data-addressbook-id="{{ user.address_book_id  }}"
			>
		</div>
	</div>
	<div ng-if="conv.users.length > 2" class="avatar-list-container" >
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
<div
	class="conv-list-item-avatar"
	ng-if="conv.id === $parent.active.conv && conv.users.length > 2"
	>
	<ul>
		<li
			ng-repeat="(key, user) in conv.users | userFilter"
			class="avatar-list-expanded-item"
			>
			<div
				tipsy
				title="{{ user.displayname }}"
				class="avatar-list-avatar"
				avatar
				data-size="40"
				data-id="{{ user.id }}"
				data-displayname="{{ user.displayname }}"
				data-addressbook-backend="{{ user.address_book_backend }}"
				data-addressbook-id="{{ user.address_book_id  }}"
				>
			</div>
			{{ user.displayname }}
		</li>
		<li
			class="avatar-list-expanded-item invite-button"
			ng-click="$parent.view.inviteClick();"
			>
			<div
				class="icon-add icon-20 avatar-list-avatar">
				&nbsp;
			</div>
			<?php p($l->t('Add Person')); ?>
		</li>
	</ul>
</div>