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