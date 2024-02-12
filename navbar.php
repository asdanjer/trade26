<!-- navbar.php -->
<!-- Latest compiled and minified CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Server 26 Stock Market</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link" href="index.php">View</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="add.php">Submit</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="add_mass.php">Mass Submit</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="add_shop.php">Add Shop</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="logout.php">Logout</a>
        </li>
      </ul>
      <button class="btn btn-outline-success" type="button" id="onlinePlayersBtn" data-bs-toggle="modal" data-bs-target="#onlinePlayersModal">Online Players</button>
    </div>
  </div>
</nav>

<!-- Modal Structure -->
<div class="modal fade" id="onlinePlayersModal" tabindex="-1" aria-labelledby="onlinePlayersModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="onlinePlayersModalLabel">Online Players</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Content will be loaded here -->
        <div id="onlinePlayersList">Loading...</div>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
  $("#onlinePlayersBtn").click(function() {
    $.ajax({
      url: "src-a/fetch_players.php",
      type: "GET",
      dataType: "json", // Expecting JSON response
      success: function(data) {
        var playersList = "<ul class='list-group'>"; // Start the list
        $.each(data, function(index, player) {
          playersList += "<li class='list-group-item'>" + player + "</li>"; // Create list item for each player
        });
        playersList += "</ul>"; // Close the list
        $("#onlinePlayersList").html(playersList); // Insert the list into the modal
      },
      error: function() {
        $("#onlinePlayersList").html("Failed to load players.");
      }
    });
  });
});
</script>
