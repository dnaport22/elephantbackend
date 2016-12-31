<?php

/**
 * This file holds settings for web server.
 */

class Settings 
{
	/**
	 * Server urls.
	 *
	 * @var string
	 */
	private $live_server = "service.myelephant.xyz";
  private $dev_server = "develop.myelephant.xyz";
  private $test_server = "test.myelephant.xyz";
  private $dev_mode = false;
  public $cre_location = "../../config/config.ini";

  /**
   * Get credentials for server type, i.e. develop, test, live
   * 
   * @param string $server
   *    Current server.
   * 
   * @return string 
   */
  public function getServerUrl($location) {
  	if ($location === $this->live_server) {
  		return 'myelephant.xyz';
  	}
    elseif ($location === $this->dev_server) {
      return 'developweb.myelephant.xyz';
    }
    elseif ($location === $this->test_server) {
      return 'testweb.myelephant.xyz';
    }
  }

  /**
	 * Get credentials for server type, i.e. develop, test, live
	 * 
	 * @param string $server
	 * 	  Current server.
	 * 
	 * @return string db_credentials
	 */
  public function getDbCredentials($location) {
  	if(!$this->dev_mode) {
  		if ($location === $this->live_server) {
  			return 'db_credentials_live';
  		}
			elseif ($location === $this->dev_server) {
				return 'db_credentials_dev';
			}
			elseif ($location === $this->test_server) {
				return 'db_credentials_test';
			}
  	}
  	else {
  		return 'db_credentials_local';
  	}
  	
  }

  public function setDevMode($mode) {
  	if (!$mode) {
  		$this->dev_mode = false;
  	}
  	else {
  		$this->dev_mode = true;
  	}
  }

  public function setCreLocation($new_location) {
  	$this->cre_location = $new_location;
  }
  
}

$server_settings = new Settings();

# Settings for local development.
$server_settings->setDevMode(false); # Change parameter to true to enable local development mode.
// Uncomment the line below and add path to credentials file.
//$server_settings->setCreLocation("../config/config.ini"); 