<div ng-if="view.elements.files" class="files-no-hide" id="files-container">
		<table>
			<tr class="file-row">
				<th class="file-owner file-element">
					Owner
				</th>
				<th class="file-time file-element">
					Time
				</th>
				<th class="file-path file-element">
					Path
				</th>
				<th>
					Preview
				</th>
			</tr>
			<tr
				class="file-row"
				ng-repeat="file in convs[active.conv].files"
			>
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
				<td>
					<img src='/index.php/core/preview.png?file={{ file.path }}&x=36&y=36&forceIcon=1' >
				</td>
			</tr>
		</table>
</div>