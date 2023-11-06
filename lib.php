<?php

// Every file should have GPL and copyright in the header - we skip it in tutorials but you should not skip it for real.

// This line protects the file from being accessed by a URL directly.                                                               
defined('MOODLE_INTERNAL') || die();

// We will add callbacks here as we add features to our theme.

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