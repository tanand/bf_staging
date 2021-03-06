<?php
        include ("config.php");
        session_start();

        $authCode = $_GET[code];
        
        if ($authCode == null || $authCode == "") {
            $authorizeUrl = "https://beta-api.att.com/oauth/authorize";
            $authorizeUrl .= "?scope=".$scope;
            $authorizeUrl .= "&client_id=".$api_key;
            $authorizeUrl .= "&redirect_uri=".$authorize_redirect_uri;

            header("Location: $authorizeUrl");
        } else {
            // **********************************************************************
            // ** code to get access token
            // **********************************************************************
            $accessTok_Url = "https://beta-api.att.com/oauth/access_token";
            $accessTok_Url .= "?client_id=".$api_key;
            $accessTok_Url .= "&client_secret=".$secret_key;
            $accessTok_Url .= "&code=" . $authCode . "&grant_type=authorization_code";

            $accessTok_headers = array(
            'Content-Type: application/x-www-form-urlencoded'
            );
            
            // print "<p>URL to get Access Token:</br>$accessTok_Url</p>";
            $accessTok = curl_init();
            curl_setopt($accessTok, CURLOPT_URL, $accessTok_Url);
            curl_setopt($accessTok, CURLOPT_HTTPGET, 1);
            curl_setopt($accessTok, CURLOPT_HEADER, 0);
            curl_setopt($accessTok, CURLINFO_HEADER_OUT, 0);
            curl_setopt($accessTok, CURLOPT_HTTPHEADER, $accessTok_headers);
            curl_setopt($accessTok, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($accessTok, CURLOPT_SSL_VERIFYPEER, false);
            $accessTok_response = curl_exec($accessTok);

		$responseCode=curl_getinfo($accessTok,CURLINFO_HTTP_CODE);
		//if the url is successful,fetch the access token and store it in the session.
		//else display the error.
        	if($responseCode==200)
        	{
            $jsonObj = json_decode($accessTok_response);
            $accessToken = $jsonObj->{'access_token'};
            $_SESSION["payment_access_token"]=$accessToken;
		}
		else {
		echo curl_error($accessTok);
		}
            curl_close ($accessTok);
            header("location:index.php");
            exit;
        }
?>