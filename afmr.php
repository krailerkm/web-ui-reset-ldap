<?php
# Print Head - for HTML Page
echo "
<!DOCTYPE html>
<html>
<head>
<title>AFMR PAGE RESET PASSWORD</title>
<link rel='shortcut icon' href='/images/icon/favicon.ico'>
</head>
<body>
<table width='470' border='0' align='center' cellpadding='0' cellspacing='0' style='border:1px solid lightgrey; padding-left:5px; padding-right:5px; padding-bottom:10px'>
<tbody>
<tr>
<td style='padding-top:0px'><img data-imagetype='External' src='https://ldap1.meelab.th.com/images/email/email_header_logo_password.png' width='538' height='65' alt='meelab'>
</td>
<tr>
<td>
<br>
<div align='center' style='color:#949494; font-weight:bold'>";
# Stage 2 - for submit step first time code
if((isset($_POST["email"]) > isset($_POST["code"])) && is_null($_POST["pass1"]) && is_null($_POST["pass2"])){
	# Debug Stage 2
	#echo "::::::::Stage 2::::::::<br>";
	# Set ldap function connecttion
	ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
	ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);
	# Debug email receive
	#echo $_POST["email"]."<br>";
	# Gen text code
	$rawcode = date("Y-m-d h:i:s");
	# Debug text code
	#echo $rawcode."<br>";
	# Encode text code
	$encoderaw = base64_encode($rawcode);
	# Debug encode test
	#echo $encoderaw."<br>";
	# Cut last 2 or == char from code encode string
	$showencoderaw = substr($encoderaw, 0, -2);
	# Debug cut 2 char from code encode
	#echo $showencoderaw."<br>";
	# Set ldap server
	$adServer = "ldap1.meelab.th.com";
	# Set ldap user name
	$ldaprdn = "Y249bGRhcGFkbSxkYz1rc2MsZGM9bmV0";
	# Set ldap password
	$enpass = "U3lzQGRtaW4tMjAxOA==";
	# Set post email to string
	$email = $_POST['email'];
	# Sprit name and domain from @
	$domain = explode("@", $email);
	# Debug sprit name and domain
	#echo $domain[0]."<br>";
	#echo $domain[1]."<br>";
	# Connect ldap
	if($ldapconn = ldap_connect($adServer)){
		# Debug connect ldap done
		#echo "LDAP connect successful... <br>";
		# Bind ldap
		if(ldap_bind($ldapconn, base64_decode($ldaprdn), base64_decode($enpass))) {
			# Debug bind ldap
			#echo "LDAP bind successful... <br>";
			# If email in @meelab.net
			# Search ldap
			$sr = ldap_search($ldapconn, "uid=".$domain[0].",dc=meelab,dc=net", "(objectclass=*)");
			# Debug search ldap
			#echo $sr."<br>";
			# First entry ldap
			$entry = ldap_first_entry($ldapconn, $sr);
			# Debug first entry ldap
			#echo $entry."<br>";
			# Get values ldap
			$values = ldap_get_values($ldapconn, $entry, "uid");
			# Debug get values ldap
			#echo $values[0]."<br>";
			# Get exsit user on ldap
			if(isset($email) && ($domain[1] == "meelab.net") && ($domain[0] == $values[0])) {
				# Debug email in meelab.net
				#echo "Have doamin meelab.net <br>";
				# Set user from name sprit
				$dn = "uid=".$domain[0].",dc=meelab,dc=net";
				# Set parameter
				$newEntry = array('description' => $encoderaw );
				# Mod ldap
				if(ldap_mod_replace($ldapconn, $dn, $newEntry)){
					# Debug mod ldap
					#echo "LDAP update succeded... <br>";
					echo "<div>Guard code generated, Please check you email</div><br>";
					# Set for send email to
					$to = $email;
					# Set subject email
					$subject = "Your Login AFMR meelab Account: Access from web";
					# Set message email
					$message = "
					<!DOCTYPE html>
					<html>
					<head>
					<title>AFMR EMAIL PAGE</title>
					<link rel='shortcut icon' href='/images/icon/favicon.ico'>
					</head>
					<body>
					<tbody>
					<tr>
					<td>
					<table width='470' border='0' align='center' cellpadding='0' cellspacing='0' style='border:1px solid lightgrey; padding-left:5px; padding-right:5px; padding-bottom:10px'>
					<tbody>
					<tr>
					<td style='padding-top:0px'><img data-imagetype='External' src='https://image.ibb.co/cwCVnS/email_header_logo.png' width='538' height='65' alt='meelab'>
					</td>
					</tr>
					<tr>
					<td style='padding-top:32px'><span style='padding-top: 16px; padding-bottom: 16px; font-size: 24px; color: #000000; font-family: Arial, Helvetica, sans-serif, serif, EmojiFont; font-weight: bold;'>Dear ".$domain[0].", </span><br>
					</td>
					</tr>
					<tr>
					<td style='padding-top:12px'><span style='font-size: 17px; color: #949494; font-family: Arial, Helvetica, sans-serif, serif, EmojiFont; font-weight: bold;'>
					<p>Here is AFMR Guard code you need to login to account ".$domain[0].":</p>
					</span></td>
					</tr>
					<tr>
					<td bgcolor='#99CCFF'>
					<div align='center'><span style='font-size: 24px; color: #000000; font-family: Arial, Helvetica, sans-serif, serif, EmojiFont; font-weight: bold;'>".$showencoderaw."</span> </div>
					</td>
					</tr>
					<tr>
					<td style='padding:20px; font-size:12px; line-height:17px; color:#c6d4df; font-family:Arial,Helvetica,sans-serif'>
					<p style='padding-bottom:10px; color:#949494'>This email was generated for reset account authentication firewall and mail relay or AFMR because of a login attempt from a web <a href='https://www.meelab.net/th/customerservice-faq.aspx' target='_blank' rel='noopener noreferrer' style='color:#999999'>located at ".$_SERVER['REMOTE_ADDR']."</a> The login attempt included your correct account name and password.</p>
					<p style='padding-bottom:10px; color:#949494'>The Guard code is required to complete the login. <span style='color:#949494; font-weight:bold'>No one can access your account without also accessing this email.</span></p>
					<p style='padding-bottom:10px; color:#949494'><span style='color:#949494; font-weight:bold'>If you are not attempting to login</span> then please change your meelab password, and consider changing your email password as well to ensure your account security.</p>
					<p style='padding-top:10px; color:#949494'>If you are unable to access your account then <a href='https://www.meelab.net/th/customerservice-outage.aspx' target='_blank' rel='noopener noreferrer' style='color:#999999'>use this link report admin recovery specific account</a> for assistance recovering your account.</p>
					</td>
					</tr>
					<tr>
					<td style='font-size:12px; color:#9b9b9b; padding-top:16px; padding-bottom:60px'>meelab Sysadmin Team<br>
					<a href='mailto:sysadmin@meelab.net' target='_top' style='color:#9b9b9b'>sysadmin@meelab.net</a><br>
					</td>
					</tr>
					</tbody>
					</table>
					</td>
					</tr>
					<tr>
					<td>
					<table width='460' height='55' border='0' align='center' cellpadding='0' cellspacing='0'>
					<tbody>
					<tr valign='top'>
					<td width='110'><a href='https://www.meelab.net' target='_blank' rel='noopener noreferrer' style=''><img data-imagetype='External' src='https://image.ibb.co/eZnfnS/meelab_logo.jpg' alt='meelab' width='92' height='52' hspace='0' vspace='0' border='0' align='top'></a></td>
					<td width='350' valign='top'><span style='color: #999999; font-size: 9px; font-family: Verdana, Arial, Helvetica, sans-serif, serif, EmojiFont;'>Copyright � 2018 meelab Commercial Internet Co., Ltd. All rights reserved and trademarks are property of their respective owners in the TH.</span> </td>
					</tr>
					</tbody>
					</table>
					</td>
					</tr>
					</tbody>
					</body>
					</html>
					";
					# Set header email
					$headers = "MIME-Version: 1.0" . "\r\n";
					$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
					$headers .= 'From: <noreply-afmr@cloud.meelab.net>' . "\r\n";
					# Send email
					if(mail($to,$subject,$message,$headers)) {
						# Debug send email
						#echo "Email send succeded... <br>";
					}
					# Can not send email
					else {
						# Debug can not send email
						echo "<div style='color:#FF5330; font-weight:bold'>Email send failed...</div><br>";
					}
					# Print Page 2 - submmit code
					echo "
					<form action='#code' method='POST'>
						<div><label for='code'>Code: </label>
						<input id='code' type='text' name='code' /></div><br>
						<div><label for='email'>E-mail: </label>
						<input id='email' type='text' name='email' value=".$_POST["email"]." readonly /></div><br>
						<div><input type='submit' name='submit' value='Submit' /></div><br>
					</form>";
				}
				# Can not mod ldap
				else {
					# Debug can not mod ldap
					echo "<div style='color:#FF5330; font-weight:bold'>*LDAP update failed...</div><br>";
				}
			}
			# If email not in @meelab.net
			else {
				# Debug email not in @ meelab.net
				echo "<div style='color:#FF5330; font-weight:bold'>*Not use domain meelab.net or user not exist in system.</div><br>";
				# Print relad
				echo "
				<a href='https://ldap1.meelab.th.com/index.html'>
					<input type='button' value='Reload' /><br>
				</a><br>";
			}
		}
		# Can not bing ldap
		else{
			# Debug can not bind ldap
			#echo "LDAP bind failed... <br>";
		}
	}
	# Can not connect ldap
	else {
		# Debug can not connect ldap
		echo "<div style='color:#FF5330; font-weight:bold'>*LDAP connect failed...</div><br>";
	}
}
# Stage 1.1 - check password
elseif(isset($_POST["checkpasswd"])) {
	# Dedig checkpasswd
	#echo $_POST["checkpasswd"]."<br>";
	# Print Page 1.1 - submmit password
	echo "
	<div>Please fill information for verification password.</div><br>
	<form action='#checkpasswd' method='POST'>
		<div><label for='usernamepassck'>E-mail: </label>
		<input id='usernamepassck' type='text' name='usernamepassck' /></div><br>
		<div><label for='passck'>Password: </label>
		<input id='passck' type='password' name='passck' /></div><br>
		<div><input type='submit' name='submit' value='Verify' /></div><br>
	</form>";
}
# Stage 1.2 - verify password
elseif(isset($_POST["usernamepassck"]) && isset($_POST["passck"])) {
	# Debug usernamepassck
	#echo $_POST["usernamepassck"]."<br>";
	# Debug passck
	#echo $_POST["passck"]."<br>";
	# Check username or password empty
	if(empty($_POST["usernamepassck"]) && empty($_POST["passck"])){
		# Debug Check username or password empty
		echo "<div style='color:#FF5330; font-weight:bold'>*E-mail & Password is empty, Please try again.</div><br>";
		# Print relad
		echo "
		<a href='https://ldap1.meelab.th.com/index.html'>
			<input type='button' value='Reload' /><br>
		</a><br>";
	}
	# Check username rmpty
	elseif(empty($_POST["usernamepassck"])){
		# Debug Check username empty
		echo "<div style='color:#FF5330; font-weight:bold'>*E-mail, Please try again.</div><br>";
		# Print relad
		echo "
		<a href='https://ldap1.meelab.th.com/index.html'>
			<input type='button' value='Reload' /><br>
		</a><br>";
	}
	# Check password rmpty
	elseif(empty($_POST["passck"])){
		# Debug Check password empty
		echo "<div style='color:#FF5330; font-weight:bold'>*Password is empty, Please try again.</div><br>";
		# Print relad
		echo "
		<a href='https://ldap1.meelab.th.com/index.html'>
			<input type='button' value='Reload' /><br>
		</a><br>";
	}
	# verify password running
	else{
		# Debug verify password running
		#echo "Other";
		# Set ldap function connecttion
		ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);
		# Set ldap server
		$adServer = "ldap1.meelab.th.com";
		# Set ldap username
		$ldaprdn = "Y249bGRhcGFkbSxkYz1rc2MsZGM9bmV0";
		# Set ldap password
		$enpass = "U3lzQGRtaW4tMjAxOA==";
		# Set usernamepassck
		$usernamepassck = $_POST["usernamepassck"];
		# Set usernamepassck
		$passck = $_POST["passck"];
		# Sprit name and domain from email by @
		$domain = explode("@", $usernamepassck);
		# Connect ldap
		if($ldapconn = ldap_connect($adServer)) {
			# Debug connect ldap done
			#echo "LDAP connect successful... <br>";
			# Search ldap
			$sr = ldap_search($ldapconn, "uid=".$domain[0].",dc=meelab,dc=net", "(objectclass=*)");
			# Debug search ldap
			#echo $sr."<br>";
			# First entry ldap
			$entry = ldap_first_entry($ldapconn, $sr);
			# Debug first entry ldap
			#echo $entry."<br>";
			# Get values ldap
			$values = ldap_get_values($ldapconn, $entry, "userPassword");
			# Debug get values ldap
			#echo $values[0]."<br>";
			# Debug md5 in put
			#echo "{MD5}".base64_encode(pack("H*",md5($passck)))."<br>";
			# verify password matching
			if($values[0] == "{MD5}".base64_encode(pack("H*",md5($passck)))){
				# Debug password matching
				echo "<div style='color:#33CC33; font-weight:bold'>Password correct.</div><br>";
				# Print relad
				echo "
				<a href='https://ldap1.meelab.th.com/index.html'>
					<input type='button' value='Reload' /><br>
				</a><br>";
			}
			# verify password not matching
			else{
				# Debug password not matching
				echo "<div style='color:#FF5330; font-weight:bold'>*E-mail not exist or Password incorrect.</div><br>";
				# Print relad
				echo "
				<a href='https://ldap1.meelab.th.com/index.html'>
					<input type='button' value='Reload' /><br>
				</a><br>";
			}
		}
		else{
			# Debug can not connect ldap
			echo "<div style='color:#FF5330; font-weight:bold'>*LDAP connect failed...</div><br>";
		}
	}
}
# Stage 3 - for submit required gard code to email
elseif(isset($_POST["email"]) && isset($_POST["code"]) && is_null($_POST["pass1"]) && is_null($_POST["pass2"])){
	# Debug Stage 3
	#echo "::::::::Stage 3::::::::<br>";
	# Debug email
	#echo $_POST["email"]."<br>";
	# Debug code
	#echo $_POST["code"]."<br>";
	# Set ldap function connecttion
	ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
	ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);
	# Set ldap server
	$adServer = "ldap1.meelab.th.com";
	# Set ldap username
	$ldaprdn = "Y249bGRhcGFkbSxkYz1rc2MsZGM9bmV0";
	# Set ldap password
	$enpass = "U3lzQGRtaW4tMjAxOA==";
	# Set email
	$email = $_POST['email'];
	# Sprit name and domain from email
	$domain = explode("@", $email);
	# Debug sprit name and domain
	#echo $domain[0]."<br>";
	#echo $domain[1]."<br>";
	# Sum char == to last of sting
	$decoderaw = base64_decode($_POST["code"]."==");
	# Debug sum char ==
	#echo $decoderaw."<br>";
	# Show now time
	$datetimenow = date("Y-m-d h:i:s");
	# Debug show now time
	#echo $datetimenow."<br>";
	# Diff time code input and now
	$difftime = strtotime($datetimenow) - strtotime($decoderaw);
	# Debug diff time for code
	#echo $difftime."<br>";
	# Connect ldap
	if($ldapconn = ldap_connect($adServer)) {
		# Debug connect ldap done
		#echo "LDAP connect successful... <br>";
		# Search ldap
		$sr = ldap_search($ldapconn, "uid=".$domain[0].",dc=meelab,dc=net", "(objectclass=*)");
		# Debug search ldap
		#echo $sr."<br>";
		# First entry ldap
		$entry = ldap_first_entry($ldapconn, $sr);
		# Debug first entry ldap
		#echo $entry."<br>";
		# Get values ldap
		$values = ldap_get_values($ldapconn, $entry, "description");
		# Debug get values ldap
		#echo $values[0]."<br>";
		# Encode post
		$encodepost = $_POST["code"]."==";
		# Debug encode post
		#echo $encodepost."<br>";
		# In 10 min and gard key correct
		if(($difftime <= 600) && ($encodepost == $values[0])){
			# Debug in 10 min
			#echo "Code OK...<br>";
			# Print Page 3 - submmit password
			echo "
			<form action='#setpass' method='POST'>
				<div><label for='pass1'>Password: </label>
				<input id='pass1' type='password' name='pass1' /></div><br>
				<div><label for='pass2'>Confirm Password: </label>
				<input id='pass2' type='password' name='pass2' /></div><br>
				<div><label for='code'>Code: </label>
				<input id='code' type='text' name='code' value=".$_POST["code"]." readonly /></div><br>
				<div><label for='email'>E-mail: </label>
				<input id='email' type='text' name='email' value=".$_POST["email"]." readonly /></div><br>
				<div><input type='submit' name='submit' value='Submit' /></div><br>
			</form>";
		}
		# Not in 10 min
		else{
			# Debug not in 10 min
			echo "<div style='color:#FF5330; font-weight:bold'>*Guard code has expired OR Guard code not correct.</div><br>";
			# Gen text code
			$rawcode = date("Y-m-d h:i:s");
			# Debug text code
			#echo $rawcode."<br>";
			# Encode text code
			$encoderaw = base64_encode($rawcode);
			# Debug encode text code
			#echo $encoderaw."<br>";
			# Cut last 2 or == char from code encode string
			$showencoderaw = substr($encoderaw, 0, -2);
			# Debug cut 2 char from code encode
			#echo $showencoderaw."<br>";
			# Bind ldap
			if(ldap_bind($ldapconn, base64_decode($ldaprdn), base64_decode($enpass))) {
				# Debug bind ldap
				#echo "LDAP bind successful... <br>";
				# Set user from name sprit
				$dn = "uid=".$domain[0].",dc=meelab,dc=net";
				# Set parameter
				$newEntry = array('description' => $encoderaw );
				# Mod ldap
				if(ldap_mod_replace($ldapconn, $dn, $newEntry)){
					# Debug mod ldap
					#echo "LDAP update succeded... <br>";
					echo "<div>New guard code generated, Please check you email again.</div><br>";
					# Set to email
					$to = $email;
					# Set subject email
					$subject = "Your Login AFMR meelab Account: Access from web";
					# Set message email
					$message = "
					<!DOCTYPE html>
					<html>
					<head>
					<title>AFMR EMAIL PAGE</title>
					<link rel='shortcut icon' href='/images/icon/favicon.ico'>
					</head>
					<body>
					<tbody>
					<tr>
					<td>
					<table width='470' border='0' align='center' cellpadding='0' cellspacing='0' style='border:1px solid lightgrey; padding-left:5px; padding-right:5px; padding-bottom:10px'>
					<tbody>
					<tr>
					<td style='padding-top:0px'><img data-imagetype='External' src='https://image.ibb.co/cwCVnS/email_header_logo.png' width='538' height='65' alt='meelab'>
					</td>
					</tr>
					<tr>
					<td style='padding-top:32px'><span style='padding-top: 16px; padding-bottom: 16px; font-size: 24px; color: #000000; font-family: Arial, Helvetica, sans-serif, serif, EmojiFont; font-weight: bold;'>Dear ".$domain[0].", </span><br>
					</td>
					</tr>
					<tr>
					<td style='padding-top:12px'><span style='font-size: 17px; color: #949494; font-family: Arial, Helvetica, sans-serif, serif, EmojiFont; font-weight: bold;'>
					<p>Here is AFMR Guard code you need to login to account ".$domain[0].":</p>
					</span></td>
					</tr>
					<tr>
					<td bgcolor='#99CCFF'>
					<div align='center'><span style='font-size: 24px; color: #000000; font-family: Arial, Helvetica, sans-serif, serif, EmojiFont; font-weight: bold;'>".$showencoderaw."</span> </div>
					</td>
					</tr>
					<tr>
					<td style='padding:20px; font-size:12px; line-height:17px; color:#c6d4df; font-family:Arial,Helvetica,sans-serif'>
					<p style='padding-bottom:10px; color:#949494'>This email was generated for reset account authentication firewall and mail relay or AFMR because of a login attempt from a web <a href='https://www.meelab.net/th/customerservice-faq.aspx' target='_blank' rel='noopener noreferrer' style='color:#999999'>located at ".$_SERVER['REMOTE_ADDR']."</a> The login attempt included your correct account name and password.</p>
					<p style='padding-bottom:10px; color:#949494'>The Guard code is required to complete the login. <span style='color:#949494; font-weight:bold'>No one can access your account without also accessing this email.</span></p>
					<p style='padding-bottom:10px; color:#949494'><span style='color:#949494; font-weight:bold'>If you are not attempting to login</span> then please change your meelab password, and consider changing your email password as well to ensure your account security.</p>
					<p style='padding-top:10px; color:#949494'>If you are unable to access your account then <a href='https://www.meelab.net/th/customerservice-outage.aspx' target='_blank' rel='noopener noreferrer' style='color:#999999'>use this link report admin recovery specific account</a> for assistance recovering your account.</p>
					</td>
					</tr>
					<tr>
					<td style='font-size:12px; color:#9b9b9b; padding-top:16px; padding-bottom:60px'>meelab Sysadmin Team<br>
					<a href='mailto:sysadmin@meelab.net' target='_top' style='color:#9b9b9b'>sysadmin@meelab.net</a><br>
					</td>
					</tr>
					</tbody>
					</table>
					</td>
					</tr>
					<tr>
					<td>
					<table width='460' height='55' border='0' align='center' cellpadding='0' cellspacing='0'>
					<tbody>
					<tr valign='top'>
					<td width='110'><a href='https://www.meelab.net' target='_blank' rel='noopener noreferrer' style=''><img data-imagetype='External' src='https://image.ibb.co/eZnfnS/meelab_logo.jpg' alt='meelab' width='92' height='52' hspace='0' vspace='0' border='0' align='top'></a></td>
					<td width='350' valign='top'><span style='color: #999999; font-size: 9px; font-family: Verdana, Arial, Helvetica, sans-serif, serif, EmojiFont;'>Copyright � 2018 meelab Commercial Internet Co., Ltd. All rights reserved and trademarks are property of their respective owners in the TH.</span> </td>
					</tr>
					</tbody>
					</table>
					</td>
					</tr>
					</tbody>
					</body>
					</html>
					";
					# Set email header
					$headers = "MIME-Version: 1.0" . "\r\n";
					$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
					$headers .= 'From: <noreply-afmr@cloud.meelab.net>' . "\r\n";
					# Send email
					if(mail($to,$subject,$message,$headers)){
						# Debug send email
						#echo "Email send succeded... <br>";
					}
					# Can not send email
					else{
						# Debug can not send email
						echo "<div style='color:#FF5330; font-weight:bold'>*Email send failed...</div><br>";
					}
				}
				# Can not mod ldap
				else{
					# Debug can not mod ldap
					echo "<div style='color:#FF5330; font-weight:bold'>*LDAP update failed...</div><br>";
				}
			}
			# Can not bind ldap
			else {
				# Debug can not bind ldap
				#echo "LDAP bind failed... <br>";
			}
			# Print Page 2 - submmit code
			echo "
			<form action='#code' method='POST'>
				<div><label for='code'>Code: </label>
				<input id='code' type='text' name='code' /></div><br>
				<div><label for='code'>E-mail: </label>
				<input id='email' type='text' name='email' value=".$_POST["email"]." readonly /></div><br>
				<div><input type='submit' name='submit' value='Submit' /></div><br>
			</form>";
		}
	}
	# Can not connect ldap
	else{
		# Debug can not connect ldap
		echo "<div style='color:#FF5330; font-weight:bold'>*LDAP connect failed...</div><br>";
	}
}
# Stage 4 - for submit required gard code to email
elseif(isset($_POST["email"]) && isset($_POST["code"]) && isset($_POST["pass1"]) && isset($_POST["pass2"])){
	# Debug Stage 4
	#echo "::::::::Stage 4::::::::<br>";
	# Set ldap function connecttion
	ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
	ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);
	# Debug pass1
	#echo $_POST["pass1"]."<br>";
	# Debug pass2
	#echo $_POST["pass2"]."<br>";
	# Debug email
	#echo $_POST["email"]."<br>";
	# Debug code
	#echo $_POST["code"]."<br>";
	# Set email
	$email = $_POST["email"];
	# Set ldap server
	$adServer = "ldap1.meelab.th.com";
	# Set ldap username
	$ldaprdn = "Y249bGRhcGFkbSxkYz1rc2MsZGM9bmV0";
	# Set ldap password
	$enpass = "U3lzQGRtaW4tMjAxOA==";
	# If password matching
	if($_POST["pass1"] == $_POST["pass2"]){
		# Debug password matching
		#echo "Password matching ...<br>";
		# Connect ldap
		if($ldapconn = ldap_connect($adServer)) {
			# Debug connect ldap done
			#echo "LDAP connect successful... <br>";
			# Bind ldap
			if(ldap_bind($ldapconn, base64_decode($ldaprdn), base64_decode($enpass))) {
				# Debug bind ldap
				#echo "LDAP bind successful... <br>";
				# Sprit name and domain from email by @
				$domain = explode("@", $email);
				# Debug sprit name and domain
				#echo $domain[0]."<br>";
				#echo $domain[1]."<br>";
				# Set user from name sprit
				$dn = "uid=".$domain[0].",dc=meelab,dc=net";
				# Set password
				$newPassword = $_POST["pass1"];
				# Set description
				$descriptiondone = "Reset password done ".date("Y-m-d h:i:s");
				# Set parameter
				$newEntry = array('userPassword' => "{MD5}".base64_encode(pack("H*",md5($newPassword))), 'description' => $descriptiondone );
				# Mod ldap
				if(ldap_mod_replace($ldapconn, $dn, $newEntry)){
					# Debug mod ldap
					#echo "LDAP update succeded... <br>";
				}
				# Can not mod ldap
				else {
					# Debug can not mod ldap
					echo "<div style='color:#FF5330; font-weight:bold'>*LDAP update failed...</div><br>";
				}
			}
			# Can not bing ldap
			else {
				# Debug can not bind ldap
				#echo "LDAP bind failed... <br>";
			}
		}
		# Can not connect ldap
		else{
			# Debug can not connect ldap
			echo "<div style='color:#FF5330; font-weight:bold'>*LDAP connect failed...</div><br>";
		}
		# Show password changed
		echo "<div style='color:#33CC33; font-weight:bold'>Password Changed.</div><br>";
		# Print relad
		echo "
		<a href='https://ldap1.meelab.th.com/index.html'>
			<input type='button' value='Reload' /><br>
		</a><br>";
	}
	# If password not matching
	else{
		# Debug Password not matching
		echo "<div style='color:#FF5330; font-weight:bold'>*Password not matching.</div><br>";
		# Print Page 3 - submmit password
		echo "
		<form action='#setpass' method='POST'>
			<div><label for='pass1'>Password: </label>
			<input id='pass1' type='password' name='pass1' /></div><br>
			<div><label for='pass2'>Confirm Password: </label>
			<input id='pass2' type='password' name='pass2' /></div><br>
			<div><label for='code'>Code: </label>
			<input id='code' type='text' name='code' value=".$_POST["code"]." readonly /></div><br>
			<div><label for='email'>E-mail: </label>
			<input id='email' type='text' name='email' value=".$_POST["email"]." readonly /></div><br>
			<div><input type='submit' name='submit' value='Submit' /></div><br>
		</form>";
	}
}
# Stage 1 - for submit required gard code to email
else{
	# Debug Stage 1
	#echo "::::::::Stage 1::::::::<br>";
	# Print page 1 - submit email
	echo "
	<form action='#email' method='POST'>
		<div><label for='email'>E-mail: </label>
		<input id='email' type='text' name='email' />
		<input type='submit' name='submit' value='Submit' /></div><br>
	</form>
	<form action='#checkpasswd' method='POST'>
		<div><input id='checkpasswd' type='hidden' name='checkpasswd' value='checkpasswd' readonly />
		<input id='checkpasswd' type='submit' name='submit' value='Check Password' /></div><br>
	</form>";
}
# Print End - for HTML Page
echo "
</div>
</td>
</tr>
</tbody>
</table>
<table width='460' height='55' border='0' align='center' cellpadding='0' cellspacing='0'>
<tbody>
<tr valign='top'>
<td width='110'><a href='https://www.meelab.net' target='_blank' rel='noopener noreferrer' style=''><img data-imagetype='External' src='https://ldap1.meelab.th.com/images/email/meelab_logo.jpg' alt='meelab' width='92' height='52' hspace='0' vspace='0' border='0' align='top'></a></td>
<td width='350' valign='top'><span style='color: #999999; font-size: 9px; font-family: Verdana, Arial, Helvetica, sans-serif, serif, EmojiFont;'>Copyright � 2018 meelab Commercial Internet Co., Ltd. All rights reserved and trademarks are property of their respective owners in the TH.</span> </td>
</tr>
</tbody>
</table>
</body>
</html>";
?>
