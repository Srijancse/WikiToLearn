<?php

# See includes/DefaultSettings.php for all configurable settings
# and their default values, but don't forget to make changes in _this_
# file, not there.
#
# Further documentation for configuration settings may be found at:
# http://www.mediawiki.org/wiki/Manual:Configuration_settings

$wtl_development=false;
if ($wtl_development || getenv("WTL_PRODUCTION") == "1"){
  error_reporting(-1);
  ini_set("display_errors",1);
}

$IP = "/var/www/WikiToLearn/mediawiki/";
putenv("MW_INSTALL_PATH=$IP");

# Protect against web entry
if (!defined('MEDIAWIKI')) {
    exit;
}

# Mobile detection
if (isset($_SERVER['HTTP_USER_AGENT'])) {
    $_SERVER['HTTP_X_DEVICE'] = $_SERVER['HTTP_USER_AGENT'];
} else {
    $_SERVER['HTTP_X_DEVICE'] = "";
}
#error_log("device from ls.php");

ini_set('memory_limit', '64M');
$wgMaxShellMemory = 524288;

## Uncomment this to disable output compression
# $wgDisableOutputCompression = true;

$wgMetaNamespace = "Project";

require_once( "$IP/../domains.php" );

if (array_search($wiki_domain, $wiki_allow_domains) === FALSE) {
    $wiki = "notfound";
}

$wgCookieDomain = "." . $wiki_domain;
$wgSecureLogin = true;
// $wgCookieSecure = true;
## The URL base path to the directory containing the wiki;
## defaults for all runtime URL paths are based off of this.
## For more information on customizing the URLs please see:
## http://www.mediawiki.org/wiki/Manual:Short_URL

$wgUsePathInfo = false; #required since v1.11.0
$wgScriptPath = "";
$wgArticlePath = "/$1";
$wgScript = "$wgScriptPath/index.php";
$wgRedirectScript = "$wgScriptPath/redirect.php";

## The relative URL path to the skins directory
$wgStylePath = "$wgScriptPath/skins";

$wgMainCacheType = CACHE_MEMCACHED;
$wgMemCachedServers = array(
    "memcached:11211", # one gig on this box
);

$wgCacheDirectory = "$IP/cache/";
$wgEnableSidebarCache = true;

## UPO means: this is also a user preference option

$wgEnableEmail = true;
$wgEnableUserEmail = true; # UPO

$wgEmergencyContact = "webmaster@kde.org";
$wgPasswordSender = "webmaster@kde.org";

$wgEnotifUserTalk = true; # UPO
$wgEnotifWatchlist = false; # UPO
$wgEmailAuthentication = true;

## Database settings
// $wgJobRunRate = 0.01;

$wgDBtype = "mysql";
$wgDBserver = "mysql";

$wgSharedDB = 'sharedwikitolearn'; # The $wgDBname for the wiki database holding the main user table
$wgSharedTables[] = array('user', 'user_properties', 'user_groups', 'interwiki', 'iwlinks');

# Site language code, should be one of ./languages/Language(.*).php
# Make sure you give permission to sharedwikitolearn database to the user in question.
switch ($wiki) {
    case "it":
    case "en":
    case "fr":
    case "es":
    case "de":
    case "pt":
    case "sv":
        $wgLanguageCode = $wiki;
        require_once("$IP/../secrets/" . $wiki . "wikitolearn.php");
        break;
    case "pool":
    case "meta":
        include_once("$IP/extensions/Translate/Translate.php");
        require_once("$IP/../secrets/" . $wiki . "wikitolearn.php");
        break;
    default:
    header("Location: //www." . $wiki_domain . "/");
    break;
}

$wgSitename = "WikiToLearn - collaborative textbooks";
$wgLogo = "$wgStylePath/Neverland/images/logos/en.png";

