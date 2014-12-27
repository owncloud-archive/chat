<!--
	This div shows the avatars + displayname of the conversation entry
	This div is only visible when:
 		 - the conversation isn't active
 		 OR
 		 - the conversation is active
 		 - AND the conversation has only 2 users in it
-->
<div
	ng-if="conv.id !== $parent.$session.conv || ( conv.id === $parent.$session.conv && conv.users.length === 2)"
	class="conv-list-item-avatar"
	>
	<!--
		This div shows the avatar of the Contact in the conversation + a green dot when it's online
		This div is only visible when:
			- the conversation has only 2 users in it
	-->
	<div ng-if="conv.users.length === 2" class="avatar-list-container" >
		<div class="online-dot-container">
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
				online
			>
			</div>
			<div>
				<!--
				This is a place holder div for the green dot which is used to indicate the online status of the contact
				-->
				&nbsp;
			</div>
		</div>
	</div>
	<!--
		This div shows the avatar of the Contacts in the conversation WITHOUT a green dot when it's online
		This div is only visible when:
			- the conversation has more than 2 users in it
		There are maximum 4 avatars shown, which are all 1/4 size of the other avatars
	-->
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
	<!--
		This div shows the displayname of the conversation
	-->
	<span
		displayname
		data-users="{{ conv.users }}"
		class="left avatar-list-displayname"
		ng-class="{bold : conv.new_msg === true}"
		>
	</span>
</div>
<!--
	This div shows the avatars + displayname of the conversation entry
	This div is only visible when:
 		 - the conversation isn active
 		 - AND the conversation has more than 2 users in it
-->
<div
	class="conv-list-item-avatar"
	ng-if="conv.id === $parent.$session.conv && conv.users.length > 2"
	>
	<!-- This ul holds the list with avatars and the buttons -->
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
					online
				>
				</div>
				<div>
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