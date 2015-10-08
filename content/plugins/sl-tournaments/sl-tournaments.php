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
function CallSLTVAPI($method, $url, $data = false)
{
	$curl = curl_init();
	switch ($method)
	{
		case "POST":
			curl_setopt($curl, CURLOPT_POST, 1);

			if ($data)
				curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
			break;
		case "PUT":
			curl_setopt($curl, CURLOPT_PUT, 1);
			break;
		default:
			if ($data)
				$url = sprintf("%s?%s", $url, http_build_query($data));
	}

	// Optional Authentication:
	//curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	//curl_setopt($curl, CURLOPT_USERPWD, "username:password");

	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

	$result = curl_exec($curl);

	curl_close($curl);

	return $result;
}
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


if( !function_exists("sltv_save_user") ) {
	function sltv_save_user( $user_id ) {
		$user = get_user_by( 'id', $user_id );
		
		$avatar_url = get_avatar_url($user_id);
		$type = pathinfo($avatar_url, PATHINFO_EXTENSION);
		$data = file_get_contents($avatar_url);
		$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

		if ($user) {
			CallSLTVAPI("POST", "http://api.sltv.pro/api/v1/users/". $user_id, array(
				'email' => $user->get("user_email"),
				'nick' => $user->get("user_nicename"),
				'logo' => $base64
			));
		}
	}
}
add_action( 'user_register', 'sltv_save_user', 10, 1 );

if( !function_exists("sltv_update_user") ) {
	function sltv_update_user( $user_id ) {
		$main_data = get_userdata($user_id);
		$custom_user_data = get_user_meta( $user_id, 'custom_user_fields');
		$avatar_url = get_avatar_url($user_id);

		$data_2_send = array(
			'nick'          => $main_data->get('display_name')
		);
		if ( isset( $custom_user_data[0]["country"] ) ) {
			$data_2_send["country"] = $custom_user_data[0]["country"];
		}
		if ( isset($main_data -> $first_name) ) {
			$data_2_send["first_name"] = $main_data -> $first_name;
		}
		if ( isset($main_data -> $last_name) ) {
			$data_2_send["last_name"] = $main_data -> $last_name;
		}
		/*
		if ( isset( $custom_user_data[0]["skype"] ) ) {
			$data_2_send["skype"] = $custom_user_data[0]["skype"];
		}
		*/
		/*
		if ( isset( $custom_user_data[0]["gender"] ) ) {
			$data_2_send["gender"] = $custom_user_data[0]["gender"];
		}

		*/
		if ( isset($avatar_url) ) {
			$type = pathinfo($avatar_url, PATHINFO_EXTENSION);
			$data = file_get_contents($avatar_url);
			$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
			$data_2_send["logo"] = $base64;
		}
		CallSLTVAPI("PUT", "http://api.sltv.pro/api/v1/users/". $user_id, $data_2_send);
	}
}
add_action( 'profile_update', 'sltv_update_user', 10, 2 );

