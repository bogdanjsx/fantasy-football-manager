function PlayerCard({playerData}) {
  return (
    <div id="player" className="card-generator playercard centered card-large gold fut18">
    <div id="art" className="playercard-art not-draggable"></div>
    <div id="name" className="playercard-name">{playerData.name}</div>
    <div id="rating" className="playercard-rating">{playerData.overall}</div>
    <div id="position" className="playercard-position">{playerData.positions[0]}</div>
    
    <div id="attr1" className="playercard-attr playercard-attr1"><span>{playerData.pace}</span> <span className="playercard-thin">PAC</span></div>
    <div id="attr2" className="playercard-attr playercard-attr2"><span>{playerData.shooting}</span> <span className="playercard-thin">SHO</span></div>
    <div id="attr3" className="playercard-attr playercard-attr3"><span>{playerData.passing}</span> <span className="playercard-thin">PAS</span></div>
    <div id="attr4" className="playercard-attr playercard-attr4"><span>{playerData.dribbling}</span> <span className="playercard-thin">DRI</span></div>
    <div id="attr5" className="playercard-attr playercard-attr5"><span>{playerData.defending}</span> <span className="playercard-thin">DEF</span></div>
    <div id="attr6" className="playercard-attr playercard-attr6"><span>{playerData.physical}</span> <span className="playercard-thin">PHY</span></div>
    
    <div id="nation" className="playercard-nation change-nation draggable ui-draggable">
        <img src={playerData.flag}/>
    </div>
    <div id="club" className="playercard-club change-club draggable ui-draggable">
        <img src={playerData.club_logo}/>
    </div>
    <div id="picture" className="playercard-picture change-player draggable ui-draggable" style={{left: "50px", top: "17px"}}>
        <img src={playerData.photo}/>
    </div>
    </div>
  );
}  

var count = 11;
var randomPlayerList = generateRandomPlayer(count);

for(let i = 0; i < count; i++)
{
    var reactVar = React.createElement(PlayerCard, {"playerData" : randomPlayerList[i]});
    ReactDOM.render(reactVar, document.getElementById("players" + i));
}
