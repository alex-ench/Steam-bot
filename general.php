<?php
session_start();
require __DIR__ . '\vendor\autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ConnectException;

if (isset($_POST['profile-option'])) {
    if (in_array('avatar', $_POST['profile-option'])) $send_avatar = true; else $send_avatar = false;
    if (in_array('background', $_POST['profile-option'])) $send_background = true; else $send_background = false;
    if (in_array('favorite-badge', $_POST['profile-option'])) $send_favorite_badge = true; else $send_favorite_badge = false;
    if (in_array('main-info', $_POST['profile-option'])) $send_main_info = true; else $send_main_info = false;
    if (in_array('showcases', $_POST['profile-option'])) $send_showcases = true; else $send_showcases = false;
    if (in_array('theme', $_POST['profile-option'])) $send_theme = true; else $send_theme = false;
    $access_token = $_SESSION['steam_accesstoken'];
    $path_to_data = __DIR__ . '\data\/' . $_SESSION['steam_steamid'];

    $client = new Client([
        'base_uri' => 'https://steamcommunity.com',
        'timeout' => 20.0,
    ]);

    function SendAvatar($path_to_data)
    {
        echo "<div class='box'><div class='container'><span class='start-finish-section'>[AVATAR]</span></div><div class='container'><div class='output-container'>" . PHP_EOL;
        $file = json_decode(file_get_contents($path_to_data . "\avatars.json"), TRUE);
        $avatar_rand = array_rand($file['avatars'], 1);
        $avatar = $file['avatars'][$avatar_rand];
        echo "<div class='output-items'><span class='output-info-name'>Avatar: </span><span class='item-output' id='avatar'><a href='https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/" . substr($avatar, 0, 2) . "/" . $avatar . "_medium.jpg' target='_blank'>{$avatar}</a></span></div></div></div>" . PHP_EOL;
        echo "<div class='container'><span class='start-finish-section'>[AVATAR]</span></div></div></div></div>" . PHP_EOL;
        return $avatar;
    }

    function SendBackground($path_to_data)
    {
        echo "<div class='box'><div class='container'><span class='start-finish-section'>[BACKGROUND]</span></div><div class='container'><div class='output-container'>" . PHP_EOL;
        $file = json_decode(file_get_contents($path_to_data . '\profile_items.json'), TRUE);
        $titles = array('appid', 'name', 'communityitemid');
        if (isset($file['profile_items']['profile_backgrounds'])){
        	$background_rand = array_rand($file['profile_items']['profile_backgrounds'], 1);
        	foreach ($titles as $title) $background[$title] = $file['profile_items']['profile_backgrounds'][$background_rand][$title];
        	echo "<div class='output-items'><span class='output-info-name'>Background: </span><span class='item-output' id='background'><a href='https://steamcommunity.com/profiles/{$_SESSION['steam_steamid']}/inventory/#753_6_{$background['communityitemid']}' target='_blank'>{$background['name']}</a> from <a href='https://store.steampowered.com/app/{$background['appid']}' target='_blank'>{$background['appid']}</a></span></div></div></div>" . PHP_EOL;
        	echo "<div class='container'><span class='start-finish-section'>[BACKGROUND]</span></div>" . PHP_EOL;
    	} else {
            foreach ($titles as $title) $background[$title] = NULL;
            echo "<div class='container'><div class='output-items'><span class='error'>No backgrounds were found.</span></div></div>";
    	}
        return $background;
    }

    function SendFavoriteBadge($path_to_data)
    {
        echo "<div class='box'><div class='container'><span class='start-finish-section'>[FAVORITE BADGE]</span></div><div class='container'><div class='output-container'>" . PHP_EOL;
        $file = json_decode(file_get_contents($path_to_data . '\badges.json'), TRUE);
        if (isset($file['badges'])){
        	$badge_favorite_rand = array_rand($file['badges'], 1);
        	$badge_favorite = $file['badges'][$badge_favorite_rand]['badgeid'];
        	echo "<div class='output-items'><span class='output-info-name'>Favorite badge: </span><span class='item-output' id='favorite-badge'><a href='https://steamcommunity.com/profiles/{$_SESSION['steam_steamid']}/badges/{$badge_favorite}' target='_blank'>{$badge_favorite}</a></span></div></div></div>" . PHP_EOL;
        	echo "<div class='container'><span class='start-finish-section'>[FAVORITE BADGE]</span></div>" . PHP_EOL;
    	} else {
    		$badge_favorite = NULL;
    		echo "<div class='container'><span class='error'>No badges were found.</span></div>" . PHP_EOL;
    	}
        return $badge_favorite;
    }

    function SendTheme($path_to_data)
    {
        echo "<div class='box'><div class='container'><span class='start-finish-section'>[THEME]</span></div><div class='container'><div class='output-container'>" . PHP_EOL;
        $file = json_decode(file_get_contents($path_to_data . '\themes.json'), TRUE);
        $theme_rand = array_rand($file['themes'], 1);
        $theme = $file['themes'][$theme_rand]['theme_id'];
        echo "<div class='output-items'><span class='output-info-name'>Theme: </span><span class='item-output' id='theme'>" . $file['themes'][$theme_rand]['title'] . "</span></div></div></div>" . PHP_EOL;
        echo "<div class='container'><span class='start-finish-section'>[THEME]</span></div>" . PHP_EOL;
        return $theme;
    }

    function CheckPost($client, $url)
    {
        try {
            $response = $client->request('POST', $url);
            if ($response->getStatusCode() >= 300) {
                $statusCode = $response->getStatusCode();
            } else {
                $statusCode = $response->getStatusCode();
            }
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $statusCode = $e->getResponse()->getStatusCode();
            }
        } catch (ConnectException $e) {
            $handlerContext = $e->getHandlerContext();
            if ($handlerContext['errno'] ?? 0) {
                $errno = (int)($handlerContext['errno']);
            }
            $errorMessage = $handlerContext['error'] ?? $e->getMessage();
            echo "<div class='container'><span class='error'>Status code: Status code: " . $errno . ", Message: " . $errorMessage . "</span></div>" . PHP_EOL;
        }
        if (isset($statusCode) and empty($errno)) {
            return $statusCode;
        }
    }

    if ($send_main_info) {
        $main_info = true;
        require(__DIR__ . '\main_info.php');
        if ($main_info !== false) {
            $body_info = "
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"sessionID\"

{$_SESSION['cookies']['sessionid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"type\"

profileSave
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"weblink_1_title\"


------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"weblink_1_url\"


------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"weblink_2_title\"


------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"weblink_2_url\"


------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"weblink_3_title\"


------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"weblink_3_url\"


------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"personaName\"

{$personaName}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"real_name\"

{$real_name}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"customURL\"

{$_SESSION['steam_customurl']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"country\"

{$_SESSION['steam_loccountrycode']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"state\"

{$_SESSION['steam_locstatecode']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"city\"

{$_SESSION['steam_loccityid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"summary\"

{$summary}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"type\"

profileSave
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"sessionID\"

{$_SESSION['cookies']['sessionid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"json\"

1
------{$_SESSION['WebKitFormBoundary']}
";
            $content_length = mb_strlen($body_info, '8bit');
            $headers_info = [
                'Host' => 'steamcommunity.com',
                'Connection' => 'keep-alive',
                'Content-Length' => "{$content_length}",
                'Accept' => 'application/json, text/plain, */*',
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88 Safari/537.36',
                'Content-Type' => "multipart/form-data; boundary=----{$_SESSION['WebKitFormBoundary']}",
                'Origin' => 'https://steamcommunity.com',
                'Sec-Fetch-Site' => 'same-origin',
                'Sec-Fetch-Mode' => 'cors',
                'Sec-Fetch-Dest' => 'empty',
                'Referer' => "https://steamcommunity.com/profiles/{$_SESSION['steam_steamid']}/edit/info",
                'Accept-Encoding' => 'gzip, deflate, br',
                'Accept-Language' => 'ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7',
                'Cookie' => "steamCountry={$_SESSION['cookies']['steamCountry']}; steamMachineAuth{$_SESSION['steam_steamid']}={$_SESSION['cookies']['steamMachineAuth']}; sessionid={$_SESSION['cookies']['sessionid']}; browserid={$_SESSION['cookies']['browserid']}; Steam_Language={$_SESSION['cookies']['Steam_Language']}; timezoneOffset={$_SESSION['cookies']['timezoneOffset']}; _ga={$_SESSION['cookies']['_ga']}; _gid={$_SESSION['cookies']['_gid']}; steamLoginSecure={$_SESSION['cookies']['steamLoginSecure']}; steamRememberLogin={$_SESSION['cookies']['steamRememberLogin']}",
            ];
            $request = new Request('POST', "/profiles/{$_SESSION['steam_steamid']}/edit/info", $headers_info, $body_info);
            $client->send($request);
        }
    }
    if ($send_showcases) {
        $showcases = true;
        require_once(__DIR__ . '\showcases.php');
        if ($showcases !== false) {
            $body_showcase = "
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"profile_showcase[]\"

{$profile_showcase[0]}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"profile_showcase_purchaseid[]\"

0
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"profile_showcase[]\"

{$profile_showcase[1]}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"profile_showcase_purchaseid[]\"

0
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"profile_showcase[]\"

{$profile_showcase[2]}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"profile_showcase_purchaseid[]\"

0
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"profile_showcase[]\"

{$profile_showcase[3]}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"profile_showcase_purchaseid[]\"

0
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"profile_showcase[]\"

{$profile_showcase[4]}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"profile_showcase_purchaseid[]\"

0
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"profile_showcase[]\"

{$profile_showcase[5]}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"profile_showcase_purchaseid[]\"

0
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"profile_showcase[]\"

{$profile_showcase[6]}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"profile_showcase_purchaseid[]\"

0
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"profile_showcase[]\"

{$profile_showcase[7]}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"profile_showcase_purchaseid[]\"

0
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"profile_showcase[]\"

{$profile_showcase[8]}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"profile_showcase_purchaseid[]\"

0
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"profile_showcase[]\"

{$profile_showcase[9]}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"profile_showcase_purchaseid[]\"

0
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"profile_showcase[]\"

{$profile_showcase[10]}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"profile_showcase_purchaseid[]\"

0
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"profile_showcase[]\"

{$profile_showcase[11]}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"profile_showcase_purchaseid[]\"

0
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"profile_showcase[]\"

{$profile_showcase[12]}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"profile_showcase_purchaseid[]\"

0
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"profile_showcase[]\"

{$profile_showcase[13]}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"profile_showcase_purchaseid[]\"

0
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"profile_showcase[]\"

{$profile_showcase[14]}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"profile_showcase_purchaseid[]\"

0
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"profile_showcase[]\"

{$profile_showcase[15]}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"profile_showcase_purchaseid[]\"

0
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"profile_showcase[]\"

{$profile_showcase[16]}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"profile_showcase_purchaseid[]\"

0
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"profile_showcase[]\"

{$profile_showcase[17]}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"profile_showcase_purchaseid[]\"

0
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"profile_showcase[]\"

{$profile_showcase[18]}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"profile_showcase_purchaseid[]\"

0
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"profile_showcase[]\"

{$profile_showcase[19]}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"profile_showcase_purchaseid[]\"

0
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"profile_showcase[]\"

{$profile_showcase[20]}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"profile_showcase_purchaseid[]\"

0
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[2_0][0_0][appid]\"

{$game_collector[0]['appid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[2_0][1_0][appid]\"

{$game_collector[1]['appid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[2_0][2_0][appid]\"

{$game_collector[2]['appid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[2_0][3_0][appid]\"

{$game_collector[3]['appid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[3_0][0][appid]\"

{$items[0]['appid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[3_0][0][item_contextid]\"

{$items[0]['item_contextid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[3_0][0][item_assetid]\"

{$items[0]['item_assetid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[3_0][1][appid]\"

{$items[1]['appid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[3_0][1][item_contextid]\"

{$items[1]['item_contextid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[3_0][1][item_assetid]\"

{$items[1]['item_assetid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[3_0][2][appid]\"

{$items[2]['appid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[3_0][2][item_contextid]\"

{$items[2]['item_contextid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[3_0][2][item_assetid]\"

{$items[2]['item_assetid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[3_0][3][appid]\"

{$items[3]['appid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[3_0][3][item_contextid]\"

{$items[3]['item_contextid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[3_0][3][item_assetid]\"

{$items[3]['item_assetid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[3_0][4][appid]\"

{$items[4]['appid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[3_0][4][item_contextid]\"

{$items[4]['item_contextid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[3_0][4][item_assetid]\"

{$items[4]['item_assetid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[3_0][5][appid]\"

{$items[5]['appid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[3_0][5][item_contextid]\"

{$items[5]['item_contextid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[3_0][5][item_assetid]\"

{$items[5]['item_assetid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[3_0][6][appid]\"

{$items[6]['appid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[3_0][6][item_contextid]\"

{$items[6]['item_contextid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[3_0][6][item_assetid]\"

{$items[6]['item_assetid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[3_0][7][appid]\"

{$items[7]['appid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[3_0][7][item_contextid]\"

{$items[7]['item_contextid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[3_0][7][item_assetid]\"

{$items[7]['item_assetid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[3_0][8][appid]\"

{$items[8]['appid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[3_0][8][item_contextid]\"

{$items[8]['item_contextid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[3_0][8][item_assetid]\"

{$items[8]['item_assetid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[3_0][9][appid]\"

{$items[9]['appid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[3_0][9][item_contextid]\"

{$items[9]['item_contextid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[3_0][9][item_assetid]\"

{$items[9]['item_assetid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[4_0][0_0][appid]\"

{$items_for_trade[0]['appid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[4_0][0_0][item_contextid]\"

{$items_for_trade[0]['item_contextid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[4_0][0_0][item_assetid]\"

{$items_for_trade[0]['item_assetid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[4_0][1_0][appid]\"

{$items_for_trade[1]['appid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[4_0][1_0][item_contextid]\"

{$items_for_trade[1]['item_contextid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[4_0][1_0][item_assetid]\"

{$items_for_trade[1]['item_assetid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[4_0][2_0][appid]\"

{$items_for_trade[2]['appid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[4_0][2_0][item_contextid]\"

{$items_for_trade[2]['item_contextid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[4_0][2_0][item_assetid]\"

{$items_for_trade[2]['item_assetid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[4_0][3_0][appid]\"

{$items_for_trade[3]['appid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[4_0][3_0][item_contextid]\"

{$items_for_trade[3]['item_contextid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[4_0][3_0][item_assetid]\"

{$items_for_trade[3]['item_assetid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[4_0][4_0][appid]\"

{$items_for_trade[4]['appid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[4_0][4_0][item_contextid]\"

{$items_for_trade[4]['item_contextid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[4_0][4_0][item_assetid]\"

{$items_for_trade[4]['item_assetid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[4_0][5_0][appid]\"

{$items_for_trade[5]['appid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[4_0][5_0][item_contextid]\"

{$items_for_trade[5]['item_contextid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[4_0][5_0][item_assetid]\"

{$items_for_trade[5]['item_assetid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[4_0][6_0][notes]\"

{$items_for_trade[6]['notes']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"profile_showcase_style_5_0\"

1
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[5_0][0][badgeid]\"

{$badge_collector[0]['badgeid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[5_0][0][appid]\"


------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[5_0][0][border_color]\"


------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[5_0][1][badgeid]\"

{$badge_collector[1]['badgeid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[5_0][1][appid]\"


------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[5_0][1][border_color]\"


------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[5_0][2][badgeid]\"

{$badge_collector[2]['badgeid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[5_0][2][appid]\"


------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[5_0][2][border_color]\"


------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[5_0][3][badgeid]\"

{$badge_collector[3]['badgeid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[5_0][3][appid]\"


------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[5_0][3][border_color]\"


------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[5_0][4][badgeid]\"

{$badge_collector[4]['badgeid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[5_0][4][appid]\"


------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[5_0][4][border_color]\"


------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[5_0][5][badgeid]\"

{$badge_collector[5]['badgeid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[5_0][5][appid]\"


------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[5_0][5][border_color]\"


------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[6_0][0][appid]\"

{$game_favorite[0]['appid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[7_0][0][publishedfileid]\"

{$screenshots[0]['publishedfileid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[7_0][1][publishedfileid]\"

{$screenshots[1]['publishedfileid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[7_0][2][publishedfileid]\"

{$screenshots[2]['publishedfileid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[7_0][3][publishedfileid]\"

{$screenshots[3]['publishedfileid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[8_0][0][title]\"

{$custom_info_box[0]['title']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[8_0][0][notes]\"

{$custom_info_box[0]['notes']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[9_0][0][accountid]\"

{$favorite_group[0]['accountid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[11_0][0][appid]\"

{$workshop['favorited_workshop']['appid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[11_0][0][publishedfileid]\"

{$workshop['favorited_workshop']['publishedfileid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[12_0][0][appid]\"

{$workshop['created_workshop'][0]['appid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[12_0][0][publishedfileid]\"

{$workshop['created_workshop'][0]['publishedfileid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[12_0][1][appid]\"

{$workshop['created_workshop'][1]['appid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[12_0][1][publishedfileid]\"

{$workshop['created_workshop'][1]['publishedfileid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[12_0][2][appid]\"

{$workshop['created_workshop'][2]['appid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[12_0][2][publishedfileid]\"

{$workshop['created_workshop'][2]['publishedfileid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[12_0][3][appid]\"

{$workshop['created_workshop'][3]['appid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[12_0][3][publishedfileid]\"

{$workshop['created_workshop'][3]['publishedfileid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[12_0][4][appid]\"

{$workshop['created_workshop'][4]['appid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[12_0][4][publishedfileid]\"

{$workshop['created_workshop'][4]['publishedfileid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[13_0][0][publishedfileid]\"

{$artworks_created[0]['publishedfileid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[13_0][1][publishedfileid]\"

{$artworks_created[1]['publishedfileid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[13_0][2][publishedfileid]\"

{$artworks_created[2]['publishedfileid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[13_0][3][publishedfileid]\"

{$artworks_created[3]['publishedfileid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[14_0][0][publishedfileid]\"

{$videos[0]['publishedfileid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[14_0][1][publishedfileid]\"

{$videos[1]['publishedfileid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[14_0][2][publishedfileid]\"

{$videos[2]['publishedfileid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[14_0][3][publishedfileid]\"

{$videos[3]['publishedfileid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[15_0][0][appid]\"

{$guide_favorite[0]['appid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[15_0][0][publishedfileid]\"

{$guide_favorite[0]['publishedfileid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[16_0][0][appid]\"

{$guides_created[0]['appid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[16_0][0][publishedfileid]\"

{$guides_created[0]['publishedfileid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[16_0][1][appid]\"

{$guides_created[1]['appid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[16_0][1][publishedfileid]\"

{$guides_created[1]['publishedfileid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[16_0][2][appid]\"

{$guides_created[2]['appid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[16_0][2][publishedfileid]\"

{$guides_created[2]['publishedfileid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[16_0][3][appid]\"

{$guides_created[3]['appid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[16_0][3][publishedfileid]\"

{$guides_created[3]['publishedfileid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[17_0][0][title]\"

{$achievements[0]['title']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[17_0][0][appid]\"

{$achievements[0]['appid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[17_0][1][title]\"

{$achievements[1]['title']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[17_0][1][appid]\"

{$achievements[1]['appid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[17_0][2][title]\"

{$achievements[2]['title']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[17_0][2][appid]\"

{$achievements[2]['appid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[17_0][3][title]\"

{$achievements[3]['title']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[17_0][3][appid]\"

{$achievements[3]['appid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[17_0][4][title]\"

{$achievements[4]['title']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[17_0][4][appid]\"

{$achievements[4]['appid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[17_0][5][title]\"

{$achievements[5]['title']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[17_0][5][appid]\"

{$achievements[5]['appid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[17_0][6][title]\"

{$achievements[6]['title']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[17_0][6][appid]\"

{$achievements[6]['appid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[22_0][0][publishedfileid]\"

{$artwork_featured[0]['publishedfileid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[23_0][0][appid]\"

{$completionist[0]['appid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"rgShowcaseConfig[23_0][1][appid]\"

{$completionist[1]['appid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"type\"

showcases
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"sessionID\"

{$_SESSION['cookies']['sessionid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"json\"

1
------{$_SESSION['WebKitFormBoundary']}--
";
            $content_length = mb_strlen($body_showcase, '8bit');
            $headers_showcase = [
                'Host' => 'steamcommunity.com',
                'Connection' => 'keep-alive',
                'Content-Length' => "{$content_length}",
                'Accept' => 'application/json, text/plain, */*',
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36',
                'Content-Type' => "multipart/form-data; boundary=----{$_SESSION['WebKitFormBoundary']}",
                'Origin' => 'https://steamcommunity.com',
                'Sec-Fetch-Site' => 'same-origin',
                'Sec-Fetch-Mode' => 'cors',
                'Sec-Fetch-Dest' => 'empty',
                'Referer' => "https://steamcommunity.com/profiles/{$_SESSION['steam_steamid']}/edit/showcases",
                'Accept-Encoding' => 'gzip, deflate, br',
                'Accept-Language' => 'ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7',
                'Cookie' => "steamCountry={$_SESSION['cookies']['steamCountry']}; steamMachineAuth{$_SESSION['steam_steamid']}={$_SESSION['cookies']['steamMachineAuth']}; sessionid={$_SESSION['cookies']['sessionid']}; browserid={$_SESSION['cookies']['browserid']}; Steam_Language={$_SESSION['cookies']['Steam_Language']}; timezoneOffset={$_SESSION['cookies']['timezoneOffset']}; _ga={$_SESSION['cookies']['_ga']}; _gid={$_SESSION['cookies']['_gid']}; steamLoginSecure={$_SESSION['cookies']['steamLoginSecure']}; steamRememberLogin={$_SESSION['cookies']['steamRememberLogin']}",
            ];
            $request = new Request('POST', "/profiles/{$_SESSION['steam_steamid']}/edit/showcases", $headers_showcase, $body_showcase);
            $client->send($request);
        }
    }
    if ($send_avatar) {
        if (file_exists($path_to_data . '\avatars.json')) {
            $avatar = SendAvatar($path_to_data);
            $_SESSION['steam_avatarmedium'] = "https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/" . substr($avatar, 0, 2) . "/" . $avatar . "_medium.jpg";
            $url = "https://steamcommunity.com/actions/selectPreviousAvatar/";
            $body_avatar = "
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"sessionid\"

{$_SESSION['cookies']['sessionid']}
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"json\"

1
------{$_SESSION['WebKitFormBoundary']}
Content-Disposition: form-data; name=\"sha\"

{$avatar}
------{$_SESSION['WebKitFormBoundary']}
";
            $content_length = mb_strlen($body_avatar, '8bit');
            $headers = [
                'Host' => 'steamcommunity.com',
                'Connection' => 'keep-alive',
                'Content-Length' => "{$content_length}",
                'Accept' => 'application/json, text/plain, */*',
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88 Safari/537.36',
                'Content-Type' => "multipart/form-data; boundary=----{$_SESSION['WebKitFormBoundary']}",
                'Origin' => 'https://steamcommunity.com',
                'Sec-Fetch-Site' => 'same-origin',
                'Sec-Fetch-Mode' => 'cors',
                'Sec-Fetch-Dest' => 'empty',
                'Referer' => "https://steamcommunity.com/profiles/{$_SESSION['steam_steamid']}/edit/avatar",
                'Accept-Encoding' => 'gzip, deflate, br',
                'Accept-Language' => 'ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7',
                'Cookie' => "steamCountry={$_SESSION['cookies']['steamCountry']}; steamMachineAuth{$_SESSION['steam_steamid']}={$_SESSION['cookies']['steamMachineAuth']}; sessionid={$_SESSION['cookies']['sessionid']}; browserid={$_SESSION['cookies']['browserid']}; Steam_Language={$_SESSION['cookies']['Steam_Language']}; timezoneOffset={$_SESSION['cookies']['timezoneOffset']}; _ga={$_SESSION['cookies']['_ga']}; _gid={$_SESSION['cookies']['_gid']}; steamLoginSecure={$_SESSION['cookies']['steamLoginSecure']}; steamRememberLogin={$_SESSION['cookies']['steamRememberLogin']}",
            ];
            $request = new Request('POST', $url, $headers, $body_avatar);
            $client->send($request);
        } else {
            echo "<div class='box'><div class='output-container'><div class='output-items'><span class='error'>File with avatars does not exist!</span></div></div></div>";
        }
    }
    if ($send_background) {
        if (file_exists($path_to_data . '\profile_items.json')) {
            $background = SendBackground($path_to_data);
            $url = "https://api.steampowered.com/IPlayerService/SetProfileBackground/v1/?access_token={$_SESSION['steam_accesstoken']}&communityitemid={$background['communityitemid']}";
            $statusCode = CheckPost($client, $url);
            if (isset($statusCode) and $statusCode === 200) {
                $request = new Request('POST', $url);
                $client->send($request);
                echo "<div class='container'><span class='success'>Sending background successful.</span></div></div></div></div>" . PHP_EOL;
            }
            if (isset($statusCode) and $statusCode !== 200) {
                echo "<div class='container'><span class='error'>Status code: " . $statusCode . ", Something is wrong.</span></div></div></div></div>" . PHP_EOL;
            }
        } else {
            echo "<div class='box'><div class='output-items'><span class='error'>File with backgrounds does not exist!</span></div></div>";
        }
    }
    if ($send_favorite_badge) {
        if (file_exists($path_to_data . '\badges.json')) {
            $badge_favorite = SendFavoriteBadge($path_to_data);
            $url = "https://api.steampowered.com/IPlayerService/SetFavoriteBadge/v1/?access_token={$_SESSION['steam_accesstoken']}&badgeid=$badge_favorite";
            $statusCode = CheckPost($client, $url);
            if (isset($statusCode) and $statusCode === 200) {
                $request = new Request('POST', $url);
                $client->send($request);
                echo "<div class='container'><span class='success'>Sending favorite badge successful.</span></div></div></div></div>" . PHP_EOL;
            }
            if (isset($statusCode) and $statusCode !== 200) {
                echo "<div class='container'><span class='error'>Status code: " . $statusCode . ", Something is wrong.</span></div></div></div></div>" . PHP_EOL;
            }
        } else {
            echo "<div class='box'><div class='output-items'><span class='error'>File with badges does not exist!</span></div></div>";
        }
    }
    if ($send_theme) {
        if (file_exists($path_to_data . '\themes.json')) {
            $theme = SendTheme($path_to_data);
            $url = "https://api.steampowered.com/IPlayerService/SetProfileTheme/v1/?access_token={$_SESSION['steam_accesstoken']}&theme_id=$theme";
            $statusCode = CheckPost($client, $url);
            if (isset($statusCode) and $statusCode === 200) {
                $request = new Request('POST', $url);
                $client->send($request);
                echo "<div class='container'><span class='success'>Sending theme successful.</span></div></div></div></div>" . PHP_EOL;
            }
            if (isset($statusCode) and $statusCode !== 200) {
                echo "<div class='container'><span class='error'>Status code: " . $statusCode . ", Something is wrong.</span></div></div></div></div>" . PHP_EOL;
            }
        } else {
            echo "<div class='box'><div class='output-items'><span class='error'>File with themes does not exist!</span></div></div>";
        }
    }
} else {
    echo "<div class='box'><div class='output-items'><span class='error'>All options is disabled.</span></div></div>";
}