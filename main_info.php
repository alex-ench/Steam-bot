<?php
if (isset($_POST['main-info-option'])) {
    echo "<div class='box'><div class='container'><span class='start-finish-section'>[MAIN INFO]</span></div><div class='container'><div class='output-container'>" . PHP_EOL;
    if (in_array('location', $_POST['main-info-option'])) {
        $path_location = __DIR__ . '\data\unchangeable\location.json';
        if (file_exists($path_location)) $send_location = true; else {
            $send_location = false;
            echo "<div class='output-items'><span class='error'>The required file (location.json) do not exist.</span></div>" . PHP_EOL;
        }
    } else $send_location = false;
    if (in_array('nickname', $_POST['main-info-option'])) {
        $path_adjectives = __DIR__ . '\data\unchangeable\adjectives.json';
        if (file_exists($path_adjectives)) $send_nickname = true; else {
            $send_nickname = false;
            echo "<div class='output-items'><span class='error'>The required file (adjectives.json) do not exist.</span></div>" . PHP_EOL;
        }
    } else $send_nickname = false;
    if (in_array('real-name', $_POST['main-info-option'])) {
        $path_not_change_data = __DIR__ . '\data\unchangeable\not_change_data.json';
        $path_real_name = __DIR__ . '\data\unchangeable\real_name.json';
        if (file_exists($path_not_change_data) and file_exists($path_real_name)) $send_real_name = true; else {
            $send_real_name = false;
            echo "<div class='output-items'><span class='error'>The required files (not_change_data.json or real_name.json) do not exist.</span></div>" . PHP_EOL;
        }
    } else $send_real_name = false;
    if (in_array('summary', $_POST['main-info-option'])) {
        $path_not_change_data = __DIR__ . '\data\unchangeable\not_change_data.json';
        if (file_exists($path_not_change_data)) $send_summary = true; else {
            $send_summary = false;
            echo "<div class='output-items'><span class='error'>The required files (not_change_data.json) do not exist.</span></div>" . PHP_EOL;
        }
    } else $send_summary = false;

    function Nickname($nickname, $path_adjectives)
    {
        $file = json_decode(file_get_contents($path_adjectives), TRUE);
        $rand_name = array_rand($file['adjectives'], 1);
        $adjectives = $file['adjectives'][$rand_name];
        $personaName = $nickname . ' is ' . $adjectives;
        echo "<div class='output-items'><span class='output-info-name'>Nickname: </span><span class='item-output' id='nickname'>" . $personaName . "</span></div>" . PHP_EOL;
        return $personaName;
    }

    function RealName($path_not_change_data, $path_real_name)
    {
        //emoji
        $file = json_decode(file_get_contents($path_not_change_data), TRUE);
        $emoji = array();
        $count = count($file['emojis_objects']);
        do {
            $possible = rand(0, $count - 1);
            if (!isset($numbers[$possible])) {
                $numbers[$possible] = true;
            }
        } while (count($numbers) < 4);
        $numberss = array_keys($numbers);
        for ($number = 0; $number < 4; $number++) {
            $emoji[$number] = $file['emojis_objects'][$numberss[$number]];
        }
        //age
        $age_rand = array_rand($file['age'], 1);
        $age = $file['age'][$age_rand];
        $file = json_decode(file_get_contents($path_real_name), TRUE);
        $first_name_rand = array_rand($file['first_name'], 1);
        $first_name = $file['first_name'][$first_name_rand];
        $last_name_rand = array_rand($file['last_name'], 1);
        $last_name = $file['last_name'][$last_name_rand];
        $name = array();
        array_push($name, $first_name, $last_name);
        $real_name = "/" . $emoji[0] . "/ (" . $name[0] . ") |" . $emoji[1] . "| (" . $name[1] . ") |" . $emoji[2] . "| (" . $age . ") \\" . $emoji[3] . "\\";
        echo "<div class='output-items'><span class='output-info-name'>Real name: </span><span class='item-output' id='real-name'>" . $real_name . "</span></div>" . PHP_EOL;
        return $real_name;
    }

    function Location($path_location)
    {
        $location = array();
        $file = json_decode(file_get_contents($path_location), true);
        //country
        $prefix = array_rand($file['countries'], 1);
        $location['country'] = $file['countries'][$prefix][0];
        //state
        if (!empty($file['countries'][$prefix][1])) {
            $prefix2 = array_rand($file['countries'][$prefix][1]);
            $location['state'] = $file['countries'][$prefix][1][$prefix2][0];
        } else {
            $prefix2 = NULL;
            $location['state'] = NULL;
        }
        //city
        if (!empty($file['countries'][$prefix][1][$prefix2][1])) {
            $prefix3 = array_rand($file['countries'][$prefix][1][$prefix2][1]);
            $location['city'] = $file['countries'][$prefix][1][$prefix2][1][$prefix3];
        } else {
            $location['city'] = NULL;
        }
        echo "<div class='output-items'><span class='output-info-name'>Location: </span><span class='item-output' id='location'>" . $location['country'] . " " . $location['state'] . " " . $location['city'] . "</span></div>" . PHP_EOL;
        return $location;
    }

    function Summary($path_not_change_data)
    {
        $file = json_decode(file_get_contents($path_not_change_data), TRUE);
        $ascii = $file['ascii'];
        array_splice($ascii, 62);
        shuffle($ascii);
        $summary = implode(" ", $ascii);
        echo "<div class='output-items'><span class='output-info-name'>Summary: </span><span class='item-output' id='summary'>" . $summary . "</span></div>" . PHP_EOL;
        return $summary;
    }

    //echo "<div class='output-items'><span class='output-info-name'>Option randomization is disabled.</span></div>" . PHP_EOL;

    if ($send_nickname) {
        $personaName = Nickname($_POST['nickname-input'], $path_adjectives);
        $_SESSION['steam_personaname'] = $personaName;
    } else $personaName = $_SESSION['steam_personaname'];
    if ($send_real_name) {
        $real_name = RealName($path_not_change_data, $path_real_name);
        $_SESSION['steam_realname'] = $real_name;
    } else $real_name = $_SESSION['steam_realname'];
    if ($send_location) {
        $location = Location($path_location);
        $_SESSION['steam_loccountrycode'] = $location['country'];
        $_SESSION['steam_locstatecode'] = $location['state'];
        $_SESSION['steam_loccityid'] = $location['city'];
    } else {
        $location['country'] = $_SESSION['steam_loccountrycode'];
        $location['state'] = $_SESSION['steam_locstatecode'];
        $location['city'] = $_SESSION['steam_loccityid'];
    }
    if ($send_summary) $summary = Summary($path_not_change_data); else $summary = NULL;
    echo "</div></div><div class='container'><span class='start-finish-section'>[MAIN INFO]</span></div></div>" . PHP_EOL;
} else {
    $main_info = false;
    echo "<div class='box'><div class='output-items'><span class='error'>[MAIN INFO] – All options is disabled.</span></div></div>";
}