<?php

// Every file should have GPL and copyright in the header - we skip it in tutorials but you should not skip it for real.

// This line protects the file from being accessed by a URL directly.                                                               
defined('MOODLE_INTERNAL') || die();

// We will add callbacks here as we add features to our theme.



function theme_oncapflege_get_pre_scss($theme) {
    $conten = '';

    //$theme = theme_config::load('learnr');
    //return theme_learnr_get_pre_scss($theme);
    return $content;
}


/**
 * Inject additional SCSS.
 *
 * @param theme_config $theme The theme config object.
 * @return string
 */
function theme_oncapflege_get_extra_scss($theme) {
    // // Initialize extra SCSS.
    $content = '';

    $learnr = theme_config::load('learnr');
    $imageurl = $learnr->setting_file_url('backgroundimage', 'backgroundimage');

    // Sets the background image, and its settings.
    if (!empty($imageurl)) {
        $content .= '@media (min-width: 768px) {';
        $content .= 'body { ';
        $content .= "background-image: url('$imageurl'); background-size: cover;";
        $content .= ' } }';
    }


    // $learnr = theme_config::load('learnr');
    // $content = theme_learnr_get_extra_scss($learnr);
   return $content;
}

function theme_oncapflege_get_main_scss_content($theme) {                                                                                
    global $CFG;                                                                                                                    
                                                                                                                                    
    $scss = '';     
    $themeparent = theme_config::load('learnr'); 
    $learnrScss = theme_learnr_get_main_scss_content($themeparent);
    $scss .= $learnrScss;
                
     // Pre CSS - this is loaded AFTER any prescss from the setting but before the main scss.                                        
     $pre = file_get_contents($CFG->dirroot . '/theme/oncapflege/scss/pre.scss');                                                         
     // Post CSS - this is loaded AFTER the main scss but before the extra scss from the setting.                                    
     $post = file_get_contents($CFG->dirroot . '/theme/oncapflege/scss/post.scss');                                                       
     $toggleinstr = file_get_contents($CFG->dirroot . '/theme/oncapflege/scss/toggleinstructions.scss');                                                       
     $togglepwd = file_get_contents($CFG->dirroot . '/theme/oncapflege/scss/togglepassword.scss');                                                       
     $wizard = file_get_contents($CFG->dirroot . '/theme/oncapflege/scss/loginformwizard.scss');                                                       
                                                                                                                                     
     // Combine them together.                                                                                                       
     return $pre . "\n" . $scss . "\n" . $post . "\n" . $toggleinstr . "\n" . $togglepwd . "\n" . $wizard;                                           
}