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
    )
}

function GenericList({items}) {
    return (
        <ul>
          {items.map(function(listValue){
            return <li key={listValue.name}><PlayerListItem player={listValue}/></li>;
          })}
        </ul>
    )
}

function ManagerList({items}) {
    return (
        <div className="container">
          {items.map(function(listValue){
            return <ManagerListItem key={listValue.team_name} manager={listValue}/>;
          })}
        </div>
    )
}

function PlayerListItem({player}){
    return (
        <div className="row">
            <div>{player.name}</div>
            <div>{player.overall}</div>
            <button className="replace">Replace</button>
        </div>
    )
}

function ManagerListItem({manager}){
    return (
        <div className="row">
            <div className="col-md">{manager.team_name}</div>
            <div className="col-md">{manager.overall}</div>
            <div className="col-md"><button className="play">Play</button></div>
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
    $("#dialog").dialog({
    autoOpen: false
    });

    //Open it when #opener is clicked
    $("#opener").click(function () {
        $("#dialog").dialog("open");
    });

    //When the button in the form is clicked, take the input value and set that as the value of `.myTarget`
    $('.formSaver').on('click', function () {
        $('.myTarget').text($('.myInput').val());
        $("#dialog").dialog('close');
    });

    var reactVar = React.createElement(GenericList, {"items" : [{"name": "ronaldo", "overall": 95}, {"name": "messi", "overall": 94}]});
    ReactDOM.render(reactVar, document.getElementById("content"));
}

function playTab() {
    var reactVar = React.createElement(ManagerList, {"items" : [{"team_name": "ronaldo", "overall": 95}, {"team_name": "messi", "overall": 94}]});
    ReactDOM.render(reactVar, document.getElementById("content"));
}

playTab();