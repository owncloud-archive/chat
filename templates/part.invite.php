<div  ng-if="view.elements.invite" id="invite-container">
<!--	<div id="invite-container-inner">-->
		<div class="invite-contact-search">
			<input class="filter-field" type="text" placeholder="<?php p($l->t('Search in users'));?>" ng-model="inviteSearch.displayname">
		</div>
		<ul>
			<li
				class="invite-contact"
				ng-repeat="contact in contacts | userFilter | filter:inviteSearch"
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
					class="invite-contact-avatar"
					>
				</div>
				<div class="invite-contact-name">
					{{ contact.displayname }}
				</div>
			</li>
		</ul>
<!--	</div>-->
</div>