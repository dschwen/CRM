<?php
/**
 * User: gdawoud
 * Date: 12/9/2014
 * Time: 11:00 PM
 */
require_once 'Mailchimp.php';

class ChurchInfoMailchimp
{

    private $isActive = false;
    private $myMailchimp;

    public function __construct()
    {

        $apikey = "";
        // Read in report settings from database
        $rsConfig = mysql_query("SELECT cfg_name, IFNULL(cfg_value, cfg_default) AS value FROM config_cfg WHERE cfg_name='mailChimpApiKey'");
        if ($rsConfig) {
            while (list($cfg_name, $cfg_value) = mysql_fetch_row($rsConfig)) {
                $apikey = $cfg_value;
            }
        }

        if ($apikey != "") {
            $this->isActive = true;
            $this->myMailchimp = new MailChimp($apikey);
        }
    }

    function isActive()
    {
        return $this->isActive;
    }

    function isEmailInMailChimp($email)
    {

        if (!$this->isActive) {
            return "Mailchimp is not active";
        }

        if ($email == "") {
            return "";
        }

        try {
            $lists = $this->myMailchimp->helper->listsForEmail(array("email" => $email));
            $listNames = array();
            foreach ($lists as $val) {
                array_push($listNames, $val["name"]);
            }
            return implode(",", $listNames);
        } catch (Exception $e) {
            return "";
        }

    }

    function getLists()
    {
        if (!$this->isActive) {
            return "Mailchimp is not active";
        }
        try {
            $result = $this->myMailchimp->lists->getList();
            return $result["data"];
        } catch (Exception $e) {
            return "";
        }
    }

}