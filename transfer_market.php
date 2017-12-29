<!DOCTYPE HTML>
<html>

<head>
  <title>Fantasy football manager</title>
  <meta name="description" content="Fantasy football simulator" />
  <meta name="keywords" content="Web Game, HTML, Javascript, PHP, football, manager, simulator" />
  <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
  <link rel="stylesheet" type="text/css" href="style/style.css" />
  <script>
    $( function() {
      $( "#dialog" ).dialog();
    } );
  </script>
</head>

<body>
  <div id="main">
    <div id="site_content">
      <div id="content">
        <div>Here is the rest of the page. Hopefully we can get the value from the dialog form?! Should display <span class="myTarget">here</span> when finished.</div>
        <div id="dialog" title="Basic dialog">
            <p>This is an animated dialog which is useful for displaying information. The dialog window can be moved, resized and closed with the 'x' icon.</p>
            <input class="myInput" type="text" />
            <button class="formSaver">Save me!</button>
        </div>
        <button id="opener">Open Dialog</button>
      </div>
    </div>
  </div>
</body>
</html>