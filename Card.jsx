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
    let chemistryColor = "red";
    if (player.chemistry > 4) {
        chemistryColor = "#d8b82b";
        if (player.chemistry > 7) {
            chemistryColor = "#2bd136";
        }
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
            <div className="playercard-attr playercard-chem" style={{color: chemistryColor}}>Chm: {player.chemistry}</div> 
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