if( !function_exists("add_custom_profile_fields") ) {
	function add_custom_profile_fields( $profileuser ) {
		$customData = get_user_meta( $profileuser->ID, 'custom_user_fields', true );
		?>
		<h2><?php _e( 'Custom User Profile Fields' ); ?></h2>
		<table class="form-table">
			<tr>
				<th><label for="custom_user_fields_skype"><?php _e( 'Skype' ); ?></label></th>
				<td><input type="text" name="custom_user_fields_skype" id="custom_skype"
				           value="<?php if ( isset( $customData['skype'] ) ) {
					           echo esc_attr( $customData['skype'] );
				           } ?>" class="regular-text"/></td>
			</tr>

			<tr>
				<th><label for="custom_user_fields_country"><?php _e( 'Choose country' ); ?></label></th>
				<td>
					<select name="custom_user_fields_country" id="custom_country">
						<option value="">Please Select</option>
						<option value="AF" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "AF" );
						} ?>>Afghanistan
						</option>
						<option value="AX" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "AX" );
						} ?>>Åland Islands
						</option>
						<option value="AL" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "AL" );
						} ?>>Albania
						</option>
						<option value="DZ" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "DZ" );
						} ?>>Algeria
						</option>
						<option value="AS" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "AS" );
						} ?>>American Samoa
						</option>
						<option value="AD" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "AD" );
						} ?>>Andorra
						</option>
						<option value="AO" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "AO" );
						} ?>>Angola
						</option>
						<option value="AI" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "AI" );
						} ?>>Anguilla
						</option>
						<option value="AQ" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "AQ" );
						} ?>>Antarctica
						</option>
						<option value="AG" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "AG" );
						} ?>>Antigua and Barbuda
						</option>
						<option value="AR" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "AR" );
						} ?>>Argentina
						</option>
						<option value="AM" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "AM" );
						} ?>>Armenia
						</option>
						<option value="AW" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "AW" );
						} ?>>Aruba
						</option>
						<option value="AU" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "AU" );
						} ?>>Australia
						</option>
						<option value="AT" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "AT" );
						} ?>>Austria
						</option>
						<option value="AZ" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "AZ" );
						} ?>>Azerbaijan
						</option>
						<option value="BS" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "BS" );
						} ?>>Bahamas
						</option>
						<option value="BH" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "BH" );
						} ?>>Bahrain
						</option>
						<option value="BD" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "BD" );
						} ?>>Bangladesh
						</option>
						<option value="BB" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "BB" );
						} ?>>Barbados
						</option>
						<option value="BY" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "BY" );
						} ?>>Belarus
						</option>
						<option value="BE" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "BE" );
						} ?>>Belgium
						</option>
						<option value="BZ" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "BZ" );
						} ?>>Belize
						</option>
						<option value="BJ" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "BJ" );
						} ?>>Benin
						</option>
						<option value="BM" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "BM" );
						} ?>>Bermuda
						</option>
						<option value="BT" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "BT" );
						} ?>>Bhutan
						</option>
						<option value="BO" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "BO" );
						} ?>>Bolivia, Plurinational State of
						</option>
						<option value="BQ" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "BQ" );
						} ?>>Bonaire, Sint Eustatius and Saba
						</option>
						<option value="BA" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "BA" );
						} ?>>Bosnia and Herzegovina
						</option>
						<option value="BW" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "BW" );
						} ?>>Botswana
						</option>
						<option value="BV" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "BV" );
						} ?>>Bouvet Island
						</option>
						<option value="BR" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "BR" );
						} ?>>Brazil
						</option>
						<option value="IO" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "IO" );
						} ?>>British Indian Ocean Territory
						</option>
						<option value="BN" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "BN" );
						} ?>>Brunei Darussalam
						</option>
						<option value="BG" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "BG" );
						} ?>>Bulgaria
						</option>
						<option value="BF" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "BF" );
						} ?>>Burkina Faso
						</option>
						<option value="BI" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "BI" );
						} ?>>Burundi
						</option>
						<option value="KH" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "KH" );
						} ?>>Cambodia
						</option>
						<option value="CM" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "CM" );
						} ?>>Cameroon
						</option>
						<option value="CA" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "CA" );
						} ?>>Canada
						</option>
						<option value="CV" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "CV" );
						} ?>>Cape Verde
						</option>
						<option value="KY" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "KY" );
						} ?>>Cayman Islands
						</option>
						<option value="CF" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "CF" );
						} ?>>Central African Republic
						</option>
						<option value="TD" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "TD" );
						} ?>>Chad
						</option>
						<option value="CL" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "CL" );
						} ?>>Chile
						</option>
						<option value="CN" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "CN" );
						} ?>>China
						</option>
						<option value="CX" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "CX" );
						} ?>>Christmas Island
						</option>
						<option value="CC" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "CC" );
						} ?>>Cocos (Keeling) Islands
						</option>
						<option value="CO" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "CO" );
						} ?>>Colombia
						</option>
						<option value="KM" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "KM" );
						} ?>>Comoros
						</option>
						<option value="CG" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "CG" );
						} ?>>Congo
						</option>
						<option value="CD" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "CD" );
						} ?>>Congo, the Democratic Republic of the
						</option>
						<option value="CK" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "CK" );
						} ?>>Cook Islands
						</option>
						<option value="CR" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "CR" );
						} ?>>Costa Rica
						</option>
						<option value="CI" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "CI" );
						} ?>>Côte d'Ivoire
						</option>
						<option value="HR" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "HR" );
						} ?>>Croatia
						</option>
						<option value="CU" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "CU" );
						} ?>>Cuba
						</option>
						<option value="CW" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "CW" );
						} ?>>Curaçao
						</option>
						<option value="CY" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "CY" );
						} ?>>Cyprus
						</option>
						<option value="CZ" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "CZ" );
						} ?>>Czech Republic
						</option>
						<option value="DK" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "DK" );
						} ?>>Denmark
						</option>
						<option value="DJ" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "DJ" );
						} ?>>Djibouti
						</option>
						<option value="DM" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "DM" );
						} ?>>Dominica
						</option>
						<option value="DO" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "DO" );
						} ?>>Dominican Republic
						</option>
						<option value="EC" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "EC" );
						} ?>>Ecuador
						</option>
						<option value="EG" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "EG" );
						} ?>>Egypt
						</option>
						<option value="SV" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "SV" );
						} ?>>El Salvador
						</option>
						<option value="GQ" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "GQ" );
						} ?>>Equatorial Guinea
						</option>
						<option value="ER" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "ER" );
						} ?>>Eritrea
						</option>
						<option value="EE" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "EE" );
						} ?>>Estonia
						</option>
						<option value="ET" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "ET" );
						} ?>>Ethiopia
						</option>
						<option value="FK" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "FK" );
						} ?>>Falkland Islands (Malvinas)
						</option>
						<option value="FO" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "FO" );
						} ?>>Faroe Islands
						</option>
						<option value="FJ" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "FJ" );
						} ?>>Fiji
						</option>
						<option value="FI" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "FI" );
						} ?>>Finland
						</option>
						<option value="FR" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "FR" );
						} ?>>France
						</option>
						<option value="GF" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "GF" );
						} ?>>French Guiana
						</option>
						<option value="PF" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "PF" );
						} ?>>French Polynesia
						</option>
						<option value="TF" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "TF" );
						} ?>>French Southern Territories
						</option>
						<option value="GA" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "GA" );
						} ?>>Gabon
						</option>
						<option value="GM" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "GM" );
						} ?>>Gambia
						</option>
						<option value="GE" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "GE" );
						} ?>>Georgia
						</option>
						<option value="DE" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "DE" );
						} ?>>Germany
						</option>
						<option value="GH" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "GH" );
						} ?>>Ghana
						</option>
						<option value="GI" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "GI" );
						} ?>>Gibraltar
						</option>
						<option value="GR" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "GR" );
						} ?>>Greece
						</option>
						<option value="GL" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "GL" );
						} ?>>Greenland
						</option>
						<option value="GD" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "GD" );
						} ?>>Grenada
						</option>
						<option value="GP" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "GP" );
						} ?>>Guadeloupe
						</option>
						<option value="GU" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "GU" );
						} ?>>Guam
						</option>
						<option value="GT" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "GT" );
						} ?>>Guatemala
						</option>
						<option value="GG" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "GG" );
						} ?>>Guernsey
						</option>
						<option value="GN" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "GN" );
						} ?>>Guinea
						</option>
						<option value="GW" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "GW" );
						} ?>>Guinea-Bissau
						</option>
						<option value="GY" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "GY" );
						} ?>>Guyana
						</option>
						<option value="HT" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "HT" );
						} ?>>Haiti
						</option>
						<option value="HM" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "HM" );
						} ?>>Heard Island and McDonald Islands
						</option>
						<option value="VA" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "VA" );
						} ?>>Holy See (Vatican City State)
						</option>
						<option value="HN" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "HN" );
						} ?>>Honduras
						</option>
						<option value="HK" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "HK" );
						} ?>>Hong Kong
						</option>
						<option value="HU" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "HU" );
						} ?>>Hungary
						</option>
						<option value="IS" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "IS" );
						} ?>>Iceland
						</option>
						<option value="IN" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "IN" );
						} ?>>India
						</option>
						<option value="ID" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "ID" );
						} ?>>Indonesia
						</option>
						<option value="IR" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "IR" );
						} ?>>Iran, Islamic Republic of
						</option>
						<option value="IQ" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "IQ" );
						} ?>>Iraq
						</option>
						<option value="IE" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "IE" );
						} ?>>Ireland
						</option>
						<option value="IM" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "IM" );
						} ?>>Isle of Man
						</option>
						<option value="IL" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "IL" );
						} ?>>Israel
						</option>
						<option value="IT" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "IT" );
						} ?>>Italy
						</option>
						<option value="JM" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "JM" );
						} ?>>Jamaica
						</option>
						<option value="JP" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "JP" );
						} ?>>Japan
						</option>
						<option value="JE" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "JE" );
						} ?>>Jersey
						</option>
						<option value="JO" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "JO" );
						} ?>>Jordan
						</option>
						<option value="KZ" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "KZ" );
						} ?>>Kazakhstan
						</option>
						<option value="KE" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "KE" );
						} ?>>Kenya
						</option>
						<option value="KI" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "KI" );
						} ?>>Kiribati
						</option>
						<option value="KP" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "KP" );
						} ?>>Korea, Democratic People's Republic of
						</option>
						<option value="KR" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "KR" );
						} ?>>Korea, Republic of
						</option>
						<option value="KW" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "KW" );
						} ?>>Kuwait
						</option>
						<option value="KG" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "KG" );
						} ?>>Kyrgyzstan
						</option>
						<option value="LA" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "LA" );
						} ?>>Lao People's Democratic Republic
						</option>
						<option value="LV" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "LV" );
						} ?>>Latvia
						</option>
						<option value="LB" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "LB" );
						} ?>>Lebanon
						</option>
						<option value="LS" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "LS" );
						} ?>>Lesotho
						</option>
						<option value="LR" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "LR" );
						} ?>>Liberia
						</option>
						<option value="LY" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "LY" );
						} ?>>Libya
						</option>
						<option value="LI" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "LI" );
						} ?>>Liechtenstein
						</option>
						<option value="LT" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "LT" );
						} ?>>Lithuania
						</option>
						<option value="LU" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "LU" );
						} ?>>Luxembourg
						</option>
						<option value="MO" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "MO" );
						} ?>>Macao
						</option>
						<option value="MK" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "MK" );
						} ?>>Macedonia, the former Yugoslav Republic of
						</option>
						<option value="MG" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "MG" );
						} ?>>Madagascar
						</option>
						<option value="MW" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "MW" );
						} ?>>Malawi
						</option>
						<option value="MY" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "MY" );
						} ?>>Malaysia
						</option>
						<option value="MV" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "MV" );
						} ?>>Maldives
						</option>
						<option value="ML" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "ML" );
						} ?>>Mali
						</option>
						<option value="MT" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "MT" );
						} ?>>Malta
						</option>
						<option value="MH" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "MH" );
						} ?>>Marshall Islands
						</option>
						<option value="MQ" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "MQ" );
						} ?>>Martinique
						</option>
						<option value="MR" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "MR" );
						} ?>>Mauritania
						</option>
						<option value="MU" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "MU" );
						} ?>>Mauritius
						</option>
						<option value="YT" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "YT" );
						} ?>>Mayotte
						</option>
						<option value="MX" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "MX" );
						} ?>>Mexico
						</option>
						<option value="FM" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "FM" );
						} ?>>Micronesia, Federated States of
						</option>
						<option value="MD" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "MD" );
						} ?>>Moldova, Republic of
						</option>
						<option value="MC" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "MC" );
						} ?>>Monaco
						</option>
						<option value="MN" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "MN" );
						} ?>>Mongolia
						</option>
						<option value="ME" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "ME" );
						} ?>>Montenegro
						</option>
						<option value="MS" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "MS" );
						} ?>>Montserrat
						</option>
						<option value="MA" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "MA" );
						} ?>>Morocco
						</option>
						<option value="MZ" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "MZ" );
						} ?>>Mozambique
						</option>
						<option value="MM" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "MM" );
						} ?>>Myanmar
						</option>
						<option value="NA" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "NA" );
						} ?>>Namibia
						</option>
						<option value="NR" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "NR" );
						} ?>>Nauru
						</option>
						<option value="NP" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "NP" );
						} ?>>Nepal
						</option>
						<option value="NL" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "NL" );
						} ?>>Netherlands
						</option>
						<option value="NC" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "NC" );
						} ?>>New Caledonia
						</option>
						<option value="NZ" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "NZ" );
						} ?>>New Zealand
						</option>
						<option value="NI" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "NI" );
						} ?>>Nicaragua
						</option>
						<option value="NE" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "NE" );
						} ?>>Niger
						</option>
						<option value="NG" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "NG" );
						} ?>>Nigeria
						</option>
						<option value="NU" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "NU" );
						} ?>>Niue
						</option>
						<option value="NF" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "NF" );
						} ?>>Norfolk Island
						</option>
						<option value="MP" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "MP" );
						} ?>>Northern Mariana Islands
						</option>
						<option value="NO" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "NO" );
						} ?>>Norway
						</option>
						<option value="OM" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "OM" );
						} ?>>Oman
						</option>
						<option value="PK" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "PK" );
						} ?>>Pakistan
						</option>
						<option value="PW" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "PW" );
						} ?>>Palau
						</option>
						<option value="PS" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "PS" );
						} ?>>Palestinian Territory, Occupied
						</option>
						<option value="PA" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "PA" );
						} ?>>Panama
						</option>
						<option value="PG" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "PG" );
						} ?>>Papua New Guinea
						</option>
						<option value="PY" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "PY" );
						} ?>>Paraguay
						</option>
						<option value="PE" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "PE" );
						} ?>>Peru
						</option>
						<option value="PH" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "PH" );
						} ?>>Philippines
						</option>
						<option value="PN" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "PN" );
						} ?>>Pitcairn
						</option>
						<option value="PL" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "PL" );
						} ?>>Poland
						</option>
						<option value="PT" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "PT" );
						} ?>>Portugal
						</option>
						<option value="PR" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "PR" );
						} ?>>Puerto Rico
						</option>
						<option value="QA" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "QA" );
						} ?>>Qatar
						</option>
						<option value="RE" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "RE" );
						} ?>>Réunion
						</option>
						<option value="RO" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "RO" );
						} ?>>Romania
						</option>
						<option value="RU" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "RU" );
						} ?>>Russian Federation
						</option>
						<option value="RW" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "RW" );
						} ?>>Rwanda
						</option>
						<option value="BL" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "BL" );
						} ?>>Saint Barthélemy
						</option>
						<option value="SH" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "SH" );
						} ?>>Saint Helena, Ascension and Tristan da Cunha
						</option>
						<option value="KN" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "KN" );
						} ?>>Saint Kitts and Nevis
						</option>
						<option value="LC" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "LC" );
						} ?>>Saint Lucia
						</option>
						<option value="MF" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "MF" );
						} ?>>Saint Martin (French part)
						</option>
						<option value="PM" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "PM" );
						} ?>>Saint Pierre and Miquelon
						</option>
						<option value="VC" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "VC" );
						} ?>>Saint Vincent and the Grenadines
						</option>
						<option value="WS" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "WS" );
						} ?>>Samoa
						</option>
						<option value="SM" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "SM" );
						} ?>>San Marino
						</option>
						<option value="ST" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "ST" );
						} ?>>Sao Tome and Principe
						</option>
						<option value="SA" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "SA" );
						} ?>>Saudi Arabia
						</option>
						<option value="SN" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "SN" );
						} ?>>Senegal
						</option>
						<option value="RS" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "RS" );
						} ?>>Serbia
						</option>
						<option value="SC" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "SC" );
						} ?>>Seychelles
						</option>
						<option value="SL" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "SL" );
						} ?>>Sierra Leone
						</option>
						<option value="SG" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "SG" );
						} ?>>Singapore
						</option>
						<option value="SX" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "SX" );
						} ?>>Sint Maarten (Dutch part)
						</option>
						<option value="SK" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "SK" );
						} ?>>Slovakia
						</option>
						<option value="SI" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "SI" );
						} ?>>Slovenia
						</option>
						<option value="SB" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "SB" );
						} ?>>Solomon Islands
						</option>
						<option value="SO" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "SO" );
						} ?>>Somalia
						</option>
						<option value="ZA" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "ZA" );
						} ?>>South Africa
						</option>
						<option value="GS" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "GS" );
						} ?>>South Georgia and the South Sandwich Islands
						</option>
						<option value="SS" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "SS" );
						} ?>>South Sudan
						</option>
						<option value="ES" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "ES" );
						} ?>>Spain
						</option>
						<option value="LK" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "LK" );
						} ?>>Sri Lanka
						</option>
						<option value="SD" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "SD" );
						} ?>>Sudan
						</option>
						<option value="SR" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "SR" );
						} ?>>Suriname
						</option>
						<option value="SJ" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "SJ" );
						} ?>>Svalbard and Jan Mayen
						</option>
						<option value="SZ" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "SZ" );
						} ?>>Swaziland
						</option>
						<option value="SE" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "SE" );
						} ?>>Sweden
						</option>
						<option value="CH" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "CH" );
						} ?>>Switzerland
						</option>
						<option value="SY" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "SY" );
						} ?>>Syrian Arab Republic
						</option>
						<option value="TW" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "TW" );
						} ?>>Taiwan, Province of China
						</option>
						<option value="TJ" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "TJ" );
						} ?>>Tajikistan
						</option>
						<option value="TZ" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "TZ" );
						} ?>>Tanzania, United Republic of
						</option>
						<option value="TH" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "TH" );
						} ?>>Thailand
						</option>
						<option value="TL" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "TL" );
						} ?>>Timor-Leste
						</option>
						<option value="TG" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "TG" );
						} ?>>Togo
						</option>
						<option value="TK" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "TK" );
						} ?>>Tokelau
						</option>
						<option value="TO" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "TO" );
						} ?>>Tonga
						</option>
						<option value="TT" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "TT" );
						} ?>>Trinidad and Tobago
						</option>
						<option value="TN" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "TN" );
						} ?>>Tunisia
						</option>
						<option value="TR" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "TR" );
						} ?>>Turkey
						</option>
						<option value="TM" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "TM" );
						} ?>>Turkmenistan
						</option>
						<option value="TC" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "TC" );
						} ?>>Turks and Caicos Islands
						</option>
						<option value="TV" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "TV" );
						} ?>>Tuvalu
						</option>
						<option value="UG" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "UG" );
						} ?>>Uganda
						</option>
						<option value="UA" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "UA" );
						} ?>>Ukraine
						</option>
						<option value="AE" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "AE" );
						} ?>>United Arab Emirates
						</option>
						<option value="GB" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "GB" );
						} ?>>United Kingdom
						</option>
						<option value="US" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "US" );
						} ?>>United States
						</option>
						<option value="UM" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "UM" );
						} ?>>United States Minor Outlying Islands
						</option>
						<option value="UY" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "UY" );
						} ?>>Uruguay
						</option>
						<option value="UZ" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "UZ" );
						} ?>>Uzbekistan
						</option>
						<option value="VU" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "VU" );
						} ?>>Vanuatu
						</option>
						<option value="VE" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "VE" );
						} ?>>Venezuela, Bolivarian Republic of
						</option>
						<option value="VN" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "VN" );
						} ?>>Viet Nam
						</option>
						<option value="VG" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "VG" );
						} ?>>Virgin Islands, British
						</option>
						<option value="VI" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "VI" );
						} ?>>Virgin Islands, U.S.
						</option>
						<option value="WF" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "WF" );
						} ?>>Wallis and Futuna
						</option>
						<option value="EH" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "EH" );
						} ?>>Western Sahara
						</option>
						<option value="YE" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "YE" );
						} ?>>Yemen
						</option>
						<option value="ZM" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "ZM" );
						} ?>>Zambia
						</option>
						<option value="ZW" <?php if ( isset( $customData['country'] ) ) {
							selected( $customData['country'], "ZW" );
						} ?>>Zimbabwe
						</option>
					</select>
				</td>
			</tr>
			<tr>
				<th><label for="custom_user_fields_gender"><?php _e( 'Gender' ); ?></label></th>
				<td>
					<select name="custom_user_fields_gender" id="custom_gender">
						<option value="">Please Select</option>
						<option value="male" <?php if ( isset( $customData['gender'] ) ) {
							selected( $customData['gender'], "male" );
						} ?>>Male
						</option>
						<option value="female" <?php if ( isset( $customData['gender'] ) ) {
							selected( $customData['gender'], "female" );
						} ?>>Female
						</option>
					</select>
				</td>
			</tr>
		</table>
		<?php
	}
}
if( !function_exists("update_custom_profile") ) {
	function update_custom_profile( $user_id ) {
		$userData = array();

		if ( ! empty( $_POST['custom_user_fields_skype'] ) ) {
			$userData['skype'] = sanitize_text_field( $_POST['custom_user_fields_skype'] );
		}

		if ( ! empty( $_POST['custom_user_fields_country'] ) ) {
			$userData['country'] = sanitize_text_field( $_POST['custom_user_fields_country'] );
		}

		if ( ! empty( $_POST['custom_user_fields_gender'] ) ) {
			$userData['gender'] = sanitize_text_field( $_POST['custom_user_fields_gender'] );
		}

		if ( ! empty( $userData ) ) {
			update_user_meta( $user_id, 'custom_user_fields', $userData );
		}
	}
}
add_action( 'personal_options_update', 'update_custom_profile' );
add_action( 'edit_user_profile_update', 'update_custom_profile' );
add_action('show_user_profile', 'add_custom_profile_fields');
add_action('edit_user_profile', 'add_custom_profile_fields');