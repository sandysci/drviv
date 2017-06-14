<?php /**
 * @version 1.0
 * @package Booking Calendar 
 * @category Admin Panel - Dashboard functions
 * @author wpdevelop
 *
 * @web-site http://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com 
 * 
 * @modified 2016-03-16
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit, if accessed directly


////////////////////////////////////////////////////////////////////////////////
// D a s h b o a r d      W i d g e t
////////////////////////////////////////////////////////////////////////////////
    
/** Setup Widget for Dashboard */
function wpbc_dashboard_widget_setup(){            

   // Check, if we have permission  to  show Widget  ///////////////////////////
   $is_user_activated = apply_bk_filter('multiuser_is_current_user_active',  true ); //FixIn: 6.0.1.17
   if ( ! $is_user_activated ) 
       return false;

   $user_role = get_bk_option( 'booking_user_role_booking' );
   if ( ! wpbc_is_current_user_have_this_role( $user_role ) )
       return false;
   
   
   // Add Booking Calendar Widget  to Dashboard  ///////////////////////////////
   $bk_dashboard_widget_id = 'booking_dashboard_widget';
   wp_add_dashboard_widget( $bk_dashboard_widget_id, sprintf( __( 'Booking Calendar', 'booking' ) ), 'wpbc_dashboard_widget_show', null );

   
   // Sort Dashboard. Add Booking Calendar widget to top ///////////////////////
   global $wp_meta_boxes;
   $normal_dashboard = $wp_meta_boxes['dashboard']['normal']['core'];
   if ( isset( $normal_dashboard[$bk_dashboard_widget_id] ) ) {
        // Backup and delete our new dashbaord widget from the end of the array
        $example_widget_backup = array( $bk_dashboard_widget_id => $normal_dashboard[$bk_dashboard_widget_id] );
        unset( $normal_dashboard[$bk_dashboard_widget_id] );
    } else
        $example_widget_backup = array();

    // Sometimes, some other plugins can modify this item, so its can be not a array
    if ( is_array( $normal_dashboard ) ) {                                      
        // Merge the two arrays together so our widget is at the beginning
        if ( is_array( $normal_dashboard ) )
            $sorted_dashboard = array_merge( $example_widget_backup, $normal_dashboard );
        else
            $sorted_dashboard = $example_widget_backup;
        // Save the sorted array back into the original metaboxes
        $wp_meta_boxes['dashboard']['normal']['core'] = $sorted_dashboard;
    }
}
add_action( 'wp_dashboard_setup', 'wpbc_dashboard_widget_setup' );



