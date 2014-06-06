<div ng-if="view.elements.newConv" id="controls">
	<p>
		Chose a contact and start a conversation
	</p>
</div>
<div
	ng-class="{'loading icon-loading': contacts.length == 0}"
	class="content-info"
	ng-if="view.elements.newConv"
	>
	<div
		class="contact-container"
		ng-repeat="contact in contacts | backendFilter:active.backend | userFilter"
		ng-click="startNewConv(contact)"
		>
		<div
			class="contact"
			data-size="200"
			data-onlinesize="20"
			data-parent="true"
			data-id="{{ contact.id }}"
			data-displayname="{{ contact.displayname }}"
			data-addressbook-backend="{{ contact.address_book_backend }}"
			data-addressbook-id="{{ contact.address_book_id  }}"
			avatar
			>
		</div>
		<div class="contact-label">
			{{ contact.displayname }}
		</div>
	</div>
</div>