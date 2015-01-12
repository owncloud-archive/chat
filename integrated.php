<?php
header('Content-Type: text/html');
?>
<section id="app" 	ng-controller="ConvController">
	<div
		ng-class="{'chat-sidebar-toggle-visible':view.elements.sidebar, 'chat-sidebar-toggle-hidden':!view.elements.sidebar}"
		class="chat-sidebar-toggle-visible"
		id="chat-sidebar-toggle"
		ng-click="view.toggle('sidebar')"
		>
	</div>
	<div
		id="chat-sidebar"
		ng-show="view.elements.sidebar"
		>
		<div id="filter-container">
			<!-- Field to start an instant chat via XMPP -->
			<form ng-submit="view.startInstantChat()">
				<input type="text" placeholder="" ng-model="fields.jid" >
			</form>
			<!-- Field to search in the conversations -->
			<input class="filter-field" type="text" placeholder="" ng-model="search.name">
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
					<!--
						This div shows the avatar of the Contacts in the conversation WITHOUT a green dot when it's online
						This div is only visible when:
							- the conversation has more than 2 users in it
						There are maximum 4 avatars shown, which are all 1/4 size of the other avatars
					-->
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
				<!--
					This div shows the avatars + displayname of the conversation entry
					This div is only visible when:
						  - the conversation is active
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
							<div
								class="online-dot-container"
								ng-if="$parent.$parent.avatarsEnabled === 'true'"
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
							<div
								ng-if="$parent.$parent.avatarsEnabled === 'false'"
								online
								data-id="{{ user.id }}"
								class="online-dot-displayname-nav online-dot-displayname-nav-expanded"
								>
								<!--
								This is a place holder div for the green dot which is used to indicate the online status of the contact
								-->
								&nbsp;
							</div>
							<div class="avatar-list-displayname">
								{{ user.displayname }}
							</div>
						</li>
						<li
							class="avatar-list-expanded-button invite-button"
							ng-click="$parent.view.inviteClick();"
							>
							<div
								class="icon-add icon-20 avatar-list-avatar  expanded-icon">
								&nbsp;
							</div>
							Add person
						</li>
						<li
							class="avatar-list-expanded-button  files-button"
							ng-click="$parent.view.showFiles()"
							>
							<div
								class="icon-file icon-20 avatar-list-avatar expanded-icon"
								>
								&nbsp;
							</div>
							View attached files
						</li>
					</ul>
				</div>


<!--				 These buttons are shown on the right side of the enty -->
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
	</div>
</section>