$wgForeignFileRepos[] = array(
    'class' => 'ForeignDBRepo',
    'name' => 'poolwiki',
    'url' => "//pool." . $wiki_domain . "/images",
    'directory' => '/var/www/WikiToLearn/mediawiki/images/',
    'hashLevels' => 2, // This must be the same for the other family member
    'dbType' => $wgDBtype,
    'dbServer' => $wgDBserver,
    'dbUser' => $wgDBuser,
    'dbPassword' => $wgDBpassword,
    'dbFlags' => DBO_DEFAULT,
    'dbName' => 'poolwikitolearn',
    'tablePrefix' => '',
    'hasSharedCache' => true,
    'descBaseUrl' => '//pool.' . $wiki_domain . '/Image:',
    'fetchDescription' => false
);

if (!isset($wgDBname)) {
    $wgDBname = $wgDBuser;
}
$wgEnableAPI = true;


## To enable image uploads, make sure the 'images' directory
## is writable, then set this to true:
$wgEnableUploads = true;
$wgUseImageMagick = true;
$wgImageMagickConvertCommand = "/usr/bin/convert";

$wgFileExtensions[] = 'pdf';
$wgFileExtensions[] = 'svg';
$wgFileExtensions[] = 'svgz';
$wgFileExtensions[] = 'tex';
$wgGroupPermissions['*']['upload'] = true;
#$wgGroupPermissions['Editor']['autopatrol'] = true;
$wgGroupPermissions['sysop']['meta'] = true;
# $wgGroupPermissions['user']['upload'] = true;
# InstantCommons allows wiki to use images from http://commons.wikimedia.org
$wgUseInstantCommons = true;

## If you use ImageMagick (or any other shell command) on a
## Linux server, this will need to be set to the name of an
## available UTF-8 e
$wgShellLocale = "en_US.utf8";

## If you want to use image uploads under safe mode,
## create the directories images/archive, images/thumb and
## images/temp, and make them all writable. Then uncomment
## this, if it's not already uncommented:
#$wgHashedUploadDirectory = false;
## Set $wgCacheDirectory to a writable directory on the web server
## to make your wiki go slightly faster. The directory should not
## be publically accessible from the web.
#$wgCacheDirectory = "$IP/cache";

$wgUseSharedUploads = true;
$wgSharedUploadPath = '//pool.' . $wiki_domain . '/images';
$wgSharedUploadDirectory = '$IP/images/';
$wgHashedSharedUploadDirectory = true;
$wgUploadNavigationUrl = "//pool." . $wiki_domain . "/index.php/Special:Upload";
$wgUploadMissingFileUrl = "//pool." . $wiki_domain . "/index.php/Special:Upload";


## For attaching licensing metadata to pages, and displaying an
## appropriate copyright notice / icon. GNU Free Documentation
## License and Creative Commons licenses are supported so far.
$wgEnableCreativeCommonsRdf = true;
$wgRightsPage = "Project:Copyright"; # Set to the title of a wiki page that describes your license/copyright
$wgRightsUrl = "http://creativecommons.org/licenses/by-sa/3.0/";
// $wgRightsUrl  = "//www." . $wiki_domain . "/index.php/Project:Copyright";
$wgRightsText = "Creative Commons Attribution Share Alike 3.0 &amp; GNU FDL";
$wgRightsIcon = "{$wgStylePath}/common/images/cc-by-sa.png";
// $wgRightsIcon = "{$wgStylePath}/common/images/gfdlcc.png";
# Path to the GNU diff3 utility. Used for conflict resolution.
$wgDiff3 = "/usr/bin/diff3";

$wgSVGConverter = 'inkscape';

# Query string length limit for ResourceLoader. You should only set this if
# your web server has a query string length limit (then set it to that limit),
# or if you have suhosin.get.max_value_length set in php.ini (then set it to
# that value)
$wgResourceLoaderMaxQueryLength = 512;

$wgHooks['ParserClearState'][] = 'efMWNoTOC';

function efMWNoTOC($parser) {
    $parser->mShowToc = false;
    return true;
}

# Bigger uploads
$wgMaxUploadSize = 2147483648;

# Protect only uploads
$wgAllowExternalImagesFrom = array('http://www.' . $wiki_domain . '/', 'http://www.pledgie.com');

$wgUseETag = true;

# Don't sitemap files
#$wgSitemapNamespaces = array('0', '2', '3', '4', '6', '8');

