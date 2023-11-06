<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Theme LearnR - Settings file
 *
 * @package    theme_learnr
 * @copyright  2022 Alexander Bias, lern.link GmbH <alexander.bias@lernlink.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use \theme_learnr\admin_setting_configdatetime;
use \theme_learnr\admin_setting_configstoredfilealwayscallback;

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig || has_capability('theme/learnr:configure', context_system::instance())) {

    // How this file works:
    // This theme's settings are divided into multiple settings pages.
    // This is quite unusual as Boost themes would have a nice tabbed settings interface.
    // However, as we are using many hide_if constraints for our settings, we would run into the
    // stupid "Too much data passed as arguments to js_call_amd..." debugging message if we would
    // pack all settings onto just one settings page.
    // To achieve this goal, we create a custom admin settings category and fill it with several settings pages.
    // However, there is still the $settings variable which is expected by Moodle coreto be filled with the theme
    // settings and which is automatically added to the admin settings tree in one settings page.
    // To avoid that there appears an empty "LearnR" settings page near our own custom settings category,
    // we set $settings to null.

    // Avoid that the theme settings page is auto-created.
    $settings = null;

    // Create custom admin settings category.
    $ADMIN->add('themes', new admin_category('theme_oncapflege',
            get_string('pluginname', 'theme_oncapflege', null, true)));

    // Create empty settings page structure to make the site administration work on non-admin pages.
    if (!$ADMIN->fulltree) {
        // Create Look settings page
        // (and allow users with the theme/learnr:configure capability to access it).
        $tab = new admin_settingpage('theme_oncapflege_look',
                get_string('configtitlelook', 'theme_learnr', null, true),
                'theme/learnr:configure');
        $ADMIN->add('theme_oncapflege', $tab);
 
    }

    // Create full settings page structure.
    // @codingStandardsIgnoreLine
    else if ($ADMIN->fulltree) {

        // Require the necessary libraries.
        require_once($CFG->dirroot . '/theme/learnr/lib.php');
        require_once($CFG->dirroot . '/theme/learnr/locallib.php');

        // Prepare options array for select settings.
        // Due to MDL-58376, we will use binary select settings instead of checkbox settings throughout this theme.
        $yesnooption = array(THEME_LEARNR_SETTING_SELECT_YES => get_string('yes'),
                THEME_LEARNR_SETTING_SELECT_NO => get_string('no'));

        // Prepare regular expression for checking if the value is a percent number (from 0% to 100%) or a pixel number
        // (with 3 or 4 digits) or a viewport width number (from 0 to 100).
        $widthregex = '/^((\d{1,2}|100)%)|((\d{1,2}|100)vw)|(\d{3,4}px)$/';


        // Create Look settings page with tabs
        // (and allow users with the theme/learnr:configure capability to access it).
        $page = new theme_boost_admin_settingspage_tabs('theme_oncapflege_look',
                get_string('configtitlelook', 'theme_learnr', null, true),
                'theme/learnr:configure');


        // Create general settings tab.
        $tab = new admin_settingpage('theme_oncapflege_look_general', get_string('generalsettings', 'theme_boost', null, true));

        // Create theme presets heading.
        $name = 'theme_oncapflege/presetheading';
        $title = get_string('presetheading', 'theme_learnr', null, true);
        $setting = new admin_setting_heading($name, $title, null);
        $tab->add($setting);

        // Replicate the preset setting from theme_boost, but use our own file area.
        $name = 'theme_oncapflege/preset';
        $title = get_string('preset', 'theme_boost', null, true);
        $description = get_string('preset_desc', 'theme_boost', null, true);
        $default = 'default.scss';

        $context = context_system::instance();
        $fs = get_file_storage();
        $files = $fs->get_area_files($context->id, 'theme_learnr', 'preset', 0, 'itemid, filepath, filename', false);

        $choices = [];
        foreach ($files as $file) {
            $choices[$file->get_filename()] = $file->get_filename();
        }
        $choices['default.scss'] = 'default.scss';
        $choices['plain.scss'] = 'plain.scss';

        $setting = new admin_setting_configthemepreset($name, $title, $description, $default, $choices, 'learnr');
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        // Replicate the preset files setting from theme_boost.
        $name = 'theme_oncapflege/presetfiles';
        $title = get_string('presetfiles', 'theme_boost', null, true);
        $description = get_string('presetfiles_desc', 'theme_boost', null, true);
        $setting = new admin_setting_configstoredfile($name, $title, $description, 'preset', 0,
                array('maxfiles' => 20, 'accepted_types' => array('.scss')));
        $tab->add($setting);

        //Begin DBN Update
        // Sections Display Options.
        $name = 'theme_oncapflege/sectionstyle';
        $title = get_string('sectionstyle' , 'theme_learnr');
        $description = get_string('sectionstyle_desc', 'theme_learnr');
        $option1 = get_string('sections-learnr', 'theme_learnr');
        $option2 = get_string('sections-boxed', 'theme_learnr');
        $option3 = get_string('sections-boost', 'theme_learnr');
        $option4 = get_string('sections-bars', 'theme_learnr');
        $default = '1';
        $choices = array('1'=>$option1, '2'=>$option2, '3'=>$option3, '4'=>$option4);
        $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        // Course Tile Display Styles
        $name = 'theme_oncapflege/coursetilestyle';
        $title = get_string('coursetilestyle' , 'theme_learnr');
        $description = get_string('coursetilestyle_desc', 'theme_learnr');
        $coursestyle1 = get_string('coursestyle1', 'theme_learnr');
        $coursestyle2 = get_string('coursestyle2', 'theme_learnr');
        $coursestyle3 = get_string('coursestyle3', 'theme_learnr');
        $coursestyle4 = get_string('coursestyle4', 'theme_learnr');
        $coursestyle5 = get_string('coursestyle5', 'theme_learnr');
        $coursestyle6 = get_string('coursestyle6', 'theme_learnr');
        $coursestyle7 = get_string('coursestyle7', 'theme_learnr');
        $coursestyle10 = get_string('coursestyle8', 'theme_learnr');
        $default = '10';
        $choices = array('1'=>$coursestyle1, '2'=>$coursestyle2, '3'=>$coursestyle3, '4'=>$coursestyle4, '5'=>$coursestyle5, '6'=>$coursestyle6, '7'=>$coursestyle7,'8'=>$coursestyle10);
        $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        // trim title setting.
        $name = 'theme_oncapflege/trimtitle';
        $title = get_string('trimtitle', 'theme_learnr');
        $description = get_string('trimtitle_desc', 'theme_learnr');
        $default = '256';
        $choices = array(
                '15' => '15',
                '20' => '20',
                '30' => '30',
                '40' => '40',
                '50' => '50',
                '60' => '60',
                '70' => '70',
                '80' => '80',
                '90' => '90',
                '100' => '100',
                '110' => '110',
                '120' => '120',
                '130' => '130',
                '140' => '140',
                '150' => '150',
                '175' => '175',
                '200' => '200',
                '256' => '256',
                );
        $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        // trim title setting.
        $name = 'theme_oncapflege/trimsummary';
        $title = get_string('trimsummary', 'theme_learnr');
        $description = get_string('trimsummary_desc', 'theme_learnr');
        $default = '300';
        $choices = array(
                '30' => '30',
                '60' => '60',
                '90' => '90',
                '100' => '100',
                '150' => '150',
                '200' => '200',
                '250' => '250',
                '300' => '300',
                '350' => '350',
                '400' => '400',
                '450' => '450',
                '500' => '500',
                '600' => '600',
                '800' => '800',
                );
        $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        // Courses height
        $name = 'theme_oncapflege/courseboxheight';
        $title = get_string('courseboxheight', 'theme_learnr');
        $description = get_string('courseboxheight_desc', 'theme_learnr');;
        $default = '250px';
        $choices = array(
                '200px' => '200px',
                '225px' => '225px',
                '250px' => '250px',
                '275px' => '275px',
                '300px' => '300px',
                '325px' => '325px',
                '350px' => '350px',
                '375px' => '375px',
                '400px' => '400px',
                );
        $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);
        //End DBN Update

        // Add tab to settings page.
        $page->add($tab);


        // Create SCSS tab.
        $tab = new admin_settingpage('theme_oncapflege_look_scss', get_string('scsstab', 'theme_learnr', null, true));

        // Create Raw SCSS heading.
        $name = 'theme_oncapflege/scssheading';
        $title = get_string('scssheading', 'theme_learnr', null, true);
        $setting = new admin_setting_heading($name, $title, null);
        $tab->add($setting);

        // Replicate the Raw initial SCSS setting from theme_boost.
        $name = 'theme_oncapflege/scsspre';
        $title = get_string('rawscsspre', 'theme_boost', null, true);
        $description = get_string('rawscsspre_desc', 'theme_boost', null, true);
        $default = '';
        $setting = new admin_setting_scsscode($name, $title, $description, $default, PARAM_RAW);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        // Replicate the Raw SCSS setting from theme_boost.
        $name = 'theme_oncapflege/scss';
        $title = get_string('rawscss', 'theme_boost', null, true);
        $description = get_string('rawscss_desc', 'theme_boost', null, true);
        $default = '';
        $setting = new admin_setting_scsscode($name, $title, $description, $default, PARAM_RAW);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        // Add tab to settings page.
        $page->add($tab);


        // Create course tab.
        $tab = new admin_settingpage('theme_oncapflege_look_course',
                get_string('coursetab', 'theme_learnr', null, true));


         // Begin DBN Update.
        // Show/hide course index navigation.
        // Tinjohn comment.
        // Those configs are implemented via e.g. for showprogressbar like in learnr.         
        // Like: $hasprogressbar = (empty($this->page->theme->settings->showprogressbar)) ? false : true;.
        // Thus they need to be configured in a child theme.
        $name = 'theme_oncapflege/showcourseindexnav';
        $title = get_string('showcourseindexnav', 'theme_learnr');
        $description = get_string('showcourseindexnav_desc', 'theme_learnr');
        $default = '1';
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        // Show hide course management panel.
        $name = 'theme_oncapflege/showcoursemanagement';
        $title = get_string('showcoursemanagement', 'theme_learnr');
        $description = get_string('showcoursemanagement_desc', 'theme_learnr');
        $default = '1';
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        // Show/hide course progressbar learnr.
        $name = 'theme_oncapflege/showprogressbar';
        $title = get_string('showprogressbar', 'theme_learnr');
        $description = get_string('showprogressbar_desc', 'theme_learnr');
        $default = '0';
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);
        
        // Show hide Latest Courses learnr.
        $name = 'theme_oncapflege/showlatestcourses';
        $title = get_string('showlatestcourses', 'theme_learnr');
        $description = get_string('showlatestcourses_desc', 'theme_learnr');
        $default = '1';
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        // Show hide easy enrollment btn.
        $name = 'theme_oncapflege/showeasyenrolbtn';
        $title = get_string('showeasyenrolbtn', 'theme_learnr');
        $description = get_string('showeasyenrolbtn_desc', 'theme_learnr');
        $default = '0';
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        // Show Course Activities Grouping Menu
        $name = 'theme_oncapflege/showcourseactivities';
        $title = get_string('showcourseactivities', 'theme_learnr');
        $description = get_string('showcourseactivities_desc', 'theme_learnr');
        $default = '0';
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        //End DBN Update.

        // Add tab to settings page.
        $page->add($tab);

        // Begin DBN Update.
        // Create static pages tab.
        $tab = new admin_settingpage('theme_oncapflege_content_iconnavbar',
                get_string('iconnavbartab', 'theme_learnr', null, true));

        // This is the descriptor for the page.
        $name = 'theme_oncapflege/iconnavinfo';
        $heading = get_string('iconnavinfo', 'theme_learnr');
        $information = get_string('iconnavinfo_desc', 'theme_learnr');
        $setting = new admin_setting_heading($name, $heading, $information);
        $tab->add($setting);
        
        // This is the descriptor for teacher create a course
        $name = 'theme_oncapflege/createinfo';
        $heading = get_string('createinfo', 'theme_learnr');
        $information = get_string('createinfodesc', 'theme_learnr');
        $setting = new admin_setting_heading($name, $heading, $information);
        $tab->add($setting);

        // Creator Icon
        $name = 'theme_oncapflege/createicon';
        $title = get_string('navicon', 'theme_learnr');
        $description = get_string('navicondesc', 'theme_learnr');
        $default = 'edit';
        $setting = new admin_setting_configtext($name, $title, $description, $default);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        $name = 'theme_oncapflege/createbuttontext';
        $title = get_string('naviconbuttontext', 'theme_learnr');
        $description = get_string('naviconbuttontextdesc', 'theme_learnr');
        $default = get_string('naviconbuttoncreatetextdefault', 'theme_learnr');
        $setting = new admin_setting_configtext($name, $title, $description, $default);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        $name = 'theme_oncapflege/createbuttonurl';
        $title = get_string('naviconbuttonurl', 'theme_learnr');
        $description = get_string('naviconbuttonurldesc', 'theme_learnr');
        $default =  $CFG->wwwroot.'/course/edit.php?category=1';
        $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_URL);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);


        // This is the descriptor for teacher create a course
        $name = 'theme_oncapflege/sliderinfo';
        $heading = get_string('sliderinfo', 'theme_learnr');
        $information = get_string('sliderinfodesc', 'theme_learnr');
        $setting = new admin_setting_heading($name, $heading, $information);
        $tab->add($setting);

        // Creator Icon
        $name = 'theme_oncapflege/slideicon';
        $title = get_string('navicon', 'theme_learnr');
        $description = get_string('naviconslidedesc', 'theme_learnr');
        $default = '';
        $setting = new admin_setting_configtext($name, $title, $description, $default);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        $name = 'theme_oncapflege/slideiconbuttontext';
        $title = get_string('naviconbuttontext', 'theme_learnr');
        $description = get_string('naviconbuttontextdesc', 'theme_learnr');
        $default = '';
        $setting = new admin_setting_configtext($name, $title, $description, $default);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        // Slide Textbox.
        $name = 'theme_oncapflege/slidetextbox';
            $title = get_string('slidetextbox', 'theme_learnr');
            $description = get_string('slidetextbox_desc', 'theme_learnr');
            $default = '';
            $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $tab->add($setting);



            // This is the descriptor for icon One
            $name = 'theme_oncapflege/navicon1info';
            $heading = get_string('navicon1', 'theme_learnr');
            $information = get_string('navicondesc', 'theme_learnr');
            $setting = new admin_setting_heading($name, $heading, $information);
            $tab->add($setting);

            // icon One
            $name = 'theme_oncapflege/nav1icon';
            $title = get_string('navicon', 'theme_learnr');
            $description = get_string('navicondesc', 'theme_learnr');
            $default = '';
            $setting = new admin_setting_configtext($name, $title, $description, $default);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $tab->add($setting);

            $name = 'theme_oncapflege/nav1buttontext';
            $title = get_string('naviconbuttontext', 'theme_learnr');
            $description = get_string('naviconbuttontextdesc', 'theme_learnr');
            $default = '';
            $setting = new admin_setting_configtext($name, $title, $description, $default);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $tab->add($setting);

            $name = 'theme_oncapflege/nav1buttonurl';
            $title = get_string('naviconbuttonurl', 'theme_learnr');
            $description = get_string('naviconbuttonurldesc', 'theme_learnr');
            $default = '';
            $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_URL);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $tab->add($setting);

            $name = 'theme_oncapflege/nav1target';
            $title = get_string('marketingurltarget' , 'theme_learnr');
            $description = get_string('marketingurltargetdesc', 'theme_learnr');
            $target1 = get_string('marketingurltargetself', 'theme_learnr');
            $target2 = get_string('marketingurltargetnew', 'theme_learnr');
            $target3 = get_string('marketingurltargetparent', 'theme_learnr');
            $default = 'target1';
            $choices = array('_self'=>$target1, '_blank'=>$target2, '_parent'=>$target3);
            $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $tab->add($setting);

            // This is the descriptor for icon One
            $name = 'theme_oncapflege/navicon2info';
            $heading = get_string('navicon2', 'theme_learnr');
            $information = get_string('navicondesc', 'theme_learnr');
            $setting = new admin_setting_heading($name, $heading, $information);
            $tab->add($setting);

            $name = 'theme_oncapflege/nav2icon';
            $title = get_string('navicon', 'theme_learnr');
            $description = get_string('navicondesc', 'theme_learnr');
            $default = '';
            $setting = new admin_setting_configtext($name, $title, $description, $default);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $tab->add($setting);

            $name = 'theme_oncapflege/nav2buttontext';
            $title = get_string('naviconbuttontext', 'theme_learnr');
            $description = get_string('naviconbuttontextdesc', 'theme_learnr');
            $default = '';
            $setting = new admin_setting_configtext($name, $title, $description, $default);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $tab->add($setting);

            $name = 'theme_oncapflege/nav2buttonurl';
            $title = get_string('naviconbuttonurl', 'theme_learnr');
            $description = get_string('naviconbuttonurldesc', 'theme_learnr');
            $default = '';
            $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_URL);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $tab->add($setting);

            $name = 'theme_oncapflege/nav2target';
            $title = get_string('marketingurltarget' , 'theme_learnr');
            $description = get_string('marketingurltargetdesc', 'theme_learnr');
            $target1 = get_string('marketingurltargetself', 'theme_learnr');
            $target2 = get_string('marketingurltargetnew', 'theme_learnr');
            $target3 = get_string('marketingurltargetparent', 'theme_learnr');
            $default = 'target1';
            $choices = array('_self'=>$target1, '_blank'=>$target2, '_parent'=>$target3);
            $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $tab->add($setting);

            // This is the descriptor for icon three
            $name = 'theme_oncapflege/navicon3info';
            $heading = get_string('navicon3', 'theme_learnr');
            $information = get_string('navicondesc', 'theme_learnr');
            $setting = new admin_setting_heading($name, $heading, $information);
            $tab->add($setting);

            $name = 'theme_oncapflege/nav3icon';
            $title = get_string('navicon', 'theme_learnr');
            $description = get_string('navicondesc', 'theme_learnr');
            $default = '';
            $setting = new admin_setting_configtext($name, $title, $description, $default);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $tab->add($setting);

            $name = 'theme_oncapflege/nav3buttontext';
            $title = get_string('naviconbuttontext', 'theme_learnr');
            $description = get_string('naviconbuttontextdesc', 'theme_learnr');
            $default = '';
            $setting = new admin_setting_configtext($name, $title, $description, $default);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $tab->add($setting);

            $name = 'theme_oncapflege/nav3buttonurl';
            $title = get_string('naviconbuttonurl', 'theme_learnr');
            $description = get_string('naviconbuttonurldesc', 'theme_learnr');
            $default = '';
            $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_URL);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $tab->add($setting);

            $name = 'theme_oncapflege/nav3target';
            $title = get_string('marketingurltarget' , 'theme_learnr');
            $description = get_string('marketingurltargetdesc', 'theme_learnr');
            $target1 = get_string('marketingurltargetself', 'theme_learnr');
            $target2 = get_string('marketingurltargetnew', 'theme_learnr');
            $target3 = get_string('marketingurltargetparent', 'theme_learnr');
            $default = 'target1';
            $choices = array('_self'=>$target1, '_blank'=>$target2, '_parent'=>$target3);
            $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $tab->add($setting);

            // This is the descriptor for icon four
            $name = 'theme_oncapflege/navicon4info';
            $heading = get_string('navicon4', 'theme_learnr');
            $information = get_string('navicondesc', 'theme_learnr');
            $setting = new admin_setting_heading($name, $heading, $information);
            $tab->add($setting);

            $name = 'theme_oncapflege/nav4icon';
            $title = get_string('navicon', 'theme_learnr');
            $description = get_string('navicondesc', 'theme_learnr');
            $default = '';
            $setting = new admin_setting_configtext($name, $title, $description, $default);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $tab->add($setting);

            $name = 'theme_oncapflege/nav4buttontext';
            $title = get_string('naviconbuttontext', 'theme_learnr');
            $description = get_string('naviconbuttontextdesc', 'theme_learnr');
            $default = '';
            $setting = new admin_setting_configtext($name, $title, $description, $default);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $tab->add($setting);

            $name = 'theme_oncapflege/nav4buttonurl';
            $title = get_string('naviconbuttonurl', 'theme_learnr');
            $description = get_string('naviconbuttonurldesc', 'theme_learnr');
            $default =  '';
            $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_URL);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $tab->add($setting);

            $name = 'theme_oncapflege/nav4target';
            $title = get_string('marketingurltarget' , 'theme_learnr');
            $description = get_string('marketingurltargetdesc', 'theme_learnr');
            $target1 = get_string('marketingurltargetself', 'theme_learnr');
            $target2 = get_string('marketingurltargetnew', 'theme_learnr');
            $target3 = get_string('marketingurltargetparent', 'theme_learnr');
            $default = 'target1';
            $choices = array('_self'=>$target1, '_blank'=>$target2, '_parent'=>$target3);
            $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $tab->add($setting);

            // This is the descriptor for icon four
            $name = 'theme_oncapflege/navicon5info';
            $heading = get_string('navicon5', 'theme_learnr');
            $information = get_string('navicondesc', 'theme_learnr');
            $setting = new admin_setting_heading($name, $heading, $information);
            $tab->add($setting);

            $name = 'theme_oncapflege/nav5icon';
            $title = get_string('navicon', 'theme_learnr');
            $description = get_string('navicondesc', 'theme_learnr');
            $default = '';
            $setting = new admin_setting_configtext($name, $title, $description, $default);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $tab->add($setting);

            $name = 'theme_oncapflege/nav5buttontext';
            $title = get_string('naviconbuttontext', 'theme_learnr');
            $description = get_string('naviconbuttontextdesc', 'theme_learnr');
            $default = '';
            $setting = new admin_setting_configtext($name, $title, $description, $default);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $tab->add($setting);

            $name = 'theme_oncapflege/nav5buttonurl';
            $title = get_string('naviconbuttonurl', 'theme_learnr');
            $description = get_string('naviconbuttonurldesc', 'theme_learnr');
            $default = '';
            $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_URL);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $tab->add($setting);

            $name = 'theme_oncapflege/nav5target';
            $title = get_string('marketingurltarget' , 'theme_learnr');
            $description = get_string('marketingurltargetdesc', 'theme_learnr');
            $target1 = get_string('marketingurltargetself', 'theme_learnr');
            $target2 = get_string('marketingurltargetnew', 'theme_learnr');
            $target3 = get_string('marketingurltargetparent', 'theme_learnr');
            $default = 'target1';
            $choices = array('_self'=>$target1, '_blank'=>$target2, '_parent'=>$target3);
            $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $tab->add($setting);

            // This is the descriptor for icon six
            $name = 'theme_oncapflege/navicon6info';
            $heading = get_string('navicon6', 'theme_learnr');
            $information = get_string('navicondesc', 'theme_learnr');
            $setting = new admin_setting_heading($name, $heading, $information);
            $tab->add($setting);

            $name = 'theme_oncapflege/nav6icon';
            $title = get_string('navicon', 'theme_learnr');
            $description = get_string('navicondesc', 'theme_learnr');
            $default = '';
            $setting = new admin_setting_configtext($name, $title, $description, $default);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $tab->add($setting);

            $name = 'theme_oncapflege/nav6buttontext';
            $title = get_string('naviconbuttontext', 'theme_learnr');
            $description = get_string('naviconbuttontextdesc', 'theme_learnr');
            $default = '';
            $setting = new admin_setting_configtext($name, $title, $description, $default);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $tab->add($setting);

            $name = 'theme_oncapflege/nav6buttonurl';
            $title = get_string('naviconbuttonurl', 'theme_learnr');
            $description = get_string('naviconbuttonurldesc', 'theme_learnr');
            $default = '';
            $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_URL);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $tab->add($setting);

            $name = 'theme_oncapflege/nav6target';
            $title = get_string('marketingurltarget' , 'theme_learnr');
            $description = get_string('marketingurltargetdesc', 'theme_learnr');
            $target1 = get_string('marketingurltargetself', 'theme_learnr');
            $target2 = get_string('marketingurltargetnew', 'theme_learnr');
            $target3 = get_string('marketingurltargetparent', 'theme_learnr');
            $default = 'target1';
            $choices = array('_self'=>$target1, '_blank'=>$target2, '_parent'=>$target3);
            $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $tab->add($setting);

            // This is the descriptor for icon seven
            $name = 'theme_oncapflege/navicon7info';
            $heading = get_string('navicon7', 'theme_learnr');
            $information = get_string('navicondesc', 'theme_learnr');
            $setting = new admin_setting_heading($name, $heading, $information);
            $tab->add($setting);

            $name = 'theme_oncapflege/nav7icon';
            $title = get_string('navicon', 'theme_learnr');
            $description = get_string('navicondesc', 'theme_learnr');
            $default = '';
            $setting = new admin_setting_configtext($name, $title, $description, $default);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $tab->add($setting);

            $name = 'theme_oncapflege/nav7buttontext';
            $title = get_string('naviconbuttontext', 'theme_learnr');
            $description = get_string('naviconbuttontextdesc', 'theme_learnr');
            $default = '';
            $setting = new admin_setting_configtext($name, $title, $description, $default);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $tab->add($setting);

            $name = 'theme_oncapflege/nav7buttonurl';
            $title = get_string('naviconbuttonurl', 'theme_learnr');
            $description = get_string('naviconbuttonurldesc', 'theme_learnr');
            $default = '';
            $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_URL);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $tab->add($setting);

            $name = 'theme_oncapflege/nav7target';
            $title = get_string('marketingurltarget' , 'theme_learnr');
            $description = get_string('marketingurltargetdesc', 'theme_learnr');
            $target1 = get_string('marketingurltargetself', 'theme_learnr');
            $target2 = get_string('marketingurltargetnew', 'theme_learnr');
            $target3 = get_string('marketingurltargetparent', 'theme_learnr');
            $default = 'target1';
            $choices = array('_self'=>$target1, '_blank'=>$target2, '_parent'=>$target3);
            $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $tab->add($setting);

        // This is the descriptor for icon eight
        $name = 'theme_oncapflege/navicon8info';
        $heading = get_string('navicon8', 'theme_learnr');
        $information = get_string('navicondesc', 'theme_learnr');
        $setting = new admin_setting_heading($name, $heading, $information);
        $tab->add($setting);

        $name = 'theme_oncapflege/nav8icon';
        $title = get_string('navicon', 'theme_learnr');
        $description = get_string('navicondesc', 'theme_learnr');
        $default = '';
        $setting = new admin_setting_configtext($name, $title, $description, $default);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        $name = 'theme_oncapflege/nav8buttontext';
        $title = get_string('naviconbuttontext', 'theme_learnr');
        $description = get_string('naviconbuttontextdesc', 'theme_learnr');
        $default = '';
        $setting = new admin_setting_configtext($name, $title, $description, $default);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        $name = 'theme_oncapflege/nav8buttonurl';
        $title = get_string('naviconbuttonurl', 'theme_learnr');
        $description = get_string('naviconbuttonurldesc', 'theme_learnr');
        $default = '';
        $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_URL);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        $name = 'theme_oncapflege/nav8target';
        $title = get_string('marketingurltarget' , 'theme_learnr');
        $description = get_string('marketingurltargetdesc', 'theme_learnr');
        $target1 = get_string('marketingurltargetself', 'theme_learnr');
        $target2 = get_string('marketingurltargetnew', 'theme_learnr');
        $target3 = get_string('marketingurltargetparent', 'theme_learnr');
        $default = 'target1';
        $choices = array('_self'=>$target1, '_blank'=>$target2, '_parent'=>$target3);
        $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        // Add tab to settings page.
        $page->add($tab);
        // End DBN Update.

        // Add settings page to the admin settings category.
        $ADMIN->add('theme_oncapflege', $page);


    }
}
