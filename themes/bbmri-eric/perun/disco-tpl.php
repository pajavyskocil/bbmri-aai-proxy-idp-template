<?php

/**
 * This is simple example of template for perun Discovery service
 *
 * Allow type hinting in IDE
 * @var sspmod_perun_DiscoTemplate $this
 */

$this->data['jquery'] = array('core' => TRUE, 'ui' => TRUE, 'css' => TRUE);

$this->data['head'] = '<link rel="stylesheet" media="screen" type="text/css" href="' . SimpleSAML\Module::getModuleUrl('discopower/style.css')  . '" />';
$this->data['head'] .= '<link rel="stylesheet" media="screen" type="text/css" href="' . SimpleSAML\Module::getModuleUrl('bbmri/res/css/disco.css')  . '" />';

$this->data['head'] .= '<script type="text/javascript" src="' . SimpleSAML\Module::getModuleUrl('discopower/js/jquery.livesearch.js')  . '"></script>';
$this->data['head'] .= '<script type="text/javascript" src="' . SimpleSAML\Module::getModuleUrl('discopower/js/suggest.js')  . '"></script>';

$this->data['head'] .= searchScript();



$this->includeAtTemplateBase('includes/header.php');



if (!empty($this->getPreferredIdp())) {

	echo '<p class="descriptionp">your previous selection</p>';
	echo '<div class="metalist list-group">';
	echo showEntry($this, $this->getPreferredIdp(), true);
	echo '</div>';


	echo getOr();
}




echo '<div class="row">';
foreach ($this->getIdps('social') AS $idpentry) {

	echo '<div class="col-md-4">';
	echo '<div class="metalist list-group">';
	echo showEntry($this, $idpentry, false);
	echo '</div>';
	echo '</div>';

}
echo '</div>';



echo getOr();



echo '<p class="descriptionp">';
echo 'your institutional account';
echo '</p>';

echo '<div class="inlinesearch">';
echo '	<form id="idpselectform" action="?" method="get">
			<input class="inlinesearchf form-control input-lg" placeholder="Type the name of your institution" 
			type="text" value="" name="query" id="query" autofocus oninput="document.getElementById(\'list\').style.display=\'block\';"/>
		</form>';
echo '</div>';


echo '<div class="metalist list-group" id="list">';
foreach ($this->getIdps() AS $idpentry) {
	echo showEntry($this, $idpentry, false);
}
echo '</div>';


echo '<br>';
echo '<br>';


echo '<div class="no-idp-found alert alert-info">';
if ($this->isOriginalSpNonFilteringIdPs()) {
	echo 'Still can\'t find your institution? Create an account at Hostel by clicking the button below or contact us at <a href="mailto:aai-infrastructure@lists.bbmri-eric.eu?subject=Request%20for%20adding%20new%20IdP">aai-infrastructure@lists.bbmri-eric.eu</a>';
	echo '<div class="metalist list-group">';
	echo '<a class="btn btn-block social" href="https://adm.hostel.eduid.cz/registrace/k1" style="background: #43554a">';
	echo '<img src="data:image/png;base64,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">';
	echo '<strong>Create account at Hostel</strong>';
	echo '</a>';
	echo '</div>';


} else {
	echo 'Can\'t find your institution? Select it in extended list and help us <a class="btn btn-primary" href="https://perun.bbmri-eric.eu/add-institution/">add your institution</a>';
}
echo '</div>';





$this->includeAtTemplateBase('includes/footer.php');








function searchScript() {

	$script = '<script type="text/javascript">

	$(document).ready(function() { 
		$("#query").liveUpdate("#list");
	});
	
	</script>';

	return $script;
}

/**
 * @param sspmod_perun_DiscoTemplate $t
 * @param array $metadata
 * @param bool $favourite
 * @return string html
 */
function showEntry($t, $metadata, $favourite = false) {

	if (isset($metadata['tags']) && in_array('social', $metadata['tags'])) {
		return showEntrySocial($t, $metadata);
	}

	$extra = ($favourite ? ' favourite' : '');
	$html = '<a class="metaentry' . $extra . ' list-group-item" href="' . $t->getContinueUrl($metadata['entityid']) . '">';

	$html .= '<strong>' . $t->getTranslatedEntityName($metadata) . '</strong>';

	$html .= showIcon($metadata);

	$html .= '</a>';

	return $html;
}

/**
 * @param sspmod_perun_DiscoTemplate $t
 * @param array $metadata
 * @return string html
 */
function showEntrySocial($t, $metadata) {

	$bck = 'white';
	if (!empty($metadata['color'])) {
		$bck = $metadata['color'];
	}

	$html = '<a class="btn btn-block social" href="' . $t->getContinueUrl($metadata['entityid'])  . '" style="background: '. $bck .'">';

	$html .= '<img src="' . $metadata['icon'] . '">';

	$html .= '<strong>Sign in with ' . $t->getTranslatedEntityName($metadata) . '</strong>';

	$html .= '</a>';

	return $html;
}


function showIcon($metadata) {
	$html = '';
	// Logos are turned off, because they are loaded via URL from IdP. Some IdPs have bad configuration, so it breaks the WAYF.

	/*if (isset($metadata['UIInfo']['Logo'][0]['url'])) {
		$html .= '<img src="' . htmlspecialchars(\SimpleSAML\Utils\HTTP::resolveURL($metadata['UIInfo']['Logo'][0]['url'])) . '" class="idp-logo">';
	} else if (isset($metadata['icon'])) {
		$html .= '<img src="' . htmlspecialchars(\SimpleSAML\Utils\HTTP::resolveURL($metadata['icon'])) . '" class="idp-logo">';
	}*/

	return $html;
}


function getOr() {
	$or  = '<div class="hrline">';
	$or .= '	<span>or</span>';
	$or .= '</div>';
	return $or;
}

