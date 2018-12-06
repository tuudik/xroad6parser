<?php
$environments = array(
  0 => array(
    'id' => 'ee',
    'url' => 'http://x-tee.ee/anchors/EE_public-anchor.xml',
    'name' => 'EE - Production'),
    1 => array(
      'id' => 'eeTest',
      'url' => 'http://x-tee.ee/anchors/ee-test_public_anchor.xml',
      'name' => 'ee-test - Test'),
      2 => array(
        'id' => 'eeDev',
        'url' => 'http://x-tee.ee/anchors/ee-dev_public_anchor.xml',
        'name' => 'ee-dev - Development')
      );
      function getInternalConf($url)
      {
        $xml = simplexml_load_file($url) or die ("Error: Cannot access XML");
        return $xml->source[0]->downloadURL;
      }

      if(isset($_POST['selectedEnv']))
      {
        $selectedEnv = $_POST['selectedEnv'];

        foreach ($environments as $key => $val){
          if($val['id']===$selectedEnv){
            $url = $val['url'];
            $envName = $val['name'];
            $envID = $val['id'];
          }
        }

        $intConfURL = getInternalConf($url);
        $ip=(parse_url($intConfURL, PHP_URL_HOST));

        foreach (file($intConfURL) as $lineNumber => $line) {
          if (strpos($line, "shared-params.xml") !== false) {
            $sharedParamsURL = $line;
          }
        }
        $sharedParamsURL = 'http://'.$ip . substr($sharedParamsURL, strpos($sharedParamsURL, " ") + 1);
        $sharedParamsURL = trim(preg_replace('/\s+/', ' ', $sharedParamsURL));
        $sharedParamsXML = simplexml_load_file($sharedParamsURL);
        if(!isset($_POST['subsystemID']))
        {
          $filename = $selectedEnv.".csv";
          if (file_exists($filename)) {
            unlink($filename);
          }
          $file = fopen($filename, "w") or die("Unable to open file!");
          fwrite($file, "\xEF\xBB\xBFsubSystem,memberCode,memberName\n");

          echo "<option selected>- Select subsystem: -</option>";
          foreach ($sharedParamsXML->member as $member)
          {
            echo "<optgroup label=\"".$member->memberCode." - ".$member->name."\">";
            foreach($member->subsystem as $subsystem){
              echo "<option value=\"".$subsystem['id']."\">".$subsystem->subsystemCode." - ".$member->memberCode." - ".$member->name."</option>";
              fwrite($file, $subsystem->subsystemCode.",".$member->memberCode.",\"".$member->name."\"\n");
            }
            echo "<\optgroup>";
          }
          fclose($file);
        }
      }
      if(isset($_POST['subsystemID']))
      {
        $subsystemID = $_POST['subsystemID'];
        $securityServers = $sharedParamsXML->xpath("/ns3:conf/securityServer[client='".$subsystemID."']");
        $subsystemInfo = $sharedParamsXML->xpath("/ns3:conf/member/subsystem[contains(@id,'".$subsystemID."'')]/..");
        $memberClassCodeInfo = $subsystemInfo->memberClass->code;
        $memberCodeInfo = $subsystemInfo->memberCode;
        echo "<strong>Environment: </strong>".$envName."<br/>";
        echo "<strong>Subsystem ID: </strong>".$subsystemID."<br/>";
        echo "<strong>FQDN: </strong>".$envID." : ".$memberClassCodeInfo." : ".$memberCodeInfo."<br/>";
        if (empty($securityServers)) {
          echo "<br/><strong>Subsystem is not registered in any Security Server</strong>";
        } else {
          foreach($securityServers as $securityServer){
            $ssOwnerID = $securityServer->owner;
            $ssOwner = $sharedParamsXML->xpath("/ns3:conf/member[@id='".$ssOwnerID."']")[0];
            echo "<br/><strong>Server Owner ID: </strong>" . $ssOwnerID . "<br/>";
            echo "<strong>Owner Name: </strong>" . $ssOwner->name. "<br/>";
            echo "<strong>Owner Code: </strong>" .  $ssOwner->memberCode . "<br/>";
            echo "<strong>Server Code: </strong>" . $securityServer->serverCode . "<br/>";
            $ip = $securityServer->address;
            if (!filter_var($ip, FILTER_VALIDATE_IP) === false) {
              echo("<strong>Server IP: </strong> $ip <br/>");
            } else {
              echo("<strong>Server DNS: </strong> $ip <br/>");
              echo("<strong>Server IP: </strong>" . gethostbyname($ip) . "<br/>");
            }
          }
        }
        echo "<br/><br/>Information retrieved from:<br/>";
        echo "Anchor: <a href=\"".$url."\" target=\"_blank\">".$url."</a><br/>";
        echo "Shared params: <a href=\"".$sharedParamsURL."\" target=\"_blank\">".$sharedParamsURL."</a><br/>";
        echo "List of all subsystems in the global configuration: <a href=\"".$selectedEnv.".csv\">CSV</a> (last modified: ".date('Y-m-d H:i:s', filemtime($selectedEnv.".csv")).")";
      }
      ?>
