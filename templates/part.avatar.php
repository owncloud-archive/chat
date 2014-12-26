<div ng-if="conv.id !== $parent.$session.conv || ( conv.id === $parent.$session.conv && conv.users.length === 2)" class="conv-list-item-avatar">
	<div
		ng-if="conv.users.length === 2 && $parent.$parent.avatarsEnabled === 'true'"
		class="avatar-list-container"
	>
		<div
			class="online-dot-container"
		>
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
			<div
				online
				ng-repeat="(key, user) in conv.users | userFilter"
				data-id="{{ user.id }}"
			>
				<!--
				This is a place holder div for the green dot which is used to indicate the online status of the contact
				-->
				&nbsp;
			</div>
		</div>
	</div>
	<div
		ng-if="conv.users.length > 2  && $parent.$parent.avatarsEnabled === 'true'"
		class="avatar-list-container"
	>
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
	<div
		ng-if="$parent.$parent.avatarsEnabled === 'false'"
		online
		ng-repeat="(key, user) in conv.users | userFilter"
		data-id="{{ user.id }}"
		class="online-dot-displayname-nav"
		>
		<!--
		This is a place holder div for the green dot which is used to indicate the online status of the contact
		-->
		&nbsp;
	</div>
</div>
<div
	class="conv-list-item-avatar"
	ng-if="conv.id === $parent.$session.conv && conv.users.length > 2"
	>
	<ul>
		<li
			ng-repeat="(key, user) in conv.users | userFilter"
			class="avatar-list-expanded-item"
			>
			<div class="online-dot-container">
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
				<div
					online
					ng-repeat="(key, user) in conv.users | userFilter"
					data-id="{{ user.id }}"
				>
					<!--
					This is a place holder div for the green dot which is used to indicate the online status of the contact
					-->
					&nbsp;
				</div>
			</div>
			{{ user.displayname }}
		</li>
		<li
			class="avatar-list-expanded-button invite-button"
			ng-click="$parent.view.inviteClick();"
			>
			<div
				class="icon-add icon-20 avatar-list-avatar">
				&nbsp;
			</div>
			<?php p($l->t('Add Person')); ?>
		</li>
		<li
			class="avatar-list-expanded-button  files-button"
			ng-click="$parent.view.showFiles()"
		>
			<div
				class="icon-file icon-20 avatar-list-avatar"
			>
				&nbsp;
			</div>
			<?php p($l->t('View Attached files')); ?>
		</li>
	</ul>
</div>