function TransferPlayerList({items}) {
    return (
        <div className="transferlist btn-group" role="group">
            <div className="container">
            <div className="row">
                <div className="col-3 transferlistitem"><b>Name</b></div>
                <div className="col-1 transferlistitem"><b>Rating</b></div>
                <div className="col-1 transferlistitem"><b>Position</b></div>
                <div className="col-2 transferlistitem"><b>Nationality</b></div>
                <div className="col-1 transferlistitem"><b>Club</b></div>
                <div className="col-2 transferlistitem"><b>Price</b></div>
                <div className="col-2 transferlistitem"></div>
            </div>
                {items.map(function(listValue){
                    return <TransferPlayerListItem key={listValue.name} player={listValue}/>;
                })}
            </div>
        </div>
    )
}

function TransferPlayerListItem({player}){

    function buyPlayer() {
        $("#loadingModal").modal('show');
        $.ajax({
            method: "POST",
            url: "api.php/buyPlayer/" + player._id + "/" +player.ownerId
        }).done(function(response) {
            transferMarketTab();
        });
    }

    return (
        <div className="row">
            <div className="col-3 transferlistitem">{player.name}</div>
            <div className="col-1 transferlistitem">{player.overall}</div>
            <div className="col-1 transferlistitem">{player.positions[0]}</div>
            <div className="col-2 transferlistitem"><img className="transferlistflagimage" src={player.flag} /></div>
            <div className="col-1 transferlistitem"><img className="transferlistclubimage" src={player.club_logo} /></div>
            <div className="col-2 transferlistitem">{player.price}</div>            
            <div className="col-2 transferlistitem"><button className="btn btn-primary" onClick={buyPlayer}>Buy</button></div>
        </div>

    )
}