<?php
/*------------------------------------------------------------------------
# mod_rapid_contact - Rapid Contact
# ------------------------------------------------------------------------
# author    Christopher Mavros - Mavxr.com
# copyright Copyright (C) 2008 Mavxr.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: https://mavxr.com
# Technical Support:  Forum - https://mavxr.com/support/forum
-------------------------------------------------------------------------*/

// no direct access
\defined( '_JEXEC' ) or die( 'Restricted access' );

print $params->get('email_pretext', 'You received a message from '). $posted_email . "\n\n";
print $CORRECT_MESSAGE;