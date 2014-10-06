<div ng-if="view.elements.files" class="files-no-hide" id="files-container">
	<p class="files-title"><?php p($l->t('Files attached to this conversation')); ?></p>
	<table>
		<tr>
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
		</tr>
		<tr
			class="file-row"
			ng-repeat="file in convs[active.conv].files"
		>
			<td class="file-preview">
				<img src='/index.php/core/preview.png?file={{ file.path }}&x=36&y=36&forceIcon=1' >
			</td>
			<td class="file-owner file-element">
				{{ file.user.displayname }}
			</td>
			<td lass="file-time file-element"
				time
				data-timestamp="{{ file.timestamp }}"
			>
			</td>
			<td class="file-path file-element ">
				{{ file.path }}
			</td>
		</tr>
	</table>
	<p
		class="files-title"
		ng-click="view.showFilePicker()"
	>
		<?php p($l->t('Attach more files')); ?>
	</p>
</div>