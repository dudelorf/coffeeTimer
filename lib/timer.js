function Timer(recipeObject, outputElem)
{
	this.methodName = recipeObject.methodName;
	this.volume = Number(recipeObject.defaultVolume);
	this.phases = [];
	for (var p = 0; p < recipeObject.phases.length; p++)
	{
		this.phases[p] = Number(recipeObject.phases[p]);
	}
	this.brewRatio = Number(recipeObject.brewRatio);
	this.displayObj = outputElem;
	initDisplay(this);
	this.timerActive = false;
	this.phaseNum = 0;
	this.phaseTime = 0;
	
	//getter functions
	Timer.prototype.getMethodName = function()
	{
		return this.methodName;
	}
	Timer.prototype.getVolume = function()
	{
		return this.volume;
	}
	Timer.prototype.getBrewRatio = function()
	{
		return this.brewRatio;
	}
	Timer.prototype.getTotalTime = function()
	{
		var totalTime = 0;
		for (var p = 0; p < this.phases.length; p++)
		{
			totalTime += this.phases[p];
		}
		return totalTime;
	}
	
	Timer.prototype.showTime = function(secs)
	//takes time in seconds and outputs formatted string to display object
	{
		outputStr = "";
		var mins = Math.floor(secs / 60);
		if (mins < 10)
		{
			mins = "0" + mins;
		}
		var seconds = secs % 60;
		if (seconds < 10)
		{
			seconds = "0" + seconds;
		}
		outputStr += mins + ":" + seconds;
		
		document.getElementById("clock").innerHTML = outputStr;
	}
	
	//interactivity functions
	Timer.prototype.increaseVolume = function()
	{
		if(!timerActive)
		this.volume += 1;
	}
	Timer.prototype.decreaseVolume = function()
	{
		if(!timerActive)
		this.volume -= 1;
	}
	Timer.prototype.startTimer = function()
	{
		this.phaseNum = 0;
		this.phaseTime = this.phases[this.phaseNum];
		//assign first memo
		this.phaseNum++;
		this.showTime(this.phaseTime);
		setTimeout(this.decreaseTime.bind(this), 300);
	}
	
	Timer.prototype.decreaseTime = function()
	{
		this.showTime(--this.phaseTime);
		if(this.phaseTime == 0)
		{
			if(this.phaseNum == this.phases.length)
			{
				alert("all done");
				this.timerActive = false;
				return;
			}
			else
			{
				this.phaseTime = this.phases[this.phaseNum] + 1;
				//assign next memo
				this.phaseNum++;
			}
		}
		setTimeout(this.decreaseTime.bind(this), 300);
	}
}

function initDisplay(that)
{
	var clock = document.createElement("p");
	clock.setAttribute("id", "clock");
	var memo = document.createElement("p");
	memo.setAttribute("id", "memo");
	memo.innerHTML = "Click start to begin";
	var startButton = document.createElement("input");
	startButton.setAttribute("type", "button");
	startButton.setAttribute("value", "Start");
	startButton.addEventListener("click", function(){that.startTimer();});
	that.displayObj.appendChild(clock);
	that.displayObj.appendChild(memo);
	that.displayObj.appendChild(startButton);
}