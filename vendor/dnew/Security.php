<?php

class Security {
  const FAKE_USER_ID = 'FAKE_USER_ID';

  public static function addWarning($ipAdress, $warningType) {
    global $_world;
    $_world['warning'][$ipAdress][] = date("Y-m-d H:i:s") . ' - ' . $warningType;

    if (count($_world['warning'][$ipAdress]) >= 3) {
      // add to a ban file, client.php must read it and answer a 'You are banned for thoses reason: ---'
    }
  }
}