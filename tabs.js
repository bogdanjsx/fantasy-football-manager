function myTeamTab() {
    $.ajax({
        method: "GET",
        url: "api.php/getBenchedPlayers/"
    }).done(function(bench) {
        bench = JSON.parse(bench).map((player) => JSON.parse(player))
        $.ajax({
            method: "GET",
            url: "api.php/getStartingEleven/"
        }).done(function(activeTeam) {
            activeTeam = JSON.parse(activeTeam);
    
            for(const playerPosition in activeTeam){
                player = JSON.parse(activeTeam[playerPosition]["player"]);
                player["chemistry"] = JSON.parse(activeTeam[playerPosition]["chemistry"])
                var reactVar = React.createElement(Card, {"player" : player, "position": playerPosition, "bench": bench});
                ReactDOM.render(reactVar, document.getElementById(playerPosition));
            }
        });
    });

}

function myPlayersTab() {
    $.ajax({
        method: "GET",
        url: "api.php/getBenchedPlayers/"
    }).done(function(itemsArray) {
        itemsArray = JSON.parse(itemsArray).map((item) => JSON.parse(item));
        var reactVar = React.createElement(AllPlayersList, {"items" : itemsArray});
        ReactDOM.render(reactVar, document.getElementById("playerList"));
    });

}

function playTab() {
    $.ajax({
        method: "GET",
        url: "api.php/getPlayOpponents/"
    }).done(function(itemsArray) {
        itemsArray = JSON.parse(itemsArray);
        var reactVar = React.createElement(ManagerList, {"items" : itemsArray});
        ReactDOM.render(reactVar, document.getElementById("content"));
    });
}

function statsTab() {

}

playTab();