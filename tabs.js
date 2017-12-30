function myTeamTab() {
    $.ajax({
        method: "GET",
        url: "api.php/getStartingEleven/"
    }).done(function(activeTeam) {
        window.x = activeTeam;
        console.log(activeTeam[0])
        activeTeam = JSON.parse(activeTeam)
        for(let i = 0; i < 11; i++) {
            var reactVar = React.createElement(Card, {"player" : JSON.parse(activeTeam[i])});
            ReactDOM.render(reactVar, document.getElementById("player" + i));
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

playTab();

