<?php

// Every file should have GPL and copyright in the header - we skip it in tutorials but you should not skip it for real.

// This line protects the file from being accessed by a URL directly.                                                               
defined('MOODLE_INTERNAL') || die();

// We will add callbacks here as we add features to our theme.

function theme_oncapflege_get_main_scss_content($theme) {                                                                                
    global $CFG;                                                                                                                    
                                                                                                                                    
    $scss = '';     
    $themeparent = theme_config::load('learnr');                                                                                                                
    $filename = !empty($themeparent->settings->preset) ? $themeparent->settings->preset : null;                                                 
    $fs = get_file_storage();                                                                                                       
                                                                                                                                    
    $context = context_system::instance();                                                                                          
    if ($filename == 'default.scss') {                                                                                              
        // We still load the default preset files directly from the boost theme. No sense in duplicating them.                      
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/default.scss');                                        
    } else if ($filename == 'plain.scss') {                                                                                         
        // We still load the default preset files directly from the boost theme. No sense in duplicating them.                      
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/plain.scss');                                          
    // theme_oncapflege is very important here maybe not                                                                                                                                
    } else if ($filename && ($presetfile = $fs->get_file($context->id, 'theme_learnr', 'preset', 0, '/', $filename))) {              
        // This preset file was fetched from the file area for theme_oncapflege and not theme_boost (see the line above).                
        $scss .= $presetfile->get_content();                                                                                        
    } else {                                                                                                                        
        // Safety fallback - maybe new installs etc.                                                                                
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/default.scss');                                        
    }          
    
    $scss .= "\n";
    // LearnR
    $scss .= file_get_contents($CFG->dirroot . '/theme/learnr/scss/learnr/pre.scss');
    if ($filename && ($presetfile = $fs->get_file($context->id, 'theme_learnr', 'preset', 0, '/', $filename))) {
        $scss .= $presetfile->get_content();
    } else {
        // Safety fallback - maybe new installs etc.
        $scss .= file_get_contents($CFG->dirroot . '/theme/learnr/scss/preset/default.scss');
    }

    // Begin DBN Update.
    if ($theme->settings->sectionstyle == 1) {
        $scss .= file_get_contents($CFG->dirroot . '/theme/learnr/scss/sections/sections-learnr.scss');
    }

    if ($theme->settings->sectionstyle == 2) {
        $scss .= file_get_contents($CFG->dirroot . '/theme/learnr/scss/sections/sections-boxed.scss');
    }

    if ($theme->settings->sectionstyle == 3) {
        $scss .= file_get_contents($CFG->dirroot . '/theme/learnr/scss/sections/sections-boost.scss');
    }

    if ($theme->settings->sectionstyle == 4) {
        $scss .= file_get_contents($CFG->dirroot . '/theme/learnr/scss/sections/sections-bars.scss');
    }
    // End DBN Update.

    $scss .= file_get_contents($CFG->dirroot . '/theme/learnr/scss/learnr/post.scss');

                
     // Pre CSS - this is loaded AFTER any prescss from the setting but before the main scss.                                        
     $pre = file_get_contents($CFG->dirroot . '/theme/oncapflege/scss/pre.scss');                                                         
     // Post CSS - this is loaded AFTER the main scss but before the extra scss from the setting.                                    
     $post = file_get_contents($CFG->dirroot . '/theme/oncapflege/scss/post.scss');                                                       
     $wizard = file_get_contents($CFG->dirroot . '/theme/oncapflege/scss/loginformwizard.scss');                                                       
                                                                                                                                     
     // Combine them together.                                                                                                       
     return $pre . "\n" . $scss . "\n" . $post . "\n" . $wizard;                                           
}