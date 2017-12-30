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