$wgDefaultUserOptions['useeditwarning'] = 1;
$wgDefaultSkin = 'neverland';
require_once "$IP/skins/Neverland/Neverland.php";

$wgAllowImageTag = true;

if (getenv("WTL_PRODUCTION") == "1") {
    $wgEnableDnsBlacklist = true;
    $wgDnsBlacklistUrls = array('xbl.spamhaus.org', 'dnsbl.tornevall.org');
}

$wgCapitalLinkOverrides[ NS_FILE ] = false;


$wgVirtualRestConfig['modules']['parsoid'] = array(
  // URL to the Parsoid instance
  // Use port 8142 if you use the Debian package
  'url' => 'http://parsoid:8000',
  // Parsoid "domain", see below (optional)
  'domain' => isset($_SERVER['SERVER_NAME'])?$_SERVER['SERVER_NAME']:"",
);


/* extensions loading */

// Cite extension for references as footnotes
wfLoadExtension("Cite");
$wgCiteEnablePopups = true;

// Collection extension
require_once("$IP/extensions/Collection/Collection.php");
$wgGroupPermissions['sysop']['collectionsaveascommunitypage'] = true;
$wgGroupPermissions['user']['collectionsaveasuserpage']      = true;
# configuration borrowed from wmf-config/CommonSettings.php
# in operations/mediawiki-config
$wgCollectionFormatToServeURL['rdf2latex'] = $wgCollectionFormatToServeURL['rdf2text'] = 'http://ocg:17080';
# MediaWiki namespace is not a good default
$wgCommunityCollectionNamespace = NS_PROJECT;
# Sidebar cache doesn't play nice with this
$wgEnableSidebarCache = false;
$wgCollectionFormats = array(
    'rdf2latex' => 'PDF',
    'rdf2text' => 'Plain text',
);
$wgCollectionRendererSettings['columns']['default'] = 1;
$wgLicenseURL = "//creativecommons.org/licenses/by-sa/3.0/";
$wgCollectionPortletFormats = array('rdf2latex', 'rdf2text');
//$wgCollectionMWServeURL = ("http://tools.pediapress.com/mw-serve/");
//$wgParserCacheType = CACHE_ACCEL; // # Don't break math rendering

// Captcha
wfLoadExtensions( array( 'ConfirmEdit', 'ConfirmEdit/ReCaptchaNoCaptcha' ) );
$wgCaptchaClass = 'ReCaptchaNoCaptcha';
$wgReCaptchaSendRemoteIP = true;
/// These keys are Google's test keys. Configure them appropriately in secrets
$wgReCaptchaSiteKey = '6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI';
$wgReCaptchaSecretKey = '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe';

//ContributionScores
require_once("$IP/extensions/ContributionScores/ContributionScores.php");
$wgContribScoreIgnoreBots = true;          // Exclude Bots from the reporting - Can be omitted.
$wgContribScoreIgnoreBlockedUsers = true;  // Exclude Blocked Users from the reporting - Can be omitted.
$wgContribScoresUseRealName = true;        // Use real user names when available - Can be omitted. Only for MediaWiki 1.19 and later.
$wgContribScoreDisableCache = false;       // Set to true to disable cache for parser function and inclusion of table.
#Each array defines a report - 7,50 is "past 7 days" and "LIMIT 50" - Can be omitted.
$wgContribScoreReports = array(
    array(30, 20),
    array(90, 20));

//DMath
wfLoadExtension("DMath");

//DockerAccess
require_once( "$IP/extensions/DockerAccess/DockerAccess.php" );
$virtualFactoryURL = "http://babbage.wikitolearn.org";
$virtualFactoryImages = array(
    'ubuntu-base' => "Minimal LXDE image",
    'novnc-kde' => "KDE Development Image",
    'qt5' => "Qt5 Development Image",
    'root' => "ROOT Basic Image",
);

//Echo for notification
require_once( "$IP/extensions/Echo/Echo.php" );

//EmbedVideo
include_once("$IP/extensions/EmbedVideo/EmbedVideo.php");

