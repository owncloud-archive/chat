angular.module('chat').factory('xmpp', ['convs', 'contacts', 'initvar', 'session', 'authorize', function(convs, contacts, initvar, $session, authorize) {
	var $XMPP = {
		jid: initvar.backends.xmpp.config.jid,
		password: initvar.backends.xmpp.config.password,
		bosh_url: initvar.backends.xmpp.config.bosh_url,
		conn : null,
		/**
		 * Called when we receive a new message
		 * This function adds the message to the correct conversation if it exits otherwise it will create a new conversation first
		 * @param msg XMLDocument
		 * @returns true
		 * */
		onMessage : function(msg){
			var to = msg.getAttribute('to');
			var from = msg.getAttribute('from');
			var type = msg.getAttribute('type');
			var elems = msg.getElementsByTagName('body');

			if (type == "chat" && elems.length > 0) {
				var body = elems[0];
				var convId = Strophe.getBareJidFromJid(from);
				if (!convs.exists(convId)) {
					var contact = contacts.generateTempContact(convId, false, convId,
						[{"id": "xmpp", "displayname": "XMPP", "protocol": "xmpp", "namespace": "xmpp", "value": convId}]);
					contacts.contacts[convId] = contact;
					convs.addConv(convId, [contact, contacts.self()], 'xmpp', [], []);
				}
				convs.addChatMsg(
					convId,
					contacts.findByBackendValue('xmpp', convId),
					Strophe.getText(body),
					Time.now(),
					'xmpp'
				);
			}
			return true;
		},
		/**
		 * This function is called when we are connected and authorized to the XMPP server
		 * This function requests the XMPP roster
		 * This function will call the generateConvs function to initialize the Conversation list
		 * @param status
		 */
		onConnect : function(status){
			if (status == Strophe.Status.CONNECTED) {
				initvar.backends.xmpp.connected = true;

				$XMPP.con.addHandler($XMPP.onMessage, null, 'message', null, null, null);

				// Get roster information
				var iq = $iq({type: "get"}).c('query', {xmlns: "jabber:iq:roster"});
				$XMPP.con.addHandler($XMPP.processRoster, 'jabber:iq:roster', 'iq');
				$XMPP.con.send(iq);
				$XMPP.con.addHandler($XMPP.onPresence, null, "presence")
				$XMPP.con.send($pres());
				$XMPP.generateConvs();

			} else if (status == Strophe.Status.AUTHFAIL){
				// TODO
				alert('auth fail');
			}
		},
		/**
		 * This function process the roster when it's fetched from the XMPP server.
		 * This can happen multiple times during a session. E.g. when a contact
		 * was removed or added to the roster.
		 * Each item in the roster which isn't in the Contacts app will be added
		 * to the Contacts app/DB. It will also subscribe to the presence of
		 * that contact.
		 * @param iq XMLDocument
		 * @returns true
		 */
		processRoster : function (iq) {
			var contactsToAdd = [];
			var contactsToRemove = [];
			$(iq).find('item').each(function () {
				// for each contact in the roster

				var jid = $(this).attr('jid');
				var bareJid = Strophe.getBareJidFromJid(jid);
				var name = $(this).attr('name') || jid;
				var subscription = $(this).attr('subscription');
				if ($XMPP.roster.indexOf(bareJid) === -1 && subscription !== 'remove') {
					$XMPP.roster.push(bareJid);
				}
				// Check if the contact is know
				var contact = contacts.findByBackendValue('xmpp', bareJid);
				if (!contact && subscription !== 'remove') {
					// add contact
					var oContact = {
						"FN": name,
						"IMPP": bareJid
					};
					if(contactsToAdd.indexOf(oContact) === -1){
						contactsToAdd.push(oContact);
					}
				}
			});

			if (contactsToAdd.length > 0) {
				// add the contacts from the roster to the contacts
				contacts.addContacts(contactsToAdd, function () {
					$XMPP.generateConvs();
				});
			}
			return true; // Keep this handler
		},
		/**
		 * This function fetch all contacts which supports XMPP from the contacts app.
		 * And it create conversations with all these contacts.
		 */
		generateConvs : function () {
			var XMPPContacts = contacts.findByBackend('xmpp');
			for (var key in XMPPContacts) {
				var XMPPContact = XMPPContacts[key];
				for (var backendKey in XMPPContact.backends) {
					var backend = XMPPContact.backends[backendKey];
					if (backend.id === 'xmpp') {
						convs.addConv(backend.value, [XMPPContact, contacts.self()], 'xmpp', [], []);
					}
				}
			}
		},
		onRaw : function (data) {
		},
		/**
		 * This function handles three things
		 * 1. a contact asks to subscribe: we ask the user if he want to approve or deny this request
		 * 2. a contact's presence has changed to online
		 * 3. a contact's presence has changed to offline
		 * @param iq XMLDocument
		 * @returns true
		 */
		onPresence : function (iq) {
			var presenceType = $(iq).attr('type');
			var from = $(iq).attr('from');
			var bareJid =  Strophe.getBareJidFromJid(from);
			var contact = contacts.findByBackendValue('xmpp', bareJid);
			var user =  Strophe.getNodeFromJid(from);

			if (contact === false && user === $session.user.id){
				// TODO
				return true;
			}

			if (presenceType === 'subscribe') {
				authorize.jid = bareJid;
				authorize.name = contact.displayname;
				authorize.approve = function () {
					$XMPP.con.send($pres({to: authorize.jid, "type": "subscribed"}));
					$XMPP.con.send($pres({to: authorize.jid, "type": "subscribe"}));
					authorize.show = false;
					authorize.jid = null;
					authorize.name = null;
				};
				authorize.deny = function () {
					$XMPP.con.send($pres({to: authorize.jid, "type": "unsubscribed"}));
					authorize.show = false;
					authorize.jid = null;
					authorize.name = null;;
				};
				authorize.show = true;

			} else if (presenceType !== 'error') {
				if (presenceType === 'unavailable') {
					contacts.markOffline(contact.id)
				} else {
					var show = $(iq).find("show").text();
					if (show === "" || show === "chat") {
						contacts.markOnline(contact.id);
					} else {
						contacts.markOffline(contact.id);
					}
				}
			}
			return true;
		},
		roster : []
	};

	return {
		init : function(){
			//Create connection
			initvar.backends.xmpp.configErrors = []; // Reset all errors
			if (
				$XMPP.jid !== null
				&& $XMPP.jid !== ''
				&& typeof $XMPP.jid !== 'undefined'
				&& $XMPP.password !== null
				&& $XMPP.password !== ''
				&& typeof $XMPP.password !== 'undefined'
				&& $XMPP.bosh_url !== null
				&& $XMPP.bosh_url !== ''
				&& typeof $XMPP.bosh_url !== 'undefined'
			) {
				try {
					// Connect to XMPP sever
					$XMPP.con = new Strophe.Connection($XMPP.bosh_url);
					$XMPP.con.connect($XMPP.jid, $XMPP.password, $XMPP.onConnect);
					$XMPP.con.rawInput = $XMPP.onRaw;
					$XMPP.con.rawOutput = $XMPP.onRaw;
				} catch (e) {
					if (e.name === 'TypeError' && e.message === 'service is undefined') {
						initvar.backends.xmpp.configErrors.push('Service is undefined');
					}
				}
			} else {
				initvar.backends.xmpp.configErrors.push('Fill in all the fields');
			}
		},
		quit : function(){
		},
		sendChatMsg : function(convId, msg){
			var reply = $msg({to: convId, from: $XMPP.jid , type: 'chat'}).c('body', null,msg
			);
			$XMPP.con.send(reply.tree());
		},
		invite : function(convId, userToInvite, groupConv, callback){
		},
		newConv : function(userToInvite, success){
			// add temp contact to the contacts
			var bareJid = Strophe.getBareJidFromJid(userToInvite);
			var contact = contacts.generateTempContact(bareJid, false, bareJid,
				[{"id": "xmpp", "displayname": "XMPP", "protocol": "xmpp", "namespace": "xmpp", "value": bareJid}]);
			contacts.contacts[bareJid] = contact;
			convs.addConv(bareJid, [contact, contacts.self()], 'xmpp', [], []);

		},
		attachFile : function(convId, paths, user){
		},
		removeFile : function(convId, path){
		},
		configChanged : function(){
			$XMPP.jid = initvar.backends.xmpp.config.jid;
			$XMPP.password = initvar.backends.xmpp.config.password;
			$XMPP.bosh_url = initvar.backends.xmpp.config.bosh_url;
			if(
				typeof $XMPP.con !== 'undefined'
				&& $XMPP.con !== null
			) {
				$XMPP.con.disconnect();
			}
			this.init();
		},
		addContactToRoster : function (backendValue) {
			var bareJid = Strophe.getBareJidFromJid(backendValue);
			var contact = contacts.findByBackendValue('xmpp', bareJid);
			var name = contact.displayname;
			// add contact to roster
			var iq = $iq({type: "set"}).c("query", {xmlns: "jabber:iq:roster"})
				.c("item", {jid: bareJid, name: name});
			$XMPP.con.sendIQ(iq);

			var subscribe = $pres({to: bareJid, "type": "subscribe"});
			$XMPP.con.send(subscribe);
			// set subscription for presence
		},
		removeContactFromRoster : function (backendValue) {
			var iq = $iq({type: "set"}).c("query", {xmlns: Strophe.NS.ROSTER}).c("item", {jid: backendValue, subscription: "remove"});
			$XMPP.con.sendIQ(iq);
			// Remove contact from roster
			var index = $XMPP.roster.indexOf(backendValue);
			delete $XMPP.roster[index];
		},
		contactInRoster : function (id) {
			if ($XMPP.roster.indexOf(id) === -1){
				return false;
			} else {
				return true;
			}
		}
	};
}]);
