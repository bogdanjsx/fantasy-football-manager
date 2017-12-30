function Card({player}) {
    // Only display last name of player
    let displayName = player.name.split('.')
    displayName = displayName[displayName.length - 1]

    let statsArray = [];
    if (player.positions[0] === 'GK') {
        statsArray = [
            [player['gk diving'], 'DIV'],
            [player['gk handling'], 'HAN'],
            [player['gk kicking'], 'KIC'],
            [player['gk reflexes'], 'REF'],
            [player['gk speed'], 'SPD'],
            [player['gk positioning'], 'POS']
        ];
    } else {
        statsArray = [
            [player['pace'], 'PAC'],
            [player['shooting'], 'SHO'],
            [player['passing'], 'PAS'],
            [player['dribbling'], 'DRI'],
            [player['defending'], 'DEF'],
            [player['physical'], 'PHY']
        ];
    }

    return (
    <div data-toggle="modal" data-target="#replaceModal">
        <div className="playercard fut16 card-small gold" style={{display: 'inline-block'}}>
            <div className="hover"></div>
            <div className="playercard-rating">{player.overall}</div>
            <div className="playercard-name">
                <span>{displayName}</span>
            </div>
            <div className="playercard-position">{player.positions[0]}</div>
            <div className="playercard-nation"><img src={player.flag}/></div>
            <div className="playercard-club"><img src={player.club_logo}/></div>
            <div className="playercard-picture">
                <img src={player.photo}/>
            </div>
            <div className="playercard-mid-bar">
            </div>

            <div className="playercard-attr playercard-attr1">{statsArray[0][0]} {statsArray[0][1]}</div>
            <div className="playercard-attr playercard-attr2">{statsArray[1][0]} {statsArray[1][1]}</div>
            <div className="playercard-attr playercard-attr3">{statsArray[2][0]} {statsArray[2][1]}</div>
            <div className="playercard-attr playercard-attr4">{statsArray[3][0]} {statsArray[3][1]}</div>
            <div className="playercard-attr playercard-attr5">{statsArray[4][0]} {statsArray[4][1]}</div>
            <div className="playercard-attr playercard-attr6">{statsArray[5][0]} {statsArray[5][1]}</div>
        </div>

        <div className="modal" id="replaceModal" tabIndex="-1" role="dialog" aria-hidden="true">
            <div className="modal-dialog modal-lg" role="document">
                <div className="modal-content">
                <div className="modal-header">
                    <h5 className="modal-title">Replace player</h5>
                    <button type="button" className="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div className="modal-body">
                    <PlayerList items={[{"name": "ronaldo", "overall": 95}, {"name": "messi", "overall": 94}]} />
                </div>
                <div className="modal-footer">
                </div>
                </div>
            </div>
        </div>
    </div>
    )
}

function PlayerList({items}) {
    return (
        <div className="managerlist btn-group" role="group">
            <div className="container">
            <div className="row">
                <div className="col-4 managerlistitem"><b>Player name</b></div>
                <div className="col-2 managerlistitem"><b>Player rating</b></div>
                <div className="col-2 managerlistitem"><b>Player position</b></div>
                <div className="col-4 managerlistitem"></div>
            </div>
                {items.map(function(listValue){
                    return <PlayerListItem key={listValue.name} player={listValue}/>;
                })}
            </div>
        </div>
    )
}

function PlayerListItem({player}){
    return (
        <div className="row">
            <div className="col-4 managerlistitem">{player.name}</div>
            <div className="col-2 managerlistitem">{player.overall}</div>
            <div className="col-2 managerlistitem">{player.overall}</div>            
            <div className="col-4 managerlistitem"><button className="btn btn-primary" data-toggle="modal" data-target="#replaceModal">Replace</button></div>
        </div>

    )
}

function ManagerList({items}) {
    return (
        <div className="managerlist">
            <div className="container">
            <div className="row">
            <div className="col-8 managerlistitem"><b>Team name</b></div>
            <div className="col-2 managerlistitem"><b>Team rating</b></div>
            <div className="col-2 managerlistitem"></div>
            </div>
            {items.map(function(listValue){
                return <ManagerListItem key={listValue.team_name} manager={listValue}/>;
            })}
            </div>
        </div>
    )
}

function ManagerListItem({manager}){
    function playMatch() {
        $('#playModal').modal({show: true})

        $.ajax({
            method: "POST",
            url: "api.php/playMatch/" + manager.manager_id
        }).done(function(msg) {
                $('#matchResults').html(msg);
        });
    }

    return (
        <div className="row">
            <div className="col-8 managerlistitem">{manager.team_name}</div>
            <div className="col-2 managerlistitem">{manager.overall}</div>
            <div className="col-2 managerlistitem"><button className="btn btn-primary" onClick={playMatch}>Play</button></div>

            <div className="modal" id="playModal" tabIndex="-1" role="dialog" aria-hidden="true">
                <div className="modal-dialog modal-lg" role="document">
                    <div className="modal-content">
                    <div className="modal-header">
                        <h5 className="modal-title">Match results</h5>
                        <button type="button" className="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div className="modal-body" id="matchResults">
                        Please wait for the match to be played.
                    </div>
                    <div className="modal-footer">
                        <button type="button" className="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                    </div>
                </div>
          </div>
        </div>
    )
} 

function renderTeam() {
    var activeTeam = getStartingEleven();
    var count = 11;
    for(let i = 0; i < count; i++)
    {
        var reactVar = React.createElement(Card, {"player" : JSON.parse(activeTeam[i])});
        ReactDOM.render(reactVar, document.getElementById("player" + i));
    }
}

function openDialog() {
    var reactVar = React.createElement(PlayerList, {"items" : [{"name": "ronaldo", "overall": 95}, {"name": "messi", "overall": 94}]});
    ReactDOM.render(reactVar, document.getElementById("content"));
}

function playTab() {
    var awayManagers = getPlayOpponents();
    var count = awayManagers.length;

    var itemsArray = [];
    
    for(const managerID in awayManagers)
    {
        var tempArray = {
            "manager_id" : awayManagers[managerID]["manager_id"],
            "team_name" : awayManagers[managerID]["team_name"],
            "overall" : Math.round(awayManagers[managerID]["overall"])
        };

       itemsArray.push(tempArray);
       
    }

     var reactVar = React.createElement(ManagerList, {"items" : itemsArray});
     ReactDOM.render(reactVar, document.getElementById("content"));
    
}

playTab();