//Flow for talk pages
require_once( "$IP/extensions/Flow/Flow.php" );
# These lines enable Flow on the "Project talk" and "User talk" namespaces
// $wgNamespaceContentModels[NS_PROJECT_TALK] = CONTENT_MODEL_FLOW_BOARD;
// $wgNamespaceContentModels[NS_USER_TALK] = CONTENT_MODEL_FLOW_BOARD;

//Gadgets
wfLoadExtension( "Gadgets" );

//googleAnalytics
require_once( "$IP/extensions/googleAnalytics/googleAnalytics.php" );

//LiquidThreads for discussion page system
require_once( "$IP/extensions/LiquidThreads/LiquidThreads.php" );

// MathJax
wfLoadExtension("Math");
//$wgUseMathJax = true;
//$wgDefaultUserOptions['math'] = MW_MATH_MATHJAX;
$wgMathValidModes[] = 'MW_MATH_MATHML';
$wgDefaultUserOptions['math'] = 'MW_MATH_MATHML';
$wgMathMathMLUrl = 'http://mathoid:10044/';

//Nuke for mass delete pages
wfLoadExtension("Nuke");

// Add parser functions (for if, else, ...)
wfLoadExtension( "ParserFunctions" );


wfLoadExtension( 'Renameuser' );


// SubapageList needs it
require_once( "$IP/extensions/ParserHooks/ParserHooks.php" );
#require_once( "$IP/extensions/SubPageList/SubPageList.php" );
// Add subpage capabilities
$wgNamespacesWithSubpages = array_fill(0, 200, true);
$wgNamespacesWithSubpages[NS_USER] = true;


// Highlight extension:
wfLoadExtension("SyntaxHighlight_GeSHi");

// Custom extension ?
require_once( "$IP/extensions/Theorems/Theorems.php" );

//Translate extension
$wgGroupPermissions['translator']['translate'] = true;
$wgGroupPermissions['translator']['skipcaptcha'] = true; // Bug 34182: needed with ConfirmEdit
$wgTranslateDocumentationLanguageCode = 'qqq';
# Add this if you want to enable access to page translation
$wgGroupPermissions['sysop']['pagetranslation'] = true;


wfLoadExtension( "UserMerge" );
// By default nobody can use this function, enable for bureaucrat?
$wgGroupPermissions['sysop']['usermerge'] = true;

// require_once("$IP/extensions/VisualEditor/VisualEditor.php");

// awesome editor
wfLoadExtension( "WikiEditor" );
$wgDefaultUserOptions['usebetatoolbar'] = 1;
$wgDefaultUserOptions['usebetatoolbar-cgd'] = 1;
$wgDefaultUserOptions['wikieditor-preview'] = 1;


// Licence WTFPL 2.0
// Modifies the toolbar to be editable
// Ask Gianluca about this
$wgHooks['BaseTemplateToolbox'][] = 'modifyToolbox';

function modifyToolbox( BaseTemplate $baseTemplate, array &$toolbox ) {

  static $keywords = array( 'WHATLINKSHERE', 'RECENTCHANGESLINKED', 'FEEDS', 'CONTRIBUTIONS', 'LOG', 'BLOCKIP', 'EMAILUSER', 'USERRIGHTS', 'UPLOAD', 'SPECIALPAGES', 'PRINT', 'PERMALINK', 'INFO' );

  $modifiedToolbox = array();

  // Walk in the MediaWiki:Sidebar message, section toolbox
  foreach ( $baseTemplate->data['sidebar']['TOOLBOX'] as $value ) {
      $specialLink = false;

      // Search if the keyword exists
      foreach ( $keywords as $key ) {
          if ( $value['href'] == Title::newFromText($key)->fixSpecialName()->getLinkURL() ) {
              $specialLink = true;

              // This is a keyword, hence add this special link
              if ( array_key_exists( strtolower($key), $toolbox ) ) {
                  $modifiedToolbox[strtolower($key)] = $toolbox[strtolower($key)];
                  break;
              }
          }
      }

      // This is a normal link
      if ( !$specialLink ) {
          $modifiedToolbox[] = $value;
      }
  }

  $toolbox = $modifiedToolbox;

  return true;
}

/// WARNING, WikiToLearn Developer!
/// PLEASE KEEP THIS LINE AS LAST!
require_once("$IP/../secrets/secrets.php");