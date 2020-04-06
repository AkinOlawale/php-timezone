<?php

require('config.php');

/**
 * Description of timezone-util
 *
 * @author User
 */
class timezone_util {

    var $currentTimeZone = '';
    var $defaultTimeZone = '';

    public function __construct() {
        try {
            DISPLAY_ERRORS == true ? error_reporting(E_ALL) : error_reporting(0);
            if (PRODUCTION === true) {
                if (!isset($_SESSION['userTimeZone'])) { #check for cached timezone
                    $detailsUserTime = $this->getUserLocationDetails(['timezone']);
                    if (!empty($detailsUserTime['timezone'])) {
                        $_SESSION['userTimeZone'] = $detailsUserTime['timezone'];
                        $timeZoneToUse = $detailsUserTime['timezone'];
                    } else {
                        $timeZoneToUse = DEFAULT_TIMEZONE;
                    }
                } else {
                    $timeZoneToUse = $_SESSION['userTimeZone'];
                }
            } else {
                $timeZoneToUse = DEFAULT_TIMEZONE;
            }
            $this->currentTimeZone = $timeZoneToUse;
        } catch (Exception $ex) {
            error_log($ex->getMessage());
            exit;
        }
    }

    public function setDefaultTimeZone() {
        try {
            $defaultTimeZone = '';
            $defaultTimeZone = DEFAULT_TIMEZONE;
            if (PRODUCTION === true) {
                $defaultTimeZone = 'UTC'; #default timezone
            }
            $this->defaultTimeZone = $defaultTimeZone;
            date_default_timezone_set($defaultTimeZone);
        } catch (Exception $ex) {
            error_log($ex->getMessage());
            exit;
        }
    }

    public function getDateTimeByTimeZone($dateTime = null, string $format = 'Y-m-d H:i:s', $timezone = null) {
        try {
            $this->setDefaultTimeZone();
            $timeZoneToUse = is_null($timezone) ? $this->currentTimeZone : $timezone;
            $dateTimeToUse = is_null($dateTime) ? date($format) : $dateTime;

            $utc = new DateTime($dateTimeToUse, new DateTimeZone($this->defaultTimeZone));
            $utc->setTimezone(new DateTimeZone($timeZoneToUse));
            return $utc->format($format);
        } catch (Exception $ex) {
            error_log($ex->getMessage());
            exit;
        }
    }

    /**
     * gets current user's location parameter
     * @param array $extraParameters this can be used to get more specific set(s) of parameters
     * @return array
     */
    public function getUserLocationDetails($extraParameters = []) {
        try {
            $result = [];
            $currentIp = $_SERVER['REMOTE_ADDR'];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "http://www.geoplugin.net/json.gp?ip=" . $currentIp);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $ip_data_in = curl_exec($ch);
            curl_close($ch);

            $ip_data = json_decode($ip_data_in, true);
            $ip_data = str_replace('&quot;', '"', $ip_data);

            if (!empty($extraParameters) && count($extraParameters) > 0) {
                foreach ($extraParameters as $value) {
                    if (!empty($ip_data['geoplugin_' . $value])) {
                        $result[$value] = $ip_data['geoplugin_' . $value];
                    }
                }
            } else {
                $result = $ip_data;
            }
            return $result;
        } catch (Exception $ex) {
            error_log($ex->getMessage());
            exit;
        }
    }

}
