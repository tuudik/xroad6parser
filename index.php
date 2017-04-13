<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL);
require 'func.php'
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>X-road v6 globalconfig parser</title>
  </head>

<script type="text/javascript" src="jquery-3.2.1.min.js"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
  <script type="text/javascript">
  function env_select(val)
  {
    $.ajax({
      type: 'post',
      url: 'func.php',
      data: {
        selectedEnv:val
      },
      success: function (response) {
        document.getElementById("subsystemSelect").innerHTML=response;
      }
    });
  };

  function subsystem_select(val)
  {
    $.ajax({
      type: 'post',
      url: 'func.php',
      data: {
        selectedEnv:$('#envSelect option:selected').val(),
        subsystemID:$('#subsystemSelect option:selected').val()
      },
      success: function (response) {
        document.getElementById("result").innerHTML=response;
      }
    });
  }
  </script>
  <body style="margin:30px;padding:30px">
        <p>
        <select id="envSelect" onchange="env_select(this.value);">
                <option selected>- Select environment: -</option>
                <?php
                foreach ($environments as $environments => $value) {
                  printf('<option value="%s">%s</option>', $value['id'], $value['name']);
                } ?>
        </select>
        <br/>
        </p>
        <p id="subsystemSelect">
        </p>
      <p id="result"></p>
  </body>
</html>
