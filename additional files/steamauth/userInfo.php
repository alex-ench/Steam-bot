<?php
if (empty($_SESSION['steam_uptodate']) or empty($_SESSION['steam_personaname'])) {
    $url = file_get_contents("https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=" . $_SESSION['steam_apikey'] . "&steamids=" . $_SESSION['steamid']);
    $content = json_decode($url, true);
    $_SESSION['steam_steamid'] = $content['response']['players'][0]['steamid'];
    if ($content['response']['players'][0]['communityvisibilitystate'] === 1) $_SESSION['steam_communityvisibilitystate'] = "Account not visible";
    if ($content['response']['players'][0]['communityvisibilitystate'] === 3) $_SESSION['steam_communityvisibilitystate'] = "Account is public";
    $_SESSION['steam_profilestate'] = $content['response']['players'][0]['profilestate'];
    $_SESSION['steam_personaname'] = $content['response']['players'][0]['personaname'];
    if (isset($content['response']['players'][0]['lastlogoff'])) $_SESSION['steam_lastlogoff'] = gmdate("Y-m-d\ T H:i:s\ ", $content['response']['players'][0]['lastlogoff']); else $_SESSION[' steam_lastlogoff'] = "No last login information exists.";
    $_SESSION['steam_profileurl'] = $content['response']['players'][0]['profileurl'];
    $_SESSION['steam_avatar'] = $content['response']['players'][0]['avatar'];
    $_SESSION['steam_avatarmedium'] = $content['response']['players'][0]['avatarmedium'];
    $_SESSION['steam_avatarfull'] = $content['response']['players'][0]['avatarfull'];
    if ($content['response']['players'][0]['personastate'] === 0) $_SESSION['steam_personastate'] = "Offline";
    if ($content['response']['players'][0]['personastate'] === 1) $_SESSION['steam_personastate'] = "Online";
    if ($content['response']['players'][0]['personastate'] === 2) $_SESSION['steam_personastate'] = "Busy";
    if ($content['response']['players'][0]['personastate'] === 3) $_SESSION['steam_personastate'] = "Away";
    if ($content['response']['players'][0]['personastate'] === 4) $_SESSION['steam_personastate'] = "Snooze";
    if ($content['response']['players'][0]['personastate'] === 5) $_SESSION['steam_personastate'] = "looking to trade";
    if ($content['response']['players'][0]['personastate'] === 6) $_SESSION['steam_personastate'] = "looking to play";
    if (isset($content['response']['players'][0]['realname'])) $_SESSION['steam_realname'] = $content['response']['players'][0]['realname']; else $_SESSION['steam_realname'] = "Real name not given.";
    if (isset($content['response']['players'][0]['loccountrycode'])) $_SESSION['steam_loccountrycode'] = $content['response']['players'][0]['loccountrycode']; else $_SESSION['steam_loccountrycode'] = "Country not given.";
    if (isset($content['response']['players'][0]['locstatecode'])) $_SESSION['steam_locstatecode'] = $content['response']['players'][0]['locstatecode']; else $_SESSION['steam_locstatecode'] = " State not given.";
    if (isset($content['response']['players'][0]['loccityid'])) $_SESSION['steam_loccityid'] = $content['response']['players'][0]['loccityid']; else $_SESSION['steam_loccityid'] = " City not given.";
    if ($_SESSION['steam_loccountrycode'] !== "Country not given")
        $_SESSION['steam_location'] = $_SESSION['steam_loccountrycode'] . " " . $_SESSION['steam_locstatecode'] . " " . $_SESSION['steam_loccityid'];
    else
        $_SESSION['steam_location'] = "Location not given.";
    if (isset($content['response']['players'][0]['primaryclanid'])) $_SESSION['steam_primaryclanid'] = $content['response']['players'][0]['primaryclanid']; else $_SESSION['steam_primaryclanid'] = "Primary    clan id not set";
    $_SESSION['steam_timecreated'] = gmdate("Y-m-d\ T H:i:s\ ", $content['response']['players'][0]['timecreated']);
    $_SESSION['steam_uptodate'] = time();
    $_SESSION['steam_customurl'] = substr($_SESSION['steam_profileurl'], 30, -1);
}
// Version 3.2
?>
    
