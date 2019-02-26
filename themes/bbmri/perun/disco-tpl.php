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

const URN_CESNET_PROXYIDP_IDPENTITYID = "urn:cesnet:proxyidp:idpentityid:";

$authContextClassRef = null;
$idpEntityId = null;

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

if (isset($this->data['AuthnContextClassRef'])) {
	$authContextClassRef = $this->data['AuthnContextClassRef'];
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

	if ($authContextClassRef != null) {
		foreach ($authContextClassRef as $value) {
			if (substr($value, 0, strlen(URN_CESNET_PROXYIDP_IDPENTITYID)) === URN_CESNET_PROXYIDP_IDPENTITYID) {
				$idpEntityId = substr($value, strlen(URN_CESNET_PROXYIDP_IDPENTITYID), strlen($value));
				SimpleSAML\Logger::info("Redirecting to " . $idpEntityId);
				$url = $this->getContinueUrl($idpEntityId);
				SimpleSAML\Utils\HTTP::redirectTrustedURL($url);
				exit;
			}
		}
	}

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
        echo '<div class="metalist list-group">';
        echo '<a class="btn btn-block social" href="https://adm.hostel.eduid.cz/registrace/k1" style="background: #43554a">';
        echo '<img src="data:image/png;base64,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">';
        echo '<strong>' . $this->t('{bbmri:bbmri:create_account_hostel}') . '</strong>';
        echo '</a>';

	    echo '<a class="btn btn-block social" href="https://perun.bbmri-eric.eu/non/registrar/?vo=lifescience_hostel&targetnew=https://perun.bbmri-eric.eu/non/registrar/?vo=bbmri&targetexisting=https://perun.bbmri-eric.eu/non/registrar/?vo=bbmri&targetextended=https://perun.bbmri-eric.eu/non/registrar/?vo=bbmri" style="background: #95CC67">';
	    echo '<img src="data:image/jpeg;base64,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">';
	    echo '<strong>' . $this->t('{bbmri:bbmri:create_account_lifeScienceHostel}') . '</strong>';
	    echo '</a>';

	    echo '</div>';
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

