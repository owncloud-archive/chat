<?php 
// Fist load all style sheets
\OCP\Util::addStyle('chat', 'bootstrap-stripped');
\OCP\Util::addStyle('chat', 'tagger');
\OCP\Util::addStyle('chat', 'main');

// Second load all dependencies
\OCP\Util::addScript('chat', 'vendor/angular/angular.min');
\OCP\Util::addScript('chat', 'vendor/angular/angular-sanitize');
\OCP\Util::addScript('chat', 'vendor/tagger');
\OCP\Util::addScript('chat', 'vendor/applycontactavatar');


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
\OCP\Util::addScript('chat', 'och/handlers');
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
                    <li ng-class="{ 'conv-list-active' : view.elements.contact }" ng-click="view.unActive();view.show('contact');view.hide('chat');" id="conv-list-new-conv">
                        <div class="icon-add icon-32 left">&nbsp;</div>
                        <div class="left">New Conversation</div>
                    </li>
                    <li ng-class="{heightInvite: view.elements.inviteInput, 'conv-list-active' : conv.id === active.conv }" ng-click="view.makeActive(conv.id, $event, 'invite-button');" ng-repeat="conv in convs" class="conv-list-item" id="conv-list-{{ conv.id }}">
	                <span id="conv-new-msg-{{ conv.id }}" class="conv-new-msg">&nbsp;</span>
                        <div ng-click="view.toggle('invite');view.toggle('chat');" ng-if="conv.id == active.conv" class="icon-add right icon-20 invite-button">
                            &nbsp;
                        </div>
                        <div ng-if="conv.id == active.conv" ng-click="leave(conv.id)" class="icon-close right icon-20" >
                            &nbsp;
	                </div>
                        <div ng-repeat="user in conv.users | userFilter:active.user" class="left"
	                avatar data-size="32" data-id="{{ user.id }}" data-displayname="{{ user.displayname }}" 
	                data-addressbook-backend="{{ user.address_book_backend }}" data-addressbook-id="{{ user.address_book_id  }}"
                        >
	                </div>
	                <span ng-if="conv.users.length == 2" ng-repeat="user in conv.users | userFilter:active.user" class="left" >
	                    {{ user.displayname }}
	                </span>
                    </li>
                </ul>
            </div>
            <div ng-if="view.elements.contact" ng-class="{open: view.elements.settings}" id="app-settings">
        	<div ng-click="view.toggle('settings', $event)" id="app-settings-header">
                    <button class="settings-button">Backend</button>
                </div>
                <div  id="app-settings-content">
                    <ul>
                        <li ng-click="selectBackend(backend)" class="backend" ng-class="{'backend-selected': backend === active.backend}" data-backend="{{ backend.name }}" ng-repeat="backend in backends">
                            {{ backend.displayname }}
                        </li>
                    </ul>
                </div>
            </div>
            <div id="app-content">
        	<div ng-if="view.elements.contact" id="app-header-info">
                    {{ headerInfo }}
        	</div>
	        <div ng-class="{'loading icon-loading': contacts.length == 0}" class="content-info" ng-if="view.elements.contact" >
                    <div class="contact-container" ng-click="newConv(contact)" ng-repeat="contact in contacts | backendFilter:active.backend">
                        <div class="contact" data-size="198" data-id="{{ contact.id }}" data-displayname="{{ contact.displayname }}" data-addressbook-backend="{{ contact.address_book_backend }}" data-addressbook-id="{{ contact.address_book_id  }}" avatar> 
                        </div>
                        <div class="contact-label">
                            {{ contact.displayname }}
			</div>
                    </div>
                </div>
                <div ng-if="view.elements.invite" >
                    <div class="contact-container" ng-click="invite(contact)" ng-repeat="contact in contacts | backendFilter:convs[active.conv].backend">
	                <div class="contact" data-size="198" data-id="{{ contact.id }}" data-displayname="{{ contact.displayname }}" data-addressbook-backend="{{ contact.address_book_backend }}" data-addressbook-id="{{ contact.address_book_id  }}" avatar> 
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
                                    <div data-size="32" data-id="{{ msg.contact.id }}" data-displayname="{{ msg.contact.displayname }}" data-addressbook-backend="{{ msg.contact.address_book_backend }}" data-addressbook-id="{{ msg.contact.address_book_id  }}" avatar>
                                    </div>
                                    <p ng-bind-html="msg.msg">
                                        &nbsp;
                                    </p>
                                </div>
                            </div>
                        </div>
                    </section>
    			<footer id="chat-window-footer">
                            <form id="chat-msg-form" ng-submit="sendChatMsg()"> 
                                <div id="chat-msg-send" >
                                    <button  type="submit"><div class="icon-play">&nbsp;</div></button>
                                </div>
                                <div id="chat-msg-input">
                                    <input ng-focus="" ng-blur="" ng-model="chatMsg" autocomplete="off" type="text"  placeholder="Chat message">
                                </div>
                            </form>
    			</footer>
    		</div>
	    </div>
	</div>
</div>
