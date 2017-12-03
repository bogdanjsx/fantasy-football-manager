var canvas, canvasContext, width, height;
var ball, paddle1, paddle2;
var score1, score2;

var create_ball = function(x, y)
{
	this.x = x;
	this.y = y;
	this.color = "red";
	this.radius = 5;
	this.vx = 7;
	this.vy = -7;
}

var create_paddle1 = function(x, y) 
{
	this.x = x;
	this.y = y;
	this.color = "black";
	this.width = 100;
	this.height = 5;
}

var create_paddle2 = function(x, y)
{
	this.x = x;
	this.y = y;
	this.color = "black";
	this.width = 100;
	this.height = 5;
}

document.addEventListener('keydown', function(event)
{
	var key = event.which;
	
	//left of right for both players
	if(key == "37")
	{
		paddle1.x -= 30; 
	} 
	else if(key == "39")
	{
		paddle1.x += 30;
	}
	else if(key == "65")
	{
		paddle2.x -= 30;
	} 
	else if(key == "68")
	{
		paddle2.x += 30;
	}

	//collisions with the canvas
	if(paddle1.x <= 0)
	{
		paddle1.x = 0;
	} 
	else if(paddle1.x >= width - paddle1.width)
	{
		paddle1.x = width - paddle1.width;
	}

	if(paddle2.x <= 0)
	{
		paddle2.x = 0;
	}
	else if(paddle2.x >= width - paddle2.width)
	{
		paddle2.x = width - paddle2.width;
	}
});

window.onload = function()
{
	canvas = document.getElementById('canvas');
	canvasContext = canvas.getContext('2d');
	width = canvas.width;
	height = canvas.height;
	
	ball = new create_ball(width/2, height/2);
	paddle1 = new create_paddle1(width/2, height - 20);
	paddle2 = new create_paddle2(width/2, 20);
	
	score1 = 0;
	score2 = 0;
	beginGame();
}

function beginGame()
{
	requestAnimationFrame(beginGame);
	canvasContext.clearRect(0, 0, width, height);
	canvasContext.strokeStyle = "blue";
	canvasContext.lineWidth = 2;
	canvasContext.strokeRect(0, 0, width, height);
	ball.x += ball.vx;
	ball.y += ball.vy;

	canvasContext.fillStyle = ball.color;
	canvasContext.beginPath();
	canvasContext.arc(ball.x, ball.y, ball.radius, 0, 2 * Math.PI, true);
	canvasContext.closePath();
	canvasContext.save();  
	canvasContext.shadowBlur = 25;
	canvasContext.shadowOffsetX = 4;  
	canvasContext.shadowOffsetY = 4;  
	canvasContext.shadowColor = "red";
	canvasContext.fill();
	canvasContext.restore();

	canvasContext.fillStyle = paddle1.color;
	canvasContext.beginPath();
	canvasContext.save();  
	canvasContext.shadowBlur = 20;
	canvasContext.shadowOffsetX = 4;  
	canvasContext.shadowOffsetY = 4;  
	canvasContext.shadowColor = "black";
	canvasContext.fillRect(paddle1.x, paddle1.y, paddle1.width, paddle1.height);
	canvasContext.closePath();
	canvasContext.restore();

	canvasContext.fillStyle = paddle2.color;
	canvasContext.beginPath();
	canvasContext.save();  
	canvasContext.shadowBlur = 20;
	canvasContext.shadowOffsetX = -4;  
	canvasContext.shadowOffsetY = -4;  
	canvasContext.shadowColor = "black";
	canvasContext.fillRect(paddle2.x, paddle2.y, paddle2.width, paddle2.height);
	canvasContext.closePath();
	canvasContext.restore();
	
	//collision with walls
	if(ball.y + ball.radius > height || ball.y - ball.radius < 0)
	{
		ball.vy = -ball.vy;
	}
	if(ball.x + ball.radius > width || ball.x - ball.radius < 0)
	{
		ball.vx = -ball.vx;
	}

	//collision with first paddle
	else if(ball.y + ball.radius >= height - paddle1.height - 20)
	{
		if(ball.x + ball.radius >= paddle1.x && ball.x + ball.radius <= paddle1.x + paddle1.width)
		{
			ball.vy = -ball.vy;	
		}
		else
		{	
			score1 ++;
		}
	}
	//collision with second paddle
	else if(ball.y - ball.radius <= 20)
	{
		if(ball.x + ball.radius >= paddle2.x && ball.x + ball.radius <= paddle2.x + paddle2.width)
		{
			ball.vy = -ball.vy;
		}
		else
		{
			score2 ++;
		}
	}

	var score1Text = "Wall hits: " + score1;
	var score2Text = "Wall hits: " + score2;
	
	canvasContext.fillText(score2Text, 10, height - 5);
	canvasContext.fillText(score1Text, 10, 40);
}
