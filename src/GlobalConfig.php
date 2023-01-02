<?php

/**
 * Bootstrap custom module skeleton.  This file is an example custom module that can be used
 * to create modules that can be utilized inside the OpenEMR system.  It is NOT intended for
 * production and is intended to serve as the barebone requirements you need to get started
 * writing modules that can be installed and used in OpenEMR.
 *
 * @package   OpenEMR
 * @link      http://www.open-emr.org
 *
 * @author    Brad Sharp <brad.sharp@claimrev.com>
 * @copyright Copyright (c) 2022 Brad Sharp <brad.sharp@claimrev.com>
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

namespace OpenEMR\Modules\ClaimRevConnector;

use OpenEMR\Common\Crypto\CryptoGen;
use OpenEMR\Services\Globals\GlobalSetting;

class GlobalConfig
{
    const CONFIG_OPTION_ENVIRONMENT = 'oe_claimrev_config_environment';
    const CONFIG_OPTION_CLIENTID = 'oe_claimrev_config_clientid';
    const CONFIG_OPTION_CLIENTSECRET = 'oe_claimrev_config_clientsecret';
    const CONFIG_OPTION_SCOPE = 'oe_claimrev_config_scope';
    const CONFIG_OPTION_AUTHORITY = 'oe_claimrev_config_authority';

    const CONFIG_AUTO_SEND_CLAIM_FILES = 'oe_claimrev_config_auto_send_claim_files';
    const CONFIG_ENABLE_MENU = "oe_claimrev_config_add_menu_button";
    const CONFIG_SERVICE_TYPE_CODES = "oe_claimrev_config_service_type_codes";
    const CONFIG_ENABLE_ELIGIBILITY_CARD = "oe_claimrev_config_add_eligibility_card";
    const CONFIG_USE_FACILITY_FOR_ELIGIBILITY = "oe_claimrev_config_use_facility_for_eligibility";
    const CONFIG_ENABLE_REALTIME_ELIGIBILITY = "oe_claimrev_enable_rte";
    const CONFIG_ENABLE_RESULTS_ELIGIBILITY = "oe_claimrev_eligibility_results_age";
    const CONFIG_ENABLE_AUTO_SEND_ELIGIBILITY = "oe_claimrev_send_eligibility";


    // const CONFIG_OPTION_TEXT = 'oe_skeleton_config_option_text';
    // const CONFIG_OPTION_ENCRYPTED = 'oe_skeleton_config_option_encrypted';
    // const CONFIG_OVERRIDE_TEMPLATES = "oe_skeleton_override_twig_templates";
    // const CONFIG_ENABLE_BODY_FOOTER = "oe_skeleton_add_body_footer";
    // const CONFIG_ENABLE_FHIR_API = "oe_skeleton_enable_fhir_api";

    private $globalsArray;

    /**
     * @var CryptoGen
     */
    private $cryptoGen;

    public function __construct(array $globalsArray)
    {
        $this->globalsArray = $globalsArray;
        $this->cryptoGen = new CryptoGen();
    }

    /**
     * Returns true if all of the settings have been configured.  Otherwise it returns false.
     * @return bool
     */
    public function isConfigured()
    {
        // $keys = [self::CONFIG_OPTION_TEXT, self::CONFIG_OPTION_ENCRYPTED];
        // foreach ($keys as $key) {
        //     $value = $this->getGlobalSetting($key);
        //     if (empty($value)) {
        //         return false;
        //     }
        // }
        return true;
    }

    public function getClientId()
    {
        return $this->getGlobalSetting(self::CONFIG_OPTION_CLIENTID);
    }
    public function getClientSecret()
    {
        $encryptedValue = $this->getGlobalSetting(self::CONFIG_OPTION_CLIENTSECRET);
        return $this->cryptoGen->decryptStandard($encryptedValue);
    }

    public function getClientScope()
    {
        if($this->getGlobalSetting(self::CONFIG_OPTION_ENVIRONMENT) == "S")
        {
            return "https://stagingclaimrevcom.onmicrosoft.com/portal/api/.default";
        }
        else if($this->getGlobalSetting(self::CONFIG_OPTION_ENVIRONMENT) == "D")
        {
            return "https://claimrevportaldevelopment.onmicrosoft.com/portal/api/.default";
        }
        return "https://productionclaimrevcom.onmicrosoft.com/portal/api/.default";        
    }

    public function getClientAuthority()
    {
        if($this->getGlobalSetting(self::CONFIG_OPTION_ENVIRONMENT) == "S")
        {
            return "https://stagingclaimrevcom.b2clogin.com/stagingclaimrevcom.onmicrosoft.com/B2C_1_sign-in-only/oauth2/v2.0/token";
        }
        else if($this->getGlobalSetting(self::CONFIG_OPTION_ENVIRONMENT) == "D")
        {
            return "https://claimrevportaldevelopment.b2clogin.com/claimrevportaldevelopment.onmicrosoft.com/B2C_1_sign-in-only/oauth2/v2.0/token";
        }
        return "https://productionclaimrevcom.b2clogin.com/productionclaimrevcom.onmicrosoft.com/B2C_1_sign-in-only/oauth2/v2.0/token";   
    }

    public function getApiServer()
    {
        if($this->getGlobalSetting(self::CONFIG_OPTION_ENVIRONMENT) == "S")
        {
            return "https://testapi.claimrev.com";
        }
        else if($this->getGlobalSetting(self::CONFIG_OPTION_ENVIRONMENT) == "D")
        {
            return "https://5eab-174-128-131-22.ngrok.io";
        }
        return "https://api.claimrev.com";   
    }



    public function getAutoSendFiles()
    {
        return $this->getGlobalSetting(self::CONFIG_AUTO_SEND_CLAIM_FILES);
    }




    public function getTextOption()
    {
        return $this->getGlobalSetting(self::CONFIG_OPTION_TEXT);
    }

    /**
     * Returns our decrypted value if we have one, or false if the value could not be decrypted or is empty.
     * @return bool|string
     */
    public function getEncryptedOption()
    {
        $encryptedValue = $this->getGlobalSetting(self::CONFIG_OPTION_ENCRYPTED);
        return $this->cryptoGen->decryptStandard($encryptedValue);
    }

    public function getGlobalSetting($settingKey)
    {
        return $this->globalsArray[$settingKey] ?? null;
    }

    public function getGlobalSettingSectionConfiguration()
    {
        $settings = [
            self::CONFIG_OPTION_ENVIRONMENT => [
                'title' => 'ClaimRev Environment (P=Production)'
                ,'description' => 'The system you connect to. P for production'
                ,'type' => GlobalSetting::DATA_TYPE_TEXT
                ,'default' => 'P'
            ]
            ,self::CONFIG_OPTION_CLIENTID => [
                'title' => 'Client ID'
                ,'description' => 'Contact ClaimRev for the client ID'
                ,'type' => GlobalSetting::DATA_TYPE_TEXT
                ,'default' => ''
            ]
            ,self::CONFIG_OPTION_CLIENTSECRET => [
                'title' => 'ClaimRev Client Secret'
                ,'description' => 'Contact ClaimRev for this value'
                ,'type' => GlobalSetting::DATA_TYPE_ENCRYPTED
                ,'default' => ''
            ]
            ,self::CONFIG_SERVICE_TYPE_CODES => [
                'title' => 'Eligibility Service Type Codes'
                ,'description' => 'Comma Separated List of Service Type Codes'
                ,'type' => GlobalSetting::DATA_TYPE_TEXT
                ,'default' => '30'
            ]
            ,self::CONFIG_AUTO_SEND_CLAIM_FILES => [
                'title' => 'Auto Send Claim Files'
                ,'description' => 'Send Claim Files to ClaimRev automatically'
                ,'type' => GlobalSetting::DATA_TYPE_BOOL
                ,'default' => ''
            ]           
            ,self::CONFIG_ENABLE_MENU => [
                'title' => 'Add module menu item'
                ,'description' => 'Adding a menu item to the system (requires logging out and logging in again)'
                ,'type' => GlobalSetting::DATA_TYPE_BOOL
                ,'default' => ''
            ]
            ,self::CONFIG_ENABLE_ELIGIBILITY_CARD => [
                'title' => 'Add ClaimRev Eligibility Card To Patient Dashboard'
                ,'description' => 'Adds the ClaimRev Eligibility Card To Patient Dashboard'
                ,'type' => GlobalSetting::DATA_TYPE_BOOL
                ,'default' => ''
            ]
            ,self::CONFIG_USE_FACILITY_FOR_ELIGIBILITY => [
                'title' => 'Use Facility for Eligibility'
                ,'description' => 'Information requester will be facility rather than provider'
                ,'type' => GlobalSetting::DATA_TYPE_BOOL
                ,'default' => ''
            ]
            ,self::CONFIG_ENABLE_REALTIME_ELIGIBILITY => [
                'title' => 'Turn on Real-Time Eligibility'
                ,'description' => 'Enables eligibility checks on patients eligibility when an appointment is created'
                ,'type' => GlobalSetting::DATA_TYPE_BOOL
                ,'default' => ''
            ]
            ,self::CONFIG_ENABLE_RESULTS_ELIGIBILITY => [
                'title' => 'Eligibility Age To Stale'
                ,'description' => 'THis is the number of days to consider eligibility stale'
                ,'type' => GlobalSetting::DATA_TYPE_TEXT
                ,'default' => ''
            ]
            ,self::CONFIG_ENABLE_AUTO_SEND_ELIGIBILITY => [
                'title' => 'Turn on Eligibility Send Service'
                ,'description' => 'Enables the sending of eligibility json to ClaimRev'
                ,'type' => GlobalSetting::DATA_TYPE_BOOL
                ,'default' => ''
            ]
        ];//
        return $settings;
    }
}
