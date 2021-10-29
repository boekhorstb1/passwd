<?php
/**
 * See horde/config/prefs.php for documentation on the structure of this file.
 *
 * IMPORTANT: DO NOT EDIT THIS FILE! DO NOT COPY prefs.php TO prefs.local.php!
 * Local overrides ONLY MUST be placed in prefs.local.php or prefs.d/.
 * If the 'vhosts' setting has been enabled in Horde's configuration, you can
 * use prefs-servername.php.
 */

$prefGroups['display'] = [
    'column' => _("General Preferences"),
    'label' => _("Display Preferences"),
    'desc' => _("Select confirmation preferences, how to display the different views and choose default view."),
    'members' => ['dynamic_ui']
];

$_prefs['dynamic_ui'] = [
    'value' => '',
    'type' => 'enum',
    'enum' => [
        'basic' => _('Basic UI'),
        'material' => _('Material UI')
    ],
    'desc' => _("Which UI to present when Horde is in Dynamic mode"),
];
