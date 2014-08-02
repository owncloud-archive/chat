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
			<button ng-click="view.toggle('emojiContainer')" ><img src=" <?php echo \OCP\Util::imagePath('chat', 'emoji/smile.png'); ?>"></button>
		</div>
		<form id="chat-msg-form" ng-submit="sendChatMsg()">
			<div class="chat-msg-button" >
				<button  type="submit"><div class="icon-play">&nbsp;</div></button>
			</div>
			<div id="chat-msg-input">
				<textarea  id="chat-msg-input-field" autocomplete="off" ng-model="fields.chatMsg"  ng-enter="sendChatMsg()" placeholder="<?php p($l->t('Chat Message')); ?>"></textarea>
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