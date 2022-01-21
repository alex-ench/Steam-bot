<!doctype html>
<html lang="en">
<head>
    <link rel='stylesheet' href='additional files/style.css'>
    <link rel="shortcut icon" href="additional%20files/random.svg" type="image/x-icon">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Steam Random</title>
    <meta name="description" content="Steam Random">
    <meta name="author" content="SHISEN">
    <script type="text/javascript" src="additional%20files/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" src="additional%20files/ajax.js"></script>
</head>
<body>
<div class="big-box"><span class="welcome">Welcome!<br>Steam Random - completely randomizes your Steam profile.</span>
    <?php
    require 'additional files/steamauth/steamauth.php';
    if (!isset($_SESSION['steamid'])) {
        echo "<span class='box'><div style='margin: 0 auto; text-align: center;'>Welcome. Please log in!<br><br>";
        loginbutton(4);
        echo "</div></span>";
    } else {
    logoutbutton();
    if (!isset($_SESSION['cookies']) or count($_SESSION['cookies']) < 10) {
        ?>
        <div class='box' id='get-cookies'>
            <div class='container'>
                <div class='get-cookies'>
                    <div class='container'>
                        <div class='start-finish-section'>Get cookies in <a
                                    href='https://steamcommunity.com/login/dologin/' target='_blank'>Steam site</a>
                        </div>
                    </div>
                    <div class='container'>
                        <div class='start-finish-section'>To receive cookies, you need to follow the link, press "F12",
                            open the "Network" tab in the inspector. If it is empty, then refresh the page. Click on
                            "dologin". All you have to do is go to the Request Headers tab and right click on Cookie to
                            copy the cookie. After that, you need to insert the cookies into the input field and click
                            "OK". You are wonderful!
                        </div>
                    </div>
                    <div class='container'>
                        <form name='set-cookies' action='' method='post' id='set-cookies' style='display: flex; flex-direction: column; align-items: center;'>
                            <label for='set-cookie'></label><textarea class='set-cookie' name='set-cookie' id='set-cookie' placeholder='Set your cookies'></textarea>
                            <p>
                                <input class='send-cookies' style='cursor:pointer; display: inline;' type='submit' value='Send Cookies!'/>
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    if (!isset($_SESSION['steam_apikey']) and !isset($_SESSION['steam_accesstoken'])) {
        if (file_exists("data/{$_SESSION['steamid']}/important_data.json")) {
            $decode_json_important_data = json_decode(file_get_contents("data/{$_SESSION['steamid']}/important_data.json"), TRUE);
            $_SESSION['steam_apikey'] = $decode_json_important_data['important_data']['api_key'];
            $_SESSION['steam_accesstoken'] = $decode_json_important_data['important_data']['access_token'];
        } else {
            ?>
            <div class='box' id='get-api-token'>
                <div class='get-api-token'>
                    <div class='container'>
                        <form name='set-api-token' action='' method='post' id='set-api-token'
                              style='display: flex; flex-direction: column; align-items: center;'>
                            <div class='container' style='display: flex;flex-direction: column; align-items: center;'>
                                <div class='start-finish-section' style='width: 100%'>Get api key in
                                    <a href='https://steamcommunity.com/dev/apikey' target='_blank'>Steam site</a>
                                </div>
                                <label for='api-input'>API</label><input type='text' class='api-input' name='api-input' id='api-input' placeholder='Type api key'/>
                            </div>
                            <div class='container' style='display: flex;flex-direction: column; align-items: center;'>
                                <div class='start-finish-section' style='width: 100%'>Get access token in
                                    <a href='https://store.steampowered.com/points/shop/' target='_blank'>Steam site</a>
                                    and run script in console: <br><span class='item-output'>javascript:JSON.parse(application_config.dataset.loyaltystore).webapi_token;</span>
                                </div>
                                <label for='access-input'>Access
                                    token</label><input type='text' class='access-input' name='access-input' id='access-input' placeholder='Type access token'/>
                            </div>
                            <p>
                                <input class='send-api-token' style='cursor:pointer; display: inline;' type='submit' value='Send stuff!'/>
                            </p>
                        </form>
                    </div>
                </div>
            </div>
            <div id='answer'></div>
            <?php
        }
    }
    if (isset($_SESSION['cookies']) and isset($_SESSION['steam_apikey']) and isset($_SESSION['steam_accesstoken'])) {
    require 'additional files/steamauth/userInfo.php';
    ?>
    <span class="refresh-profile-data" id="refresh-profile-data"><img src="additional files/reload.svg" style="width: 50px; cursor: pointer;" alt="refresh-profile-data"><span>Refresh profile data</span></span>
    <div class="box" id="profile-info">
        <h4 style='margin-bottom: 3px; float:left;'>Your account</h4>
        <div class="profile-btn-slide" id="profile-btn-slide">
            <span class="profile-btn-top"></span> <span class="profile-btn-bot"></span>
        </div>
        <div class='container' id="profile-container" style="display: none">
            <div class="profile-info">
                <span class="profile-info-name">Session started/Last refresh data: </span>
                <span class="profile-item" id="session-start"><?= gmdate("Y-m-d\ T H:i:s\ ", $_SESSION['steam_uptodate']) ?></span>
            </div>
            <div class="profile-info">
                <span class="profile-info-name">Nickname: </span>
                <span class="profile-item" id="nickname"><?= $_SESSION['steam_personaname'] ?></span>
            </div>
            <div class="profile-info">
                <span class="profile-info-name">Real name: </span>
                <span class="profile-item" id="real-name"><?= $_SESSION['steam_realname'] ?></span>
            </div>
            <div class="profile-info">
                <span class="profile-info-name">Location: </span>
                <span class="profile-item" id="location"><?= $_SESSION['steam_location'] ?></span>
            </div>
            <div class="profile-info">
                <span class="profile-info-name">ID64: </span>
                <span class="profile-item" id="steam-id"><?= $_SESSION['steam_steamid'] ?></span>
            </div>
            <div class="profile-info">
                <span class="profile-info-name">Community visibility state: </span>
                <span class="profile-item" id="visibility-state"><a href="https://steamcommunity.com/profiles/<?= $_SESSION['steam_steamid'] ?>/edit/settings" target="_blank"><?= $_SESSION['steam_communityvisibilitystate'] ?></a></span>
            </div>
            <div class="profile-info">
                <span class="profile-info-name">Avatar: </span>
                <span class="profile-item"><img id="avatar-image-medium" src='<?= $_SESSION['steam_avatarmedium'] ?>' alt="avatar-iamge-medium"></span>
            </div>
            <div class="profile-info">
                <span class="profile-info-name">URL: </span>
                <span class="profile-item" id="profile-url"><a target="_blank" href="<?= $_SESSION['steam_profileurl'] ?>"><?= $_SESSION['steam_profileurl'] ?></a></span>
            </div>
            <div class="profile-info">
                <span class="profile-info-name">Persona State: </span>
                <span class="profile-item" id="persona-state"><?= $_SESSION['steam_personastate'] ?></span>
            </div>
            <div class="profile-info">
                <span class="profile-info-name">Last logoff: </span>
                <span class="profile-item" id="last-log-off"><?= $_SESSION['steam_lastlogoff'] ?></span>
            </div>
            <div class="profile-info">
                <span class="profile-info-name">Time created account: </span>
                <span class="profile-item"><?= $_SESSION['steam_timecreated'] ?></span>
            </div>
        </div>
    </div>
    <div class="box" id="parse-customization">
        <h4 style='margin-bottom: 3px; float:left;'>Parse your current customization</h4>
        <div class="parse-customization-btn-slide" id="parse-customization-btn-slide">
            <span class="parse-customization-btn-top"></span> <span class="parse-customization-btn-bot"></span>
        </div>
        <div class="container" id="parse-customization-container" style="display: none">
            <form name="parse-options" action="" method="post" id="parse-options">
                <div class="options-container">
                    <div id="profile-options">
                        <span class="container" id="parse-customization-options">
                            <span class="options-container"><label><input type="checkbox" class="parse-customization-checkbox put-checkbox" name="parse-customization[]" id="current" value="current"/>Current customization</label></span>
                            <span class="options-container"><label><input type="checkbox" class="parse-customization-checkbox put-checkbox" name="parse-customization[]" id="games-with-achievements" value="games-with-achievements"/>Games with achievements (only list of games)</label></span>
                            <span class="options-container"><label><input type="checkbox" class="parse-customization-checkbox put-checkbox" name="parse-customization[]" id="games-with-inventory" value="games-with-inventory"/>Games with inventory (only list of games)</label></span>
                            <span class="options-container"><label><input type="checkbox" class="parse-customization-checkbox put-checkbox" name="parse-customization[]" id="avatars" value="avatars"/>Avatars</label></span>
                            <span class="options-container"><label><input type="checkbox" class="parse-customization-checkbox put-checkbox" name="parse-customization[]" id="friends" value="friends"/>Friends</label></span>
                            <span class="options-container"><label><input type="checkbox" class="parse-customization-checkbox put-checkbox" name="parse-customization[]" id="games" value="games"/>Games</label></span>
                            <span class="options-container"><label><input type="checkbox" class="parse-customization-checkbox put-checkbox" name="parse-customization[]" id="inventory" value="inventory"/>Inventory</label></span>
                            <span class="options-container"><label><input type="checkbox" class="parse-customization-checkbox put-checkbox" name="parse-customization[]" id="themes" value="themes"/>Themes</label></span>
                        </span>
                        <span class="container" id="parse-customization-options">
                            <span class="options-container"><label><input type="checkbox" class="parse-customization-checkbox put-checkbox" name="parse-customization[]" id="achievements" value="achievements"/>Achievements</label></span>
                            <span class="options-container"><label><input type="checkbox" class="parse-customization-checkbox put-checkbox" name="parse-customization[]" id="artworks" value="artworks"/>Artworks</label></span>
                            <span class="options-container"><label><input type="checkbox" class="parse-customization-checkbox put-checkbox" name="parse-customization[]" id="badges" value="badges"/>Badges</label></span>
                            <span class="options-container"><label><input type="checkbox" class="parse-customization-checkbox put-checkbox" name="parse-customization[]" id="completionist" value="completionist"/>Completionist</label></span>
                            <span class="options-container"><label><input type="checkbox" class="parse-customization-checkbox put-checkbox" name="parse-customization[]" id="groups" value="groups"/>Groups</label></span>
                            <span class="options-container"><label><input type="checkbox" class="parse-customization-checkbox put-checkbox" name="parse-customization[]" id="guides" value="guides"/>Guides</label></span>
                            <span class="options-container"><label><input type="checkbox" class="parse-customization-checkbox put-checkbox" name="parse-customization[]" id="profile-items" value="profile-items"/>Profile items</label></span>
                            <span class="options-container"><label><input type="checkbox" class="parse-customization-checkbox put-checkbox" name="parse-customization[]" id="reviews" value="reviews"/>Reviews</label></span>
                            <span class="options-container"><label><input type="checkbox" class="parse-customization-checkbox put-checkbox" name="parse-customization[]" id="screenshots" value="screenshots"/>Screenshots</label></span>
                            <span class="options-container"><label><input type="checkbox" class="parse-customization-checkbox put-checkbox" name="parse-customization[]" id="videos" value="videos"/>Videos</label></span>
                            <span class="options-container"><label><input type="checkbox" class="parse-customization-checkbox put-checkbox" name="parse-customization[]" id="workshop" value="workshop"/>Workshop</label></span>
                        </span>
                    </div>
                    <div class="container" id="start-randomize">
                        <span class="button-check-options"><input type="button" class="parse-check-all" id="btn-on" value="Check all"></span>
                        <input type="submit" name="start-parse" id="start-parse" class="start-parse" id="btn-on" value="Parse!"/>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="box" id="all-options">
        <h4 style='margin-bottom: 3px; float:left;'>Randomizing your profile</h4>
        <div class="profile-random-btn-slide" id="profile-random-btn-slide">
            <span class="profile-random-btn-top"></span> <span class="profile-random-btn-bot"></span>
        </div>
        <div class="container" id="profile-random-container" style="display: none">
            <form name="options" action="" method="post" id="options">
                <div class="options-container">
                    <div id="profile-options">
                        <span class="container" id="profile-options">
                            <h4 style="margin-bottom: 3px; float:left;">Profile options</h4>
                            <span class="options-container"><label><input type="checkbox" class="profile-checkbox put-checkbox" name="profile-option[]" id="main-info" value="main-info"/>Main info</label></span>
                            <span class="options-container"><label><input type="checkbox" class="profile-checkbox put-checkbox" name="profile-option[]" id="showcases" value="showcases"/>Showcases</label></span>
                            <span class="options-container"><label><input type="checkbox" class="profile-checkbox put-checkbox" name="profile-option[]" id="avatar" value="avatar"/>Avatar</label></span>
                            <span class="options-container"><label><input type="checkbox" class="profile-checkbox put-checkbox" name="profile-option[]" id="background" value="background"/>Background</label></span>
                            <span class="options-container"><label><input type="checkbox" class="profile-checkbox put-checkbox" name="profile-option[]" id="favorite-badge" value="favorite-badge"/>Favorite Badge</label></span>
                            <span class="options-container"><label><input type="checkbox" class="profile-checkbox put-checkbox" name="profile-option[]" id="theme" value="theme"/>Theme</label></span>
                            <span class="button-check-options"><input type="button" class="profile-check-all" id="btn-on" value="Check all"></span>
                        </span> <span class="container" id="main-info-options" style="display: none">
                            <h4 style="margin-bottom: 3px; float:left;">Main info options</h4>
                            <span class="options-container"><label><input type="checkbox" class="main-info-checkbox put-checkbox" name="main-info-option[]" id="nickname" value="nickname"/>Nickname</label></span>
                            <span class="options-container"><input type="text" class="nickname-input" name="nickname-input" id="nickname-input" placeholder="Type nickname"/></span>
                            <span class="options-container"><label><input type="checkbox" class="main-info-checkbox put-checkbox" name="main-info-option[]" id="real-name" value="real-name"/>Real name</label></span>
                            <span class="options-container"><label><input type="checkbox" class="main-info-checkbox put-checkbox" name="main-info-option[]" id="location" value="location"/>Location</label></span>
                            <span class="options-container"><label><input type="checkbox" class="main-info-checkbox put-checkbox" name="main-info-option[]" id="summary" value="summary"/>Summary</label></span>
                            <span class="button-check-options"><input type="button" class="main-info-check-all" id="btn-on" value="Check all"></span>
                        </span> <span class="container" id="showcases-options" style="display: none">
                            <h4 style="margin-bottom: 3px; float:left;">Showcases options</h4>
                            <span class="options-container"><label><input type="checkbox" class="showcases-checkbox put-checkbox" name="showcases-option[]" id="achievements" value="achievements"/>Achievements</label></span>
                            <span class="options-container"><label><input type="checkbox" class="showcases-checkbox put-checkbox" name="showcases-option[]" id="artworks-created" value="artworks-created"/>Created artworks</label></span>
                            <span class="options-container"><label><input type="checkbox" class="showcases-checkbox put-checkbox" name="showcases-option[]" id="artwork-featured" value="artwork-featured"/>Featured artwork</label></span>
                            <span class="options-container"><label><input type="checkbox" class="showcases-checkbox put-checkbox" name="showcases-option[]" id="badge-collector" value="badge-collector"/>Badge collector</label></span>
                            <span class="options-container"><label><input type="checkbox" class="showcases-checkbox put-checkbox" name="showcases-option[]" id="completionist" value="completionist"/>Completionist</label></span>
                            <span class="options-container"><label><input type="checkbox" class="showcases-checkbox put-checkbox" name="showcases-option[]" id="custom-info-box" value="custom-info-box"/>Custom info box</label></span>
                            <span class="options-container"><label><input type="checkbox" class="showcases-checkbox put-checkbox" name="showcases-option[]" id="favorite-group" value="favorite-group"/>Favorite group</label></span>
                            <span class="options-container"><label><input type="checkbox" class="showcases-checkbox put-checkbox" name="showcases-option[]" id="game-collector" value="game-collector"/>Game collector</label></span>
                            <span class="options-container"><label><input type="checkbox" class="showcases-checkbox put-checkbox" name="showcases-option[]" id="game-favorite" value="game-favorite"/>Favorite game</label></span>
                            <span class="options-container"><label><input type="checkbox" class="showcases-checkbox put-checkbox" name="showcases-option[]" id="guides-created" value="guides-created"/>Created guides</label></span>
                            <span class="options-container"><label><input type="checkbox" class="showcases-checkbox put-checkbox" name="showcases-option[]" id="guide-favorite" value="guide-favorite"/>Favorite guide</label></span>
                            <span class="options-container"><label><input type="checkbox" class="showcases-checkbox put-checkbox" name="showcases-option[]" id="items" value="items"/>Items</label></span>
                            <span class="options-container"><label><input type="checkbox" class="showcases-checkbox put-checkbox" name="showcases-option[]" id="items-for-trade" value="items-for-trade"/>Items for trade</label></span>
                            <span class="options-container"><label><input type="checkbox" class="showcases-checkbox put-checkbox" name="showcases-option[]" id="screenshots" value="screenshots"/>Screenshots</label></span>
                            <span class="options-container"><label><input type="checkbox" class="showcases-checkbox put-checkbox" name="showcases-option[]" id="videos" value="videos"/>Videos</label></span>
                            <span class="options-container"><label><input type="checkbox" class="showcases-checkbox put-checkbox" name="random-showcases-position" id="random-showcases-position" value="random-showcases-position"/>Random showcases position</label></span>
                            <span class="button-check-options"><input type="button" class="showcases-check-all" id="btn-on" value="Check all"></span>
                        </span>
                    </div>
                    <div class="container" id="start-randomize">
                        <select name="start-randomize[]" id="start-randomize" required>
                            <option value="once">Once</option>
                            <option value="infinitely">Infinitely</option>
                        </select>
                        <input type="button" id="stop-infinitely" class="stop-infinitely" value="Stop infinitely"/>
                        <input type="submit" name="start-profile-random" id="start-profile-random" class="start-profile-random" value="Random!"/>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div id="randomize-answer"></div>
<div class="popup-fade">
    <div class="popup">
        <p>Task in progress.</p>
    </div>
</div>
<?php
}
}
?>
</body>
</html>