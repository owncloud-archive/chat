<?php 
\OCP\Util::addStyle('chat', 'bootstrap-stripped');
\OCP\Util::addScript('chat', 'vendor/angular/angular.min');
\OCP\Util::addScript('chat', 'vendor/angular/angular-sanitize');
\OCP\Util::addStyle('chat', 'main');
\OCP\Util::addScript('chat', 'classes');
\OCP\Util::addScript('chat', 'main');
\OCP\Util::addScript('chat', 'handlers');

?>
<div ng-controller="ConvController" ng-app="myApp" id="app">
	<div id="loading-panel" class="panel">
		<div id="loading-panel-img">
			&nbsp;
		</div>
	</div>
	<div id="main-panel" class="panel">
		<ul id="app-navigation" >
			<li ng-click="newConvShow()" id="new-conv"> 
				New Conversation
			</li>
			<li ng-click="makeActive(conv.id)" ng-repeat="conv in convs" class="conv-list-item" id="conv-list-{{ conv.id }}">
         		<span id="conv-new-msg-{{ conv.id }}" class="conv-new-msg">&nbsp;</span>
    			<span ng-click="leaveShow(conv.id)" class="icon-leave" title="Leave Conversation">X</span>
				<span ng-click="inviteShow(conv.id)" class="icon-plus" title="Invite Person to Conversation">+</span>
                <div ng-repeat="user in conv.users | filter:'!' + currentUser" class="icon-list icon-{{ user }}"></div>
                <div style="clear:both;"></div>
                <span ng-repeat="user in conv.users | filter:'!' + currentUser" class="icon-list" >
                    {{ user }}
                </span>
		    </li>
            <div id="popover" class="popover right animation-fade" style="top: {{ popover.top }}px; left: 274px;">
                <div class="arrow"></div>
                <h3 class="popover-title">{{ popover.title }}</h3>
                <div ng-if="popover.button === false" class="popover-content">
                   	<form ng-submit="popoverSubmit('submit')"> 
            		    <input id="popover-value" ng-model="popover.value" type="text" placeholder="{{ popover.placeholder }}">
            		    <input type="submit" value="{{ popover.submit }}" />
            		</form>
                </div>
                <div ng-if="popover.button === true" class="popover-content">
                    <button ng-click="popoverSubmit('no')">No</button>
                    <button ng-click="popoverSubmit('yes')">Yes</utton>
                </div>
            </div>
		</ul>
        <div id="app-content">
	        <div id="empty-window" class="panel">
    		    <div>
    	    	    Start Chatting!
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
	<div id="debug-panel" class="panel">
	    <ul>
	       <li ng-repeat="msg in debug">
	            {{ msg }}
	       </li>
	    </ul>
	    
    </div>
</div>