/** Show Booking Dashboard Widget content */
function wpbc_dashboard_widget_show() {

    global $wpdb;
    

    // <editor-fold     defaultstate="collapsed"                        desc=" Get bookings for counting number of specific bookings "  >
    $my_resources = array();
    if ( class_exists( 'wpdev_bk_multiuser' ) ) {  
        $is_superadmin = apply_bk_filter( 'multiuser_is_user_can_be_here', true, 'only_super_admin' );
        if ( ! $is_superadmin ) { 
            
            $bk_ids = apply_bk_filter( 'get_bk_resources_of_user', false );
            
            if ( $bk_ids !== false )
                foreach ( $bk_ids as $bk_id ) 
                    $my_resources[] = $bk_id->ID;
            
            
        }
    }
    $my_resources = implode( ',', $my_resources );

    $trash_bookings = '  bk.trash != 1 ';                                //FixIn: 6.1.1.10  - check also  below usage of {$trash_bookings}

    $sql_req = "SELECT DISTINCT bk.booking_id as id, dt.approved, dt.booking_date, bk.modification_date as m_date , bk.is_new as new
                FROM {$wpdb->prefix}bookingdates as dt
                INNER JOIN {$wpdb->prefix}booking as bk
                    ON bk.booking_id = dt.booking_id "
                . " WHERE {$trash_bookings} " ;
    if ($my_resources!='') $sql_req .=     " AND  bk.booking_type IN ({$my_resources}) ";

    $sql_req .=     "ORDER BY dt.booking_date" ;
    

    $sql_results = $wpdb->get_results( $sql_req );

//               [0] => stdClass Object (
//                    [id]          => 290
//                    [approved]    => 0
//                    [booking_date]=> 2014-08-29 00:00:00
//                    [m_date]      => 2016-01-21 16:44:33
//                    [new]         => 1
//               ), ...
    // </editor-fold>
         
    
    // <editor-fold     defaultstate="collapsed"                        desc=" Count bookings "  >
    $bk_array = array();
    if ( ! empty( $sql_results ) )
        foreach ( $sql_results as $v ) {
        
            if ( !isset( $bk_array[$v->id] ) )
                $bk_array[$v->id] = array( 
                                          'dates' => array()
                                        , 'bk_today' => 0
                                        , 'm_today' => 0 
                                        );
            $bk_array[$v->id]['id']       = $v->id;
            $bk_array[$v->id]['approved'] = $v->approved;
            $bk_array[$v->id]['dates'][]  = $v->booking_date;
            $bk_array[$v->id]['m_date']   = $v->m_date;                         // Modification Booking date
            $bk_array[$v->id]['new']      = $v->new;
            if ( wpbc_is_today_date( $v->booking_date ) )  $bk_array[$v->id]['bk_today'] = 1;
            if ( wpbc_is_today_date( $v->m_date ) )        $bk_array[$v->id]['m_today']  = 1;
        }

    $counter = array(   
                      'all' => 0
                    , 'new' => 0
                    , 'pending' => 0
                    , 'approved' => 0
                    , 'booking_today' => 0
                    , 'was_made_today' => 0
    );
    foreach ( $bk_array as $k => $v ) {        
        $counter['all']++;
        if ( $v['new'] )        $counter['new']++;
        if ( $v['m_today'] )    $counter['was_made_today']++;        
        if ( $v['bk_today'] )   $counter['booking_today']++;        
        if ( $v['approved'] )   $counter['approved']++;
        else                    $counter['pending']++;        
    }
    // </editor-fold>    
    
    
    wpbc_dashboard_widget_css();
                
   ?>        
   <div id="wpbc_dashboard_widget_container" >
       <?php 
       
       wpbc_dashboard_section_statistic( $counter );  
       
       ?><div style="clear:both;margin-bottom:20px;"></div><?php 
       
       wpbc_dashboard_section_version(); 
       
       //wpbc_dashboard_section_support(); 
       
       ?><div style="clear:both;"></div><?php 
       
       //wpbc_dashboard_section_news(); 
       
       
       ?>
       <div style="clear:both;"></div>            
   </div>
   <div style="clear:both;"></div>   
   <?php 
   
   /*
   <div id="modal_content1" style="display:block;width:100%;height:100px;" class="modal_content_text" >
     <iframe src="http://wpbookingcalendar.com/purchase/#content" style="border:1px solid red; width:100%;height:100px;padding:0px;margin:0px;"></iframe>
   </div>
   */
}
    

/** Get Info for Dashboard */
function wpbc_get_dashboard_info() {
    
    ob_start();  
    
    wpbc_dashboard_widget_show();
  
    return ob_get_clean();  
}


