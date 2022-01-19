<?php
if (isset($_POST['showcases-option'])) {
    $path_to_data = __DIR__ . '\data\/' . $_SESSION['steam_steamid'];
    if (file_exists($path_to_data . '\current.json')) {
        $decode_json_current = json_decode(file_get_contents($path_to_data . '\current.json'), TRUE);
        if (isset($decode_json_current['current']['ProfileCustomization']['slots_available'])) {
            if (in_array('achievements', $_POST['showcases-option'])) $send_achievements[17] = true; else $send_achievements[17] = false;
            if (in_array('artworks-created', $_POST['showcases-option'])) $send_artworks_created[13] = true; else $send_artworks_created[13] = false;
            if (in_array('artwork-featured', $_POST['showcases-option'])) $send_artwork_featured[22] = true; else $send_artwork_featured[22] = false;
            if (in_array('badge-collector', $_POST['showcases-option'])) $send_badge_collector[5] = true; else $send_badge_collector[5] = false;
            if (in_array('custom-info-box', $_POST['showcases-option'])) $send_custom_info_box[8] = true; else $send_custom_info_box[8] = false;
            if (in_array('completionist', $_POST['showcases-option'])) $send_completionist[23] = true; else $send_completionist[23] = false;
            if (in_array('favorite-group', $_POST['showcases-option'])) $send_favorite_group[9] = true; else $send_favorite_group[9] = false;
            if (in_array('game-collector', $_POST['showcases-option'])) $send_game_collector[2] = true; else $send_game_collector[2] = false;
            if (in_array('game-favorite', $_POST['showcases-option'])) $send_game_favorite[6] = true; else $send_game_favorite[6] = false;
            if (in_array('guides-created', $_POST['showcases-option'])) $send_guides_created[16] = true; else $send_guides_created[16] = false;
            if (in_array('guide-favorite', $_POST['showcases-option'])) $send_guide_favorite[15] = true; else $send_guide_favorite[15] = false;
            if (in_array('items', $_POST['showcases-option'])) $send_items[3] = true; else $send_items[3] = false;
            if (in_array('items-for-trade', $_POST['showcases-option'])) $send_items_for_trade[4] = true; else $send_items_for_trade[4] = false;
            if (in_array('screenshots', $_POST['showcases-option'])) $send_screenshots[7] = true; else $send_screenshots[7] = false;
            if (in_array('videos', $_POST['showcases-option'])) $send_videos[14] = true; else $send_videos[14] = false;

            $send_workshop = false;//11, 12     //not realized my workshop

            if (isset($_POST['random-showcases-position'])) $shuffle_showcases = true; else $shuffle_showcases = false;

            $send_all_showcases = array($send_achievements, $send_artworks_created, $send_artwork_featured, $send_badge_collector, $send_completionist, $send_custom_info_box, $send_favorite_group, $send_game_collector, $send_game_favorite, $send_guides_created, $send_guide_favorite, $send_items, $send_items_for_trade, $send_screenshots, $send_videos);

            echo "<div class='box'><div class='container'><span class='start-finish-section'>[SHOWCASES]</span></div><div class='container'><div class='output-container'>" . PHP_EOL;
            $count_showcases = $decode_json_current['current']['ProfileCustomization']['slots_available'];
            $customizations = $decode_json_current['current']['ProfileCustomization']['customizations'];

            function EnableShowcases($send_all_showcases, $customizations, $count_showcases, $shuffle_showcases)
            {
                $enabled_showcases = array();
                $profile_showcases = array();
                foreach ($send_all_showcases as $showcase) {
                    foreach ($showcase as $id => $key) {
                        if ($key === true) array_push($enabled_showcases, $id);
                    }
                }
                $number = 0;
                foreach ($customizations as $possibles) {
                    if (isset($possibles['slots']) and count($possibles['slots'][0]) !== 1) {
                        $profile_showcases[$number] = $possibles['customization_type'];
                        $number++;
                    }
                }
                if ($shuffle_showcases === true) shuffle($profile_showcases);
                $number = 0;
                foreach ($profile_showcases as $showcase) {
                    if (in_array($showcase, $enabled_showcases) and $count_showcases !== $number) {
                        $profile_showcase[$number] = $showcase;
                        $number++;
                    }
                }
                for ($number; $number < 21; $number++) {
                    $profile_showcase[$number] = 0;
                }
                return $profile_showcase;
            }

            function ResetCustomization($customizations, $num_showcase, $count_slots_in_showcase, $type_of_information)
            {
                foreach ($customizations as $customization) {
                    if ($customization['customization_type'] === $num_showcase and isset($customization['slots']) and count($customization['slots'][0]) !== 1) {
                        for ($number = 0; $number < $count_slots_in_showcase; $number++) {
                            foreach ($type_of_information as $type) {
                                if ($num_showcase !== 17) {
                                    if (count($customization['slots'][$number]) !== 1) {
                                        $array[$number][$type] = $customization['slots'][$number][$type];
                                    }
                                    if (count($customization['slots'][$number]) === 1) {
                                        $array[$number][$type] = 0;
                                    }
                                }
                                if ($num_showcase === 17) {
                                    if ($customization['slots'][$number]['slot'] !== 7 and $customization['slots'][$number]['slot'] !== 8) {
                                        if (count($customization['slots'][$number]) !== 1) {
                                            $slot = $customization['slots'][$number]['slot'];
                                            $array[$slot][$type] = $customization['slots'][$number][$type];
                                        }
                                        if (count($customization['slots'][$number]) === 1) {
                                            $slot = $customization['slots'][$number]['slot'];
                                            $array[$slot][$type] = 0;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if (($customization['customization_type'] === $num_showcase) and (!isset($customization['slots']) or count($customization['slots'][0]) === 1)) {
                        for ($number = 0; $number < $count_slots_in_showcase; $number++) {
                            foreach ($type_of_information as $type) {
                                $array[$number][$type] = 0;
                                $array[$number][$type] = 0;
                            }
                        }
                    }
                    if ($num_showcase === 4 and isset($customization['slots'][6]['notes'])) {
                        $array[6]['notes'] = $customization['slots'][6]['notes'];
                    }
                    if ($num_showcase === 4 and !isset($customization['slots'][6]['notes'])) {
                        $array[6]['notes'] = '';
                    }
                }
                return $array;
            }

            function Achievements($path_to_data, $customizations)
            {
                $file = json_decode(file_get_contents($path_to_data . '\achievements.json'), TRUE);
                if (isset($file['achievements'])) {
                    echo "<div class='container'><div class='output-items'><span class='start-finish-section'>[ACHIEVEMENTS]</span>" . PHP_EOL;
                    $numbers = array();
                    $count = count($file['achievements']);
                    if ($count >= 7) {
                        do {
                            $possible = rand(0, $count - 1);
                            if (!isset($numbers[$possible])) {
                                $numbers[$possible] = true;
                            }
                        } while (count($numbers) < 7);
                        $numberss = array_keys($numbers);
                        for ($number = 0; $number < 7; $number++) {
                            $achievements[$number]['appid'] = $file['achievements'][$numberss[$number]]['appid'];
                            $achievements[$number]['title'] = $file['achievements'][$numberss[$number]]['title'];
                            echo "<div class='container'><span class='output-info-name'>Achievement: </span><span class='item-output' id='achievements'> {$file['achievements'][$numberss[$number]]['name']} from <a href=https://store.steampowered.com/app/{$achievements[$number]['appid']} target='_blank'>{$file['achievements'][$numberss[$number]]['app_name']}</a> was established in " . $cell = $number + 1 . " cell.</span></div>" . PHP_EOL;
                        }
                    }
                    if ($count < 7) {
                        echo "<div class='notice-count'>Total number of achievements must be more than 7. Get more in-game achievements to make the showcase look complete!</div>" . PHP_EOL;
                        do {
                            $possible = rand(0, $count - 1);
                            if (!isset($numbers[$possible])) {
                                $numbers[$possible] = true;
                            }
                        } while (count($numbers) < $count);
                        $numberss = array_keys($numbers);
                        for ($number = 0; $number < $count; $number++) {
                            $achievements[$number]['appid'] = $file['achievements'][$numberss[$number]]['appid'];
                            $achievements[$number]['title'] = $file['achievements'][$numberss[$number]]['title'];
                            echo "<div class='container'><span class='output-info-name'>Achievement: </span>\"<span class='item-output' id='achievements'>{$file['achievements'][$numberss[$number]]['name']}\" from <a href=https://store.steampowered.com/app/{$achievements[$number]['appid']} target='_blank'>{$file['achievements'][$numberss[$number]]['app_name']}</a> was established in " . $cell = $number + 1 . " cell.</span></div>" . PHP_EOL;
                            $additional_number = $number + 1;
                        }
                        for ($additional_number; $additional_number < 7; $additional_number++) {
                            $achievements[$additional_number]['appid'] = 0;
                            $achievements[$additional_number]['title'] = 0;
                            echo "<div class='container'><span class='output-info-name'>Achievement: </span><span class='empty-part'>Empty " . $cell = $additional_number + 1 . " cell.</span></div>" . PHP_EOL;
                        }
                    }
                    echo "<span class='start-finish-section'>[ACHIEVEMENTS]</span></div></div>" . PHP_EOL;
                } else {
                    $type_of_information = array('appid', 'title');
                    $achievements = ResetCustomization($customizations, 17, 9, $type_of_information);
                    echo "<div class='container'><span class='error'>No achievements were found.</span></div>" . PHP_EOL;
                }
                return $achievements;
            }

            function ArtworksCreated($path_to_data, $customizations)
            {
                $file = json_decode(file_get_contents($path_to_data . '\artworks.json'), TRUE);
                if (isset($file['artworks'])) {
                    echo "<div class='container'><div class='output-items'><span class='start-finish-section'>[CREATED ARTWORKS]</span>" . PHP_EOL;
                    $numbers = array();
                    $count = count($file['artworks']);
                    if ($count >= 4) {
                        do {
                            $possible = rand(0, $count - 1);
                            if (!isset($numbers[$possible])) {
                                $numbers[$possible] = true;
                            }
                        } while (count($numbers) < 4);
                        $numberss = array_keys($numbers);
                        for ($number = 0; $number < 4; $number++) {
                            $artworks_created[$number]['publishedfileid'] = $file['artworks'][$numberss[$number]]['publishedfileid'];
                            echo "<div class='container'><span class='output-info-name'>Created artwork: </span><span class='item-output' id='created-artworks'><a href=https://steamcommunity.com/sharedfiles/filedetails/?id={$artworks_created[$number]['publishedfileid']} target='_blank'>{$artworks_created[$number]['publishedfileid']}</a> was established in " . $cell = $number + 1 . " cell.</span></div>" . PHP_EOL;
                        }
                    }
                    if ($count < 4) {
                        echo "<div class='notice-count'>The total number of artworks must be more than 4. Add more artworks to make the showcase look complete!</div>" . PHP_EOL;
                        do {
                            $possible = rand(0, $count - 1);
                            if (!isset($numbers[$possible])) {
                                $numbers[$possible] = true;
                            }
                        } while (count($numbers) < $count);
                        $numberss = array_keys($numbers);
                        for ($number = 0; $number < $count; $number++) {
                            $artworks_created[$number]['publishedfileid'] = $file['artworks'][$numberss[$number]]['publishedfileid'];
                            echo "<div class='container'><span class='output-info-name'>Created artwork: </span><span class='item-output' id='created-artworks'><a href=https://steamcommunity.com/sharedfiles/filedetails/?id={$artworks_created[$number]['publishedfileid']} target='_blank'>{$artworks_created[$number]['publishedfileid']}</a> was established in " . $cell = $number + 1 . " cell.</span></div>" . PHP_EOL;
                            $additional_number = $number + 1;
                        }
                        for ($additional_number; $additional_number < 4; $additional_number++) {
                            $artworks_created[$additional_number]['publishedfileid'] = 0;
                            echo "<div class='container'><span class='output-info-name'>Created artwork: </span><span class='empty-part'>Empty " . $cell = $additional_number + 1 . " cell.</span></div>" . PHP_EOL;
                        }
                    }
                    echo "<span class='start-finish-section'>[CREATED ARTWORKS]</span></div></div>" . PHP_EOL;
                } else {
                    $type_of_information = array('publishedfileid');
                    $artworks_created = ResetCustomization($customizations, 13, 4, $type_of_information);
                    echo "<div class='container'><span class='error'>No artworks were found.</span></div>" . PHP_EOL;
                }
                return $artworks_created;
            }

            function ArtworkFeatured($path_to_data, $customizations, $artworks_created)
            {
                $file = json_decode(file_get_contents($path_to_data . '\artworks.json'), TRUE);
                if (isset($file['artworks'])) {
                    echo "<div class='container'><div class='output-items'><span class='start-finish-section'>[FEATURED ARTWORK]</span>" . PHP_EOL;
                    $count = count($file['artworks']);
                    $artwork_featured = array();
                    $artworks_created_ids = array();
                    if (count($file['artworks']) >= 5) {
                        foreach ($artworks_created as $artwork_created) {
                            foreach ($artwork_created as $item) {
                                array_push($artworks_created_ids, $item);
                            }
                        }
                        do {
                            $possible = rand(0, $count - 1);
                            $backup = $file['artworks'][$possible]['publishedfileid'];
                            if (!in_array($backup, $artworks_created_ids)) {
                                $artwork_featured[0]['publishedfileid'] = $file['artworks'][$possible]['publishedfileid'];
                            }
                        } while (empty($artwork_featured));
                    }
                    if (count($file['artworks']) < 5) {
                        $possible = rand(0, $count - 1);
                        $artwork_featured[0]['publishedfileid'] = $file['artworks'][$possible]['publishedfileid'];
                    }
                    echo "<div class='container'><span class='output-info-name'>Featured artwork: </span><span class='item-output' id='featured-artwork'><a href=https://steamcommunity.com/sharedfiles/filedetails/?id={$artwork_featured[0]['publishedfileid']} target='_blank'>{$artwork_featured[0]['publishedfileid']}</a> was established.</span></div>" . PHP_EOL;
                    echo "<span class='start-finish-section'>[FEATURED ARTWORK]</span></div></div>" . PHP_EOL;
                } else {
                    $type_of_information = array('publishedfileid');
                    $artwork_featured = ResetCustomization($customizations, 22, 1, $type_of_information);
                    echo "<div class='container'><span class='error'>No artworks were found.</span></div>" . PHP_EOL;
                }
                return $artwork_featured;
            }

            function BadgeCollector($path_to_data, $customizations)
            {
                $file = json_decode(file_get_contents($path_to_data . '\badges.json'), TRUE);
                if (isset($file['badges'])) {
                    echo "<div class='container'><div class='output-items'><span class='start-finish-section'>[BADGE COLLECTOR]</span>" . PHP_EOL;
                    $additional_number = 1;
                    $numbers = array();
                    $count = count($file['badges']);
                    if ($count >= 6) {
                        do {
                            $possible = rand(0, $count - 1);
                            if (!isset($numbers[$possible])) {
                                $numbers[$possible] = true;
                            }
                        } while (count($numbers) < 6);
                        $numberss = array_keys($numbers);
                        for ($number = 0; $number < 6; $number++) {
                            $badge_collector[$number]['badgeid'] = $file['badges'][$numberss[$number]]['badgeid'];
                            echo "<div class='container'><span class='output-info-name'>Badge: </span><span class='item-output' id='badge-collector'><a href='https://steamcommunity.com/profiles/{$_SESSION['steam_steamid']}/badges/{$badge_collector[$number]['badgeid']}' target='_blank'>{$badge_collector[$number]['badgeid']}</a> was established in " . $cell = $number + 1 . " cell.</span></div>" . PHP_EOL;
                        }
                    }
                    if ($count < 6) {
                        echo "<div class='notice-count'>The total number of badges must be more than 6. Get more badges to make the showcase look complete!</div>" . PHP_EOL;
                        do {
                            $possible = rand(0, $count - 1);
                            if (!isset($numbers[$possible])) {
                                $numbers[$possible] = true;
                            }
                        } while (count($numbers) < $count);
                        $numberss = array_keys($numbers);
                        for ($number = 0; $number < $count; $number++) {
                            $badge_collector[$number]['badgeid'] = $file['badges'][$numberss[$number]]['badgeid'];
                            echo "<div class='container'><span class='output-info-name'>Badge: </span><span class='item-output' id='badge-collector'>({$badge_collector[$number]['badgeid']}) was established in " . $cell = $number + 1 . " cell.</span></div>" . PHP_EOL;
                            $additional_number = $number + 1;
                        }
                        for ($additional_number; $additional_number < 6; $additional_number++) {
                            $badge_collector[$additional_number]['badgeid'] = 0;
                            echo "<div class='container'><span class='output-info-name'>Badge: </span><span class='empty-part'>Empty " . $cell = $additional_number + 1 . " cell.</span></div>" . PHP_EOL;
                        }
                    }
                    echo "<span class='start-finish-section'>[BADGE COLLECTOR]</span></div></div>" . PHP_EOL;
                } else {
                    $type_of_information = array('badgeid');
                    $badge_collector = ResetCustomization($customizations, 5, 6, $type_of_information);
                    echo "<div class='container'><span class='error'>No badges were found.</span></div>" . PHP_EOL;
                }
                return $badge_collector;
            }

            function Completionist($path_to_data, $customizations)
            {
                $file = json_decode(file_get_contents($path_to_data . '\completionist.json'), TRUE);
                if (isset($file['completionist'])) {
                    echo "<div class='container'><div class='output-items'><span class='start-finish-section'>[COMPLETIONIST]</span>" . PHP_EOL;
                    $numbers = array();
                    $count = count($file['completionist']);
                    if ($count >= 2) {
                        do {
                            $possible = rand(0, $count - 1);
                            if (!isset($numbers[$possible])) {
                                $numbers[$possible] = true;
                            }
                        } while (count($numbers) < 2);
                        $numberss = array_keys($numbers);
                        for ($number = 0; $number < 2; $number++) {
                            $completionist[$number]['appid'] = $file['completionist'][$numberss[$number]]['appid'];
                            echo "<div class='container'><span class='output-info-name'>Perfect game: </span><span class='item-output' id='completionist'><a href=https://store.steampowered.com/app/{$file['completionist'][$numberss[$number]]['appid']} target='_blank'>{$file['completionist'][$numberss[$number]]['name']}</a> was established in " . $cell = $number + 1 . " cell.</span></div>" . PHP_EOL;
                        }
                    }
                    if ($count < 2) {
                        echo "<div class='notice-count'>The total number of perfect games must be more than 2. Get more perfect games to make the showcase look complete!</div>" . PHP_EOL;
                        do {
                            $possible = rand(0, $count - 1);
                            if (!isset($numbers[$possible])) {
                                $numbers[$possible] = true;
                            }
                        } while (count($numbers) < $count);
                        $numberss = array_keys($numbers);
                        for ($number = 0; $number < $count; $number++) {
                            $completionist[$number]['appid'] = $file['completionist'][$numberss[$number]]['appid'];
                            echo "<div class='container'><span class='output-info-name'>Perfect game: </span><span class='item-output' id='completionist'><a href=https://store.steampowered.com/app/{$file['completionist'][$numberss[$number]]['appid']} target='_blank'>{$file['completionist'][$numberss[$number]]['name']}</a> was established in " . $cell = $number + 1 . " cell.</span></div>" . PHP_EOL;
                            $additional_number = $number + 1;
                        }
                        for ($additional_number; $additional_number < 2; $additional_number++) {
                            $completionist[$number]['appid'] = 0;
                            echo "<div class='container'><span class='output-info-name'>Perfect game: </span><span class='empty-part'>Empty " . $cell = $additional_number + 1 . " cell.</span></div>" . PHP_EOL;
                        }
                    }
                    echo "<span class='start-finish-section'>[COMPLETIONIST]</span></div></div>" . PHP_EOL;
                } else {
                    $type_of_information = array('appid');
                    $completionist = ResetCustomization($customizations, 23, 2, $type_of_information);
                    echo "<div class='container'><span class='error'>No perfect games were found.</span></div>" . PHP_EOL;
                }
                return $completionist;
            }

            function CustomInfoBox($customizations)
            {
                echo "<div class='container'><div class='output-items'><span class='start-finish-section'>[CUSTOM INFO BOX]</span>" . PHP_EOL;
                //title
                date_default_timezone_set('UTC');
                $custom_info_box[0]['title'] = date('l jS \of F Y h:i:s A');
                echo "<div class='container'><span class='output-info-name'>Title: </span><span class='item-output' id='custom-info-box'>{$custom_info_box[0]['title']}</span></div>" . PHP_EOL;
                //notes
                if (file_exists(__DIR__ . '\data\unchangeable\not_change_data.json')) {
                    $file = json_decode(file_get_contents(__DIR__ . '\data\unchangeable\not_change_data.json'), TRUE);
                    shuffle($file['links']);
                    $link = implode(' ', $file['links']);
                    $custom_info_box[0]['notes'] = "          " . $link;
                } else {
                    $type_of_information = array('notes', 'title');
                    $custom_info_box = ResetCustomization($customizations, 8, 1, $type_of_information);
                }
                echo "<div class='container'><span class='output-info-name'>Notes: </span><span class='item-output' id='custom-info-box'>{$custom_info_box[0]['notes']}</span></div>" . PHP_EOL;
                echo "<span class='start-finish-section'>[CUSTOM INFO BOX]</span></div></div>" . PHP_EOL;
                return $custom_info_box;
            }

            function FavoriteGroup($path_to_data, $customizations)
            {
                $file = json_decode(file_get_contents($path_to_data . '\groups.json'), TRUE);
                if (isset($file['groups'])) {
                    echo "<div class='container'><div class='output-items'><span class='start-finish-section'>[FAVORITE GROUP]</span>" . PHP_EOL;
                    $count = count($file['groups']);
                    $possible = rand(0, $count - 1);
                    $favorite_group[0]['accountid'] = $file['groups'][$possible]['accountid'];
                    echo "<div class='container'><span class='output-info-name'>Group: </span><span class='item-output' id='favorite-group'>{$favorite_group[0]['accountid']}</span></div>" . PHP_EOL;
                    echo "<span class='start-finish-section'>[FAVORITE GROUP]</span></div></div>" . PHP_EOL;
                } else {
                    $type_of_information = array('accountid');
                    $favorite_group = ResetCustomization($customizations, 9, 1, $type_of_information);
                    echo "<div class='container'><span class='error'>No groups were found.</span></div>" . PHP_EOL;
                }
                return $favorite_group;
            }

            function GameCollector($path_to_data, $customizations)
            {
                $file = json_decode(file_get_contents($path_to_data . '\games.json'), TRUE);
                if (isset($file['games'])) {
                    echo "<div class='container'><div class='output-items'><span class='start-finish-section'>[GAME COLLECTOR]</span>" . PHP_EOL;
                    $additional_number = 1;
                    $numbers = array();
                    $count = count($file['games']);
                    if ($count >= 4) {
                        do {
                            $possible = rand(0, $count - 1);
                            if (!isset($numbers[$possible])) {
                                $numbers[$possible] = true;
                            }
                        } while (count($numbers) < 5);
                        $numberss = array_keys($numbers);
                        for ($number = 0; $number < 4; $number++) {
                            $game_collector[$number]['appid'] = $file['games'][$numberss[$number]]['appid'];
                            echo "<div class='container'><span class='output-info-name'>Game: </span><span class='item-output' id='game-collector'><a href=https://store.steampowered.com/app/{$file['games'][$numberss[$number]]['appid']} target='_blank'>{$file['games'][$numberss[$number]]['name']}</a> was established in " . $cell = $number + 1 . " cell.</span></div>" . PHP_EOL;
                        }
                    }
                    if ($count < 4) {
                        echo "<div class='notice-count'>The total number of badges must be more than 4. Get more badges to make the showcase look complete!</div>" . PHP_EOL;
                        do {
                            $possible = rand(0, $count - 1);
                            if (!isset($numbers[$possible])) {
                                $numbers[$possible] = true;
                            }
                        } while (count($numbers) < $count);
                        $numberss = array_keys($numbers);
                        for ($number = 0; $number < $count; $number++) {
                            $game_collector[$number]['appid'] = $file['games'][$numberss[$number]]['appid'];
                            echo "<div class='container'><span class='output-info-name'>Game: </span><span class='item-output' id='game-collector'><a href=https://store.steampowered.com/app/{$file['games'][$numberss[$number]]['appid']} target='_blank'>{$file['games'][$numberss[$number]]['name']}</a> was established in " . $cell = $number + 1 . " cell.</span></div>" . PHP_EOL;
                            $additional_number = $number + 1;
                        }
                        for ($additional_number; $additional_number < 4; $additional_number++) {
                            $game_collector[$additional_number]['appid'] = 0;
                            echo "<div class='container'><span class='output-info-name'>Game: </span><span class='empty-part'>Empty " . $cell = $additional_number + 1 . " cell.</span></div>" . PHP_EOL;
                        }
                    }
                    echo "<span class='start-finish-section'>[GAME COLLECTOR]</span></div></div>" . PHP_EOL;
                } else {
                    $type_of_information = array('appid');
                    $game_collector = ResetCustomization($customizations, 2, 4, $type_of_information);
                    echo "<div class='container'><span class='error'>No games were found.</span></div>" . PHP_EOL;
                }
                return $game_collector;
            }

            function GameFavorite($path_to_data, $customizations, $game_collector)
            {
                $file = json_decode(file_get_contents($path_to_data . '\games.json'), TRUE);
                if (isset($file['games'])) {
                    echo "<div class='container'><div class='output-items'><span class='start-finish-section'>[FAVORITE GAME]</span>" . PHP_EOL;
                    $count = count($file['games']);
                    $game_favorite = array();
                    $game_collector_ids = array();
                    if (count($file['games']) >= 5) {
                        foreach ($game_collector as $game) {
                            foreach ($game as $item) {
                                array_push($game_collector_ids, $item);
                            }
                        }
                        do {
                            $possible = rand(0, $count - 1);
                            $backup = $file['games'][$possible]['appid'];
                            if (!in_array($backup, $game_collector_ids)) {
                                $game_favorite[0]['appid'] = $file['games'][$possible]['appid'];
                            }
                        } while (empty($game_favorite));
                    }
                    if (count($file['games']) < 5) {
                        $possible = rand(0, $count - 1);
                        $game_favorite[0]['appid'] = $file['games'][$possible]['appid'];
                    }
                    echo "<div class='container'><span class='output-info-name'>Favorite game: </span><span class='item-output' id='favorite-game'><a href=https://store.steampowered.com/app/{$file['games'][$possible]['appid']} target='_blank'>{$file['games'][$possible]['name']}</a></span></div>" . PHP_EOL;
                    echo "<span class='start-finish-section'>[FAVORITE GAME]</span></div></div>" . PHP_EOL;
                } else {
                    $type_of_information = array('appid');
                    $game_favorite = ResetCustomization($customizations, 6, 1, $type_of_information);
                    echo "<div class='container'><span class='error'>No games were found.</span></div>" . PHP_EOL;
                }
                return $game_favorite;
            }

            function GuidesCreated($path_to_data, $customizations)
            {
                $file = json_decode(file_get_contents($path_to_data . '\guides.json'), TRUE);
                if (isset($file['guides']['created'])) {
                    echo "<div class='container'><div class='output-items'><span class='start-finish-section'>[CREATED GUIDES]</span>" . PHP_EOL;
                    $numbers = array();
                    $count = count($file['guides']['created']);
                    if ($count >= 4) {
                        do {
                            $possible = rand(0, $count - 1);
                            if (!isset($numbers[$possible])) {
                                $numbers[$possible] = true;
                            }
                        } while (count($numbers) < 4);
                        $numberss = array_keys($numbers);
                        for ($number = 0; $number < 4; $number++) {
                            $guides_created[$number]['appid'] = $file['guides']['created'][$numberss[$number]]['appid'];
                            $guides_created[$number]['publishedfileid'] = $file['guides']['created'][$numberss[$number]]['publishedfileid'];
                            echo "<div class='container'><span class='output-info-name'>Created guide: </span><span class='item-output' id='created-guides'><a href=https://steamcommunity.com/sharedfiles/filedetails/?id={$file['guides']['created'][$numberss[$number]]['publishedfileid']} target='_blank'>{$file['guides']['created'][$numberss[$number]]['title']}</a> from <a href=https://store.steampowered.com/app/{$guides_created[$number]['appid']} target='_blank'>{$guides_created[$number]['appid']}</a></span></div>" . PHP_EOL;
                        }
                    }
                    if ($count < 4) {
                        echo "<div class='notice-count'>The total number of created guides must be more than 4. Create more guides to make the showcase look complete!</div>" . PHP_EOL;
                        do {
                            $possible = rand(0, $count - 1);
                            if (!isset($numbers[$possible])) {
                                $numbers[$possible] = true;
                            }
                        } while (count($numbers) < $count);
                        $numberss = array_keys($numbers);
                        for ($number = 0; $number < $count; $number++) {
                            $guides_created[$number]['appid'] = $file['guides']['created'][$numberss[$number]]['appid'];
                            $guides_created[$number]['publishedfileid'] = $file['guides']['created'][$numberss[$number]]['publishedfileid'];
                            echo "<div class='container'><span class='output-info-name'>Created guide: </span><span class='item-output' id='created-guides'><a href=https://steamcommunity.com/sharedfiles/filedetails/?id={$file['guides']['created'][$numberss[$number]]['publishedfileid']} target='_blank'>{$file['guides']['created'][$numberss[$number]]['title']}</a> from <a href=https://store.steampowered.com/app/{$guides_created[$number]['appid']} target='_blank'>{$guides_created[$number]['appid']}</a></span></div>" . PHP_EOL;
                            $additional_number = $number + 1;
                        }
                        for ($additional_number; $additional_number < 4; $additional_number++) {
                            $guides_created[$additional_number]['appid'] = 0;
                            $guides_created[$additional_number]['publishedfileid'] = 0;
                            echo "<div class='container'><span class='output-info-name'>Created guide: </span><span class='empty-part'>Empty " . $cell = $additional_number + 1 . " cell.</span></div>" . PHP_EOL;
                        }
                    }
                    echo "<span class='start-finish-section'>[CREATED GUIDES]</span></div></div>" . PHP_EOL;
                } else {
                    $type_of_information = array('appid', 'publishedfileid');
                    $guides_created = ResetCustomization($customizations, 16, 4, $type_of_information);
                    echo "<div class='container'><span class='error'>No created guides were found.</span></div>" . PHP_EOL;
                }

                return $guides_created;
            }

            function GuideFavorite($path_to_data, $customizations, $guides_created)
            {
                $file = json_decode(file_get_contents($path_to_data . '\guides.json'), TRUE);
                if (isset($file['guides']['favorite'])) {
                    echo "<div class='container'><div class='output-items'><span class='start-finish-section'>[FAVORITE GUIDE]</span>" . PHP_EOL;
                    $count = count($file['guides']['favorite']);
                    $guide_favorite = array();
                    $guides_collector_ids = array();
                    if (isset($file['guides']['created'])) {
                        foreach ($guides_created as $guide) {
                            array_push($guides_collector_ids, $guide['publishedfileid']);
                        }
                        do {
                            $category = array_rand($file['guides'], 1);
                            $count = count($file['guides'][$category]);
                            $possible = rand(0, $count - 1);
                            $backup = $file['guides'][$category][$possible]['publishedfileid'];
                            if (!in_array($backup, $guides_collector_ids)) {
                                $guide_favorite[0]['appid'] = $file['guides'][$category][$possible]['appid'];
                                $guide_favorite[0]['publishedfileid'] = $file['guides'][$category][$possible]['publishedfileid'];
                                $guide_favorite[0]['title'] = $file['guides'][$category][$possible]['title'];
                            }
                        } while (empty($guide_favorite));
                    }
                    if (!isset($file['guides']['created'])) {
                        $possible = rand(0, $count - 1);
                        $guide_favorite[0]['appid'] = $file['guides']['favorite'][$possible]['appid'];
                        $guide_favorite[0]['publishedfileid'] = $file['guides']['favorite'][$possible]['publishedfileid'];
                        $guide_favorite[0]['title'] = $file['guides']['favorite'][$possible]['title'];
                    }
                    echo "<div class='container'><span class='output-info-name'>Favorite guide: </span><span class='item-output' id='favorite-guide'><a href=https://steamcommunity.com/sharedfiles/filedetails/?id={$guide_favorite[0]['publishedfileid']} target='_blank'>{$guide_favorite[0]['title']}</a> from <a href=https://store.steampowered.com/app/{$guide_favorite[0]['appid']} target='_blank'>{$guide_favorite[0]['appid']}</a></span></div>" . PHP_EOL;
                    echo "<span class='start-finish-section'>[FAVORITE GUIDE]</span></div></div>" . PHP_EOL;
                } else {
                    $type_of_information = array('appid', 'publishedfileid');
                    $guide_favorite = ResetCustomization($customizations, 15, 1, $type_of_information);
                    echo "<div class='container'><span class='error'>No favorite guides were found.</span></div>" . PHP_EOL;
                }
                return $guide_favorite;
            }

            function Items($path_to_data, $customizations)
            {
                $titles = array('appid', 'item_contextid', 'item_assetid', 'name');
                $file = json_decode(file_get_contents($path_to_data . '\inventory.json'), TRUE);
                if (isset($file['inventory'])) {
                    echo "<div class='container'><div class='output-items'><span class='start-finish-section'>[ITEMS]</span>" . PHP_EOL;
                    $numbers = array();
                    $count = count($file['inventory']);
                    if ($count >= 10) {
                        do {
                            $possible = rand(0, $count - 1);
                            if (!isset($numbers[$possible])) {
                                $numbers[$possible] = true;
                            }
                        } while (count($numbers) < 10);
                        $numberss = array_keys($numbers);
                        for ($number = 0; $number < 10; $number++) {
                            foreach ($titles as $title) {
                                $items[$number][$title] = $file['inventory'][$numberss[$number]][$title];
                            }
                            echo "<div class='container'><span class='output-info-name'>Item: </span><span class='item-output' id='items'><a href=https://steamcommunity.com/profiles/{$_SESSION['steam_steamid']}/inventory/#{$items[$number]['appid']}_{$items[$number]['item_contextid']}_{$items[$number]['item_assetid']} target='_blank'>{$items[$number]['name']}</a> from <a href=https://store.steampowered.com/app/{$items[$number]['appid']} target='_blank'>{$items[$number]['appid']}</a> was established in " . $cell = $number + 1 . " cell</span></div>" . PHP_EOL;
                        }
                    }
                    if ($count < 10) {
                        echo "<div class='notice-count'>The total number of items in inventory must be more than 10. Get more items to make the showcase look complete!</div>" . PHP_EOL;
                        do {
                            $possible = rand(0, $count - 1);
                            if (!isset($numbers[$possible])) {
                                $numbers[$possible] = true;
                            }
                        } while (count($numbers) < $count);
                        $numberss = array_keys($numbers);
                        for ($number = 0; $number < $count; $number++) {
                            foreach ($titles as $title) {
                                $items[$number][$title] = $file['inventory'][$numberss[$number]][$title];
                            }
                            echo "<div class='container'><span class='output-info-name'>Item: </span><span class='item-output' id='items'><a href=https://steamcommunity.com/profiles/{$_SESSION['steam_steamid']}/inventory/#{$items[$number]['appid']}_{$items[$number]['item_contextid']}_{$items[$number]['item_assetid']} target='_blank'>{$items[$number]['name']}</a> from <a href=https://store.steampowered.com/app/{$items[$number]['appid']} target='_blank'>{$items[$number]['appid']}</a> was established in " . $cell = $number + 1 . " cell</span></div>" . PHP_EOL;
                            $additional_number = $number + 1;
                        }
                        for ($additional_number; $additional_number < 10; $additional_number++) {
                            foreach ($titles as $title) {
                                $items[$additional_number][$title] = 0;
                            }
                            echo "<div class='container'><span class='output-info-name'>Item: </span><span class='empty-part'>Empty " . $cell = $additional_number + 1 . " cell.</span></div>" . PHP_EOL;
                        }
                    }
                    echo "<span class='start-finish-section'>[ITEMS]</span></div></div>" . PHP_EOL;
                } else {
                    $type_of_information = array('appid', 'item_contextid', 'item_assetid');
                    $items = ResetCustomization($customizations, 3, 10, $type_of_information);
                    echo "<div class='container'><span class='error'>No items in inventory were found.</span></div>" . PHP_EOL;
                }
                return $items;
            }

            function ItemsForTrade($path_to_data, $customizations)
            {
                $titles = array('appid', 'item_contextid', 'item_assetid', 'name');
                $file = json_decode(file_get_contents($path_to_data . '\inventory.json'), TRUE);
                if (isset($file['inventory'])) {
                    $n = 0;
                    do {
                        foreach ($file['inventory'] as $item) {
                            if ($n === count($file['inventory']) - 1) {
                                $answer = 'no';
                            }
                            if ($item['tradable'] === 1) {
                                $answer = 'yes';
                                break;
                            }
                            $n++;
                        }
                    } while (empty($answer));
                    if ($answer === 'yes') {
                        echo "<div class='container'><div class='output-items'><span class='start-finish-section'>[ITEMS FOR TRADE]</span>" . PHP_EOL;
                        $numbers = array();
                        $count = count($file['inventory']);
                        if ($count >= 6) {
                            do {
                                $possible = rand(0, $count - 1);
                                if (!isset($numbers[$possible]) and $file['inventory'][$possible]['tradable'] === 1) {
                                    $numbers[$possible] = true;
                                }
                            } while (count($numbers) < 6);
                            $numberss = array_keys($numbers);
                            for ($number = 0; $number < 6; $number++) {
                                foreach ($titles as $title) {
                                    $items_for_trade[$number][$title] = $file['inventory'][$numberss[$number]][$title];
                                }
                                echo "<div class='container'><span class='output-info-name'>Item for trade: </span><span class='item-output' id='items-for-trade'><a href=https://steamcommunity.com/profiles/{$_SESSION['steam_steamid']}/inventory/#{$items_for_trade[$number]['appid']}_{$items_for_trade[$number]['item_contextid']}_{$items_for_trade[$number]['item_assetid']} target='_blank'>{$items_for_trade[$number]['name']}</a> from <a href=https://store.steampowered.com/app/{$items_for_trade[$number]['appid']} target='_blank'>{$items_for_trade[$number]['appid']}</a> was established in " . $cell = $number + 1 . " cell</span></div>" . PHP_EOL;
                            }
                        }
                        if ($count < 6) {
                            echo "<div class='notice-count'>The total number of items up for trade in inventory must be more than 10. Get more items to make the showcase look complete!</div>" . PHP_EOL;
                            do {
                                $possible = rand(0, $count - 1);
                                if (!isset($numbers[$possible]) and $file['inventory'][$possible]['tradable'] === 1) {
                                    $numbers[$possible] = true;
                                }
                            } while (count($numbers) < $count);
                            $numberss = array_keys($numbers);
                            for ($number = 0; $number < $count; $number++) {
                                foreach ($titles as $title) {
                                    $items_for_trade[$number][$title] = $file['inventory'][$numberss[$number]][$title];
                                }
                                echo "<div class='container'><span class='output-info-name'>Item for trade: </span><span class='item-output' id='items-for-trade'><a href=https://steamcommunity.com/profiles/{$_SESSION['steam_steamid']}/inventory/#{$items_for_trade[$number]['appid']}_{$items_for_trade[$number]['item_contextid']}_{$items_for_trade[$number]['item_assetid']} target='_blank'>{$items_for_trade[$number]['name']}</a> from <a href=https://store.steampowered.com/app/{$items_for_trade[$number]['appid']} target='_blank'>{$items_for_trade[$number]['appid']}</a> was established in " . $cell = $number + 1 . " cell</span></div>" . PHP_EOL;
                                $additional_number = $number + 1;
                            }
                            for ($additional_number; $additional_number < 6; $additional_number++) {
                                foreach ($titles as $title) {
                                    $items_for_trade[$additional_number][$title] = 0;
                                }
                                echo "<div class='container'><span class='output-info-name'>Item for trade: </span><span class='empty-part'>Empty " . $cell = $additional_number + 1 . " cell.</span></div>" . PHP_EOL;
                            }
                        }
                        if (file_exists(__DIR__ . '\data\unchangeable\not_change_data.json')) {
                            $file = json_decode(file_get_contents(__DIR__ . '\data\unchangeable\not_change_data.json'), TRUE);
                            shuffle($file['greetings']);
                            $greeting = implode(' ', $file['greetings']);
                            $items_for_trade[6]['notes'] = "      " . $greeting;
                            echo "<div class='container'><span class='output-info-name'>Notes: </span><span class='item-output' id='items-for-trade'>" . $items_for_trade[6]['notes'] . "</span></div>" . PHP_EOL;
                        } else {
                            $items_for_trade[6]['notes'] = '';
                            echo "<div class='container'><span class='output-info-name'>Notes: </span><span class='empty-part'>Empty</span></div>" . PHP_EOL;
                        }
                        echo "<span class='start-finish-section'>[ITEMS FOR TRADE]</span></div></div>" . PHP_EOL;
                    }
                    if ($answer === 'no') {
                        $type_of_information = array('appid', 'item_contextid', 'item_assetid');
                        $items_for_trade = ResetCustomization($customizations, 4, 6, $type_of_information);
                        echo "<div class='container'><span class='error'>No tradable items in inventory were found.</span></div>" . PHP_EOL;
                    }
                } else {
                    $type_of_information = array('appid', 'item_contextid', 'item_assetid');
                    $items_for_trade = ResetCustomization($customizations, 4, 6, $type_of_information);
                    echo "<div class='container'><span class='error'>No items in inventory were found.</span></div>" . PHP_EOL;
                }
                return $items_for_trade;
            }

            function Screenshots($path_to_data, $customizations)
            {
                $file = json_decode(file_get_contents($path_to_data . '\screenshots.json'), TRUE);
                if (isset($file['screenshots'])) {
                    echo "<div class='container'><div class='output-items'><span class='start-finish-section'>[SCREENSHOTS]</span>" . PHP_EOL;
                    $numbers = array();
                    $count = count($file['screenshots']);
                    if ($count >= 4) {
                        do {
                            $possible = rand(0, $count - 1);
                            if (!isset($numbers[$possible])) {
                                $numbers[$possible] = true;
                            }
                        } while (count($numbers) < 4);
                        $numberss = array_keys($numbers);
                        for ($number = 0; $number < 4; $number++) {
                            $screenshots[$number]['publishedfileid'] = $file['screenshots'][$numberss[$number]]['publishedfileid'];
                            echo "<div class='container'><span class='output-info-name'>Created screenshot: </span><span class='item-output' id='created-screenshots'><a href=https://steamcommunity.com/sharedfiles/filedetails/?id={$screenshots[$number]['publishedfileid']} target='_blank'>{$file['screenshots'][$numberss[$number]]['publishedfileid']}</a> was established in " . $cell = $number + 1 . " cell</span></div>" . PHP_EOL;
                        }
                    }
                    if ($count < 4) {
                        echo "<div class='notice-count'>The total number of created screenshots must be more than 4. Capture more game moments to make your showcase look complete!</div>" . PHP_EOL;
                        do {
                            $possible = rand(0, $count - 1);
                            if (!isset($numbers[$possible])) {
                                $numbers[$possible] = true;
                            }
                        } while (count($numbers) < $count);
                        $numberss = array_keys($numbers);
                        for ($number = 0; $number < $count; $number++) {
                            $screenshots[$number]['publishedfileid'] = $file['screenshots'][$numberss[$number]]['publishedfileid'];
                            echo "<div class='container'><span class='output-info-name'>Created screenshot: </span><span class='item-output' id='created-screenshots'><a href=https://steamcommunity.com/sharedfiles/filedetails/?id={$screenshots[$number]['publishedfileid']} target='_blank'>{$file['screenshots'][$numberss[$number]]['publishedfileid']}</a> was established in " . $cell = $number + 1 . " cell</span></div>" . PHP_EOL;
                            $additional_number = $number + 1;
                        }
                        for ($additional_number; $additional_number < 4; $additional_number++) {
                            $screenshots[$additional_number]['publishedfileid'] = 0;
                            echo "<div class='container'><span class='output-info-name'>Created screenshot: </span><span class='empty-part'>Empty " . $cell = $additional_number + 1 . " cell.</span></div>" . PHP_EOL;
                        }
                    }
                    echo "<span class='start-finish-section'>[SCREENSHOTS]</span></div></div>" . PHP_EOL;
                } else {
                    $type_of_information = array('publishedfileid');
                    $screenshots = ResetCustomization($customizations, 7, 4, $type_of_information);
                    echo "<div class='container'><span class='error'>No created screenshots were found.</span></div>" . PHP_EOL;
                }
                return $screenshots;
            }

            function Videos($path_to_data, $customizations)
            {
                $file = json_decode(file_get_contents($path_to_data . '\videos.json'), TRUE);
                if (isset($file['videos'])) {
                    echo "<div class='container'><div class='output-items'><span class='start-finish-section'>[VIDEOS]</span>" . PHP_EOL;
                    $numbers = array();
                    $count = count($file['videos']);
                    if ($count >= 4) {
                        do {
                            $possible = rand(0, $count - 1);
                            if (!isset($numbers[$possible])) {
                                $numbers[$possible] = true;
                            }
                        } while (count($numbers) < 4);
                        $numberss = array_keys($numbers);
                        for ($number = 0; $number < 4; $number++) {
                            $videos[$number]['publishedfileid'] = $file['videos'][$numberss[$number]]['publishedfileid'];
                            echo "<div class='container'><span class='output-info-name'>Video: </span><span class='item-output' id='videos'><a href=https://steamcommunity.com/sharedfiles/filedetails/?id={$videos[$number]['publishedfileid']} target='_blank'>{$file['videos'][$numberss[$number]]['title']}</a> was established in " . $cell = $number + 1 . " cell</span></div>" . PHP_EOL;
                        }
                    }
                    if ($count < 4) {
                        echo "<div class='notice-count'>The total number of videos must be more than 4. Add more videos to make the showcase look complete!</div>" . PHP_EOL;
                        do {
                            $possible = rand(0, $count - 1);
                            if (!isset($numbers[$possible])) {
                                $numbers[$possible] = true;
                            }
                        } while (count($numbers) < $count);
                        $numberss = array_keys($numbers);
                        for ($number = 0; $number < $count; $number++) {
                            $videos[$number]['publishedfileid'] = $file['videos'][$numberss[$number]]['publishedfileid'];
                            echo "<div class='container'><span class='output-info-name'>Video: </span><span class='item-output' id='videos'><a href=https://steamcommunity.com/sharedfiles/filedetails/?id={$videos[$number]['publishedfileid']} target='_blank'>{$file['videos'][$numberss[$number]]['title']}</a> was established in " . $cell = $number + 1 . " cell</span></div>" . PHP_EOL;
                            $additional_number = $number + 1;
                        }
                        for ($additional_number; $additional_number < 4; $additional_number++) {
                            $videos[$additional_number]['publishedfileid'] = 0;
                            echo "<div class='container'><span class='output-info-name'>Video: </span><span class='empty-part'>Empty " . $cell = $additional_number + 1 . " cell.</span></div>" . PHP_EOL;
                        }
                    }
                    echo "<span class='start-finish-section'>[VIDEOS]</span></div></div>" . PHP_EOL;
                } else {
                    $type_of_information = array('publishedfileid');
                    $videos = ResetCustomization($customizations, 14, 4, $type_of_information);
                    echo "<div class='container'><span class='error'>No videos were found.</span></div>" . PHP_EOL;
                }
                return $videos;
            }

            /*function Workshop() {

            }*/

            $profile_showcase = EnableShowcases($send_all_showcases, $customizations, $count_showcases, $shuffle_showcases);

            echo "<div class='container'><div class='output-items'><span class='output-info-name'>Number of showcases available: </span><span class='notice-count'>{$count_showcases}</span></div></div>";
            if ($send_achievements and in_array(17, $profile_showcase)) {
                if (file_exists($path_to_data . '\achievements.json')) {
                    $achievements = Achievements($path_to_data, $customizations);
                } else {
                    $type_of_information = array('appid', 'title');
                    $achievements = ResetCustomization($customizations, 17, 9, $type_of_information);
                    echo "<div class='container'><div class='output-items'><span class='error'>No achievements were found.</span></div></div>" . PHP_EOL;
                }
            } else {
                $type_of_information = array('appid', 'title');
                $achievements = ResetCustomization($customizations, 17, 9, $type_of_information);
                //echo "<div class='container'><div class='output-items'><span class='disabled'>Sending achievements is disabled or was not included in the selection.</span></div></div>" . PHP_EOL;
            }
            if ($send_artworks_created and in_array(13, $profile_showcase)) {
                if (file_exists($path_to_data . '\artworks.json')) {
                    $artworks_created = ArtworksCreated($path_to_data, $customizations);
                } else {
                    $type_of_information = array('publishedfileid');
                    $artworks_created = ResetCustomization($customizations, 13, 4, $type_of_information);
                    echo "<div class='container'><div class='output-items'><span class='error'>No artworks were found.</span></div></div>" . PHP_EOL;
                }
            } else {
                $type_of_information = array('publishedfileid');
                $artworks_created = ResetCustomization($customizations, 13, 4, $type_of_information);
                //echo "<div class='container'><div class='output-items'><span class='disabled'>Sending artworks is disabled or was not included in the selection.</span></div></div>" . PHP_EOL;
            }
            if ($send_artwork_featured and in_array(22, $profile_showcase)) {
                if (file_exists($path_to_data . '\artworks.json')) {
                    $artwork_featured = ArtworkFeatured($path_to_data, $customizations, $artworks_created);
                } else {
                    $type_of_information = array('publishedfileid');
                    $artwork_featured = ResetCustomization($customizations, 22, 1, $type_of_information);
                    echo "<div class='container'><div class='output-items'><span class='error'>No artworks were found.</span></div></div>" . PHP_EOL;
                }
            } else {
                $type_of_information = array('publishedfileid');
                $artwork_featured = ResetCustomization($customizations, 22, 1, $type_of_information);
                //echo "<div class='container'><div class='output-items'><span class='disabled'>Sending featured artworks is disabled or was not included in the selection.</span></div></div>" . PHP_EOL;
            }
            if ($send_badge_collector and in_array(5, $profile_showcase)) {
                if (file_exists($path_to_data . '\badges.json')) {
                    $badge_collector = BadgeCollector($path_to_data, $customizations);
                } else {
                    $type_of_information = array('badgeid');
                    $badge_collector = ResetCustomization($customizations, 5, 6, $type_of_information);
                    echo "<div class='container'><div class='output-items'><span class='error'>No badges were found.</span></div></div>" . PHP_EOL;
                }
            } else {
                $type_of_information = array('badgeid');
                $badge_collector = ResetCustomization($customizations, 5, 6, $type_of_information);
                //echo "<div class='container'><div class='output-items'><span class='disabled'>Sending badges is disabled or was not included in the selection.</span></div></div>" . PHP_EOL;
            }
            if ($send_completionist and in_array(23, $profile_showcase)) {
                if (file_exists($path_to_data . '\completionist.json')) {
                    $completionist = Completionist($path_to_data, $customizations);
                } else {
                    $type_of_information = array('appid');
                    $completionist = ResetCustomization($customizations, 23, 2, $type_of_information);
                    echo "<div class='container'><div class='output-items'><span class='error'>No perfect games were found.</span></div></div>" . PHP_EOL;
                }
            } else {
                $type_of_information = array('appid');
                $completionist = ResetCustomization($customizations, 23, 2, $type_of_information);
                //echo "<div class='container'><div class='output-items'><span class='disabled'>Sending perfect games is disabled or was not included in the selection.</span></div></div>" . PHP_EOL;
            }
            if ($send_custom_info_box and in_array(8, $profile_showcase)) {
                if (file_exists(__DIR__ . '\data\unchangeable\not_change_data.json')) {
                    $custom_info_box = CustomInfoBox($customizations);
                } else {
                    $type_of_information = array('title', 'notes');
                    $custom_info_box = ResetCustomization($customizations, 8, 1, $type_of_information);
                    echo "<div class='container'><div class='output-items'><span class='error'>No file with immutable data.</span></div></div>" . PHP_EOL;
                }
            } else {
                $type_of_information = array('title', 'notes');
                $custom_info_box = ResetCustomization($customizations, 8, 1, $type_of_information);
                //echo "<div class='container'><div class='output-items'><span class='disabled'>Sending custom info box is disabled or was not included in the selection.</span></div></div>" . PHP_EOL;
            }
            if ($send_favorite_group and in_array(9, $profile_showcase)) {
                if (file_exists($path_to_data . '\groups.json')) {
                    $favorite_group = FavoriteGroup($path_to_data, $customizations);
                } else {
                    $type_of_information = array('accountid');
                    $favorite_group = ResetCustomization($customizations, 9, 1, $type_of_information);
                    echo "<div class='container'><div class='output-items'><span class='error'>No groups were found.</span></div></div>" . PHP_EOL;
                }
            } else {
                $type_of_information = array('accountid');
                $favorite_group = ResetCustomization($customizations, 9, 1, $type_of_information);
                //echo "<div class='container'><div class='output-items'><span class='disabled'>Sending favorite group is disabled or was not included in the selection.</span></div></div>" . PHP_EOL;
            }
            if ($send_game_collector and in_array(2, $profile_showcase)) {
                if (file_exists($path_to_data . '\games.json')) {
                    $game_collector = GameCollector($path_to_data, $customizations);
                } else {
                    $type_of_information = array('appid');
                    $game_collector = ResetCustomization($customizations, 2, 4, $type_of_information);
                    echo "<div class='container'><div class='output-items'><span class='error'>No games were found.</span></div></div>" . PHP_EOL;
                }
            } else {
                $type_of_information = array('appid');
                $game_collector = ResetCustomization($customizations, 2, 4, $type_of_information);
                //echo "<div class='container'><div class='output-items'><span class='disabled'>Sending game collector is disabled or was not included in the selection.</span></div></div>" . PHP_EOL;
            }
            if ($send_game_favorite and in_array(6, $profile_showcase)) {
                if (file_exists($path_to_data . '\games.json')) {
                    $game_favorite = GameFavorite($path_to_data, $customizations, $game_collector);
                } else {
                    $type_of_information = array('appid');
                    $game_favorite = ResetCustomization($customizations, 6, 1, $type_of_information);
                    echo "<div class='container'><div class='output-items'><span class='error'>No games were found.</span></div></div>" . PHP_EOL;
                }
            } else {
                $type_of_information = array('appid');
                $game_favorite = ResetCustomization($customizations, 6, 1, $type_of_information);
                //echo "<div class='container'><div class='output-items'><span class='disabled'>Sending favoritegame is disabled or was not included in the selection.</span></div></div>" . PHP_EOL;
            }
            if ($send_guides_created and in_array(16, $profile_showcase)) {
                if (file_exists($path_to_data . '\guides.json')) {
                    $guides_created = GuidesCreated($path_to_data, $customizations);
                } else {
                    $type_of_information = array('appid', 'publishedfileid');
                    $guides_created = ResetCustomization($customizations, 16, 4, $type_of_information);
                    echo "<div class='container'><div class='output-items'><span class='error'>No guides were found.</span></div></div>" . PHP_EOL;
                }
            } else {
                $type_of_information = array('appid', 'publishedfileid');
                $guides_created = ResetCustomization($customizations, 16, 4, $type_of_information);
                //echo "<div class='container'><div class='output-items'><span class='disabled'>Sending created guides is disabled or was not included in the selection.</span></div></div>" . PHP_EOL;
            }
            if ($send_guide_favorite and in_array(15, $profile_showcase)) {
                if (file_exists($path_to_data . '\guides.json')) {
                    $guide_favorite = GuideFavorite($path_to_data, $customizations, $guides_created);
                } else {
                    $type_of_information = array('appid', 'publishedfileid');
                    $guide_favorite = ResetCustomization($customizations, 15, 1, $type_of_information);
                    echo "<div class='container'><div class='output-items'><span class='error'>No favorite guides were found.</span></div></div>" . PHP_EOL;
                }
            } else {
                $type_of_information = array('appid', 'publishedfileid');
                $guide_favorite = ResetCustomization($customizations, 15, 1, $type_of_information);
                //echo "<div class='container'><div class='output-items'><span class='disabled'>Sending favorite guide is disabled or was not included in the selection.</span></div></div>" . PHP_EOL;
            }
            if ($send_items and in_array(3, $profile_showcase)) {
                if (file_exists($path_to_data . '\inventory.json')) {
                    $items = Items($path_to_data, $customizations);
                } else {
                    $type_of_information = array('appid', 'item_contextid', 'item_assetid');
                    $items = ResetCustomization($customizations, 3, 10, $type_of_information);
                    echo "<div class='container'><div class='output-items'><span class='error'>No items in inventory were found.</span></div></div>" . PHP_EOL;
                }
            } else {
                $type_of_information = array('appid', 'item_contextid', 'item_assetid');
                $items = ResetCustomization($customizations, 3, 10, $type_of_information);
                //echo "<div class='container'><div class='output-items'><span class='disabled'>Sending items is disabled or was not included in the selection.</span></div></div>" . PHP_EOL;
            }
            if ($send_items_for_trade and in_array(4, $profile_showcase)) {
                if (file_exists($path_to_data . '\inventory.json')) {
                    $items_for_trade = ItemsForTrade($path_to_data, $customizations);
                } else {
                    $type_of_information = array('appid', 'item_contextid', 'item_assetid');
                    $items_for_trade = ResetCustomization($customizations, 4, 6, $type_of_information);
                    echo "<div class='container'><div class='output-items'><span class='error'>No items in inventory were found.</span></div></div>" . PHP_EOL;
                }
            } else {
                $type_of_information = array('appid', 'item_contextid', 'item_assetid');
                $items_for_trade = ResetCustomization($customizations, 4, 6, $type_of_information);
                //echo "<div class='container'><div class='output-items'><span class='disabled'>Sending items for trade is disabled or was not included in the selection.</span></div></div>" . PHP_EOL;
            }
            if ($send_screenshots and in_array(7, $profile_showcase)) {
                if (file_exists($path_to_data . '\screenshots.json')) {
                    $screenshots = Screenshots($path_to_data, $customizations);
                } else {
                    $type_of_information = array('publishedfileid');
                    $screenshots = ResetCustomization($customizations, 7, 4, $type_of_information);
                    echo "<div class='container'><div class='output-items'><span class='error'>No screenshots were found.</span></div></div>" . PHP_EOL;
                }
            } else {
                $type_of_information = array('publishedfileid');
                $screenshots = ResetCustomization($customizations, 7, 4, $type_of_information);
                //echo "<div class='container'><div class='output-items'><span class='disabled'>Sending screenshots is disabled or was not included in the selection.</span></div></div>" . PHP_EOL;
            }
            if ($send_videos and in_array(14, $profile_showcase)) {
                if (file_exists($path_to_data . '\videos.json')) {
                    $videos = Videos($path_to_data, $customizations);
                } else {
                    $type_of_information = array('publishedfileid');
                    $videos = ResetCustomization($customizations, 14, 4, $type_of_information);
                    echo "<div class='container'><div class='output-items'><span class='error'>No videos were found.</span></div></div>" . PHP_EOL;
                }
            } else {
                $type_of_information = array('publishedfileid');
                $videos = ResetCustomization($customizations, 14, 4, $type_of_information);
                //echo "<div class='container'><div class='output-items'><span class='disabled'>Sending videos is disabled or was not included in the selection.</span></div></div>" . PHP_EOL;
            }
            if ($send_workshop and in_array(11, $profile_showcase)) {
                require_once(__DIR__ . '\showcases\workshop.php');
            } else {
                $workshop['favorited_workshop']['appid'] = 0;
                $workshop['favorited_workshop']['publishedfileid'] = 0;
                for ($number = 0; $number < 5; $number++) {
                    $workshop['created_workshop'][$number]['appid'] = 0;
                    $workshop['created_workshop'][$number]['publishedfileid'] = 0;
                }
                //echo "<div class='container'><div class='output-items'><span class='disabled'>Sending workshop is disabled or was not included in the selection.</span></div></div>" . PHP_EOL;
            }
            echo "</div></div><div class='container'><span class='start-finish-section'>[SHOWCASES]</span></div></div></div>" . PHP_EOL;
        } else {
            echo "<div class='box'><div class='output-items'><span class='error'>There is no way for you to put up display showcases.</span></div></div>";
            $showcases = false;
        }
    } else {
        echo "<div class='box'><div class='output-items'><span class='error'>File with the current account information does not exist.</span></div></div>";
        $showcases = false;
    }
} else {
    echo "<div class='box'><div class='output-items'><span class='error'>[SHOWCASES] – All options is disabled.</span></div></div>";
    $showcases = false;
}