<?php
$environments = array(
  0 => array(
    'id' => 'ee',
    'url' => 'http://x-road.eu/packages/EE_public-anchor.xml',
    'name' => 'EE - Production'),
  1 => array(
    'id' => 'eeTest',
    'url' => 'http://x-road.eu/packages/ee-test_public_anchor.xml',
    'name' => 'ee-test - Test'),
  2 => array(
    'id' => 'eeDev',
    'url' => 'http://x-road.eu/packages/ee-dev_public_anchor.xml',
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
  foreach ($sharedParamsXML->member as $member)
  {
    echo "<optgroup label=\"".$member->memberCode." - ".$member->name."\">";
    foreach($member->subsystem as $subsystem){
      echo "<option value=\"".$subsystem['id']."\">".$subsystem->subsystemCode."</option>";
    }
  }
}
}
if(isset($_POST['subsystemID']))
{
  $subsystemID = $_POST['subsystemID'];
  $securityServers = $sharedParamsXML->xpath("/ns3:conf/securityServer[client='".$subsystemID."']");

  echo "<strong>Environment: </strong>".$envName."<br/>";
  echo "<strong>Subsystem ID: </strong>".$subsystemID."<br/>";
  foreach($securityServers as $securityServer){
    $ssOwnerID = $securityServer->owner;
    $ssOwner = $sharedParamsXML->xpath("/ns3:conf/member[@id='".$ssOwnerID."']")[0];
    echo "<br/><strong>Server Owner ID: </strong>" . $ssOwnerID . "<br/>";
    echo "<strong>Owner Name: </strong>" . $ssOwner->name. "<br/>";
    echo "<strong>Owner Code: </strong>" .  $ssOwner->memberCode . "<br/>";
    echo "<strong>Server Code: </strong>" . $securityServer->serverCode . "<br/>";
    echo "<strong>Server IP: </strong>" . $securityServer->address . "<br/>";
  }
  echo"<br/><br/> Information retrieved from:<br/>Anchor: ";
  echo $url."<br/>";
  echo "Shared params: ".$sharedParamsURL."<br/>";
}



?>
