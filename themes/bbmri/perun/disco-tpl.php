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

const WARNING_CONFIG_FILE_NAME = 'config-warning.php';
const WARNING_IS_ON = 'isOn';
const WARNING_USER_CAN_CONTINUE = 'userCanContinue';
const WARNING_TITLE = 'title';
const WARNING_TEXT = 'text';

$warningIsOn = false;
$warningUserCanContinue = null;
$warningTitle = null;
$warningText = null;
$config = null;

try {
	$config = SimpleSAML_Configuration::getConfig(WARNING_CONFIG_FILE_NAME);
} catch (Exception $ex) {
	SimpleSAML\Logger::warning("bbmri:disco-tpl: missing or invalid config-warning file");
}

if ($config != null) {
	try {
		$warningIsOn = $config->getBoolean(WARNING_IS_ON);
	} catch (Exception $ex) {
		SimpleSAML\Logger::warning("bbmri:disco-tpl: missing or invalid isOn parameter in config-warning file");
		$warningIsOn = false;
	}
}

if ($warningIsOn) {
	try {
		$warningUserCanContinue = $config->getBoolean(WARNING_USER_CAN_CONTINUE);
	} catch (Exception $ex) {
		SimpleSAML\Logger::warning("bbmri:disco-tpl: missing or invalid userCanContinue parameter in config-warning file");
		$warningUserCanContinue = true;
	}
	try {
		$warningTitle = $config->getString(WARNING_TITLE);
		$warningText = $config->getString(WARNING_TEXT);
		if (empty($warningTitle) || empty($warningText)) {
			throw new Exception();
		}
	} catch (Exception $ex) {
		SimpleSAML\Logger::warning("bbmri:disco-tpl: missing or invalid title or text in config-warning file");
		$warningIsOn = false;
	}
}

# Do not show social IdPs when using addInstitutionApp, show just header Add Institution
if ($this->isAddInstitutionApp()) {
	// Translate title in header
	$this->data['header'] = $this->t('{bbmri:bbmri:add_institution}');
	$this->includeAtTemplateBase('includes/header.php');
} else {

	if ($warningIsOn && !$warningUserCanContinue) {
		$this->data['header'] = $this->t('{bbmri:bbmri:warning}');
	}


	$this->includeAtTemplateBase('includes/header.php');

	if ($warningIsOn) {
		if ($warningUserCanContinue) {
			echo '<div class="alert alert-warning">';
		} else {
			echo '<div class="alert alert-danger">';
		}
		echo '<h4> <strong>' . $warningTitle . '</strong> </h4>';
		echo $warningText;
		echo '</div>';
	}

	if (!$warningIsOn || $warningUserCanContinue) {
		if (!empty($this->getPreferredIdp())) {

			echo '<p class="descriptionp">' . $this->t('{bbmri:bbmri:previous_selection}') . '</p>';
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
		if (empty($this->getPreferredIdp())) {
			echo '<div class="descriptionp" >';
			echo $this->t('{bbmri:bbmri:create_lifescience_account}');

			echo '<a class="btn-link" href="https://perun.bbmri-eric.eu/non/registrar/?vo=lifescience_hostel&targetnew=https://perun.bbmri-eric.eu/non/registrar/?vo=bbmri&targetexisting=https://perun.bbmri-eric.eu/non/registrar/?vo=bbmri&targetextended=https://perun.bbmri-eric.eu/non/registrar/?vo=bbmri" ';
			echo '<strong>' . $this->t('{bbmri:bbmri:here}') . '</strong>';
			echo '</a>';

			echo '</div>';
		}

        echo getOr();


		echo '<p class="descriptionp">';
		echo $this->t('{bbmri:bbmri:institutional_account}');
		echo '</p>';
	}
}

if (!$warningIsOn || $warningUserCanContinue) {
    echo '<div class="inlinesearch">';
    echo '	<form id="idpselectform" action="?" method="get">
			<input class="inlinesearchf form-control input-lg" placeholder="' . $this->t('{bbmri:bbmri:type_name_institution}') . '" 
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
    if ($this->isAddInstitutionApp()) {
        echo $this->t('{bbmri:bbmri:cannot_find_institution}') . '<a href="mailto:aai-infrastructure@lists.bbmri-eric.eu?subject=Request%20for%20adding%20new%20IdP">aai-infrastructure@lists.bbmri-eric.eu</a>';
    } else {
        echo $this->t('{bbmri:bbmri:cannot_find_institution_extended}') . '<a class="btn btn-primary" href="https://login.bbmri-eric.eu/add-institution/">' . $this->t('{bbmri:bbmri:add_institution}') . '</a>';
    }
    echo '</div>';
}





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

