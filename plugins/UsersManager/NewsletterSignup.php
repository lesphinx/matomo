<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link http://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\UsersManager;

use Exception;
use Piwik\Config;
use Piwik\Container\StaticContainer;
use Piwik\Http;
use Piwik\Option;
use Piwik\Url;

class NewsletterSignup
{
    public static function signupForNewsletter($userLogin, $email, $matomoOrg = false, $professionalServices = false)
    {
        // Don't bother if they aren't signing up for at least one newsletter
        if (! ($matomoOrg || $professionalServices)) {
            return;
        }

        $url = Config::getInstance()->General['api_service_url'];
        $url .= '/1.0/subscribeNewsletter/';

        $params = array(
            'email'     => $email,
            'piwikorg'  => (int)$matomoOrg,
            'piwikpro'  => (int)$professionalServices,
            'url'       => Url::getCurrentUrlWithoutQueryString(),
            'language'  => StaticContainer::get('Piwik\Translation\Translator')->getCurrentLanguage(),
        );

        $url .= '?' . Http::buildQuery($params);
        try {
            Http::sendHttpRequest($url, $timeout = 2);
            $optionKey = 'UsersManager.newsletterSignup.' . $userLogin;
            Option::set($optionKey, 1);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}