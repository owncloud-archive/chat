<!--
	This div holds the content of the active conversation
-->
<div ng-if="view.elements.chat" >
	<!--
		This section holds the message list
	-->
	<div class="nano" nano >
		<div id="chat-wrapper" class="nano-content">
			<div
				ng-repeat="(key,msg) in msgs"
				class="chat-row"
				ng-class="{'height-40': msg.contact.id !== msgs[key-1].contact.id && msg.contact.id !== msgs[key+1].contact.id}"
				>
				<div
					class="chat-msg column"
					ng-bind-html="msg.msg | enhanceFiles | emoji"
					>
					&nbsp;
				</div>
				<div
					class="chat-avatar column"
					ng-if="$parent.$parent.avatarsEnabled === 'true'"
					>
					<div
						ng-hide="msg.contact.id === msgs[key-1].contact.id"
						data-size="40"
						data-id="{{::msg.contact.id }}"
						data-displayname="{{::msg.contact.displayname }}"
						data-addressbook-backend="{{::msg.contact.address_book_backend }}"
						data-addressbook-id="{{::msg.contact.address_book_id }}"
						avatar
						tipsy
						title="{{::msg.contact.displayname }}"
						>
					</div>
				</div>
				<div
					class="chat-time column"
					>
					{{::msg.time.hours }} : {{::msg.time.minutes }}
				</div>
			</div>
		</div>
	</div>
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
					placeholder="<?php p($l->t('Chat Message')); ?>"
					update
					update-func="sendChatMsg"
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
				ng-model-options="{ updateOn: 'default', debounce: {'default': 500} }"
				class="emoji-no-hide"
				>
				<div
					title="{{::emoji.key }}"
					class="emojione-{{::emoji.value.toUpperCase() }} emoji-no-hide"
					>
				</div>
			</li>
		</ul>
	</div>
</div>
