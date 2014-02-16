<?php 
// Fist load all style sheets
\OCP\Util::addStyle('chat', 'bootstrap-stripped');
\OCP\Util::addStyle('chat', 'tagger');
\OCP\Util::addStyle('chat', 'main');

// Second load all dependencies
\OCP\Util::addScript('chat', 'vendor/angular/angular.min');
\OCP\Util::addScript('chat', 'vendor/angular/angular-sanitize');
\OCP\Util::addScript('chat', 'vendor/tagger');

// Third load the Chat object definition
\OCP\Util::addScript('chat', 'chat');

// Fourth load all Chat sub objects
\OCP\Util::addScript('chat', 'app/chat.app');
\OCP\Util::addScript('chat', 'app/chat.app.ui');
\OCP\Util::addScript('chat', 'app/chat.app.view');
\OCP\Util::addScript('chat', 'app/chat.app.util');

/***************************/
// Here all backend files will be loaded
\OCP\Util::addScript('chat', 'och/chat.och');
\OCP\Util::addScript('chat', 'och/chat.och.on');
\OCP\Util::addScript('chat', 'och/chat.och.util');
\OCP\Util::addScript('chat', 'och/chat.och.api');
\OCP\Util::addScript('chat', 'och/handlers');
/***************************/

// Fifth load all angular files
\OCP\Util::addScript('chat', 'app/app');
\OCP\Util::addScript('chat', 'app/controllers/convcontroller');

// At last load the main handlers file, this will boot up the Chat app
\OCP\Util::addScript('chat', 'handlers');
?>
<div ng-click="view.hide('settings', $event, 'backend')" ng-controller="ConvController" ng-app="chat" id="app">
    <div class="icon icon-loading" id="main-panel" ng-if="!initDone">
        &nbsp;
    </div>
	<div ng-if="initDone" id="main-panel" >
		<div id="app-navigation" >
			<ul>
				<li id="conv-list-new-conv">
				    <form ng-submit="newConv(userToInvite)">
				        <div tagger ng-model="userToInvite" options="contactsList" single disable-new id="new-conv-input" class="tagger-input" placeholder="Contact name" type="text" >
				        </div>
				        <button id="new-conv-button" class="primary tagger-button">
	                    	<div class="icon icon-add icon-20">&nbsp;</div>
	                    </button>
	            	</form>
				</li>
				<li ng-class="{heightInvite: view.elements.inviteInput, 'conv-list-active' : conv.id === activeConv }" ng-click="view.makeActive(conv.id)" ng-repeat="conv in convs" class="conv-list-item" id="conv-list-{{ conv.id }}">
	                            <span id="conv-new-msg-{{ conv.id }}" class="conv-new-msg">&nbsp;</span>
	                            <div ng-click="view.toggle('inviteInput')" ng-if="conv.id == activeConv" class="icon icon-add right icon-20">
	                                &nbsp;
	                            </div>
	                            <div ng-if="conv.id == activeConv" ng-click="leave(conv.id)" class="icon icon-close right icon-20" >
	                                &nbsp;
	                            </div>
	                            <div ng-repeat="user in conv.users | filter:'!' + currentUser" class="left" avatar data-size="32" data-user="{{ user }}">
	                                
	                            </div>
	                <span ng-if="conv.users.length == 2" ng-repeat="user in conv.users | filter:'!' + currentUser" class="left" >
	                    {{ user }}
	                </span>
	                <div style="clear:both;"></div>
	                <div id="invite-container" ng-if="view.elements.inviteInput">
	                    <form ng-submit="invite(userInvite, conv.id)">
	                        <div tagger ng-model="userInvite" options="contactsList" single disable-new id="new-conv-invite" class="tagger-input" placeholder="Contact name" type="text" >
	    			        </div>
	    			        <button id="new-conv-button" class="primary tagger-button">
	    		                <div class="icon icon-add icon-20">&nbsp;</div>
	    	    	        </button>
	                    </form>
	                </div>
			    </li>
			</ul>
        </div>
        <div ng-class="{open: view.elements.settings}" id="app-settings">
        	<div ng-click="view.toggle('settings', $event)" id="app-settings-header">
				<button class="settings-button">Backend</button>
			</div>
			<div  id="app-settings-content">
				<ul>
					<li ng-click="selectBackend(backend)" class="backend" ng-class="{'backend-selected': backend.name === selectedBackend.name}" data-backend="{{ backend.name }}" ng-repeat="backend in backends">
						{{ backend.displayname }}
					</li>
				</ul>
			</div>
       	</div>
        <div id="app-content">
	        <div ng-class="{'icon loading icon-loading': contacts.length == 0}" ng-if="view.elements.contact" >
                <div  ng-click="newConv(contact.displayname)" ng-repeat="contact in contacts | backendFilter:selectedBackend" class="contact" style="background-image: url(/index.php/apps/contacts/addressbook/local/1/contact/{{ contact.id }}/photo);">
                    <label>
                        {{ contact.displayname }}
                    </label>
                </div>
    	    </div>
        	<div ng-if="view.elements.chat" >
    			<section ng-click="focusMsgInput()" id="chat-window-body">
    				<div id="chat-window-msgs">
    					<div class="chat-msg-container" ng-repeat="msg in convs[activeConv].msgs">
    						<div ng-if="msg.time" class="chat-msg-time">
    							{{ msg.time.hours }} : {{ msg.time.minutes }}
    						</div>
    						<div class="chat-msg">
    							<div data-user="{{ msg.user }}" data-backend="{{ this.$parent.backend }}" data-size="32" avatar>
    							</div>
    							<p ng-bind-html="msg.msg">
                                    placeholder
    							</p>
    						</div>
    					</div>
    				</div>
    			</section>
    			<footer id="chat-window-footer">
    				<form ng-submit="sendChatMsg()"> 
    					<input ng-focus="" ng-blur="" ng-model="chatMsg" autocomplete="off" type="text" id="chat-msg-input" placeholder="Chat message">
    					<input id="chat-msg-send" type="submit"  value="Send" />
    				</form>
    			</footer>
    		</div>
	    </div>
	</div>
</div>
