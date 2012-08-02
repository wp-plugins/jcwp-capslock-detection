<?php
  /*
    Plugin Name: jcwp capslock detection
    Plugin URI: http://jaspreetchahal.org/wordpress-caps-lock-detection-plugin-for-login-password-fields/
    Description: This plugin shows a tooltip when user's have their CAPS lock on while typing their password to login. 
    Author: Jaspreet Chahal
    Version: 1.09
    Author URI: http://jaspreetchahal.org
    License: GPLv2 or later
    */

    /*
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
    */
    
    // if not an admin just block access
    if(preg_match('/admin\.php/',$_SERVER['REQUEST_URI']) && is_admin() == false) {
        return false;
    }
    
    register_activation_hook(__FILE__,'jcorgcld_activate');
    function jcorgcld_activate() {
            add_option('jcorgcld_active','1');
            add_option('jcorgcld_fallback','WARNING: CAPS Lock is on');
            add_option('jcorgcld_position','w');
            add_option('jcorgcld_fade',"No");
            add_option('jcorgcld_html','No');
            add_option('jcorgcld_offset',"10");
            add_option('jcorgcld_linkback','No');
            add_option('jcorgcld_opacity','0.8');
    }
    
    add_action("admin_menu","jcorgcld_menu");
    function jcorgcld_menu() {
        add_options_page('JCWP CapsLock', 'JCWP CapsLock Detection', 'manage_options', 'jcorgcld-plugin', 'jcorgcld_plugin_options');
    }
    add_action('admin_init','jcorgcld_regsettings');
    function jcorgcld_regsettings() {        
        register_setting("jcorgcld-setting","jcorgcld_active");
        register_setting("jcorgcld-setting","jcorgcld_fallback");
        register_setting("jcorgcld-setting","jcorgcld_position");
        register_setting("jcorgcld-setting","jcorgcld_fade");
        register_setting("jcorgcld-setting","jcorgcld_html");     
        register_setting("jcorgcld-setting","jcorgcld_offset");     
        register_setting("jcorgcld-setting","jcorgcld_opacity");     
        register_setting("jcorgcld-setting","jcorgcld_linkback");    
        wp_enqueue_script('jquery');
        wp_enqueue_script('jcorgcld_script',plugins_url("jccapslock.js",__FILE__));
        wp_enqueue_style('jcorgcld_styles',plugins_url("jccapslock.css",__FILE__));
    }   
    
    add_action('wp_head','jcorgcld_inclscript',20);
    add_action('login_head','jcorgcld_inclscript',20);
    function jcorgcld_inclscript() {
        wp_enqueue_script('jquery');
        wp_enqueue_script('jcorgcld_script',plugins_url("jccapslock.js",__FILE__),array("jquery"));
        wp_enqueue_style('jcorgcld_styles',plugins_url("jccapslock.css",__FILE__));
        if(get_option('jcorgcld_active') == "1") {
        ?> 
         <script> 
         jQuery(document).ready(function(){
             
             jQuery(":password").CapsLockAlert({
                    delayIn: 0,      
                    delayOut: 0,     
                    fade: <?php echo trim(get_option("jcorgcld_fade"))=='Yes'?'true':'false'?>,     
                    fallback: '<?php echo strlen(trim(get_option("jcorgcld_fallback")))>0?str_replace("'","\'",trim(html_entity_decode(get_option("jcorgcld_fallback")))):'WARNING: CAPS Lock is on'?>',    
                    gravity: '<?php echo strlen(trim(get_option("jcorgcld_position")))>0?str_replace("'","\'",trim(get_option("jcorgcld_position"))):'w'?>',    
                    html: <?php echo trim(get_option("jcorgcld_html"))=='Yes'?'true':'false'?>,     
                    live: false,     
                    offset: <?php echo strlen(trim(get_option("jcorgcld_offset")))>0?trim(get_option("jcorgcld_offset")):'10'?>,      
                    opacity: <?php echo strlen(trim(get_option("jcorgcld_opacity")))>0?trim(get_option("jcorgcld_opacity")):'0.8'?>,    
                    title: 'title', 
                    trigger: 'manual', 
                    stylize:true
                });
         });
         </script>
         
        <?php
        if(get_option('jcorgcld_linkback') =="Yes") {
            echo '<a style="font-size:0em !important;color:transparent !important" href="http://jaspreetchahal.org">Scroll to top is powered by http://jaspreetchahal.org</a>';
        }
        }
    }
    
    function jcorgcld_plugin_options() {
        jcorgCldDonationDetail();           
        ?> 
        <style type="text/css">
        .jcorgbsuccess, .jcorgberror {   border: 1px solid #ccc; margin:0px; padding:15px 10px 15px 50px; font-size:12px;}
        .jcorgbsuccess {color: #FFF;background: green; border: 1px solid  #FEE7D8;}
        .jcorgberror {color: #B70000;border: 1px solid  #FEE7D8;}
        .jcorgb-errors-title {font-size:12px;color:black;font-weight:bold;}
        .jcorgb-errors { border: #FFD7C4 1px solid;padding:5px; background: #FFF1EA;}
        .jcorgb-errors ul {list-style:none; color:black; font-size:12px;margin-left:10px;}
        .jcorgb-errors ul li {list-style:circle;line-height:150%;/*background: url(/images/icons/star_red.png) no-repeat left;*/font-size:11px;margin-left:10px; margin-top:5px;font-weight:normal;padding-left:15px}
        td {font-weight: normal;}
        </style><br>
        <div class="wrap" style="float: left;" >
            <?php             
            
            screen_icon('tools');?>
            <h2>JaspreetChahal's CAPS lock detection plugin settings</h2>
            <?php 
                $errors = get_settings_errors("",true);
                $errmsgs = array();
                $msgs = "";
                if(count($errors) >0)
                foreach ($errors as $error) {
                    if($error["type"] == "error")
                        $errmsgs[] = $error["message"];
                    else if($error["type"] == "updated")
                        $msgs = $error["message"];
                }

                echo jcorgCldMakeErrorsHtml($errmsgs,'warning1');
                if(strlen($msgs) > 0) {
                    echo "<div class='jcorgbsuccess' style='width:90%'>$msgs</div>";
                }

            ?><br><br>
            <form action="options.php" method="post" id="jcorgbotinfo_settings_form">
            <?php settings_fields("jcorgcld-setting");?>
            <table class="widefat" style="width: 700px;" cellpadding="7">
                <tr valign="top">
                    <th scope="row">Enabled</th>
                    <td><input type="radio" name="jcorgcld_active" <?php if(get_option('jcorgcld_active') == "1"|| get_option('jcorgcld_active') == "") echo "checked='checked'";?>
                            value="1" 
                            /> Yes
                            <input type="radio" name="jcorgcld_active" <?php if(get_option('jcorgcld_active') == "0" ) echo "checked='checked'";?>
                            value="0" 
                            /> No 
                    </td>
                </tr>     
               <tr valign="top">
                    <th width="25%" scope="row">Tooltip message</th>
                    <td><input type="text" name="jcorgcld_fallback"
                            value="<?php echo get_option('jcorgcld_fallback'); ?>"  style="padding:5px" size="40"/></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Is tooltip message HTML</th>
                    <td><input type="radio" name="jcorgcld_html" <?php if(get_option('jcorgcld_html') == "Yes") echo "checked='checked'";?>
                            value="Yes" 
                            /> Yes
                            <input type="radio" name="jcorgcld_html" <?php if(get_option('jcorgcld_html') == "No" || get_option('jcorgcld_html') == "") echo "checked='checked'";?>
                            value="No" 
                            /> No 
                    </td>
                </tr>  
                <tr valign="top">
                    <th scope="row">Tooltip position</th>
                    <td>
                    <select name="jcorgcld_position">
                    <option value="e" <?php if(get_option('jcorgcld_position') == "e"){  _e('selected');}?> >Left</option>
                    <option value="w" <?php if(get_option('jcorgcld_position') == "w") { _e('selected');}?> >Right</option>
                    <option value="n" <?php if(get_option('jcorgcld_position') == "n") { _e('selected');}?> >Top</option>
                    <option value="s" <?php if(get_option('jcorgcld_position') == "s") { _e('selected');}?> >Bottom</option>
                    </select>
               </tr>
                <tr valign="top">
                    <th scope="row">Use Fade effect</th>
                    <td><input type="radio" name="jcorgcld_fade" <?php if(get_option('jcorgcld_fade') == "Yes") echo "checked='checked'";?>
                            value="Yes" 
                            /> Yes
                            <input type="radio" name="jcorgcld_fade" <?php if(get_option('jcorgcld_fade') == "No" || get_option('jcorgcld_fade') == "") echo "checked='checked'";?>
                            value="No" 
                            /> No 
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Tooltip Offset</th>
                    <td><input type="number" name="jcorgcld_offset"
                            value="<?php echo get_option('jcorgcld_offset'); ?>"  style="padding:5px" size="4"/>px e.g. 10</td>
                </tr> 
                <tr valign="top">
                    <th scope="row">Tooltip opacity</th>
                    <td><input type="text" name="jcorgcld_opacity" id="jcorgcld_opacity"
                            value="<?php echo get_option('jcorgcld_opacity'); ?>"  style="padding:5px" size="10"/> e.g. 0.9</td>
                </tr>  
                <tr valign="top">
                    <th scope="row">Link to authors website</th>
                    <td><input type="checkbox" name="jcorgcld_linkback"
                            value="Yes" <?php if(get_option('jcorgcld_linkback') =="Yes") echo "checked='checked'";?> /> <br>
                            <Strong>An inivisible link will be placed in the footer which points to http://jaspreetchahal.org</strong></td>
                </tr> 
        </table>
        <p class="submit">
            <input type="submit" class="button-primary"
                value="Save Changes" />
        </p>          
            </form>
        </div>
        <?php     
        echo "<div style='float:left;margin-left:20px;margin-top:75px'>".jcorgCldfeeds()."</div>";
    }
    
    function jcorgCldDonationDetail() {
        ?>    
        <style type="text/css"> .jcorgcr_donation_uses li {float:left; margin-left:20px;font-weight: bold;} </style> 
        <div style="padding: 10px; background: #f1f1f1;border:1px #EEE solid; border-radius:15px;width:98%"> 
        <h2>If you like this Plugin, please consider donating</h2> 
        You can choose your own amount. Developing this awesome plugin took a lot of effort and time; days and weeks of continuous voluntary unpaid work. 
        If you like this plugin or if you are using it for commercial websites, please consider a donation to the author to 
        help support future updates and development. 
        <div class="jcorgcr_donation_uses"> 
        <span style="font-weight:bold">Main uses of Donations</span><ol ><li>Web Hosting Fees</li><li>Cable Internet Fees</li><li>Time/Value Reimbursement</li><li>Motivation for Continuous Improvements</li></ol> </div> <br class="clear"> <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=MHMQ6E37TYW3N"><img src="https://www.paypalobjects.com/en_AU/i/btn/btn_donateCC_LG.gif" /></a> <br><br><strong>For help please visit </strong><br> 
        <a href="http://jaspreetchahal.org/wordpress-caps-lock-detection-plugin-for-login-password-fields/">http://jaspreetchahal.org/wordpress-caps-lock-detection-plugin-for-login-password-fields/</a> <br><strong> </div>
        
        <?php
        
    }
    function jcorgCldfeeds() {
        $list = "
        <table style='width:400px;' class='widefat'>
        <tr>
            <th>
            Latest posts from JaspreetChahal.org
            </th>
        </tr>
        ";
        $max = 5;
        $feeds = fetch_feed("http://feeds.feedburner.com/jaspreetchahal/mtDg");
        $cfeeds = $feeds->get_item_quantity($max); 
        $feed_items = $feeds->get_items(0, $cfeeds); 
        if ($cfeeds > 0) {
            foreach ( $feed_items as $feed ) {    
                if (--$max >= 0) {
                    $list .= " <tr><td><a href='".$feed->get_permalink()."'>".$feed->get_title()."</a> </td></tr>";}
            }            
        }
        return $list."</table>";
    }
    
    
    function jcorgCldMakeErrorsHtml($errors,$type="error")
    {
        $class="jcorgberror";
        $title=__("Please correct the following errors","jcorgbot");
        if($type=="warnings") {
            $class="jcorgberror";
            $title=__("Please review the following Warnings","jcorgbot");
        }
        if($type=="warning1") {
            $class="jcorgbwarning";
            $title=__("Please review the following Warnings","jcorgbot");
        }
        $strCompiledHtmlList = "";
        if(is_array($errors) && count($errors)>0) {
                $strCompiledHtmlList.="<div class='$class' style='width:90% !important'>
                                        <div class='jcorgb-errors-title'>$title: </div><ol>";
                foreach($errors as $error) {
                      $strCompiledHtmlList.="<li>".$error."</li>";
                }
                $strCompiledHtmlList.="</ol></div>";
        return $strCompiledHtmlList;
        }
    }