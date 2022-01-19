<?php
session_start();
require __DIR__ . '\vendor\autoload.php';
require __DIR__ . '\additional files\simple_html_dom.php';
ini_set('max_execution_time', 600);

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ConnectException;

if (isset($_POST['parse-customization'])) {
    if (in_array('current', $_POST['parse-customization'])) {
        $parse_current_customization = true;
    } else {
        $parse_current_customization = false;
    }
    if (in_array('games-with-achievements', $_POST['parse-customization'])) {
        $parse_games_with_achievements = true;
    } else {
        $parse_games_with_achievements = false;
    }
    if (in_array('games-with-inventory', $_POST['parse-customization'])) {
        $parse_games_with_inventory = true;
    } else {
        $parse_games_with_inventory = false;
    }
    if (in_array('achievements', $_POST['parse-customization'])) {
        $parse_achievements = true;
    } else {
        $parse_achievements = false;
    }
    if (in_array('artworks', $_POST['parse-customization'])) {
        $parse_artworks = true;
    } else {
        $parse_artworks = false;
    }
    if (in_array('avatars', $_POST['parse-customization'])) {
        $parse_avatars = true;
    } else {
        $parse_avatars = false;
    }
    if (in_array('badges', $_POST['parse-customization'])) {
        $parse_badges = true;
    } else {
        $parse_badges = false;
    }
    if (in_array('completionist', $_POST['parse-customization'])) {
        $parse_completionist = true;
    } else {
        $parse_completionist = false;
    }
    if (in_array('friends', $_POST['parse-customization'])) {
        $parse_friends = true;
    } else {
        $parse_friends = false;
    }
    if (in_array('games', $_POST['parse-customization'])) {
        $parse_games = true;
    } else {
        $parse_games = false;
    }
    if (in_array('groups', $_POST['parse-customization'])) {
        $parse_groups = true;
    } else {
        $parse_groups = false;
    }
    if (in_array('guides', $_POST['parse-customization'])) {
        $parse_guides = true;
    } else {
        $parse_guides = false;
    }
    if (in_array('inventory', $_POST['parse-customization'])) {
        $parse_inventory = true;
    } else {
        $parse_inventory = false;
    }
    if (in_array('profile-items', $_POST['parse-customization'])) {
        $parse_profile_items = true;
    } else {
        $parse_profile_items = false;
    }
    if (in_array('reviews', $_POST['parse-customization'])) {
        $parse_reviews = true;
    } else {
        $parse_reviews = false;
    }
    if (in_array('screenshots', $_POST['parse-customization'])) {
        $parse_screenshots = true;
    } else {
        $parse_screenshots = false;
    }
    if (in_array('themes', $_POST['parse-customization'])) {
        $parse_themes = true;
    } else {
        $parse_themes = false;
    }
    if (in_array('videos', $_POST['parse-customization'])) {
        $parse_videos = true;
    } else {
        $parse_videos = false;
    }
    if (in_array('workshop', $_POST['parse-customization'])) {
        $parse_workshop = true;
    } else {
        $parse_workshop = false;
    }
    $client = new Client([
        'base_uri' => 'https://steamcommunity.com',
        'timeout' => 20.0,
        'http_errors' => true,
    ]);
    $path_parse = __DIR__ . "\data\/{$_SESSION['steam_steamid']}";

    function CurrentCustomization($path_parse, $client)
    {
        echo "<div class='container'><div class='output-items'><span class='start-finish-section'>[CURRENT CUSTOMIZATION]</span>" . PHP_EOL;
        $file_output = $path_parse . '\current.json';
        //'GetPlayerSummaries' => array('players'),
        $methods = array('GetProfileCustomization' => array('customizations', 'slots_available', 'profile_theme', 'profile_preferences'),
            'GetProfileItemsEquipped' => array('profile_background', 'mini_profile_background', 'avatar_frame', 'animated_avatar', 'profile_modifier'),
            'GetFavoriteBadge' => array('has_favorite_badge', 'badgeid'),);
        foreach ($methods as $method => $type) {
            if ($method === 'GetProfileCustomization') {
                $url = "https://api.steampowered.com/IPlayerService/$method/v1/?access_token={$_SESSION['steam_accesstoken']}&steamid={$_SESSION['steam_steamid']}&include_inactive_customizations=1";
            }
            if ($method === 'GetProfileItemsEquipped' or $method === 'GetFavoriteBadge') {
                $url = "https://api.steampowered.com/IPlayerService/$method/v1/?access_token={$_SESSION['steam_accesstoken']}&steamid={$_SESSION['steam_steamid']}";
            }
            if ($method === 'GetPlayerSummaries') {
                $url = "https://api.steampowered.com/ISteamUser/$method/v2/?key={$_SESSION['steam_apikey']}&steamids={$_SESSION['steam_steamid']}";
            }
            $statusCode = CheckGet($client, $url);
            if (isset($statusCode) and $statusCode === 200) {
                $response = $client->request('GET', $url);
                $response_body = $response->getBody();
                $decodejson = json_decode($response_body, TRUE);
                for ($i = 0; $i < count($type); $i++) {
                    if (!empty($decodejson['response'][$type[$i]])) {
                        $current['current'][substr($method, 3, 20)][$type[$i]] = $decodejson['response'][$type[$i]];
                        echo "<div class='container'><span class='output-info-name'>Current </span><span class='item-output' id='current'>" . str_replace("_", " ", $type[$i]) . " has been recorded.</span></div>" . PHP_EOL;
                        }
                    if (empty($decodejson['response'][$type[$i]])) {
                        echo "<div class='container'><span class='output-info-name'>No current </span><span class='item-output' id='current'>" . str_replace("_", " ", $type[$i]) . " were found.</span></div>" . PHP_EOL;
                        }
                }
            }
            if (isset($statusCode) and $statusCode !== 200) {
                echo "<div class='container'><span class='error'>Status code: " . $statusCode . ", Something is wrong.</span></div>" . PHP_EOL;
            }
        }
        echo "<span class='start-finish-section'>[CURRENT CUSTOMIZATION]</span></div></div>" . PHP_EOL;
        file_put_contents($file_output, json_encode($current, JSON_UNESCAPED_UNICODE));
    }

    function GamesWithAchievements($path_parse, $client)
    {
        echo "<div class='container'><div class='output-items'><span class='start-finish-section'>[GAMES WITH ACHIEVEMENTS]</span>" . PHP_EOL;
        $file_output = $path_parse . '\games_with_achievements.json';
        $headers = [
            'Host' => 'steamcommunity.com',
            'Connection' => 'keep-alive',
            'Cache-Control' => 'max-age=0',
            'sec-ch-ua' => ' Not;A Brand";v="99", "Yandex";v="91", "Chromium";v="91',
            'sec-ch-ua-mobile' => '?0',
            'Upgrade-Insecure-Requests' => "1",
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.135 YaBrowser/21.6.3.757 Yowser/2.5 Safari/537.36',
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
            'Sec-Fetch-Site' => 'cross-site',
            'Sec-Fetch-Mode' => 'navigate',
            'Sec-Fetch-User' => '?1',
            'Sec-Fetch-Dest' => 'document',
            'Accept-Encoding' => 'gzip, deflate, br',
            'Accept-Language' => 'ru,en;q=0.9',
            'Cookie' => "timezoneOffset={$_SESSION['cookies']['timezoneOffset']};  _ga={$_SESSION['cookies']['_ga']}; _gid={$_SESSION['cookies']['_gid']}; sessionid={$_SESSION['cookies']['sessionid']}; browserid={$_SESSION['cookies']['browserid']}; Steam_Language={$_SESSION['cookies']['Steam_Language']}; steamCountry={$_SESSION['cookies']['steamCountry']}; steamLoginSecure={$_SESSION['cookies']['steamLoginSecure']}; steamMachineAuth{$_SESSION['steam_steamid']}={$_SESSION['cookies']['steamMachineAuth']}; steamRememberLogin={$_SESSION['cookies']['steamRememberLogin']};",
        ];
        $url = "https://steamcommunity.com/profiles/{$_SESSION['steam_steamid']}/edit";
        $games_with_achievements = array();
        $statusCode = CheckGet($client, $url);
        if (isset($statusCode) and $statusCode === 200) {
            $request = new Request('GET', $url, $headers);
            $response = $client->send($request);
            $response_body = $response->getBody();
            $get = str_get_html($response_body);
            foreach ($get->find('*[text/javascript]') as $backup) {
                if (strpos($backup->innertext, "g_rgAchievementShowcaseGamesWithAchievements") !== false) {
                    $a = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $backup->innertext);
                    $games_with_achievements['games_with_achievements'] = json_decode(trim(substr($a, 47), ";"), true);
                }
            }
            if (!empty($games_with_achievements)) {
                echo "<div class='container'><span class='output-info-name'>Games with achievements: </span><span class='item-output' id='games-with-achievements'>" . count($games_with_achievements['games_with_achievements']) . " found.</span></div>" . PHP_EOL;
                file_put_contents($file_output, json_encode($games_with_achievements, JSON_UNESCAPED_UNICODE));
            } else {
                echo "<div class='container'><span class='error'>No games with achievements were found.</span></div>" . PHP_EOL;
                if (file_exists($file_output)) {
                    unlink($file_output);
                }
            }
        }
        if (isset($statusCode) and $statusCode !== 200) {
            echo "<div class='container'><span class='error'>Status code: " . $statusCode . ", Something is wrong.</span></div>" . PHP_EOL;
        }
        echo "<span class='start-finish-section'>[GAMES WITH ACHIEVEMENTS]</span></div></div>" . PHP_EOL;
    }

    function GamesWithInventory($path_parse, $client)
    {
        echo "<div class='container'><div class='output-items'><span class='start-finish-section'>[GAMES WITH INVENTORY]</span>" . PHP_EOL;
        $file_output = $path_parse . "\games_with_inventory.json";
        $url = "https://steamcommunity.com/profiles/{$_SESSION['steam_steamid']}/inventory/";
        $statusCode = CheckGet($client, $url);
        if (isset($statusCode) and $statusCode === 200) {
            $request = new Request('GET', $url);
            $response = $client->send($request);
            $response_body = $response->getBody();
            $get = str_get_html($response_body);
            foreach ($get->find('*[text/javascript]') as $backup) {
                if (strpos($backup->innertext, 'var g_rgAppContextData = ') !== false) {
                    preg_match('/{"(.*)"}}}}/', $backup, $m);
                    if (!empty($m[0])) {
                        $decodejson = json_decode($m[0], true);
                    }
                }
            }
            if (isset($decodejson)) {
                $keys = array_keys($decodejson);
                $titles = array('appid', 'name', 'asset_count', 'trade_permissions', 'owner_only', 'icon', 'inventory_logo');
                for ($i = 0; $i < count($decodejson); $i++) {
                    foreach ($titles as $title) {
                        $games_with_inventory['games_with_inventory'][$i][$title] = $decodejson[$keys[$i]][$title];
                    }
                    $keys_context = array_keys($decodejson[$keys[$i]]['rgContexts']);
                    $games_with_inventory['games_with_inventory'][$i]['contextid'] = $decodejson[$keys[$i]]['rgContexts'][$keys_context[0]]['id'];
                    echo "<div class='container'><span class='output-info-name'>Game with inventory: </span><span class='item-output' id='games-with-achievements'>({$games_with_inventory['games_with_inventory'][$i]['name']}) has been recorded.</span></div>" . PHP_EOL;
                }
                echo "<div class='container'><span class='output-info-name'>Games with inventory: </span><span class='item-output' id='games-with-achievements'>" . count($games_with_inventory['games_with_inventory']) . " found.</span></div>" . PHP_EOL;
                file_put_contents($file_output, json_encode($games_with_inventory, JSON_UNESCAPED_UNICODE));
            } else {
                echo "<div class='container'><span class='error'>No games with inventory were found.</span></div>" . PHP_EOL;
                if (file_exists($file_output)) {
                    unlink($file_output);
                }
            }
        }
        if (isset($statusCode) and $statusCode !== 200) {
            echo "<div class='container'><span class='error'>Status code: " . $statusCode . ", Something is wrong.</span></div>" . PHP_EOL;
        }
        echo "<span class='start-finish-section'>[GAMES WITH INVENTORY]</span></div></div>" . PHP_EOL;
    }

    function Achievements($path_parse, $client)
    {
        echo "<div class='container'><div class='output-items'><span class='start-finish-section'>[ACHIEVEMENTS]</span>" . PHP_EOL;
        $file_output = $path_parse . '\achievements.json';
        if (file_exists($path_parse . '\games_with_achievements.json')) {
            $headers = [
                'Host' => 'steamcommunity.com',
                'Connection' => 'keep-alive',
                'Cache-Control' => 'max-age=0',
                'sec-ch-ua' => ' Not;A Brand";v="99", "Yandex";v="91", "Chromium";v="91',
                'sec-ch-ua-mobile' => '?0',
                'Upgrade-Insecure-Requests' => "1",
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.135 YaBrowser/21.6.3.757 Yowser/2.5 Safari/537.36',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
                'Sec-Fetch-Site' => 'cross-site',
                'Sec-Fetch-Mode' => 'navigate',
                'Sec-Fetch-User' => '?1',
                'Sec-Fetch-Dest' => 'document',
                'Accept-Encoding' => 'gzip, deflate, br',
                'Accept-Language' => 'ru,en;q=0.9',
                'Cookie' => "timezoneOffset={$_SESSION['cookies']['timezoneOffset']}; _ga={$_SESSION['cookies']['_ga']}; _gid={$_SESSION['cookies']['_gid']}; sessionid={$_SESSION['cookies']['sessionid']}; browserid={$_SESSION['cookies']['browserid']}; Steam_Language={$_SESSION['cookies']['Steam_Language']}; steamCountry={$_SESSION['cookies']['steamCountry']}; steamLoginSecure={$_SESSION['cookies']['steamLoginSecure']}; steamMachineAuth{$_SESSION['steam_steamid']}={$_SESSION['cookies']['steamMachineAuth']}; steamRememberLogin={$_SESSION['cookies']['steamRememberLogin']};",
            ];
            $url = "https://steamcommunity.com/profiles/{$_SESSION['steam_steamid']}/ajaxgetachievementsforgame/753";
            $request = new Request('GET', $url, $headers);
            $response = $client->send($request);
            if ($response->getStatusCode() === 200) {
                $games_with_achievements = json_decode(file_get_contents($path_parse . '\games_with_achievements.json'), JSON_UNESCAPED_UNICODE);
                $number = 0;
                for ($number_game = 0; $number_game < count($games_with_achievements['games_with_achievements']); $number_game++) {
                    $name = $games_with_achievements['games_with_achievements'][$number_game]['name'];
                    $appid = $games_with_achievements['games_with_achievements'][$number_game]['appid'];
                    $url = "https://steamcommunity.com/profiles/{$_SESSION['steam_steamid']}/ajaxgetachievementsforgame/$appid";
                    $request = new Request('GET', $url, $headers);
                    $response = $client->send($request);
                    if ($response->getStatusCode() === 200) {
                        $response_body = $response->getBody();
                        $get = str_get_html($response_body);
                        if ($get->find('div.achievement_list_item') == true) {
                            foreach ($get->find('div.achievement_list_item') as $backup) {
                                $achievements['achievements'][$number]['app_name'] = $name;
                                $achievements['achievements'][$number]['appid'] = $appid;
                                $achievements['achievements'][$number]['title'] = $backup->getAttribute('data-statid') . "_" . $backup->getAttribute('data-bit');
                                $achievements['achievements'][$number]['name'] = $backup->childNodes(1)->childNodes(0)->innertext;
                                $number++;
                            }
                            echo "<div class='container'><span class='output-info-name'>Achievements: </span><span class='item-output' id='achievements'><a href=https://store.steampowered.com/app/{$appid} target='_blank'>{$name}</a> has been recorded.</span></div>" . PHP_EOL;
                        }
                    }
                    if ($response->getStatusCode() !== 200) {
                        echo "<div class='container'><span class='error'>Status code: " . $response->getStatusCode() . ", Something is wrong.</span></div>" . PHP_EOL;
                    }
                }
                if (isset($achievements['achievements'])) {
                    echo "<div class='container'><span class='output-info-name'>Achievements: </span><span class='item-output' id='achievements'>" . count($achievements['achievements']) . " found.</span></div>" . PHP_EOL;
                    file_put_contents($file_output, json_encode($achievements, JSON_UNESCAPED_UNICODE));
                } else {
                    echo "<div class='container'><span class='error'>No achievements were found.</span></div>" . PHP_EOL;
                    if (file_exists($file_output)) {
                        unlink($file_output);
                    }
                }
            }
            if ($response->getStatusCode() !== 200) {
                echo "<div class='container'><span class='error'>Status code: " . $response->getStatusCode() . ", Something is wrong.</span></div>" . PHP_EOL;
            }
        } else {
            echo "<div class='container'><span class='error'>There are no games with achievements on the account.</span></div>" . PHP_EOL;
            if (file_exists($file_output)) {
                unlink($file_output);
            }
        }
        echo "<span class='start-finish-section'>[ACHIEVEMENTS]</span></div></div>" . PHP_EOL;
    }

    function Artworks($path_parse, $client)
    {
        echo "<div class='container'><div class='output-items'><span class='start-finish-section'>[ARTWORKS]</span>" . PHP_EOL;
        $file_output = $path_parse . '\artworks.json';
        $headers = [
            'Host' => 'steamcommunity.com',
            'Connection' => 'keep-alive',
            'Cache-Control' => 'max-age=0',
            'sec-ch-ua' => ' Not;A Brand";v="99", "Yandex";v="91", "Chromium";v="91',
            'sec-ch-ua-mobile' => '?0',
            'Upgrade-Insecure-Requests' => "1",
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.135 YaBrowser/21.6.3.757 Yowser/2.5 Safari/537.36',
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
            'Sec-Fetch-Site' => 'cross-site',
            'Sec-Fetch-Mode' => 'navigate',
            'Sec-Fetch-User' => '?1',
            'Sec-Fetch-Dest' => 'document',
            'Accept-Encoding' => 'gzip, deflate, br',
            'Accept-Language' => 'ru,en;q=0.9',
            'Cookie' => "timezoneOffset={$_SESSION['cookies']['timezoneOffset']}; _ga={$_SESSION['cookies']['_ga']}; _gid={$_SESSION['cookies']['_gid']}; sessionid={$_SESSION['cookies']['sessionid']}; browserid={$_SESSION['cookies']['browserid']}; Steam_Language={$_SESSION['cookies']['Steam_Language']}; steamCountry={$_SESSION['cookies']['steamCountry']}; steamLoginSecure={$_SESSION['cookies']['steamLoginSecure']}; steamMachineAuth{$_SESSION['steam_steamid']}={$_SESSION['cookies']['steamMachineAuth']}; steamRememberLogin={$_SESSION['cookies']['steamRememberLogin']};",
        ];
        $url = "https://steamcommunity.com/id/shisenee/publishedfilebrowsepopup/myart/";
        $request = new Request('GET', $url, $headers);
        $response = $client->send($request);
        $response_body = $response->getBody();
        if ($response->getStatusCode() === 200) {
            $get = str_get_html($response_body);
            if ($verification = $get->find('a.pagelink', -1)) {
                $count_pages = $verification->innertext;
            } else {
                $count_pages = 1;
            }
            $number = 0;
            for ($page = 1; $page <= $count_pages; $page++) {
                $url = "https://steamcommunity.com/profiles/{$_SESSION['steam_steamid']}/publishedfilebrowsepopup/myart/?p=$page";
                $request = new Request('GET', $url, $headers);
                $response = $client->send($request);
                if ($response->getStatusCode() === 200) {
                    $response_body = $response->getBody();
                    $get = str_get_html($response_body);
                    if ($get->find('div.publishedfile_popup_items') == true) {
                        foreach ($get->find('div.publishedfile_popup_screenshot') as $backup) {
                            $backup = explode("'", $backup->getAttribute('onclick'));
                            $artworks['artworks'][$number]['publishedfileid'] = $backup[1];
                            echo "<div class='container'><span class='output-info-name'>Artwork: </span><span class='item-output' id='artworks'><a href=https://steamcommunity.com/sharedfiles/filedetails/?id={$artworks['artworks'][$number]['publishedfileid']} target='_blank'>{$artworks['artworks'][$number]['publishedfileid']}</a> has been recorded.</span></div>" . PHP_EOL;
                            $number++;
                        }
                    }
                }
                if ($response->getStatusCode() !== 200) {
                    echo "<div class='container'><span class='error'>Status code: " . $response->getStatusCode() . ", Something is wrong.</span></div>" . PHP_EOL;
                }
            }
            if (isset($artworks['artworks'])) {
                echo "<div class='container'><span class='output-info-name'>Artworks: </span><span class='item-output' id='artworks'>" . count($artworks['artworks']) . " found.</span></div>" . PHP_EOL;
                file_put_contents($file_output, json_encode($artworks, true));
            } else {
                echo "<div class='container'><span class='error'>No artworks were found.</span></div>" . PHP_EOL;
                if (file_exists($file_output)) {
                    unlink($file_output);
                }
            }
        }
        if ($response->getStatusCode() !== 200) {
            echo "<div class='container'><span class='error'>Status code: " . $response->getStatusCode() . ", Something is wrong.</span></div>" . PHP_EOL;
        }
        echo "<span class='start-finish-section'>[ARTWORKS]</span></div></div>" . PHP_EOL;
    }

    function Avatars($path_parse, $client)
    {
        echo "<div class='container'><div class='output-items'><span class='start-finish-section'>[AVATARS]</span>" . PHP_EOL;
        $file_output = $path_parse . '\avatars.json';
        $url = "https://api.steampowered.com/ICommunityService/GetAvatarHistory/v1/?access_token={$_SESSION['steam_accesstoken']}&steamid={$_SESSION['steam_steamid']}";
        $statusCode = CheckPost($client, $url);
        if (isset($statusCode) and $statusCode === 200) {
            $response = $client->request('POST', $url);
            $response_body = $response->getBody();
            $json_decode = json_decode($response_body, TRUE);
            if (array_key_exists('avatars', $json_decode['response'])) {
                $decode_main = $json_decode['response']['avatars'];
                for ($number = 0; $number < count($decode_main); $number++) {
                    $avatars['avatars'][$number] = $json_decode['response']['avatars'][$number]['avatar_sha1'];
                    echo "<div class='container'><span class='output-info-name'>Avatar: </span><span class='item-output' id='avatars'><a href=https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/" . substr($avatars['avatars'][$number], 0, 2) . "/{$avatars['avatars'][$number]}_full.jpg target='_blank'>{$avatars['avatars'][$number]}</a> has been recorded.</span></div>" . PHP_EOL;
                }
                echo "<div class='container'><span class='output-info-name'>Avatars: </span><span class='item-output' id='avatars'>" . count($avatars['avatars']) . " found.</span></div>" . PHP_EOL;
                file_put_contents($file_output, json_encode($avatars, true));
            } else {
                echo "<div class='container'><span class='error'>No avatars were found.</span></div>" . PHP_EOL;
                if (file_exists($file_output)) {
                    unlink($file_output);
                }
            }
        }
        if (isset($statusCode) and $statusCode !== 200) {
            echo "<div class='container'><span class='error'>Status code: " . $statusCode . ", Something is wrong.</span></div>" . PHP_EOL;
        }
        echo "<span class='start-finish-section'>[AVATARS]</span></div></div>" . PHP_EOL;
    }

    function Badges($path_parse, $client)
    {
        echo "<div class='container'><div class='output-items'><span class='start-finish-section'>[BADGES]</span>" . PHP_EOL;
        $file_output = $path_parse . '\badges.json';
        $url = "https://api.steampowered.com/IPlayerService/GetBadges/v1/?access_token={$_SESSION['steam_accesstoken']}&steamid={$_SESSION['steam_steamid']}";
        $statusCode = CheckGet($client, $url);
        if (isset($statusCode) and $statusCode === 200) {
            $response = $client->request('GET', $url);
            $response_body = $response->getBody();
            $json_decode = json_decode($response_body, TRUE);
            if (array_key_exists('badges', $json_decode['response'])) {
                $decode_main = $json_decode['response']['badges'];
                for ($number = 0; $number < count($decode_main); $number++) {
                    $badges['badges'][$number]['badgeid'] = $json_decode['response']['badges'][$number]['badgeid'];
                    echo "<div class='container'><span class='output-info-name'>Badge: </span><span class='item-output' id='badges'><a href='https://steamcommunity.com/profiles/{$_SESSION['steam_steamid']}/badges/{$badges['badges'][$number]['badgeid']}' target='_blank'>{$badges['badges'][$number]['badgeid']}</a> has been recorded.</span></div>" . PHP_EOL;
                }
                echo "<div class='container'><span class='output-info-name'>Badges: </span><span class='item-output' id='badges'>" . count($badges['badges']) . " found.</span></div>" . PHP_EOL;
                file_put_contents($file_output, json_encode($badges, true));
            } else {
                echo "<div class='container'><span class='error'>No badges were found.</span></div>" . PHP_EOL;
                if (file_exists($file_output)) {
                    unlink($file_output);
                }
            }
        }
        if (isset($statusCode) and $statusCode !== 200) {
            echo "<div class='container'><span class='error'>Status code: " . $statusCode . ", Something is wrong.</span></div>" . PHP_EOL;
        }
        echo "<span class='start-finish-section'>[BADGES]</span></div></div>" . PHP_EOL;
    }

    function Completionist($path_parse, $client)
    {
        echo "<div class='container'><div class='output-items'><span class='start-finish-section'>[COMPLETIONIST]</span>" . PHP_EOL;
        $file_output = $path_parse . '\completionist.json';
        $headers = [
            'Host' => 'steamcommunity.com',
            'Connection' => 'keep-alive',
            'Cache-Control' => 'max-age=0',
            'sec-ch-ua' => ' Not;A Brand";v="99", "Yandex";v="91", "Chromium";v="91',
            'sec-ch-ua-mobile' => '?0',
            'Upgrade-Insecure-Requests' => "1",
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.135 YaBrowser/21.6.3.757 Yowser/2.5 Safari/537.36',
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
            'Sec-Fetch-Site' => 'cross-site',
            'Sec-Fetch-Mode' => 'navigate',
            'Sec-Fetch-User' => '?1',
            'Sec-Fetch-Dest' => 'document',
            'Accept-Encoding' => 'gzip, deflate, br',
            'Accept-Language' => 'ru,en;q=0.9',
            'Cookie' => "timezoneOffset={$_SESSION['cookies']['timezoneOffset']}; _ga={$_SESSION['cookies']['_ga']}; _gid={$_SESSION['cookies']['_gid']}; sessionid={$_SESSION['cookies']['sessionid']}; browserid={$_SESSION['cookies']['browserid']}; Steam_Language={$_SESSION['cookies']['Steam_Language']}; steamCountry={$_SESSION['cookies']['steamCountry']}; steamLoginSecure={$_SESSION['cookies']['steamLoginSecure']}; steamMachineAuth{$_SESSION['steam_steamid']}={$_SESSION['cookies']['steamMachineAuth']}; steamRememberLogin={$_SESSION['cookies']['steamRememberLogin']};",
        ];
        $url = "https://steamcommunity.com/profiles/{$_SESSION['steam_steamid']}/edit/showcases";
        $request = new Request('GET', $url, $headers);
        $response = $client->send($request);
        if ($response->getStatusCode() === 200) {
            $response_body = $response->getBody();
            $get = str_get_html($response_body);
            foreach ($get->find('*[text/javascript]') as $backup) {
                if (strpos($backup->innertext, "g_rgAchievementsCompletionshipShowcasePerfectGames") !== false) {
                    $a = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $backup->innertext);
                    $processed = json_decode(trim(substr($a, 52), ";"), true);
                } else {
                    $processed = NULL;
                }
            }
            if ($processed !== NULL) {
                for ($number = 0; $number < count($processed); $number++) {
                    $completionist['completionist'][$number]['name'] = $processed[$number]['name'];
                    $completionist['completionist'][$number]['appid'] = $processed[$number]['appid'];
                    $completionist['completionist'][$number]['num_achievements'] = $processed[$number]['num_achievements'];
                    echo "<div class='container'><span class='output-info-name'>Perfect game: </span><span class='item-output' id='completionist'><a href=https://store.steampowered.com/app/{$completionist['completionist'][$number]['appid']} target='_blank'>{$completionist['completionist'][$number]['name']}</a> with <a href='https://steamcommunity.com/profiles/{$_SESSION['steam_steamid']}/stats/AppID/{$completionist['completionist'][$number]['appid']}/?tab=achievements' target='_blank'>{$completionist['completionist'][$number]['num_achievements']}</a> achievements has been recorded.</span></div>" . PHP_EOL;
                }
            }
            if (isset($completionist['completionist'])) {
                echo "<div class='container'><span class='output-info-name'>Perfect games: </span><span class='item-output' id='completionist'>" . count($completionist['completionist']) . " found.</span></div>" . PHP_EOL;
                file_put_contents($file_output, json_encode($completionist, JSON_UNESCAPED_UNICODE));
            } else {
                echo "<div class='container'><span class='error'>No perfect games were found.</span></div>" . PHP_EOL;
                if (file_exists($file_output)) {
                    unlink($file_output);
                }
            }
        }
        if ($response->getStatusCode() !== 200) {
            echo "<div class='container'><span class='error'>Status code: " . $response->getStatusCode() . ", Something is wrong.</span></div>" . PHP_EOL;
        }
        echo "<span class='start-finish-section'>[COMPLETIONIST]</span></div></div>" . PHP_EOL;
    }

    function Friends($path_parse, $client)
    {
        echo "<div class='container'><div class='output-items'><span class='start-finish-section'>[FRIENDS]</span>" . PHP_EOL;
        $file_output = $path_parse . "\/friends.json";
        $url = "https://api.steampowered.com/ISteamUserOAuth/GetFriendList/v1/?access_token={$_SESSION['steam_accesstoken']}";
        $statusCode = CheckGet($client, $url);
        if (isset($statusCode) and $statusCode === 200) {
            $response = $client->request('GET', $url);
            $response_body = $response->getBody();
            $friends = json_decode($response_body, TRUE);
            if (!empty($friends['friends'])) {
                foreach ($friends['friends'] as $friend){
                    echo "<div class='container'><span class='output-info-name'>Friend: </span><span class='item-output' id='friends'><a href=https://steamcommunity.com/profiles/{$friend['steamid']} target='_blank'>{$friend['steamid']}</a> has been recorded.</span></div>" . PHP_EOL;
                }
                echo "<div class='container'><span class='output-info-name'>Friends: </span><span class='item-output' id='friends'>" . count($friends['friends']) . " found.</span></div>" . PHP_EOL;
                file_put_contents($file_output, json_encode($friends, true));
            } else {
                echo "<div class='container'><span class='error'>No friends were found. ;(</span></div>" . PHP_EOL;
                if (file_exists($file_output)) {
                    unlink($file_output);
                }
            }
        }
        if (isset($statusCode) and $statusCode !== 200) {
            echo "<div class='container'><span class='error'>Status code: " . $response->getStatusCode() . ", Something is wrong.</span></div>" . PHP_EOL;
        }
        echo "<span class='start-finish-section'>[FRIENDS]</span></div></div>" . PHP_EOL;
    }

    function Games($path_parse, $client)
    {
        echo "<div class='container'><div class='output-items'><span class='start-finish-section'>[GAMES]</span>" . PHP_EOL;
        $file_output = $path_parse . '\games.json';
        $url = "https://api.steampowered.com/IPlayerService/GetOwnedGames/v1/?access_token={$_SESSION['steam_accesstoken']}&steamid={$_SESSION['steam_steamid']}&include_appinfo=1&include_free_sub=1";
        $statusCode = CheckGet($client, $url);
        if (isset($statusCode) and $statusCode === 200) {
            $response = $client->request('GET', $url);
            $response_body = $response->getBody();
            $decodejson = json_decode($response_body, TRUE);
            if (array_key_exists('games', $decodejson['response'])) {
                $decode_main = $decodejson['response']['games'];
                $titles = array('appid', 'name', 'has_community_visible_stats');
                for ($i = 0; $i < $decodejson['response']['game_count']; $i++) {
                    foreach ($titles as $title) {
                        if (isset($decodejson['response']['games'][$i][$title])) {
                            $games['games'][$i][$title] = $decode_main[$i][$title];
                        } else {
                            $games['games'][$i][$title] = $decode_main[$i][$title] = false;
                        }
                    }
                    echo "<div class='container'><span class='output-info-name'>Game: </span><span class='item-output' id='games'><a href=https://store.steampowered.com/app/{$games['games'][$i]['appid']} target='_blank'>{$games['games'][$i]['name']}</a> has been recorded.</span></div>" . PHP_EOL;
                }
                echo "<div class='container'><span class='output-info-name'>Games: </span><span class='item-output' id='games'>{$decodejson['response']['game_count']} found.</span></div>" . PHP_EOL;
                file_put_contents($file_output, json_encode($games, true));
            } else {
                echo "<div class='container'><span class='error'>No games were found.</span></div>" . PHP_EOL;
                if (file_exists($file_output)) {
                    unlink($file_output);
                }
            }
        }
        if (isset($statusCode) and $statusCode !== 200) {
            echo "<div class='container'><span class='error'>Status code: " . $statusCode . ", Something is wrong.</span></div>" . PHP_EOL;
        }
        echo "<span class='start-finish-section'>[GAMES]</span></div></div>" . PHP_EOL;
    }

    function Groups($path_parse, $client)
    {
        echo "<div class='container'><div class='output-items'><span class='start-finish-section'>[GROUPS]</span>" . PHP_EOL;
        $file_output = $path_parse . '\groups.json';
        $url = "https://api.steampowered.com/ISteamUser/GetUserGroupList/v1/?key={$_SESSION['steam_apikey']}&steamid={$_SESSION['steam_steamid']}";
        $statusCode = CheckGet($client, $url);
        if (isset($statusCode) and $statusCode === 200) {
            $response = $client->request('GET', $url);
            $response_body = $response->getBody();
            $decodejson = json_decode($response_body, TRUE);
            if (!empty($decodejson['response']['groups'])) {
                $decode_main = $decodejson['response']['groups'];
                for ($i = 0; $i < count($decode_main); $i++) {
                    $groups['groups'][$i]['accountid'] = $decodejson['response']['groups'][$i]['gid'];
                }
                echo "<div class='container'><span class='output-info-name'>Groups: </span><span class='item-output' id='groups'>" . count($groups['groups']) . " found.</span></div>" . PHP_EOL;
                file_put_contents($file_output, json_encode($groups, true));
            } else {
                echo "<div class='container'><span class='error'>No groups were found.</span></div>" . PHP_EOL;
                if (file_exists($file_output)) {
                    unlink($file_output);
                }
            }
        }
        if (isset($statusCode) and $statusCode !== 200) {
            echo "<div class='container'><span class='error'>Status code: " . $statusCode . ", Something is wrong.</span></div>" . PHP_EOL;
        }
        echo "<span class='start-finish-section'>[GROUPS]</span></div></div>" . PHP_EOL;
    }

    function Guides($path_parse, $client)
    {
        echo "<div class='container'><div class='output-items'><span class='start-finish-section'>[GUIDES]</span>" . PHP_EOL;
        $file_output = $path_parse . "\guides.json";
        $headers = [
            'Host' => 'steamcommunity.com',
            'Connection' => 'keep-alive',
            'Cache-Control' => 'max-age=0',
            'sec-ch-ua' => ' Not;A Brand";v="99", "Yandex";v="91", "Chromium";v="91',
            'sec-ch-ua-mobile' => '?0',
            'Upgrade-Insecure-Requests' => "1",
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.135 YaBrowser/21.6.3.757 Yowser/2.5 Safari/537.36',
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
            'Sec-Fetch-Site' => 'cross-site',
            'Sec-Fetch-Mode' => 'navigate',
            'Sec-Fetch-User' => '?1',
            'Sec-Fetch-Dest' => 'document',
            'Accept-Encoding' => 'gzip, deflate, br',
            'Accept-Language' => 'ru,en;q=0.9',
            'Cookie' => "timezoneOffset={$_SESSION['cookies']['timezoneOffset']}; _ga={$_SESSION['cookies']['_ga']}; _gid={$_SESSION['cookies']['_gid']}; sessionid={$_SESSION['cookies']['sessionid']}; browserid={$_SESSION['cookies']['browserid']}; Steam_Language={$_SESSION['cookies']['Steam_Language']}; steamCountry={$_SESSION['cookies']['steamCountry']}; steamLoginSecure={$_SESSION['cookies']['steamLoginSecure']}; steamMachineAuth{$_SESSION['steam_steamid']}={$_SESSION['cookies']['steamMachineAuth']}; steamRememberLogin={$_SESSION['cookies']['steamRememberLogin']};",
        ];
        $urls = array("https://steamcommunity.com/profiles/{$_SESSION['steam_steamid']}/publishedfilebrowsepopup/guides/?tab=myfiles&p=1", "https://steamcommunity.com/profiles/{$_SESSION['steam_steamid']}/publishedfilebrowsepopup/guides/?tab=myfavorites&p=1");
        for ($url_number = 0; $url_number < 2; $url_number++) {
            $request = new Request('GET', $urls[$url_number], $headers);
            $response = $client->send($request);
            if ($response->getStatusCode() === 200) {
                $response_body = $response->getBody();
                if ($verification = str_get_html($response_body)->find('a.pagelink', -1)) {
                    $count_pages[$url_number] = $verification->innertext;
                } else {
                    $count_pages[$url_number] = 1;
                }
                $number = 0;
                for ($page = 1; $page <= $count_pages[$url_number]; $page++) {
                    $urls = array("https://steamcommunity.com/profiles/{$_SESSION['steam_steamid']}/publishedfilebrowsepopup/guides/?tab=myfiles&p=$page", "https://steamcommunity.com/profiles/{$_SESSION['steam_steamid']}/publishedfilebrowsepopup/guides/?tab=myfavorites&p=$page");
                    $request = new Request('GET', $urls[$url_number], $headers);
                    $response = $client->send($request);
                    if ($response->getStatusCode() === 200) {
                        $response_body = $response->getBody();
                        $get = str_get_html($response_body);
                        if ($get->find('div.workshopBrowseItems') == true) {
                            foreach ($get->find('div.workshopItem') as $backup) {
                                if ($urls[$url_number] == $urls[0]) {
                                    $guides['guides']['created'][$number]['publishedfileid'] = $backup->childNodes(0)->getAttribute("data-publishedfileid");
                                    $guides['guides']['created'][$number]['appid'] = $backup->childNodes(0)->getAttribute("data-appid");
                                    $guides['guides']['created'][$number]['title'] = $backup->childNodes(4)->childNodes(0)->innertext;
                                    echo "<div class='container'><span class='output-info-name'>Created guide: </span><span class='item-output' id='guides'><a href=https://steamcommunity.com/sharedfiles/filedetails/?id={$guides['guides']['created'][$number]['publishedfileid']} target='_blank'>{$guides['guides']['created'][$number]['title']}</a> from <a href='https://store.steampowered.com/app/{$guides['guides']['created'][$number]['appid']}/' target='_blank'>{$guides['guides']['created'][$number]['appid']}</a> has been recorded.</span></div>" . PHP_EOL;
                                    $number++;
                                }
                                if ($urls[$url_number] == $urls[1]) {
                                    $guides['guides']['favorite'][$number]['publishedfileid'] = $backup->childNodes(0)->getAttribute("data-publishedfileid");
                                    $guides['guides']['favorite'][$number]['appid'] = $backup->childNodes(0)->getAttribute("data-appid");
                                    $guides['guides']['favorite'][$number]['title'] = $backup->childNodes(5)->childNodes(0)->innertext;
                                    echo "<div class='container'><span class='output-info-name'>Favorite guide: </span><span class='item-output' id='guides'><a href=https://steamcommunity.com/sharedfiles/filedetails/?id={$guides['guides']['favorite'][$number]['publishedfileid']} target='_blank'>{$guides['guides']['favorite'][$number]['title']}</a> from <a href='https://store.steampowered.com/app/{$guides['guides']['favorite'][$number]['appid']}/' target='_blank'>{$guides['guides']['favorite'][$number]['appid']}</a> has been recorded.</span></div>" . PHP_EOL;
                                    $number++;
                                }
                            }
                        }
                    }
                    if ($response->getStatusCode() !== 200) {
                        echo "<div class='container'><span class='error'>Status code: " . $response->getStatusCode() . ", Something is wrong.</span></div>" . PHP_EOL;
                    }
                }
            }
            if ($response->getStatusCode() !== 200) {
                echo "<div class='container'><span class='error'>Status code: " . $response->getStatusCode() . ", Something is wrong.</span></div>" . PHP_EOL;
            }
        }
        $categories = array('created', 'favorite');
        if (isset($guides['guides'])) {
            foreach ($categories as $category) {
                if (isset($guides['guides'][$category])) {
                    echo "<div class='container'><span class='output-info-name'>" . ucfirst($category) . " guides: </span><span class='item-output' id='guides'>" . count($guides['guides'][$category]) . " found.</span></div>" . PHP_EOL;
                }
                if (!isset($guides['guides'][$category])) {
                    echo "<div class='container'><span class='error'>There are no {$category} guides.</span></div>" . PHP_EOL;
                }
                file_put_contents($file_output, json_encode($guides, JSON_UNESCAPED_UNICODE));
            }
        }
        if (!isset($guides['guides'])) {
            echo "<div class='container'><span class='error'>No guides were found.</span></div>" . PHP_EOL;
            if (file_exists($file_output)) {
                unlink($file_output);
            }
        }
        echo "<span class='start-finish-section'>[GUIDES]</span></div></div>" . PHP_EOL;
    }

    function Inventory($path_parse, $client)
    {
        echo "<div class='container'><div class='output-items'><span class='start-finish-section'>[INVENTORY]</span>" . PHP_EOL;
        $file_output = $path_parse . '\inventory.json';
        if (file_exists($path_parse . '\games_with_inventory.json')) {
            $headers = [
                'Host' => 'steamcommunity.com',
                'Connection' => 'keep-alive',
                'Cache-Control' => 'max-age=0',
                'sec-ch-ua' => ' Not;A Brand";v="99", "Yandex";v="91", "Chromium";v="91',
                'sec-ch-ua-mobile' => '?0',
                'Upgrade-Insecure-Requests' => "1",
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.135 YaBrowser/21.6.3.757 Yowser/2.5 Safari/537.36',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
                'Sec-Fetch-Site' => 'cross-site',
                'Sec-Fetch-Mode' => 'navigate',
                'Sec-Fetch-User' => '?1',
                'Sec-Fetch-Dest' => 'document',
                'Accept-Encoding' => 'gzip, deflate, br',
                'Accept-Language' => 'ru,en;q=0.9',
                'Cookie' => "timezoneOffset={$_SESSION['cookies']['timezoneOffset']}; _ga={$_SESSION['cookies']['_ga']}; _gid={$_SESSION['cookies']['_gid']}; sessionid={$_SESSION['cookies']['sessionid']}; browserid={$_SESSION['cookies']['browserid']}; Steam_Language={$_SESSION['cookies']['Steam_Language']}; steamCountry={$_SESSION['cookies']['steamCountry']}; steamLoginSecure={$_SESSION['cookies']['steamLoginSecure']}; steamMachineAuth{$_SESSION['steam_steamid']}={$_SESSION['cookies']['steamMachineAuth']}; steamRememberLogin={$_SESSION['cookies']['steamRememberLogin']};",
            ];
            $games_with_inventory = $path_parse . '\games_with_inventory.json';
            $decode_games = json_decode(file_get_contents($games_with_inventory), TRUE);
            $number = 0;
            for ($inventory_number = 0; $inventory_number < count($decode_games['games_with_inventory']); $inventory_number++) {
                $appid = $decode_games['games_with_inventory'][$inventory_number]['appid'];
                $game_name = $decode_games['games_with_inventory'][$inventory_number]['name'];
                $contextid = $decode_games['games_with_inventory'][$inventory_number]['contextid'];
                $count_items = $decode_games['games_with_inventory'][$inventory_number]['asset_count'];
                $url = "https://steamcommunity.com/inventory/{$_SESSION['steam_steamid']}/$appid/$contextid?l={$_SESSION['cookies']['Steam_Language']}&count=$count_items";
                $request = new Request('GET', $url, $headers);
                $response = $client->send($request);
                if ($response->getStatusCode() === 200) {
                    $response_body = $response->getBody();
                    $decodejson = json_decode($response_body, TRUE);
                    foreach ($decodejson['assets'] as $value) {
                        foreach ($decodejson['descriptions'] as $value2) {
                            if ($value['classid'] === $value2['classid']) {
                                $items['inventory'][$number]['name'] = $value2['name'];
                                $items['inventory'][$number]['appid'] = $value['appid'];
                                $items['inventory'][$number]['game_name'] = $game_name;
                                $items['inventory'][$number]['item_contextid'] = $value['contextid'];
                                $items['inventory'][$number]['item_classid'] = $value['classid'];
                                $items['inventory'][$number]['item_assetid'] = $value['assetid'];
                                $items['inventory'][$number]['tradable'] = $value2['tradable'];
                                $items['inventory'][$number]['marketable'] = $value2['marketable'];
                                $number++;
                            }
                        }
                    }
                }
                if ($response->getStatusCode() !== 200) {
                    echo "<div class='container'><span class='error'>Status code: " . $response->getStatusCode() . ", Something is wrong.</span></div>" . PHP_EOL;
                }
            }
            if (isset($items['inventory'])) {
                echo "<div class='container'><span class='output-info-name'>Items: </span><span class='item-output' id='inventory'>" . count($items['inventory']) . " found.</span></div>" . PHP_EOL;
                file_put_contents($file_output, json_encode($items, true));
            } else {
                echo "<div class='container'><span class='error'>No items in inventory were found.</span></div>" . PHP_EOL;
                if (file_exists($file_output)) {
                    unlink($file_output);
                }
            }
        }
        if (!file_exists($path_parse . '\games_with_inventory.json')) {
            echo "<div class='container'><span class='error'>There are no games with inventory on the account.</span></div>" . PHP_EOL;
            if (file_exists($file_output)) {
                unlink($file_output);
            }
        }
        echo "<span class='start-finish-section'>[INVENTORY]</span></div></div>" . PHP_EOL;
    }

    function ProfileItems($path_parse, $client)
    {
        echo "<div class='container'><div class='output-items'><span class='start-finish-section'>[PROFILE ITEMS]</span>" . PHP_EOL;
        $file_output = $path_parse . '\profile_items.json';
        $url = "https://api.steampowered.com/IPlayerService/GetProfileItemsOwned/v1/?access_token={$_SESSION['steam_accesstoken']}";
        $statusCode = CheckGet($client, $url);
        if (isset($statusCode) and $statusCode === 200) {
            $response = $client->request('GET', $url);
            $response_body = $response->getBody();
            $categories = array('profile_backgrounds', 'mini_profile_backgrounds', 'avatar_frames', 'animated_avatars');
            $titles = array('appid', 'name', 'communityitemid');
            $decodejson = json_decode($response_body, TRUE);
            foreach ($categories as $category) {
                if (array_key_exists($category, $decodejson['response'])) {
                    for ($i = 0; $i < count($decodejson['response'][$category]); $i++) {
                        foreach ($titles as $title) {
                            $profile_items['profile_items'][$category][$i][$title] = $decodejson['response'][$category][$i][$title];
                        }
                        echo "<div class='container'><span class='output-info-name'>" . ucfirst(substr(str_replace("_", " ", $category), 0, -1)) . ": </span><span class='item-output' id='profile-items'>{$profile_items['profile_items'][$category][$i]['name']} has been recorded.</span></div>" . PHP_EOL;
                    }
                    if (isset($profile_items['profile_items'][$category])) {
                        echo "<div class='container'><span class='output-info-name'>" . ucfirst(str_replace("_", " ", $category)) . ": </span><span class='item-output' id='profile-items'>" . count($profile_items['profile_items'][$category]) . " found.</span></div>" . PHP_EOL;
                    }
                } else {
                    echo "<div class='container'><span class='error'>No " . str_replace("_", " ", $category) . " were found.</span></div>" . PHP_EOL;
                }
            }
            if (isset($profile_items['profile_items'])) {
                file_put_contents($file_output, json_encode($profile_items, true));
            } else {
                echo "<div class='container'><span class='error'>No profile items were found.</span></div>" . PHP_EOL;
                if (file_exists($file_output)) {
                    unlink($file_output);
                }
            }
        }
        if (isset($statusCode) and $statusCode !== 200) {
            echo "<div class='container'><span class='error'>Status code: " . $response->getStatusCode() . ", Something is wrong.</span></div>" . PHP_EOL;
        }
        echo "<span class='start-finish-section'>[PROFILE ITEMS]</span></div></div>" . PHP_EOL;
    }

    function Reviews($path_parse, $client)
    {
        echo "<div class='container'><div class='output-items'><span class='start-finish-section'>[REVIEWS]</span>" . PHP_EOL;
        $file_output = $path_parse . '\reviews.json';
        $headers = [
            'Host' => 'steamcommunity.com',
            'Connection' => 'keep-alive',
            'Cache-Control' => 'max-age=0',
            'sec-ch-ua' => ' Not;A Brand";v="99", "Yandex";v="91", "Chromium";v="91',
            'sec-ch-ua-mobile' => '?0',
            'Upgrade-Insecure-Requests' => "1",
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.135 YaBrowser/21.6.3.757 Yowser/2.5 Safari/537.36',
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
            'Sec-Fetch-Site' => 'cross-site',
            'Sec-Fetch-Mode' => 'navigate',
            'Sec-Fetch-User' => '?1',
            'Sec-Fetch-Dest' => 'document',
            'Accept-Encoding' => 'gzip, deflate, br',
            'Accept-Language' => 'ru,en;q=0.9',
            'Cookie' => "timezoneOffset={$_SESSION['cookies']['timezoneOffset']}; _ga={$_SESSION['cookies']['_ga']}; _gid={$_SESSION['cookies']['_gid']}; sessionid={$_SESSION['cookies']['sessionid']}; browserid={$_SESSION['cookies']['browserid']}; Steam_Language={$_SESSION['cookies']['Steam_Language']}; steamCountry={$_SESSION['cookies']['steamCountry']}; steamLoginSecure={$_SESSION['cookies']['steamLoginSecure']}; steamMachineAuth{$_SESSION['steam_steamid']}={$_SESSION['cookies']['steamMachineAuth']}; steamRememberLogin={$_SESSION['cookies']['steamRememberLogin']};",
        ];
        $number = 0;
        $url = "https://steamcommunity.com/profiles/{$_SESSION['steam_steamid']}/ajaxgetrecommendedgames?public_only=1";
        $request = new Request('GET', $url, $headers);
        $response = $client->send($request);
        if ($response->getStatusCode() === 200) {
            $response_body = $response->getBody();
            $get = str_get_html($response_body);
            if ($get->find('div.game_list_results') == true) {
                foreach ($get->find('div.group_list_option') as $backup) {
                    $reviews['reviews'][$number]['appid'] = $backup->getAttribute('data-appid');
                    $reviews['reviews'][$number]['app_title'] = $backup->childNodes(1)->innertext;
                    echo "<div class='container'><span class='output-info-name'>Review: </span><span class='item-output' id='reviews'><a href=https://steamcommunity.com/profiles/{$_SESSION['steam_steamid']}/recommended/{$reviews['reviews'][$number]['appid']}/ target='_blank'>{$reviews['reviews'][$number]['app_title']}</a> has been recorded.</span></div>" . PHP_EOL;
                    $number++;
                }
            }
            if (isset($reviews['reviews'])) {
                echo "<div class='container'><span class='output-info-name'>Reviews: </span><span class='item-output' id='reviews'>" . count($reviews['reviews']) . " found.</span></div>" . PHP_EOL;
                file_put_contents($file_output, json_encode($reviews, JSON_UNESCAPED_UNICODE));
            } else {
                echo "<div class='container'><span class='error'>No reviews were found.</span></div>" . PHP_EOL;
                if (file_exists($file_output)) {
                    unlink($file_output);
                }
            }
        }
        if ($response->getStatusCode() !== 200) {
            echo "<div class='container'><span class='error'>Status code: " . $response->getStatusCode() . ", Something is wrong.</span></div>" . PHP_EOL;
        }
        echo "<span class='start-finish-section'>[REVIEWS]</span></div></div>" . PHP_EOL;
    }

    function Screenshots($path_parse, $client)
    {
        echo "<div class='container'><div class='output-items'><span class='start-finish-section'>[SCREENSHOTS]</span>" . PHP_EOL;
        $file_output = $path_parse . "\screenshots.json";
        $headers = [
            'Host' => 'steamcommunity.com',
            'Connection' => 'keep-alive',
            'Cache-Control' => 'max-age=0',
            'sec-ch-ua' => ' Not;A Brand";v="99", "Yandex";v="91", "Chromium";v="91',
            'sec-ch-ua-mobile' => '?0',
            'Upgrade-Insecure-Requests' => "1",
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.135 YaBrowser/21.6.3.757 Yowser/2.5 Safari/537.36',
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
            'Sec-Fetch-Site' => 'cross-site',
            'Sec-Fetch-Mode' => 'navigate',
            'Sec-Fetch-User' => '?1',
            'Sec-Fetch-Dest' => 'document',
            'Accept-Encoding' => 'gzip, deflate, br',
            'Accept-Language' => 'ru,en;q=0.9',
            'Cookie' => "timezoneOffset={$_SESSION['cookies']['timezoneOffset']}; _ga={$_SESSION['cookies']['_ga']}; _gid={$_SESSION['cookies']['_gid']}; sessionid={$_SESSION['cookies']['sessionid']}; browserid={$_SESSION['cookies']['browserid']}; Steam_Language={$_SESSION['cookies']['Steam_Language']}; steamCountry={$_SESSION['cookies']['steamCountry']}; steamLoginSecure={$_SESSION['cookies']['steamLoginSecure']}; steamMachineAuth{$_SESSION['steam_steamid']}={$_SESSION['cookies']['steamMachineAuth']}; steamRememberLogin={$_SESSION['cookies']['steamRememberLogin']};",
        ];
        $url = "https://steamcommunity.com/profiles/{$_SESSION['steam_steamid']}/publishedfilebrowsepopup/myscreenshots/";
        $request = new Request('GET', $url, $headers);
        $response = $client->send($request);
        if ($response->getStatusCode() === 200) {
            $response_body = $response->getBody();
            $get = str_get_html($response_body);
            if ($verification = $get->find('a.pagelink', -1)) {
                $count_pages = $verification->innertext;
            } else {
                $count_pages = 1;
            }
            $number = 0;
            for ($page = 1; $page <= $count_pages; $page++) {
                $url = "https://steamcommunity.com/profiles/{$_SESSION['steam_steamid']}/publishedfilebrowsepopup/myscreenshots/?p=$page";
                $request = new Request('GET', $url, $headers);
                $response = $client->send($request);
                if ($response->getStatusCode() === 200) {
                    $response_body = $response->getBody();
                    $get = str_get_html($response_body);
                    if ($get->find('div.publishedfile_popup_items') == true) {
                        foreach ($get->find('div.publishedfile_popup_screenshot') as $backup) {
                            $backup = explode("'", $backup->getAttribute('onclick'));
                            $screenshots['screenshots'][$number]['publishedfileid'] = $backup[1];
                            echo "<div class='container'><span class='output-info-name'>Screenshot: </span><span class='item-output' id='screenshots'><a href=https://steamcommunity.com/sharedfiles/filedetails/?id={$screenshots['screenshots'][$number]['publishedfileid']} target='_blank'>{$screenshots['screenshots'][$number]['publishedfileid']}</a> has been recorded.</span></div>" . PHP_EOL;
                            $number++;
                        }
                    }
                }
                if ($response->getStatusCode() !== 200) {
                    echo "<div class='container'><span class='error'>Status code: " . $response->getStatusCode() . ", Something is wrong.</span></div>" . PHP_EOL;
                }
            }
            if (isset($screenshots['screenshots'])) {
                echo "<div class='container'><span class='output-info-name'>Screenshots: </span><span class='item-output' id='screenshots'>" . count($screenshots['screenshots']) . " found.</span></div>" . PHP_EOL;
                file_put_contents($file_output, json_encode($screenshots, true));
            } else {
                echo "<div class='container'><span class='error'>No screenshots were found.</span></div>" . PHP_EOL;
                if (file_exists($file_output)) {
                    unlink($file_output);
                }
            }
        }
        if ($response->getStatusCode() !== 200) {
            echo "<div class='container'><span class='error'>Status code: " . $response->getStatusCode() . ", Something is wrong.</span></div>" . PHP_EOL;
        }
        echo "<span class='start-finish-section'>[SCREENSHOTS]</span></div></div>" . PHP_EOL;
    }

    function Themes($path_parse, $client)
    {
        echo "<div class='container'><div class='output-items'><span class='start-finish-section'>[THEMES]</span>" . PHP_EOL;
        $file_output = $path_parse . '\themes.json';
        $url = "https://api.steampowered.com/IPlayerService/GetProfileThemesAvailable/v1/?access_token={$_SESSION['steam_accesstoken']}";
        $statusCode = CheckGet($client, $url);
        if (isset($statusCode) and $statusCode === 200) {
            $response = $client->request('GET', $url);
            $response_body = $response->getBody();
            $json_decode = json_decode($response_body, TRUE);
            if (array_key_exists('profile_themes', $json_decode['response'])) {
                for ($number = 0; $number < count($json_decode['response']['profile_themes']); $number++) {
                    $themes['themes'][$number]['theme_id'] = $json_decode['response']['profile_themes'][$number]['theme_id'];
                    $themes['themes'][$number]['title'] = $json_decode['response']['profile_themes'][$number]['title'];
                    echo "<div class='container'><span class='output-info-name'>Theme: </span><span class='item-output' id='themes'>{$themes['themes'][$number]['title']} has been recorded.</span></div>" . PHP_EOL;
                }
                echo "<div class='container'><span class='output-info-name'>Themes: </span><span class='item-output' id='themes'>" . count($themes['themes']) . " found.</span></div>" . PHP_EOL;
                file_put_contents($file_output, json_encode($themes, true));
            } else {
                echo "<div class='container'><span class='error'>No themes were found.</span></div>" . PHP_EOL;
                if (file_exists($file_output)) {
                    unlink($file_output);
                }
            }
        }
        if (isset($statusCode) and $statusCode !== 200) {
            echo "<div class='container'><span class='error'>Status code: " . $statusCode . ", Something is wrong.</span></div>" . PHP_EOL;
        }
        echo "<span class='start-finish-section'>[THEMES]</span></div></div>" . PHP_EOL;
    }

    function Videos($path_parse, $client)
    {
        echo "<div class='container'><div class='output-items'><span class='start-finish-section'>[VIDEOS]</span>" . PHP_EOL;
        $file_output = $path_parse . '\videos.json';
        $headers = [
            'Host' => 'steamcommunity.com',
            'Connection' => 'keep-alive',
            'Cache-Control' => 'max-age=0',
            'sec-ch-ua' => ' Not;A Brand";v="99", "Yandex";v="91", "Chromium";v="91',
            'sec-ch-ua-mobile' => '?0',
            'Upgrade-Insecure-Requests' => "1",
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.135 YaBrowser/21.6.3.757 Yowser/2.5 Safari/537.36',
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
            'Sec-Fetch-Site' => 'cross-site',
            'Sec-Fetch-Mode' => 'navigate',
            'Sec-Fetch-User' => '?1',
            'Sec-Fetch-Dest' => 'document',
            'Accept-Encoding' => 'gzip, deflate, br',
            'Accept-Language' => 'ru,en;q=0.9',
            'Cookie' => "timezoneOffset={$_SESSION['cookies']['timezoneOffset']}; _ga={$_SESSION['cookies']['_ga']}; _gid={$_SESSION['cookies']['_gid']}; sessionid={$_SESSION['cookies']['sessionid']}; browserid={$_SESSION['cookies']['browserid']}; Steam_Language={$_SESSION['cookies']['Steam_Language']}; steamCountry={$_SESSION['cookies']['steamCountry']}; steamLoginSecure={$_SESSION['cookies']['steamLoginSecure']}; steamMachineAuth{$_SESSION['steam_steamid']}={$_SESSION['cookies']['steamMachineAuth']}; steamRememberLogin={$_SESSION['cookies']['steamRememberLogin']};",
        ];
        $number = 0;
        $url = "https://steamcommunity.com/profiles/{$_SESSION['steam_steamid']}/publishedfilebrowsepopup/myvideos/";
        $request = new Request('GET', $url, $headers);
        $response = $client->send($request);
        if ($response->getStatusCode() === 200) {
            $response_body = $response->getBody();
            $get = str_get_html($response_body);
            if ($get->find('div.workshopBrowseItems') == true) {
                foreach ($get->find('div.workshopItem') as $backup) {
                    $videos['videos'][$number]['publishedfileid'] = $backup->childNodes(0)->getAttribute("data-publishedfileid");
                    $videos['videos'][$number]['title'] = $backup->childNodes(4)->childNodes(0)->innertext;
                    echo "<div class='container'><span class='output-info-name'>Video: </span><span class='item-output' id='videos'><a href=https://steamcommunity.com/sharedfiles/filedetails/?id={$videos['videos'][$number]['publishedfileid']} target='_blank'>{$videos['videos'][$number]['title']}</a> has been recorded.</span></div>" . PHP_EOL;
                    $number++;
                }
            }
            if (isset($videos['videos'])) {
                echo "<div class='container'><span class='output-info-name'>Videos: </span><span class='item-output' id='videos'>" . count($videos['videos']) . " found.</span></div>" . PHP_EOL;
                file_put_contents($file_output, json_encode($videos, JSON_UNESCAPED_UNICODE));
            } else {
                echo "<div class='container'><span class='error'>No videos were found.</span></div>" . PHP_EOL;
                if (file_exists($file_output)) {
                    unlink($file_output);
                }
            }
        }
        if ($response->getStatusCode() !== 200) {
            echo "<div class='container'><span class='error'>Status code: " . $response->getStatusCode() . ", Something is wrong.</span></div>" . PHP_EOL;
        }
        echo "<span class='start-finish-section'>[VIDEOS]</span></div></div>" . PHP_EOL;
    }

    function Workshop($path_parse, $client)
    {
        echo "<div class='container'><div class='output-items'><span class='start-finish-section'>[WORKSHOP]</span>" . PHP_EOL;
        $file_output = $path_parse . "\workshop.json";
        $headers = [
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
            'Accept-Encoding' => 'gzip, deflate, br',
            'Accept-Language' => 'ru,en;q=0.9',
            'Connection' => 'keep-alive',
            'Host' => 'steamcommunity.com',
            'sec-ch-ua' => ' Not;A Brand";v="99", "Yandex";v="91", "Chromium";v="91',
            'sec-ch-ua-mobile' => '?0',
            'sec-ch-ua-platform' => 'Windows',
            'Sec-Fetch-Dest' => 'document',
            'Sec-Fetch-Mode' => 'navigate',
            'Sec-Fetch-Site' => 'same-origin',
            'Sec-Fetch-User' => '?1',
            'Upgrade-Insecure-Requests' => "1",
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.135 YaBrowser/21.6.3.757 Yowser/2.5 Safari/537.36',
            'Cookie' => "timezoneOffset={$_SESSION['cookies']['timezoneOffset']}; _ga={$_SESSION['cookies']['_ga']}; _gid={$_SESSION['cookies']['_gid']}; sessionid={$_SESSION['cookies']['sessionid']}; browserid={$_SESSION['cookies']['browserid']}; Steam_Language={$_SESSION['cookies']['Steam_Language']}; steamCountry={$_SESSION['cookies']['steamCountry']}; steamLoginSecure={$_SESSION['cookies']['steamLoginSecure']}; steamMachineAuth{$_SESSION['steam_steamid']}={$_SESSION['cookies']['steamMachineAuth']}; steamRememberLogin={$_SESSION['cookies']['steamRememberLogin']};",
        ];
        $urls = array("https://steamcommunity.com/profiles/{$_SESSION['steam_steamid']}/publishedfilebrowsepopup/workshopitemsforshowcase/?tab=myfiles&p=1", "https://steamcommunity.com/profiles/{$_SESSION['steam_steamid']}/publishedfilebrowsepopup/workshopitemsforshowcase/?tab=myfavorites&p=1");
        for ($url_number = 0; $url_number < 2; $url_number++) {
            $request = new Request('GET', $urls[$url_number], $headers);
            $response = $client->send($request);
            if ($response->getStatusCode() === 200) {
                $response_body = $response->getBody();
                if ($verification = str_get_html($response_body)->find('a.pagelink', -1)) {
                    $count_pages[$url_number] = $verification->innertext;
                } else {
                    $count_pages[$url_number] = 1;
                }
                $number = 0;
                for ($page = 1; $page <= $count_pages[$url_number]; $page++) {
                    $urls = array("https://steamcommunity.com/profiles/{$_SESSION['steam_steamid']}/publishedfilebrowsepopup/workshopitemsforshowcase/?tab=myfiles&p=$page", "https://steamcommunity.com/profiles/{$_SESSION['steam_steamid']}/publishedfilebrowsepopup/workshopitemsforshowcase/?tab=myfavorites&p=$page");
                    $request = new Request('GET', $urls[$url_number], $headers);
                    $response = $client->send($request);
                    if ($response->getStatusCode() === 200) {
                        $response_body = $response->getBody();
                        $get = str_get_html($response_body);
                        if ($get->find('div.workshopItemApp') == true) {
                            foreach ($get->find('div.workshopItem') as $backup) {
                                if ($urls[$url_number] == $urls[0]) {
                                    $workshop['workshop']['created'][$number]['publishedfileid'] = $backup->childNodes(0)->getAttribute("data-publishedfileid");
                                    $workshop['workshop']['created'][$number]['appid'] = $backup->childNodes(0)->getAttribute("data-appid");
                                    $workshop['workshop']['created'][$number]['title'] = $backup->childNodes(5)->childNodes(0)->innertext;
                                    echo "<div class='container'><span class='output-info-name'>Created workshop item: </span><span class='item-output' id='workshop'><a href=https://steamcommunity.com/sharedfiles/filedetails/?id={$workshop['workshop']['created'][$number]['publishedfileid']} target='_blank'>{$workshop['workshop']['created'][$number]['title']}</a> from <a href='https://store.steampowered.com/app/{$workshop['workshop']['created'][$number]['appid']}/' target='_blank'>{$workshop['workshop']['created'][$number]['appid']}</a> has been recorded.</span></div>" . PHP_EOL;
                                    $number++;
                                }
                                if ($urls[$url_number] == $urls[1]) {
                                    $workshop['workshop']['favorite'][$number]['publishedfileid'] = $backup->childNodes(0)->getAttribute("data-publishedfileid");
                                    $workshop['workshop']['favorite'][$number]['appid'] = $backup->childNodes(0)->getAttribute("data-appid");
                                    $workshop['workshop']['favorite'][$number]['title'] = $backup->childNodes(5)->childNodes(0)->innertext;
                                    echo "<div class='container'><span class='output-info-name'>Favorite workshop item: </span><span class='item-output' id='workshop'><a href=https://steamcommunity.com/sharedfiles/filedetails/?id={$workshop['workshop']['favorite'][$number]['publishedfileid']} target='_blank'>{$workshop['workshop']['favorite'][$number]['title']}</a> from <a href='https://store.steampowered.com/app/{$workshop['workshop']['favorite'][$number]['appid']}/' target='_blank'>{$workshop['workshop']['favorite'][$number]['appid']}</a> has been recorded.</span></div>" . PHP_EOL;
                                    $number++;
                                }
                            }
                        }
                    }
                    if ($response->getStatusCode() !== 200) {
                        echo "<div class='container'><span class='error'>Status code: " . $response->getStatusCode() . ", Something is wrong.</span></div>" . PHP_EOL;
                    }
                }
            }
            if ($response->getStatusCode() !== 200) {
                echo "<div class='container'><span class='error'>Status code: " . $response->getStatusCode() . ", Something is wrong.</span></div>" . PHP_EOL;
            }
        }
        $categories = array('created', 'favorite');
        if (isset($workshop['workshop'])) {
            foreach ($categories as $category) {
                if (isset($workshop['workshop'][$category])) {
                    echo "<div class='container'><span class='output-info-name'>" . ucfirst($category) . " workshop items: </span><span class='item-output' id='workshop'>" . count($workshop['workshop'][$category]) . " found.</span></div>" . PHP_EOL;
                }
                if (!isset($workshop['workshop'][$category])) {
                    echo "<div class='container'><span class='error'>There are no {$category} workshop items.</span></div>" . PHP_EOL;
                }
            }
            file_put_contents($file_output, json_encode($workshop, JSON_UNESCAPED_UNICODE));
        }
        if (!isset($workshop['workshop'])) {
            echo "<div class='container'><span class='error'>No workshop items were found.</span></div>" . PHP_EOL;
            if (file_exists($file_output)) {
                unlink($file_output);
            }
        }
        echo "<span class='start-finish-section'>[WORKSHOP]</span></div></div>" . PHP_EOL;
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

    function CheckGet($client, $url)
    {
        try {
            $response = $client->request('GET', $url);
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

    echo "<div class='box'><div class='container'><span class='start-finish-section'>[PARSING]</span></div><div class='container'><div class='output-container'>" . PHP_EOL;
    if (!file_exists($path_parse)) {
        if (mkdir($path_parse) == false) {
            exit("<div class='container'><div class='start-finish-section' id='answer'>The script was unable to update the data because the folder could not be created.</div></div>");
        } else {
            echo "<div class='container'><div class='start-finish-section' id='answer'>Directory created.</div></div>" . PHP_EOL;
        }
    }
    if (file_exists($path_parse)) {
        if ($parse_current_customization) {
            CurrentCustomization($path_parse, $client);
        }
        if ($parse_games_with_achievements) {
            GamesWithAchievements($path_parse, $client);
        }
        if ($parse_games_with_inventory) {
            GamesWithInventory($path_parse, $client);
        }
        if ($parse_achievements) {
            Achievements($path_parse, $client);
        }
        if ($parse_artworks) {
            Artworks($path_parse, $client);
        }
        if ($parse_avatars) {
            Avatars($path_parse, $client);
        }
        if ($parse_badges) {
            Badges($path_parse, $client);
        }
        if ($parse_completionist) {
            Completionist($path_parse, $client);
        }
        if ($parse_friends) {
            Friends($path_parse, $client);
        }
        if ($parse_games) {
            Games($path_parse, $client);
        }
        if ($parse_groups) {
            Groups($path_parse, $client);
        }
        if ($parse_guides) {
            Guides($path_parse, $client);
        }
        if ($parse_inventory) {
            Inventory($path_parse, $client);
        }
        if ($parse_profile_items) {
            ProfileItems($path_parse, $client);
        }
        if ($parse_reviews) {
            Reviews($path_parse, $client);
        }
        if ($parse_screenshots) {
            Screenshots($path_parse, $client);
        }
        if ($parse_themes) {
            Themes($path_parse, $client);
        }
        if ($parse_videos) {
            Videos($path_parse, $client);
        }
        if ($parse_workshop) {
            Workshop($path_parse, $client);
        }
    }
    if (!file_exists(__DIR__ . "/data/{$_SESSION['steam_steamid']}/current.json")) {
        CurrentCustomization($path_parse, $client);
    }
    echo "</div></div><div class='container'><span class='start-finish-section'>[PARSING]</span></div></div>" . PHP_EOL;
}