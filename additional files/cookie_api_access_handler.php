<?php
session_start();
if (isset($_POST['set-cookies']) or isset($_POST['set-api-token'])) {
    if (isset($_POST['set-cookies'])) {
        if (!empty($_POST['set-cookies'])) {
            function SetCookies()
            {
                $backup = array();
                $cookies = array();
                $raw_cookies = explode(';', $_POST['set-cookies']);
                foreach ($raw_cookies as $key => $cookie) $backup[$key] = explode("=", $cookie);
                foreach ($backup as $key => $item) {
                    $cookie_name = trim($item[0]);
                    if ($cookie_name === 'timezoneOffset') $cookies[$cookie_name] = $item[1];
                    if ($cookie_name === '_gid') $cookies[$cookie_name] = $item[1];
                    if ($cookie_name === '_ga') $cookies[$cookie_name] = $item[1];
                    if ($cookie_name === 'timezone_offset') $cookies[$cookie_name] = $item[1];
                    if ($cookie_name === 'steamCountry') $cookies[$cookie_name] = $item[1];
                    if ($cookie_name === 'sessionid') $cookies[$cookie_name] = $item[1];
                    if ($cookie_name === 'steamMachineAuth' . $_SESSION['steamid']) $cookies['steamMachineAuth'] = $item[1];
                    if ($cookie_name === 'browserid') $cookies[$cookie_name] = $item[1];
                    if ($cookie_name === 'Steam_Language') $cookies[$cookie_name] = $item[1];
                    if ($cookie_name === 'steamRememberLogin') $cookies[$cookie_name] = $item[1];
                    if ($cookie_name === 'steamLoginSecure') $cookies[$cookie_name] = $item[1];
                }
                return $cookies;
            }

            $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $boundary = substr(str_shuffle($permitted_chars), 0, 16);
            $_SESSION['WebKitFormBoundary'] = "WebKitFormBoundary$boundary";
            $_SESSION['cookies'] = SetCookies();
            if (count($_SESSION['cookies']) === 10) {
                echo "<div class='box'><div class='container'><div class='start-finish-section' id='answer'>All cookies are set!</div></div></div>";
            } elseif (count($_SESSION['cookies']) < 10) {
                echo "<div class='box'><div class='container'><div class='start-finish-section' id='answer'>The number of cookies is insufficient.</div></div></div>";
            }
        } else {
            echo "<div class='box'><div class='container'><div class='start-finish-section' id='answer'>Cookies empty!</div></div></div>";
        }
    }
    if (isset($_POST['set-api-token'])) {
        $check_api = false;
        $check_token = false;
        if (!empty($_POST['set-api-token']['set-api'])) {
            if (mb_strlen($_POST['set-api-token']['set-api']) === 32) {
                $_SESSION['steam_apikey'] = $_POST['set-api-token']['set-api'];
                $check_api = true;
            } else {
                echo "<div class='box'><div class='container'><div class='start-finish-section' id='answer'>Api key in incorrect.</div></div></div>";
            }
        }
        if (!empty($_POST['set-api-token']['set-token'])) {
            if (mb_strlen($_POST['set-api-token']['set-token']) === 32) {
                $_SESSION['steam_accesstoken'] = $_POST['set-api-token']['set-token'];
                $check_token = true;
            } else {
                echo "<div class='box'><div class='container'><div class='start-finish-section' id='answer'>Access token in incorrect.</div></div></div>";
            }
        }
        if ($check_api === true and $check_token === true) {
            echo "<div class='box'><div class='container'><div class='start-finish-section' id='answer'>Api key and access token are set!</div></div></div>";
        }
    }
    if (isset($_SESSION['cookies']) and count($_SESSION['cookies']) === 10 and isset($_SESSION['steam_apikey']) and isset($_SESSION['steam_accesstoken'])) {
        if (!file_exists("../data/{$_SESSION['steamid']}")) {
            if (mkdir("../data/{$_SESSION['steamid']}") !== false) {
                echo "<div class='box'><div class='container'><div class='start-finish-section' id='answer'>Directory created.</div></div></div>";
            } else {
                echo "<div class='box'><div class='container'><div class='start-finish-section' id='answer'>Directory not created.</div></div></div>.";
            }
        }
        if (file_exists("../data/{$_SESSION['steamid']}")) {
            $api_access_cookies = array('important_data' => ['api_key' => $_SESSION['steam_apikey'], 'access_token' => $_SESSION['steam_accesstoken'], 'cookies' => $_SESSION['cookies'], 'WebKitFormBoundary' => $_SESSION['WebKitFormBoundary']]);
            file_put_contents("../data/{$_SESSION['steamid']}/important_data.json", json_encode($api_access_cookies, true));
        }
        echo "<div class='box'><div class='container'><div class='start-finish-section' id='done'>All data are set!</div></div></div>";
    }
}