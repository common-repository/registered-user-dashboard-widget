<?php
/**

 * Plugin Name:       Registered User Dashboard Widget
 * Plugin URI:        https://outsourcingvn.com/
 * Description:       Show users registered by graph
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            OutsourcingVN 
 * Author URI:        https://outsourcingvn.com/contact-us/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       registered-user-dashboard-widget
 * Domain Path:       /languages
 */

function rudw_enqueue_scripts() {
    wp_enqueue_script( 'wpdocs-js', 'https://canvasjs.com/assets/script/canvasjs.min.js' );
    wp_enqueue_script('jquery');
    
    
}
add_action( 'admin_enqueue_scripts', 'rudw_enqueue_scripts');

function rudw_add_dashboard_widgets(){
    wp_add_dashboard_widget(
        'rudw_dashboard_widget',
        esc_html__( ' Users ', ' rudw ' ),
        'rudw_dashboard_widget_render'
        
    );
}
add_action('wp_dashboard_setup','rudw_add_dashboard_widgets');

function rudw_dashboard_widget_render(){
    echo "<script>
    jQuery(document).ready(function($){
        $('p').hide('fast');
        $('button').click(function(){
            $('#chartContainer').toggle();
            $('p').toggle();
        });
    });
      </script>";
    echo "<button><span class='dashicons dashicons-editor-ul'></span></button>";   
    
        $users = get_users();
        $dem = array();
        $dataPoints = [];
        foreach( $users as $user ) {
            $udata = get_userdata( $user->ID );
            $registered = $udata->user_registered;
            $year = date("Y", strtotime($registered));
            $month = date("F", strtotime($registered));
            $dem[$year . '-'.$month]++;  
        }
foreach($dem as $x => $x_value) {
    echo "<p>".esc_html($x).' has '. esc_html($x_value).' users'."</p>";
    array_push($dataPoints,['y' => $x_value, 'label' => $x]);
}
       
echo "<script>
window.onload = function () {
    var chart = new CanvasJS.Chart('chartContainer', {
        animationEnabled: true,
        theme: 'light2', // 'light1', 'light2', 'dark1', 'dark2'
        title:{
            text: 'USERS REGISTERED MONTHLY'
        },
        axisY: {
            title: 'USERS REGISTERED'
        },
        data: [{        
            type: 'column',  
            showInLegend: true, 
            legendMarkerColor: 'grey',
            legendText: 'MONTHLY',
            dataPoints:".  json_encode($dataPoints, JSON_NUMERIC_CHECK)."
        }]
    });
    chart.render();
    }
    </script>";
     echo "<div id='chartContainer' style='height: 300px; width: 100%;'></div>";     
}



