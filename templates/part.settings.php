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
						{{::backend.displayname }}
					</span>
					<ul>
						<li
							ng-repeat="(key, value) in backend.config"
						>
							<label class="backend-config-label">
								{{::key }}
							</label>
							<input type="text" ng-disabled="$parent.$parent.status === 'saving'" class="backend-config-value" ng-model="$parent.$parent.backend.config[key]" value="{{ value }}">
						</li>
						<li
							ng-repeat="error in backend.configErrors"
							class="setting-error"
						>
							{{ error }}
						</li>
					</ul>
				</il>
			</ul>
			<button ng-if="status === 'saving'" class="primary backend-config-save" disabled="disabled">Saving</button>
			<button ng-if="status !== 'saving'" class="primary backend-config-save">Save</button>
		</form>
	</div>
</div>