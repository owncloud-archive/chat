<!-- XMPP Specific code -->
<xmpp-authorize
	id="xmpp-authorize-container"
	ng-show="authorize.show === true"
	auto-center
	ng-cloak

	>
	{{ authorize.name}} ({{authorize.jid}}) wants to add you to his or her buddy list.

	<button
		class="approve"
		ng-click="authorize.approve()"
		ng-enter="authorize.approve()"
		autofocus="autofocus"
	>
		Approve
	</button>
	<button
		class="deny"
		ng-click="authorize.deny()"
		ng-enter="authorize.approve()"
	>
		Deny
	</button>
</xmpp-authorize>