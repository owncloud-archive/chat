<div ng-if="view.elements.files" class="files-no-hide" id="files-container">
	<p class="files-title file-element"><?php p($l->t('Files attached to this conversation')); ?></p>
	<table>
		<tr class="file-element">
			<th class="file-preview">
				&nbsp;
			</th>
			<th class="file-owner file-element">
				Owner
			</th>
			<th class="file-time file-element">
				Time
			</th>
			<th class="file-path file-element">
				Path
			</th>
			<th class="file-unshare file-element">
				Unshare
			</th>
		</tr>
		<tr
			class="file-row file-element"
			ng-repeat="(key,file) in convs[active.conv].files"
		>
			<td class="file-preview file-element">
				<img class="file-element" src='/index.php/core/preview.png?file={{ file.path }}&x=40&y=40&forceIcon=1' >
			</td>
			<td class="file-owner file-element">
				<div
					class="file-element"
					tipsy
					title="{{ file.user.displayname }}"
					class="avatar-list-avatar"
					avatar
					data-size="40"
					data-id="{{ file.user.id }}"
					data-displayname="{{ file.user.displayname }}"
					data-addressbook-backend="{{ file.user.address_book_backend }}"
					data-addressbook-id="{{ file.user.address_book_id  }}"
					online
				>
				</div>
			</td>
			<td class="file-time file-element"
				time
				data-timestamp="{{ file.timestamp }}"
			>
			</td>
			<td title="<?php p($l->t('Download ')); ?>{{ file.path }}" tipsy class="file-path file-element ">
				<p class="file-element" ng-click="$parent.$parent.view.downloadFile(file.path)">
					{{ file.path }}
				</p>
			</td>
			<th class="file-unshare file-element">
				<div
                    ng-if="file.user.id === $parent.$parent.active.user.id"
					ng-click="view.unShare(active.conv, file.path, file.timestamp, file.user, key)"
					class="files-no-hide icon-delete"
				>
					&nbsp;
				</div>
			</th>
		</tr>
	</table>
	<p
		class="files-title files-no-hide"
		ng-click="view.showFilePicker()"
	>
		<?php p($l->t('Attach more files')); ?>
	</p>
</div>