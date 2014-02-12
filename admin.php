<?php
use \OCA\Chat\DependencyInjection\DIContainer;
$DIContainer = new DIContainer('chat');
$appApi = $DIContainer['AppApi'];


\OCP\User::checkAdminUser();

\OCP\Util::addScript('chat', "oc/admin");

$tmpl = new \OCP\Template('chat', 'admin');

$backends = $appApi->getEnabledBackends();

foreach($backends as &$backend){
	if($backend->getEnabled() === 'true'){
		$backend->setChecked("checked");
	} else {
		$backend->setChecked("");
	}
}


$tmpl->assign('backends', $backends);

return $tmpl->fetchPage();