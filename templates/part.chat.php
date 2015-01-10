<!--
	This div holds the content of the active conversation
-->
<div ng-if="view.elements.chat" >
	<!--
		This section holds the message list
	-->
	<section ng-click="focusMsgInput()" id="chat-window-body">
		<div id="chat-window-msgs" scroll>
			<!-- This div holds exactly one chat message	-->
			<div
				class="chat-msg-container"
				ng-repeat="(key, msg) in convs[$session.conv].msgs | orderBy:'timestamp'"
			>
				<!-- This div holds the time of the chat message -->
				<div
					ng-if="$parent.convs[$parent.$session.conv].msgs[key-1].contact.id !== msg.contact.id"
					class="chat-msg-time"
				>
					{{ msg.time.hours }} : {{ msg.time.minutes }}
				</div>
				<!-- This div holds the Chat message and the avatar of the user which sends it-->
				<div class="chat-msg">
					<div
						class="msg-avatar-container"
						ng-if="$parent.$parent.avatarsEnabled === 'true'"
					>
						<div
							ng-if="$parent.convs[$parent.$session.conv].msgs[key-1].contact.id !== msg.contact.id "
							data-size="40"
							data-id="{{ msg.contact.id }}"
							data-displayname="{{ msg.contact.displayname }}"
							data-addressbook-backend="{{ msg.contact.address_book_backend }}"
							data-addressbook-id="{{ msg.contact.address_book_id  }}"
							avatar
                            tipsy
                            title="{{ msg.contact.displayname }}"
						>
						</div>
					</div>
					<div
						class="msg-displayname-container"
						ng-if="$parent.$parent.avatarsEnabled === 'false' && $parent.convs[$parent.$session.conv].msgs[key-1].contact.id !== msg.contact.id "
						>
						<div>
							{{ msg.contact.displayname }}
						</div>
					</div>
					<p
						ng-class="{'chat-msg-margin-left': $parent.convs[$parent.$session.conv].msgs[key-1].contact.id === msg.contact.id}"
						class="chat-msg-msg"
						ng-bind-html="msg.msg | enhanceFiles | emoji | enhanceText"
						>
						&nbsp;
					</p>
				</div>
			</div>
		</div>
	</section>
	<!--
		This element holds the Chat message input field, and the buttons to active the file picker and emoji picker
	-->
	<footer id="chat-window-footer">
		<form id="chat-msg-form" ng-submit="sendChatMsg()">
			<div
				ng-click="view.showFilePicker()"
				class="icon-file chat-msg-file-button"
			>
				&nbsp;
			</div>
			<div
				ng-click="view.showEmojiPopover()"
				class="chat-msg-emoji-button emoji-no-hide emojione-1F603"
				>
				&nbsp;
			</div>
			<div class="chat-msg-send-button" >
				<button  type="submit">
					<div class="icon-play">
						&nbsp;
					</div>
				</button>
			</div>
			<div id="chat-msg-input">
				<textarea
					id="chat-msg-input-field"
					autocomplete="off"
					ng-model="fields.chatMsg"
					ng-enter="sendChatMsg()"
					placeholder="<?php p($l->t('Chat Message')); ?>"
					ng-disabled="$parent.backends[$parent.convs[$parent.$session.conv].backend.id].connected !== true"
				></textarea>
			</div>
		</form>
	</footer>
	<!--
		This div holds the emoji-picker only when it's active
	-->
	<div
		ng-if="view.elements.emojiContainer"
		id="emoji-container"
		class="emoji-no-hide"
		>
		<div
			id="emoji-container-search"
            class="emoji-no-hide"
		>
			<input
				placeholder="Search"
				id="emoji-search"
				type="text"
				class="emoji-no-hide"
				ng-model="emojiSearch"
				>
		</div>
		<ul
			id="emoji-list"
			class="emoji-no-hide"
			>
			<li
				ng-click="addEmoji(emoji.key)"
				ng-repeat="emoji in emojis | filter:emojiSearch"
				class="emoji-no-hide"
				>
				<div
					title="{{ emoji.key }}"
					class="emojione-{{ emoji.value.toUpperCase() }} emoji-no-hide"
					>
				</div>
			</li>
		</ul>
	</div>
</div>