/** CSS for Dashboard Widget */
function wpbc_dashboard_widget_css() {
    
    ?><style type="text/css">
        #wpbc_dashboard_widget_container {
            width:100%;
        }
        #wpbc_dashboard_widget_container .wpbc_dashboard_section {
            float:left;
            margin:0px;
            padding:0px;
            width:100%;
        }
        #wpbc_dashboard_widget_container .wpbc_dashboard_section h4 {            
            font-size: 14px;
            font-weight: 600;
            margin: 5px 0 15px;
        }     
        #bk_upgrade_section p {
            font-size: 13px;
            line-height: 1.5em;
            margin: 15px 0 0;
            padding: 0;
        }
        #dashboard-widgets-wrap #wpbc_dashboard_widget_container .wpbc_dashboard_section {
           width:49%;
        }
        #dashboard-widgets-wrap #wpbc_dashboard_widget_container .bk_right {
            float:right
        }
        #dashboard-widgets-wrap #wpbc_dashboard_widget_container .border_orrange, 
        #wpbc_dashboard_widget_container .border_orrange {
            background: #fffaf1 none repeat scroll 0 0;
            border-left: 3px solid #eeab26;
            clear: both;
            margin: 5px 5px 20px;
            padding: 10px 0;
            width: 99%;
        }
        #wpbc_dashboard_widget_container .bk_header {
            color: #555555;
            font-size: 13px;
            font-weight: 600;
            line-height: 1em;
        }
        #wpbc_dashboard_widget_container .bk_table {
            background:transparent;
            border-bottom:none;
            border-top:1px solid #ECECEC;
            margin:6px 0 10px 6px;
            padding:2px 10px;
            width:95%;
            -border-radius:4px;
            -moz-border-radius:4px;
            -webkit-border-radius:4px;
            -moz-box-shadow:0 0 2px #C5C3C3;
            -webkit-box-shadow:0 0 2px #C5C3C3;
            -box-shadow:0 0 2px #C5C3C3;
        }
        #wpbc_dashboard_widget_container .bk_table td{
            border-bottom:1px solid #DDDDDD;
            line-height:19px;
            padding:4px 0px 4px 10px;
            font-size:13px;
        }
        #wpbc_dashboard_widget_container .bk_table tr td.first{
           text-align:center;
           padding:4px 0px;
        }
        #wpbc_dashboard_widget_container .bk_table tr td a {
            text-decoration: none;
        }
        #wpbc_dashboard_widget_container .bk_table tr td a span{
            font-size:18px;
            font-family: Georgia,"Times New Roman","Bitstream Charter",Times,serif;
        }
        #wpbc_dashboard_widget_container .bk_table td.bk_spec_font a{
            font-family: Georgia,"Times New Roman","Bitstream Charter",Times,serif;
            font-size:14px;
        }
        #wpbc_dashboard_widget_container .bk_table td.bk_spec_font {
            font-family: Georgia,"Times New Roman","Bitstream Charter",Times,serif;
            font-size:13px;
        }
        #wpbc_dashboard_widget_container .bk_table td.pending a{
            color:#E66F00;
        }
        #wpbc_dashboard_widget_container .bk_table td.new-bookings a{
            color:red;
        }
        #wpbc_dashboard_widget_container .bk_table td.actual-bookings a{
            color:green;
        }
        #bk_errror_loading {
             text-align: center;
             font-style: italic;
             font-size:11px;
        }
    </style><?php
}


////////////////////////////////////////////////////////////////////////////////
// S e c t i o n s
////////////////////////////////////////////////////////////////////////////////

/** Dashboard Support Section */
function wpbc_dashboard_section_support() {
   /* ?>
    <div class="wpbc_dashboard_section bk_right">
        <span class="bk_header"><?php _e('Support' ,'booking');?>:</span>
        <table class="bk_table">
            <tr class="first">
                <td style="text-align:center;" class="bk_spec_font"><a href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'wpbc-getting-started' ), 'index.php' ) ) ); ?>"
                    ><?php _e('Getting Started' ,'booking');?></a>
                </td>
            </tr>
            <tr>
                <td style="text-align:center;" class="bk_spec_font"><a target="_blank" href="http://wpbookingcalendar.com/help/"><?php _e('Help Info' ,'booking');?></a></td>
            </tr>
            <tr>
                <td style="text-align:center;" class="bk_spec_font"><a target="_blank" href="http://wpbookingcalendar.com/faq/"><?php _e('FAQ' ,'booking');?></a></td>
            </tr>
            <tr>
                <td style="text-align:center;" class="bk_spec_font"><a href="mailto:support@wpbookingcalendar.com"><?php _e('Contact email' ,'booking');?></a></td>
            </tr>                                        
            <tr>
                <td style="text-align:center;" class="bk_spec_font"><a target="_blank" href="https://wordpress.org/plugins/booking/"><?php _e('Rate plugin (thanks:)' ,'booking');?></a></td>
            </tr>
        </table>
    </div>
    <?php */
}


/** Dashboard News Section */
/*function wpbc_dashboard_section_news() { 
    
    wp_nonce_field( 'wpbc_ajax_admin_nonce', "wpbc_admin_panel_nonce_dashboard", true, true );      // Nonce for Ajax
    
    ?>
    <div style="width:95%;border:none;clear:both;margin:10px 0px;" id="bk_news_section">
        <div style="width: 96%;margin-right:0;" >
            <span class="bk_header">Booking Calendar News:</span><br/>
            <div id="bk_news" class="rssSummary"> <span style="font-size:13px;text-align:center;">Loading...</span></div>
            <div id="ajax_bk_respond" class="rssSummary" style="display:none;"></div>
            <script type="text/javascript">
                jQuery.ajax({
                    url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
                    type:'POST',
                    success: function (data, textStatus){ if( textStatus == 'success' ) jQuery('#ajax_bk_respond').html( data ); },
                    error: function (XMLHttpRequest, textStatus, errorThrown){ window.status = 'Ajax sending Error status:' + textStatus; },
                    data:{
                        action: 'CHECK_BK_NEWS',
                        wpbc_nonce: document.getElementById('wpbc_admin_panel_nonce_dashboard').value
                    }
                });
            </script>                           
        </div>
    </div>    
    <?php 
}*/


