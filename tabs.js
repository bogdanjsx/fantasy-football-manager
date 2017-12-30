function myTeamTab() {
    $.ajax({
        method: "GET",
        url: "api.php/getStartingEleven/"
    }).done(function(activeTeam) {
        activeTeam = JSON.parse(activeTeam);

        for(const playerPosition in activeTeam){
            var reactVar = React.createElement(Card, {"player" : JSON.parse(activeTeam[playerPosition]["player"])});
            ReactDOM.render(reactVar, document.getElementById(playerPosition));
        }
    });
}

function myPlayersTab() {
    var reactVar = React.createElement(PlayerList, {"items" : [{"name": "ronaldo", "overall": 95}, {"name": "messi", "overall": 94}]});
    ReactDOM.render(reactVar, document.getElementById("content"));
}

function playTab() {
    $.ajax({
        method: "GET",
        url: "api.php/getPlayOpponents/"
    }).done(function(itemsArray) {
        console.log(itemsArray)
        var reactVar = React.createElement(ManagerList, {"items" : itemsArray});
        ReactDOM.render(reactVar, document.getElementById("content"));
    });
}

//playTab();

