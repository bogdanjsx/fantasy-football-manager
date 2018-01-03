function myTeamTab() {
    $("#loadingModal").modal('show');
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
            $("#loadingModal").modal('hide');
            for(const playerPosition in activeTeam){
                player = JSON.parse(activeTeam[playerPosition]["player"]);
                player["chemistry"] = JSON.parse(activeTeam[playerPosition]["chemistry"])
                var reactVar = React.createElement(Card, {"player" : player, "position": playerPosition, "bench": bench});
                ReactDOM.render(reactVar, document.getElementById(playerPosition));
            }
        });
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

function myPlayersTab() {
    $("#loadingModal").modal('show');
    $.ajax({
        method: "GET",
        url: "api.php/getBenchedPlayers/"
    }).done(function(itemsArray) {
        $("#loadingModal").modal('hide');
        itemsArray = JSON.parse(itemsArray).map((item) => JSON.parse(item));
        var reactVar = React.createElement(AllPlayersList, {"items" : itemsArray});
        ReactDOM.render(reactVar, document.getElementById("playerList"));
    });
}


function transferMarketTab() {
    $("#loadingModal").modal('show');
    $.ajax({
        method: "GET",
        url: "api.php/getTransferMarketPlayers/"
    }).done(function(players) {
        $("#loadingModal").modal('hide');
        players = JSON.parse(players).map((item) =>
            {
                console.log(item)
                data = JSON.parse(item.player_id)
                data.price = item.price;
                data.ownerId = item.ownerId;
                return data;
            }
        );
        var reactVar = React.createElement(TransferPlayerList, {"items" : players});
        ReactDOM.render(reactVar, document.getElementById("playerList"));
    });

}

function statsTab() {
    $("#loadingModal").modal('show');
    $.ajax({
        method: "GET",
        url: "api.php/getMyStats/"
    }).done(function(stats) {
        $("#loadingModal").modal('hide');
        console.log(stats);
        window.stats = stats
        var reactVar = React.createElement(Stats, {"stats" : JSON.parse(stats)});
        ReactDOM.render(reactVar, document.getElementById("stats"));
    });
}

//playTab();