/** Dashboard Version Section */
function wpbc_dashboard_section_version() {
        
    $version = 'free';
    $version = get_bk_version();
    if ( wpbc_is_this_demo() ) 
        $version = 'free';

    if ( ( $version !== 'free' ) && ( class_exists('wpdev_bk_multiuser') === false ) ) { ?>
        <div class="wpbc_dashboard_section border_orrange" id="bk_upgrade_section"> 
            <div style="padding:0px 10px;width:96%;">
                <h4><?php _e('Upgrade to higher versions' ,'booking') ?>:</h4>
                <p>Check additional advanced functionality, which exist in higher versions and can be interesting for you <a href="http://wpbookingcalendar.com/features/" target="_blank">here &raquo;</a></p>
                <p><a class="button button-primary" style="margin-top: 10px;font-weight: 600;"  href="<?php echo wpbc_up_link(); ?>" target="_blank"><?php if ( wpbc_get_ver_sufix() == '' ) { _e('Purchase' ,'booking'); } else { _e('Upgrade Now' ,'booking'); } ?></a> </p>
            </div>
        </div>
        <div style="clear:both;"></div>
        <?php if ( wpbc_get_ver_sufix() != '' ) { ?>
            <script type="text/javascript">
                jQuery(document).ready(function(){
                    jQuery('#bk_upgrade_section').animate({opacity:1},7000).fadeOut(2000);
                });
            </script>
        <?php } ?>
    <?php } 
    
}


/** Dashboard Statistic Section */
function wpbc_dashboard_section_statistic( $counter ) {
    
    $bk_admin_url = wpbc_get_bookings_url() . '&wh_approved=';
    ?>
    <div class="wpbc_dashboard_section bk_right">
        <span class="bk_header"><?php _e('Statistic' ,'booking');?>:</span>
        <table class="bk_table">
            <tr class="first">
                <td class="first"> <a href="<?php echo $bk_admin_url,'&wh_is_new=1&wh_booking_date=3&view_mode=vm_listing'; ?>"><span class=""><?php echo $counter['new']; ?></span></a> </td>
                <td class=""> <a href="<?php echo $bk_admin_url,'&wh_is_new=1&wh_booking_date=3&view_mode=vm_listing'; ?>"><?php _e('New (unverified) booking(s)' ,'booking');?></a></td>
            </tr>
            <tr>
                <td class="first"> <a href="<?php echo $bk_admin_url,'&wh_approved=0&wh_booking_date=3&view_mode=vm_listing'; ?>"><span class=""><?php echo $counter['pending']; ?></span></a></td>
                <td class="pending"><a href="<?php echo $bk_admin_url,'&wh_approved=0&wh_booking_date=3&view_mode=vm_listing'; ?>" class=""><?php _e('Pending booking(s)' ,'booking');?></a></td>
            </tr>
        </table>
    </div>
    <div class="wpbc_dashboard_section" >
        <span class="bk_header"><?php _e('Agenda' ,'booking');?>:</span>
        <table class="bk_table">
            <tr class="first">
                <td class="first"> <a href="<?php echo $bk_admin_url,'&wh_modification_date=1&wh_booking_date=3&view_mode=vm_listing'; ?>"><span><?php echo $counter['was_made_today']; ?></span></a> </td>
                <td class="new-bookings"><a href="<?php echo $bk_admin_url,'&wh_modification_date=1&wh_booking_date=3&view_mode=vm_listing'; ?>" class=""><?php _e('New booking(s) made today' ,'booking');?></a> </td>
            </tr>
            <tr>
                <td class="first"> <a href="<?php echo $bk_admin_url,'&wh_booking_date=1&view_mode=vm_listing'; ?>"><span><?php echo $counter['booking_today']; ?></span></a> </td>
                <td class="actual-bookings"> <a href="<?php echo $bk_admin_url,'&wh_booking_date=1&view_mode=vm_listing'; ?>" class=""><?php _e('Bookings for today' ,'booking');?></a> </td>
            </tr>
        </table>
    </div>
    <?php
}