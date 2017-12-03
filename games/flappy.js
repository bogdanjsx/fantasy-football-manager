var brick;
var walls = [];
var score;

window.onload = function()
{
	start_game();
}

function start_game()
{
    brick = new component(30, 30, "red", 10, 120);
    brick.gravity = 0.05;
    score = new component("25px", "Consolas", "blue", 400, 40, "score");
    game.start();
}

var game =
{
    start : function()
	{
		this.canvas = document.getElementById('canvas');
		width = this.canvas.width;
		height = this.canvas.height;
        this.canvasContext = this.canvas.getContext('2d');
		
        this.frameCount = 0;
        this.interval = setInterval(updateGameArea, 20);
    },
    clear : function()
	{
        this.canvasContext.clearRect(0, 0, width, height);
    }
}

function component(width, height, color, x, y, type)
{
    this.type = type;
    this.score = 0;
    this.width = width;
    this.height = height;
    this.speedX = 0;
    this.speedY = 0;    
    this.x = x;
    this.y = y;
    this.gravity = 0;
    this.gravitySpeed = 0;
	
    this.update = function()
	{
        ctx = game.canvasContext;
		
        if (this.type == "score")
		{
            ctx.font = this.width + " " + this.height;
            ctx.fillStyle = color;
            ctx.fillText(this.text, this.x, this.y);
        } 
		else 
		{
            ctx.fillStyle = color;
            ctx.fillRect(this.x, this.y, this.width, this.height);
        }
    }
	
    this.newPosition = function()
	{
        this.gravitySpeed += this.gravity;
        this.x += this.speedX;
        this.y += this.speedY + this.gravitySpeed;
        this.hitBottom();
    }
	
    this.hitBottom = function()
	{
        var rockbottom = game.canvas.height - this.height;
        if (this.y > rockbottom)
		{
            this.y = rockbottom;
            this.gravitySpeed = 0;
        }
    }
	
    this.crashWith = function(obj)
	{
        var left = this.x;
        var right = this.x + this.width;
        var up = this.y;
        var down = this.y + this.height;
		
        var otherleft = obj.x;
        var otherright = obj.x + obj.width;
        var otherup = obj.y;
        var otherdown = obj.y + obj.height;
		
        var crash = true;
        if (down < otherup || up > otherdown || right < otherleft || left > otherright)
		{
            crash = false;
        }
        return crash;
    }
}

function updateGameArea()
{
    var x, height, gap, minHeight, maxHeight, minGap, maxGap;
	
    for (i = 0; i < walls.length; i += 1)
	{
        if (brick.crashWith(walls[i]))
		{
            return;
        } 
    }
    game.clear();
    game.frameCount += 1;
	
    if (game.frameCount == 1 || spawnWall(150))
	{
        x = game.canvas.width;
        minHeight = 20;
        maxHeight = 200;
        height = Math.floor(Math.random() * (maxHeight - minHeight + 1) + minHeight);
        minGap = 50;
        maxGap = 200;
        gap = Math.floor(Math.random() * (maxGap - minGap + 1) + minGap);
        walls.push(new component(10, height, "black", x, 0));
        walls.push(new component(10, x - height - gap, "black", x, height + gap));
    }
	
    for (i = 0; i < walls.length; i += 1)
	{
        walls[i].x += -1;
        walls[i].update();
    }
	
    score.text = game.frameCount;
    score.update();
    brick.newPosition();
    brick.update();
}

function spawnWall(n)
{
    if ((game.frameCount / n) % 1 == 0)
	{
		return true;
	}
    return false;
}

document.addEventListener('keydown', function(event)
{
	var key = event.which;
	
	if(key == "32")
	{
		brick.gravity = - 0.4;
	}
})

document.addEventListener('keyup', function(event)
{
	var key = event.which;
	
	if(key == "32")
	{
		brick.gravity = 0.1;
	}
})