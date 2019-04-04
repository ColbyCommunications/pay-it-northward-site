<?php
/**
 * Lando-specific configuration
 */
if ('ON' === getenv('LANDO')) {
    $site_scheme = 'https';
    $objLandoInfo = json_decode(getenv('LANDO_INFO', true));
    define('DB_NAME', $objLandoInfo->database->creds->database);
    define('DB_USER', $objLandoInfo->database->creds->user);
    define('DB_PASSWORD', $objLandoInfo->database->creds->password);
    define('DB_HOST', $objLandoInfo->database->internal_connection->host);
    define('DB_CHARSET', 'utf8');
    define('DB_COLLATE', '');
    define('WP_DEBUG', false);
    define('WP_DEBUG_LOG', false);
    define('WP_DEBUG_SCREEN', false);

    if (MULTISITE && SUBDOMAIN_INSTALL) {
        if (false !== $strPrimaryDomain = getenv('PRIMARY_DOMAIN')) {
            $aryRoutes = $objLandoInfo->appserver_nginx->urls;
            $strLookForDomain = str_replace('.', '\.', $strPrimaryDomain);
            $strPattern = sprintf('/^https:\/\/(%s[^\/]+)/', $strLookForDomain);
            $aryMatched = preg_grep($strPattern, $aryRoutes);
            if (1 === count($aryMatched)) {
                //now we have the _WHOLE_ match, but we need just the domain
                preg_match($strPattern, reset($aryMatched), $aryMatches);
                //@todo this assumes 1 exists without checking first
                $site_host = $aryMatches[1];
            } else {
                //@todo throw an error or just output the message?
                echo '<p>I found zero or too many matches for our primary domain:</p><pre>',var_export($aryMatched,true),'</pre>';exit();
            }
        } else {
            //@todo throw error or output this message?
            echo '<p>PRIMARY DOMAIN has not been defined. Please include this information in your .lando.yaml file.</p>';
        }
    }
} else {
    /**
     * Fill out if you are using a different development environment
     */
}