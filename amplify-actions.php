<?php
/**
 * @package Amplify_Actions
 * @version 0.1
 */
/*
Plugin Name: Amplify Actions
Plugin URI: https://github.com/isf-hack-night/amplify-actions
Description: Displays actions sourced from Amplify for a particular state/district.
Author: Indivisible SF
Version: 0.1
Author URI: https://indivisiblesf.org/
*/

# https://michelf.ca/projects/php-markdown/
require_once('Michelf/Markdown.inc.php');

# CSS constants for use in content prefixes.
define('BLACK_TELEPHONE', '\260e');
define('EMAIL_SYMBOL', '\01f4e7');
define('NO_BREAK_SPACE', '\00a0');

function _amplify_present_one_phone_number($office) {
	$telLink = 'tel:' . str_replace(' ', '', $office['phone']);
	$officeDesc = ($office['state'] == 'DC') ? 'DC' : $office['city'];
	print '<li class="amplify-contact-each amplify-contact-phone"><a href="' . htmlentities($telLink) . '">' . $officeDesc . '</a></li>' . "\n";
}

function _amplify_present_one_action($action) {
	$script = $action['callScriptMd'];
	if (!$script) {
		$script = $action['flexBodyMd'];
	}

	print '<div class="amplify-action">' . "\n";
	print '<h3 class="amplify-title"><span class="amplify-title-preTitle">' . htmlentities($action['preTitle']) . '</span> ' . htmlentities($action['title']) . '</h3>' . "\n";

	print '<div class="amplify-callScript">' . str_replace('<h1>', '<h4>', str_replace('</h1>', '</h4>', \Michelf\Markdown::defaultTransform($script))) . '</div>' . "\n";

	$person = isset($action['person']) ? $action['person'] : null;
	if ($person) {
		$emailLink = isset($person['emailFormUrl']) ? $person['emailFormUrl'] : null;
		$phones = isset($person['offices']) ? $person['offices'] : [];
		if ($emailLink or $phones) {
			print '<div class="amplify-contact">' . "\n";
			print '<ul class="amplify-contact-items">' . "\n";
			array_map('_amplify_present_one_phone_number', $phones);
			if ($emailLink) {
				print '<li class="amplify-contact-each amplify-contact-email"><a href="' . htmlentities($emailLink) . '">' . 'Email</a></li>' . "\n";
			}
			print '</ul>';
			print "</div>\n";
		}
	}

	print "</div>\n";
}

function amplify_actions_all($state, $district) {
	$district = 0 + $district;
	if ($district < 0 || $district > 99) {
		# That's not a district. (California has 53, the most in the Union, but let's be flexible here.)
		return;
	}
	if (1 != preg_match('/^[A-Z][A-Z]$/', $state)) {
		# That's not a state.
		return;
	}

	$file = fopen("wp-content/amplify-actions-$state$district.json", 'r');
	if (! $file) {
		print "No actions today!\n";
		return;
	}

	print '<div class="amplify-actions amplify-' . htmlentities($state) . '-' . htmlentities($district) . '">' . "\n";
	print '<h2 class="amplify-actions-header">Actions for ' . htmlentities($state) . '-' . htmlentities($district) . '</h2>' . "\n";

	$json = fread($file, 1000000);
	fclose($file);
	$actions = json_decode($json, true);

	array_map('_amplify_present_one_action', $actions['concreteActions']);

	print "</div>\n";
}

function amplify_actions_one_moc($state, $district, $personID) {
	$district = 0 + $district;
	if ($district < 0 || $district > 99) {
		# That's not a district. (California has 53, the most in the Union, but let's be flexible here.)
		return;
	}
	if (1 != preg_match('/^[A-Z][A-Z]$/', $state)) {
		# That's not a state.
		return;
	}
	# Currently, person IDs are integers, so enforce that here.
	$personID = 0 + $personID;

	$file = fopen("wp-content/amplify-actions-$state$district.json", 'r');
	if (! $file) {
		print "No actions today!\n";
		return;
	}

	$json = fread($file, 1000000);
	fclose($file);

	$allDistrictActions = json_decode($json, true);
	$needsBeginDiv = true;

	foreach ($allDistrictActions['concreteActions'] as $action) {
		if ($action['callPersonId'] === $personID || ( (! is_null($action['callPersonIds'])) && in_array($personID, $action['callPersonIds']) )) {
			if ($needsBeginDiv) {
				$prefix = $action['person']['prefix'];
				$fullName = $action['person']['firstName'] . ' ' . $action['person']['lastName'];
				$identifier = $action['person']['firstName'] . $action['person']['lastName'];

				print '<div class="amplify-actions amplify-moc-' . htmlentities('' . $identifier) . '">' . "\n";
				print '<h2 class="amplify-actions-header">Actions for ' . htmlentities($prefix) . ' ' . htmlentities($fullName) . '</h2>' . "\n";
				$needsBeginDiv = false;
			}

			_amplify_present_one_action($action);
		}
	}

	if (! $needsBeginDiv) {
		print "</div>\n";
	}
}

/*
?><html>
<head><meta charset="utf-8"><title>CA-12</title>
<style type="text/css">
.amplify-title-preTitle { font-weight: normal; }
.amplify-contact-phone:before { content: "<?php print BLACK_TELEPHONE . NO_BREAK_SPACE; ?>" }
.amplify-contact-email:before { content: "<?php print EMAIL_SYMBOL . NO_BREAK_SPACE; ?>" }
</style></head>
<body><?php
amplify_actions_all('CA', 12);
?>
</body></html>
*/
?>
