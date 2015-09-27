<?php
/**
 * Plugin Name: SL Tournaments
 * Plugin URI: http://starladder.tv
 * Description: Tournaments and no shit
 * Version: 1.0.0
 * Author: Serhii Kovalenko
 * Author URI: http://starladder.tv
 * License: GPL2
 */
defined( 'ABSPATH' ) or die( 'Plugin file cannot be accessed directly.' );

if( !function_exists("sl_tournaments") ) {
	function sl_tournaments($content) {
		$tids = explode(",", get_option('sl_tournaments_info'));
		for ($i = 0; $i < count($tids); $i++) {
			$tid = $tids[$i];
			$ch = curl_init("http://frontendmag.com/rest.php?tid=" . $tid);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			$data = curl_exec($ch);
			curl_close($ch);

			echo $data;
	    	$data = json_decode($data);	

	    	?>
			<!--
	    	<div class="panel panel-default">
			  <div class="panel-heading"><h4><?php echo $data->Name; ?></h4></div>
			  <div class="panel-body">
			  <div class="pull-left">
			  	<span class="btn btn-sm btn-primary" type="button"><?php echo $data->status; ?></span>
			    
			  </div>
			  <div class="pull-right">
			  	<span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> <?php echo $data->date; ?> 
			  	<span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> <?php echo $data->date; ?>
			  </div>  
			  </div>
			</div>
			-->
	    	<?php  
		}	
	}
	add_action('show_tournaments', 'sl_tournaments');
}
add_action( 'admin_menu', 'sl_tournaments_info_menu' );

if( !function_exists("sl_tournaments_info_menu") ) {
	function sl_tournaments_info_menu() {

	  $page_title = 'Starladder Tournaments';
	  $menu_title = 'Tournaments';
	  $capability = 'manage_options';
	  $menu_slug  = 'sl-tournaments';
	  $function   = 'sl_tournaments_info_page';
	  $icon_url   = 'dashicons-media-code';
	  $position   = 4;

	  add_menu_page( $page_title,
	                 $menu_title, 
	                 $capability, 
	                 $menu_slug, 
	                 $function, 
	                 $icon_url, 
	                 $position );
	  add_action( 'admin_init', 'update_sl_tournaments_info' );
	}
}
if( !function_exists("sl_tournaments_info_page") ) {
	function sl_tournaments_info_page(){
?>
  <h1>Starladder Tournaments</h1>
  <form method="post" action="options.php">
    <?php settings_fields( 'sl-tournaments-settings' ); ?>
    <?php do_settings_sections( 'sl-tournaments-settings' ); ?>
    <table class="form-table">
      <tr valign="top">
      <th scope="row">Extra post info:</th>
      <td><input type="text" name="sl_tournaments_info" value="<?php echo get_option('sl_tournaments_info'); ?>"/></td>
      </tr>
    </table>
    <?php submit_button(); ?>
  </form>

<?php
	}
}

if( !function_exists("update_sl_tournaments_info") ) {
	function update_sl_tournaments_info() {
	  register_setting( 'sl-tournaments-settings', 'sl_tournaments_info' );
	}
}