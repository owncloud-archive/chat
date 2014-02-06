<?php 
\OCP\Util::addStyle('chat', 'bootstrap-stripped');
\OCP\Util::addScript('chat', 'vendor/angular/angular.min');
\OCP\Util::addScript('chat', 'vendor/angular/angular-sanitize');
\OCP\Util::addScript('chat', 'vendor/tagger');
\OCP\Util::addStyle('chat', 'tagger');

\OCP\Util::addStyle('chat', 'main');
\OCP\Util::addScript('chat', 'classes');
\OCP\Util::addScript('chat', 'app/app');
\OCP\Util::addScript('chat', 'app/controllers/convcontroller');
\OCP\Util::addScript('chat', 'handlers');
?>
<div ng-controller="ConvController" ng-app="chat" id="app">
	<div id="loading-panel" class="icon icon-loading"  class="panel">
        &nbsp;
    </div>
	<div id="main-panel" class="panel">
		<ul id="app-navigation" >
			<li id="conv-list-new-conv">
			    <form ng-submit="newConv(userToInvite)">
			        <div tagger ng-model="userToInvite" options="contactsList" single disable-new id="new-conv-input" class="tagger-input" placeholder="Contact name" type="text" >
			        </div>
			        <button id="new-conv-button" class="primary tagger-button">
		                <div class="icon icon-add icon-20">&nbsp;</div>
	    	        </button>
		    	</form>
			</li>
			<li ng-class="{heightInvite: showElements.inviteInput }" ng-click="makeActive(conv.id)" ng-repeat="conv in convs" class="conv-list-item" id="conv-list-{{ conv.id }}">
         		<span id="conv-new-msg-{{ conv.id }}" class="conv-new-msg">&nbsp;</span>
    			<div ng-click="toggle('inviteInput')" ng-if="conv.id == activeConv" class="icon icon-add right icon-20">
                    &nbsp;
		        </div>
				<div ng-if="conv.id == activeConv" ng-click="leave(conv.id)" class="icon icon-close right icon-20" >
				    &nbsp;
                </div>
                <div ng-repeat="user in conv.users | filter:'!' + currentUser" class="left icon-{{ user }}"></div>
                <span ng-if="conv.users.length == 2" ng-repeat="user in conv.users | filter:'!' + currentUser" class="left" >
                    {{ user }}
                </span>
                <div style="clear:both;"></div>
                <div id="invite-container" ng-if="showElements.inviteInput">
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
        <div id="app-content">
	        <div id="empty-window" class="panel">
                <div ng-click="newConv(contact.displayname)" ng-repeat="contact in contacts" class="contact" style="background-image: url(/index.php/apps/contacts/addressbook/local/1/contact/{{ contact.id }}/photo);">
                    <label>
                        {{ contact.displayname }}
                    </label>
                </div>
    	    </div>
        	<div id="chat-window">
    			<section ng-click="focusMsgInput()" id="chat-window-body">
    				<div id="chat-window-msgs">
    					<div class="chat-msg-container" ng-repeat="msg in convs[activeConv].msgs">
    						<div ng-if="msg.time" class="chat-msg-time">
    							{{ msg.time.hours }} : {{ msg.time.minutes }}
    						</div>
    						<div class="chat-msg">
    							<div class="icon-{{ msg.user }}">
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