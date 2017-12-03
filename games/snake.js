$(document).ready(function()
{
	//Create canvas and get properties
	var canvas = $("#canvas")[0];
	var canvasContext = canvas.getContext("2d");
	var canvasWidth = $("#canvas").width();
	var canvasHeight = $("#canvas").height();
	
	var cellWidth = 15;
	var direction;
	var food;
	var score;
	var maxScore = 0;
	
	//An array of cells that make the snake
	var snakeArray;
	
	function init()
	{
		direction = "right";
		create_snake();
		create_food();

		score = 0;
		
		//Snaked is moved at a 60ms timer
		if(typeof gameLoop != "undefined")
		{
			clearInterval(gameLoop);
		}
		
		gameLoop = setInterval(paint, 60);
	}
	
	init();
	
	//Create an horizontal snake starting from top left
	function create_snake()
	{
		var snakeLength = 10;
		snakeArray = []; 
		for(var i = snakeLength - 1; i >= 0; i --)
		{
			snakeArray.push({x: i, y : 0});
		}
	}
	
	//Creates the food at a random position
	function create_food()
	{
		food = 
		{
			x: Math.round(Math.random()*(canvasWidth - cellWidth) / cellWidth), 
			y: Math.round(Math.random()*(canvasHeight - cellWidth) / cellWidth), 
		};
		
		//Search for another place for food
		if(check_self_collision(food.x, food.y, snakeArray))
		{
			create_food();
		}
	}
	
	//Paint the background on every frame
	function paint()
	{
		//Paint the canvas
		canvasContext.fillStyle = "white";
		canvasContext.fillRect(0, 0, canvasWidth, canvasWidth);
		canvasContext.strokeStyle = "blue";
		canvasContext.lineWidth = 2;
		canvasContext.strokeRect(0, 0, canvasWidth, canvasWidth);
		canvasContext.font = "15px Arial";
		
		//Store the coordinates of the head cell of the snake
		var headX = snakeArray[0].x;
		var headY = snakeArray[0].y;
		
		//Change head coordinates based on head cell
		if(direction == "right")
		{
			headX++;
		}
		else if(direction == "left")
		{
			headX--;
		}
		else if(direction == "up") 
		{
			headY--;
		}
		else if(direction == "down")
		{
			headY++;
		}
		
		//Check for collisions with the canvas and with the game itself
		if(headX == -1 || headX == canvasWidth / cellWidth || headY == -1 || headY == canvasHeight / cellWidth || check_self_collision(headX, headY, snakeArray))
		{
			//Restart the game after 1 second
			//wait(1000);
			init();
			return;
		}
		
		//Create a new head instead of moving the tail when eating food
		if(headX == food.x && headY == food.y)
		{
			var tail = {x: headX, 
						y: headY
						};
			score++;
			
			create_food();
		}
		else
		{
			//Pop the last cell and make the tail the new head
			var tail = snakeArray.pop();
			tail.x = headX; tail.y = headY;
		}
		
		//tail is now the head
		snakeArray.unshift(tail);
		
		//Paint 15px wide snake cells
		for(var i = 0; i < snakeArray.length; i++)
		{
			var cell = snakeArray[i];
			paint_cell(cell.x, cell.y);
		}
		
		//Paint the food
		paint_cell_food(food.x, food.y);
		
		//Paint the scores
		paint_score(score);
		paint_best_score(score);
	}
	
	//Paints a cell at given locations
	function paint_cell(x, y)
	{
		canvasContext.fillStyle = "black";
		canvasContext.fillRect(x * cellWidth, y * cellWidth, cellWidth, cellWidth);
		canvasContext.strokeStyle = "white";
		canvasContext.strokeRect(x * cellWidth, y * cellWidth, cellWidth, cellWidth);
	}
	
	//Paints a food cell at given locations
	function paint_cell_food(x, y)
	{
		canvasContext.fillStyle = "red";
		canvasContext.fillRect(x * cellWidth, y * cellWidth, cellWidth, cellWidth);
		canvasContext.strokeStyle = "white";
		canvasContext.strokeRect(x * cellWidth, y * cellWidth, cellWidth, cellWidth);
	}
	
	//Check collision with the snake itself
	function check_self_collision(x, y, array)
	{
		for(var i = 0; i < array.length; i++)
		{
			if(array[i].x == x && array[i].y == y)
			 return true;
		}
		return false;
	}
	
	//Wait for the specified time in ms
	function wait(ms)
	{
	   var start = new Date().getTime();
	   var end = start;
	   while(end < start + ms)
		{
		 end = new Date().getTime();
		}
	}
	
	//Paint bottom left score
	function paint_score(score)
	{
		var scoreText = "Score: " + score;
		canvasContext.fillText(scoreText, 10, canvasHeight - 5);
	}
	
	//Paint bottom right best score
	function paint_best_score(score)
	{
		if(score > maxScore)
		{
			maxScore = score;
		}
		var gameOverText = "Best score: " + maxScore;
		canvasContext.fillText(gameOverText, canvasWidth - 100, canvasHeight - 5);
	}
	
	//Keyboard controls
	$(document).keydown(function(event)
	{
		var key = event.which;
		if(key == "65" && direction != "right")
		{
			direction = "left";
		}
		else if(key == "87" && direction != "down")
		{
			direction = "up";
		}
		else if(key == "68" && direction != "left")
		{
			direction = "right";
		}
		else if(key == "83" && direction != "up")
		{
			direction = "down";
		}
	})
})