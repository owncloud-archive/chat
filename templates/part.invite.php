<!--
	This div holds the invite popup used to add new contacts to a conversation
-->
<div
	invite-mobile
	ng-if="view.elements.invite"
	class="invite-no-hide"
	id="invite-container"
	>
		<!-- Search field to search in the Contacts -->
		<div
			class="invite-contact-search invite-no-hide"
			>
			<input
				type="text"
				id="invite-search-field"
				class="filter-field invite-no-hide"
				type="text"
				placeholder="<?php p($l->t('Search in users'));?>"
				ng-model="inviteSearch.displayname"
				>
		</div>
		<!-- list with contacts -->
		<ul>
			<li
				class="invite-contact invite-no-hide"
				ng-repeat="contact in contacts | userFilter | orderObjectBy:'order':true | filter:inviteSearch | filterUsersInConv"
				ng-click="invite(contact)"
			>
				<div
					data-size="20"
					data-parent="true"
					data-id="{{ contact.id }}"
					data-displayname="{{ contact.displayname }}"
					data-addressbook-backend="{{ contact.address_book_backend }}"
					data-addressbook-id="{{ contact.address_book_id  }}"
					avatar
					class="invite-contact-avatar invite-no-hide"
                    tipsy
                    title="{{ contact.displayname }}"
					>
				</div>
				<div class="invite-contact-name invite-no-hide">
					{{ contact.displayname }}
				</div>
			</li>
		</ul>
</div>