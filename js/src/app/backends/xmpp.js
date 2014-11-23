angular.module('chat').factory('xmpp', ['convs', 'contacts', 'initvar', function(convs, contacts, initvar) {
	$XMPP = {
		on : {
			chatMessage : function(msg, from){
				var convId = from.substring(0, from.indexOf('/'));
				convs.addChatMsg(
					convId,
					contacts.findByBackendValue('xmpp', convId),
					msg,
					Time.now(),
					'xmpp'
				);
			},
			connected : function(){

			},
			disconnected : function(){

			}
		},
		jid: initvar.backends.xmpp.config.jid,
		password: initvar.backends.xmpp.config.password,
		bosh_url: initvar.backends.xmpp.config.bosh_url,
		conn : null,
		_onMessage : function(msg){
			var to = msg.getAttribute('to');
			var from = msg.getAttribute('from');
			var type = msg.getAttribute('type');
			var elems = msg.getElementsByTagName('body');

			if (type == "chat" && elems.length > 0) {
				var body = elems[0];
				$XMPP.on.chatMessage(Strophe.getText(body), from);
			}
			return true;
		},
		_onConnect : function(status){
			if (status == Strophe.Status.CONNECTING) {
				log('Strophe is connecting.');
			} else if (status == Strophe.Status.CONNFAIL) {
				log('Strophe failed to connect.');
				initvar.backends.xmpp.connected = false;
			} else if (status == Strophe.Status.DISCONNECTING) {
				log('Strophe is disconnecting.');
				initvar.backends.xmpp.connected = false;
			} else if (status == Strophe.Status.DISCONNECTED) {
				log('Strophe is disconnected.');
				initvar.backends.xmpp.connected = false;
			} else if (status == Strophe.Status.CONNECTED) {
				log('Strophe is connected.');
				initvar.backends.xmpp.connected = true;

				$XMPP.con.addHandler($XMPP._onMessage, null, 'message', null, null, null);

				// Get roster information
				var iq = $iq({type: "get"}).c('query', {xmlns: "jabber:iq:roster"});
				$XMPP.con.send(iq);
				$XMPP.con.addHandler($XMPP._processRoster, 'jabber:iq:roster', 'iq');
				$XMPP._generateConvs();

			} else if (status == Strophe.Status.AUTHFAIL){
				// TODO
				alert('auth fail');
			}
		},
		_processRoster : function (iq) {
			var contactsToAdd = [];
			var contactsToRemove = [];
			$(iq).find('item').each(function () {
				// for each contact in the roster

				var jid = $(this).attr('jid');
				console.log('found item in roster with jid ' + jid);
				var bareJid = Strophe.getBareJidFromJid(jid);
				var name = $(this).attr('name') || jid;
				var subscription = $(this).attr('subscription');
			
				// Check if the contact is know
				var contact = contacts.findByBackendValue('xmpp', bareJid);
				if (!contact) {
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
					$XMPP._generateConvs();
				});
			}
			return true; // Keep this handler
		},
		// this function creates conversations with XMPP contacts
		// called after the roster is fetched and processed
		_generateConvs : function () {
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
			console.log(data);
		}
	};

	var log = function(msg){
		console.log(msg);
	}
	Strophe.log = function (level, msg) {
		//console.log(msg);
	};

	return {
		init : function(){
			//$XMPP.
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
					$XMPP.con.connect($XMPP.jid, $XMPP.password, $XMPP._onConnect);
					$XMPP.con.rawInput = $XMPP.onRaw;
					$XMPP.con.rawOutput = $XMPP.onRaw;
				} catch (e) {
					if (e.name === 'TypeError' && e.message === 'service is undefined') {
						initvar.backends.xmpp.configErrors.push('Service is undefined');
					}
					console.log(e.name);
					console.log(e.message);
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
		}
	};
}]);
