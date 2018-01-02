<!DOCTYPE HTML>
<html>

<head>
  <title>Fantasy football manager</title>
  <meta name="description" content="Fantasy football simulator" />
  <meta name="keywords" content="Web Game, HTML, Javascript, PHP, football, manager, simulator" />
  <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
  <link rel="stylesheet" type="text/css" href="style/style.css" />
</head>

<body>
  <div id="main">
    <div id="site_content">
      
      <div class="modal fade" id="loadingModal" tabindex="-1" role="dialog" aria-labelledby="loadingModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="loadingModalLabel">Please wait</h5>
            </div>
            <div class="modal-body">
            Your request is processing, please wait...
            </div>
          </div>
        </div>
      </div>

      <div id="content">
        <div id="team">
          <div id="attack">
            <div id="LST" class="playercard"/></div>
            <div id="RST" class="playercard"/></div>
          </div>
          <div id="midfield">
            <div id="LM" class="playercard"/></div>
            <div id="LCM" class="playercard"/></div>
            <div id="RCM" class="playercard"/></div>
            <div id="RM" class="playercard"/></div>
          </div>
          <div id="defense">
            <div id="LB" class="playercard"/></div>
            <div id="LCB" class="playercard"/></div>
            <div id="RCB" class="playercard"/></div>
            <div id="RB" class="playercard"/></div>
          </div>
          <div id="goalkeeper">
            <div id="GK" class="playercard"/></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
