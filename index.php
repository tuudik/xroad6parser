<?php
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
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>

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
  };
  $(document).ready(function() {
  $(".envSelect").select2({
    placeholder: "Select environment"
  });
  $(".subsystemSelect").select2({
    placeholder: "Select subsystem"
  });
});
  </script>
  <body style="margin:30px;padding:30px">
        <p>
        <select id="envSelect" onchange="env_select(this.value);" class="envSelect" style="width:500px">
                <option selected>- Select environment: -</option>
                <?php
                foreach ($environments as $environments => $value) {
                  printf('<option value="%s">%s</option>', $value['id'], $value['name']);
                } ?>
        </select>
        <br/>
        </p>
        <select id="subsystemSelect" onchange="subsystem_select(this.value);" class="subsystemSelect" style="width:500px">
                <option selected>- Select subsystem: -</option>
              </select>
      <p id="result"></p>
  </body>
</html>
