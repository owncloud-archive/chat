<?php
// Fist load all style sheets
\OCP\Util::addStyle('chat', 'main');

// Second load all dependencies
\OCP\Util::addScript('chat', 'vendor/angular/angular.min');
\OCP\Util::addScript('chat', 'vendor/angular/angular-sanitize');
\OCP\Util::addScript('chat', 'vendor/applycontactavatar');
\OCP\Util::addScript('chat', 'vendor/online');
\OCP\Util::addScript('chat', 'vendor/angular-enhance-text.min');
\OCP\Util::addScript('chat', 'vendor/rangyinputs');
\OCP\Util::addScript('chat', 'vendor/jquery.autosize.min');



// Third load the Chat object definition
\OCP\Util::addScript('chat', 'chat');

// Fourth load all Chat sub objects
\OCP\Util::addScript('chat', 'app/chat.app');
\OCP\Util::addScript('chat', 'app/chat.app.ui');
\OCP\Util::addScript('chat', 'app/chat.app.view');
\OCP\Util::addScript('chat', 'app/chat.app.util');
\OCP\Util::addScript('chat', 'app/chat.app.cache');


/***************************/
// Here all backend files will be loaded
\OCP\Util::addScript('chat', 'och/chat.och');
\OCP\Util::addScript('chat', 'och/chat.och.on');
\OCP\Util::addScript('chat', 'och/chat.och.util');
\OCP\Util::addScript('chat', 'och/chat.och.api');
/***************************/

// Fifth load all angular files
\OCP\Util::addScript('chat', 'app/app');
\OCP\Util::addScript('chat', 'app/controllers/convcontroller');

// At last load the main handlers file, this will boot up the Chat app
\OCP\Util::addScript('chat', 'handlers');

?>
<div ng-click="view.hide('settings', $event, 'backend')" ng-controller="ConvController" ng-app="chat" id="app">
	<div style="display:none;" id="initvar">
		<?php echo $_['initvar']?>
	</div>
	<div class="icon-loading" id="main-panel" ng-if="!initDone">
		&nbsp;
	</div>
		<div ng-if="initDone" id="main-panel" >
			<div id="app-navigation" >
				<ul>
					<li ng-class="{ 'conv-list-active' : view.elements.newConv }" ng-click="view.unActive();view.show('newConv');view.hide('chat');" id="conv-list-new-conv">
						<div class="icon-add icon-32 left">&nbsp;</div>
						<div class="left">New Conversation</div>
					</li>
					<li
						ng-class="{heightInvite: view.elements.inviteInput, 'conv-list-active' : conv.id === active.conv }" 
						ng-click="view.makeActive(conv.id, $event, 'invite-button');" 
						ng-repeat="conv in convs" 
						class="conv-list-item" 
						id="conv-list-{{ conv.id }}"
					>
						<div class="conv-list-item-avatar">
							<div class="avatar-list-container"  title="{{ user.displayname }}" ng-if="key < 4" ng-repeat="(key, user) in conv.users | userFilter">
								<div
									class="left"
									avatar
									data-onlinesize="5"
									data-size="32"
									data-id="{{ user.id }}"
									data-displayname="{{ user.displayname }}"
									data-addressbook-backend="{{ user.address_book_backend }}"
									data-addressbook-id="{{ user.address_book_id  }}"
								>
								</div>
							</div>
							<div title="{{ conv.users.length - 4 }} users are hidden" ng-if="conv.users.length > 5" class="avatar-list-more">
								+ {{ conv.users.length - 4 }}
							</div>
						</div>
						<div class="conv-list-item-buttons">
							<div ng-click="view.toggle('invite');view.toggle('chat');" ng-if="conv.id == active.conv" class="icon-add right icon-20 invite-button">
								&nbsp;
							</div>
							<div ng-if="conv.id == active.conv" ng-click="leave(conv.id)" class="icon-close right icon-20" >
								&nbsp;
							</div>
						</div>
					</li>
				</ul>
			</div>
<!-- 			<div ng-if="view.elements.contact" ng-class="{open: view.elements.settings}" id="app-settings"> -->
<!-- 			<div ng-click="view.toggle('settings', $event)" id="app-settings-header"> -->
<!-- 					<button class="settings-button">Backend</button> -->
<!-- 				</div> -->
<!-- 				<div  id="app-settings-content"> -->
<!-- 					<ul> -->
<!-- 						<li ng-click="selectBackend(backend)" class="backend" ng-class="{'backend-selected': backend === active.backend}" data-backend="{{ backend.name }}" ng-repeat="backend in backends"> -->
<!-- 							{{ backend.displayname }} -->
<!-- 						</li> -->
<!-- 					</ul> -->
<!-- 				</div> -->
<!-- 			</div> -->
			<div id="app-content">
			<div ng-if="view.elements.newConv" id="controls">
				<div class="center">
					<button ng-click="newConv()" >
						Chose some contacts and start conversation
					</button>
				</div>
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
            <div ng-if="view.elements.invite" id="controls">
                <div class="center">
                    Chose some contacts and start conversation
                </div>
            </div>
            <div
                ng-class="{'loading icon-loading': contacts.length == 0}"
                class="content-info"
                ng-if="view.elements.invite"
                >
                <div
                    class="contact-container"
                    ng-repeat="contact in contacts | backendFilter:active.backend | userFilter"
                    ng-click="invite(contact)"
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
			<div ng-if="view.elements.chat" >
					<section ng-click="focusMsgInput()" id="chat-window-body">
						<div id="chat-window-msgs">
							<div class="chat-msg-container" ng-repeat="msg in convs[active.conv].msgs">
								<div ng-if="msg.time" class="chat-msg-time">
										{{ msg.time.hours }} : {{ msg.time.minutes }}
								</div>
								<div class="chat-msg">
									<div class="msg-avatar-container" >
										<div data-size="32" data-onlinesize="5" data-id="{{ msg.contact.id }}" isonline="{{ $parent.$parent.contactsObj[msg.contact.id].online }}" data-displayname="{{ msg.contact.displayname }}" data-addressbook-backend="{{ msg.contact.address_book_backend }}" data-addressbook-id="{{ msg.contact.address_book_id  }}" avatar>
										</div>
									</div>
									<p class="chat-msg-msg" ng-bind-html="msg.msg | enhanceText">
										&nbsp;
									</p>
								</div>
							</div>
						</div>
					</section>
				<footer id="chat-window-footer">
							<div class="chat-msg-button" >
								<button ng-click="view.toggle('emojiContainer')" ><img src="/apps/chat/img/emoji/smile.png"></button>
							</div>
							<form id="chat-msg-form" ng-submit="sendChatMsg()">
								<div class="chat-msg-button" >
									<button  type="submit"><div class="icon-play">&nbsp;</div></button>
								</div>
								<div id="chat-msg-input">
									<textarea  id="chat-msg-input-field" autocomplete="off" ng-model="fields.chatMsg"  ng-enter="sendChatMsg()" placeholder="Chat message"></textarea>	
								</div>
							</form>
				</footer>
				<div ng-if="view.elements.emojiContainer" id="emoji-container">
 				 	<input placeholder="Search" id="emoji-search" type="text" ng-model="emojiSearch">
					<ul id="emoji-list">
						<li ng-click="addEmoji(emoji.name)" ng-repeat="emoji in emojis | filter:emojiSearch">
							<img title="{{ emoji.name }}" src="{{ emoji.url }}" class="ec-emoji">
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
	</div>