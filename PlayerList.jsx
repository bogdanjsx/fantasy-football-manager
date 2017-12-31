function PlayerList({items, position}) {
    return (
        <div className="playerlist btn-group" role="group">
            <div className="container">
            <div className="row">
                <div className="col-4 playerlistitem"><b>Name</b></div>
                <div className="col-2 playerlistitem"><b>Rating</b></div>
                <div className="col-2 playerlistitem"><b>Position</b></div>
                <div className="col-4 playerlistitem"></div>
            </div>
                {items.map(function(listValue){
                    return <PlayerListItem key={listValue.name} player={listValue} position={position}/>;
                })}
            </div>
        </div>
    )
}

function PlayerListItem({player, position}){
    let dataTarget = "#replaceModal" + position;
    function replacePlayer() {
        $(dataTarget).modal('hide');
        $.ajax({
            method: "POST",
            url: "api.php/replacePlayer/" + position + "/" + player._id
        }).done(function(response) {
            myTeamTab();
        });
    }


    return (
        <div className="row">
            <div className="col-4 playerlistitem">{player.name}</div>
            <div className="col-2 playerlistitem">{player.overall}</div>
            <div className="col-2 playerlistitem">{player.positions[0]}</div>            
            <div className="col-4 playerlistitem">
                <button className="btn btn-primary" data-toggle="modal" data-target={dataTarget} onClick={replacePlayer}>Replace</button>
            </div>
        </div>

    )
}