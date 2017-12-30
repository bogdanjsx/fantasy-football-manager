function AllPlayersList({items}) {
    return (
        <div className="managerlist btn-group" role="group">
            <div className="container">
            <div className="row">
                <div className="col-3 managerlistitem"><b>Name</b></div>
                <div className="col-2 managerlistitem"><b>Rating</b></div>
                <div className="col-2 managerlistitem"><b>Position</b></div>
                <div className="col-2 managerlistitem"><b>Price</b></div>
                <div className="col-3 managerlistitem"></div>
            </div>
                {items.map(function(listValue){
                    return <AllPlayersListItem key={listValue.name} player={listValue}/>;
                })}
            </div>
        </div>
    )
}

function AllPlayersListItem({player}){
    return (
        <div className="row">
            <div className="col-3 managerlistitem">{player.name}</div>
            <div className="col-2 managerlistitem">{player.overall}</div>
            <div className="col-2 managerlistitem">{player.positions[0]}</div>
            <div className="col-2 managerlistitem"><input type="text" className="form-control" placeholder="Price" /></div>            
            <div className="col-3 managerlistitem"><button className="btn btn-primary">Sell</button></div>
        </div>

    )
}