<div id="app-settings">
	<div id="app-settings-header">
		<button class="settings-button"
				data-apps-slide-toggle="#app-settings-content"
			></button>
	</div>
	<div ng-controller="SettingsController" id="app-settings-content">
		<form ng-submit="save()">
			<ul>
				<il
					ng-repeat="backend in backends"
					ng-if="(backend.config | count) !== 0"
				>
					<span class="backend-config-header">
						{{ backend.displayname }}
					</span>
					<ul>
						<li
							ng-repeat="(key, value) in backend.config"
						>
							<label class="backend-config-label">
								{{ key }}
							</label>
							<input type="text" class="backend-config-value" ng-model="$parent.$parent.backend.config[key]" value="{{ value }}">
						</li>
					</ul>
				</il>
			</ul>
			<button class="primary backend-config-save">Save</button>
		</form>
	</div>
</div>