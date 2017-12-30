function PlayerList({items}) {
    return (
        <div className="playerlist btn-group" role="group">
            <div className="container">
            <div className="row">
                <div className="col-4 playerlistitem"><b>Player name</b></div>
                <div className="col-2 playerlistitem"><b>Player rating</b></div>
                <div className="col-2 playerlistitem"><b>Player position</b></div>
                <div className="col-4 playerlistitem"></div>
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
            <div className="col-4 playerlistitem">{player.name}</div>
            <div className="col-2 playerlistitem">{player.overall}</div>
            <div className="col-2 playerlistitem">{player.overall}</div>            
            <div className="col-4 playerlistitem"><button className="btn btn-primary" data-toggle="modal" data-target="#replaceModal">Replace</button></div>
        </div>

    )
}