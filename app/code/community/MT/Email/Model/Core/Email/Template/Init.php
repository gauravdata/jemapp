<?php

if (@class_exists('ES_Newsletterpopup_Model_Core_Email_Template')) {
    class MT_Email_Model_Core_Email_Template_Init extends ES_Newsletterpopup_Model_Core_Email_Template
    {}
} else if (@class_exists('MT_Giftcard_Model_Core_Email_Template')) {
    class MT_Email_Model_Core_Email_Template_Init extends MT_Giftcard_Model_Core_Email_Template_Init
    {}
} elseif (@class_exists('Ebizmarts_Mandrill_Model_Email_Template')) {
    class MT_Email_Model_Core_Email_Template_Init extends Ebizmarts_Mandrill_Model_Email_Template
    {}
} else if (@class_exists('Aschroder_SMTPPro_Model_Email_Template')) {
    class MT_Email_Model_Core_Email_Template_Init extends Aschroder_SMTPPro_Model_Email_Template
    {}
} else {
    class MT_Email_Model_Core_Email_Template_Init extends Mage_Core_Model_Email_Template
    {}
}
