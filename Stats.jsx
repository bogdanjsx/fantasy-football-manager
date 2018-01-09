function Stats({stats}) {
    console.log(stats)
    return (
        <div>
            <h1>{stats.team_name}</h1>
            <div><img src={stats.favourite_team_logo}/></div>
            <br/>
            <div className="container statsRecordBox">
                <div className="row">
                    <div className="col-12"><b>Wins: </b>{stats.record.wins}</div>
                </div>
                <div className="row">
                    <div className="col-12"><b>Draws: </b>{stats.record.draws}</div>
                </div>
                <div className="row">
                    <div className="col-12"><b>Losses: </b>{stats.record.losses}</div>
                </div>
                <br/>
                <div className="row">
                    <div className="col-12"><b>Goals for: </b>{stats.goal_difference.goals_for}</div>
                </div>
                <div className="row">
                    <div className="col-12"><b>Goals against: </b>{stats.goal_difference.goals_against}</div>
                </div>
            </div>
            <br/>
            <p><img className="coinIcon" src="https://cdn1.iconfinder.com/data/icons/money-finance-set-3/512/11-512.png"></img><b>  {stats.coins}</b></p> 
        </div>
    )
}
