/*
Timer class
Holds information from coffee recipe and implements timer functionality
*/ 

function Timer(
			recipeObject,
			startBtn, stopBtn, increaseVolBtn, decreaseVolBtn,
			method, volDisplay, clock, gCoffee, gWater, memo){

	//extract data from recipe object
	this.volume = Number(recipeObject.defaultVolume);
	this.phaseTimes = [];
	this.phaseMemos = [];
	this.phaseRatios = [];
	for (var p = 0; p < recipeObject.phaseTimes.length; p++)
	{
		this.phaseTimes[p] = Number(recipeObject.phaseTimes[p]);
		this.phaseMemos[p] = recipeObject.phaseMemos[p];
		this.phaseRatios[p] = recipeObject.phaseRatios[p];
	}
	this.brewRatio = Number(recipeObject.brewRatio);
	
	//assign input elements
	this.startButton = startBtn;
	this.stopButton = stopBtn;
	this.increaseVolumeButton = increaseVolBtn;
	this.decreaseVolumeButton = decreaseVolBtn;
	
	//assign output elements
	this.volumeDisplay = volDisplay;
	this.clockDisplay = clock;
	this.gramsCoffeeDisplay = gCoffee;
	this.gramsWaterDisplay = gWater;
	this.memoDisplay = memo;
	
	//functional properties
	this.timerActive = false;
	this.phaseNum = 0;
	this.phaseTime = 0;
	this.waterTracker = 0;
	this.waterForPhase = [];
	
	Timer.prototype.activateDisplay = function()
	//sets values to all output elements and adds event handlers to inputs
	{
	//add event handlers to inputs
	this.startButton.addEventListener("click", this.startTimer.bind(this));
	this.increaseVolumeButton.addEventListener("click", this.changeVolume.bind(this, "+"));
	this.decreaseVolumeButton.addEventListener("click", this.changeVolume.bind(this, "-"));
	
	//inialize water for phases
	this.waterForPhase = this.getWaterArr();
	
	//initialize output elements
	method.innerHTML = recipeObject.methodName;
	this.volumeDisplay.innerHTML = this.volume;	
	this.clockDisplay.innerHTML = this.showTime(this.getTotalTime());
	this.gramsCoffeeDisplay. innerHTML = this.getGramsCoffee(this.volume, this.brewRatio);
	this.gramsWaterDisplay.innerHTML = this.getTotalWater();
	this.memoDisplay.innerHTML = "Click start to begin";
	};
	

	Timer.prototype.getTotalTime = function()
	//returns total brew time
	{
		var totalTime = 0;
		for (var p = 0; p < this.phaseTimes.length; p++)
		{
			totalTime += this.phaseTimes[p];
		}
		return totalTime;
	};
	
	Timer.prototype.showTime = function(secs)
	//takes time in seconds and returns formatted string
	{
		var outputStr = "";
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
		
		return outputStr;
	};
	
	//interactivity functions
	Timer.prototype.increaseVolume = function()
	{
		if(!this.timerActive)
		this.volume += 1;
	};
	Timer.prototype.decreaseVolume = function()
	{
		if(!this.timerActive)
		this.volume -= 1;
	};
	
	Timer.prototype.changeVolume = function(opr)
	//adjusts volume and update display accordingly
	{
		//changes volume
		if (opr == '+') this.increaseVolume();
		else this.decreaseVolume();
		
		//updates display
		this.volumeDisplay.innerHTML = this.volume;
		this.gramsCoffeeDisplay.innerHTML = this.getGramsCoffee(this.volume, this.brewRatio);
		this.waterForPhase = this.getWaterArr(); //calculates water for phases with new volume
		this.gramsWaterDisplay.innerHTML = this.getTotalWater();
	};
	
	Timer.prototype.startTimer = function()
	{
		this.timerActive = true;
		this.phaseNum = 0;
		this.waterTracker = 0;

		//assign first memo
		document.getElementById("memo").innerHTML = this.phaseMemos[this.phaseNum];

		//show first phase time
		this.phaseTime = this.phaseTimes[this.phaseNum];
		document.getElementById("clock").innerHTML = 
								this.showTime(this.phaseTime);
		//calculate water for phase
		this.waterTracker += this.waterForPhase[this.phaseNum];
		this.gramsWaterDisplay.innerHTML = this.waterTracker;
		
		this.phaseNum++;
		
		setTimeout(this.decreaseTime.bind(this), 100);
	};
	
	Timer.prototype.getWaterArr = function()
	//returns array contain ml of water for each phase
	{
		var waterArr = [];
		for (var p = 0; p < this.phaseRatios.length; p++)
		{
			waterArr[p] = this.getWaterForPhase(p);
		}
		return waterArr;
	};
	
	Timer.prototype.getWaterForPhase = function(phaseNumber)
	//returns ml of water for supplied phase number
	{
		var mlWater = 0;
		var grams = this.getGramsCoffee(this.volume, this.brewRatio);
		mlWater = Math.round(grams * this.phaseRatios[phaseNumber]);
		return mlWater;
	};
	
	Timer.prototype.getTotalWater = function()
	//returns total ml of water for recipe
	{
		var totalWater = 0;
		for (var p = 0; p < this.waterForPhase.length; p++)
		{
			totalWater += this.waterForPhase[p];
		}
		return totalWater;
	};
	
	Timer.prototype.decreaseTime = function()
	{
		document.getElementById("clock").innerHTML =
							this.showTime(--this.phaseTime);
		if(this.phaseTime === 0)
		//phase has completed
		{
			if(this.phaseNum == this.phaseTimes.length)
			{
				alert("all done");
				this.timerActive = false;
				return;
			}
			else
			//move to next phase
			{
				this.phaseTime = this.phaseTimes[this.phaseNum] + 1;
				//assign next memo
				document.getElementById("memo").innerHTML = this.phaseMemos[this.phaseNum];
				//calculate water for phase and update display
				this.waterTracker += this.waterForPhase[this.phaseNum];
				this.gramsWaterDisplay.innerHTML = this.waterTracker;
				
				this.phaseNum++;
			}
		}
		setTimeout(this.decreaseTime.bind(this), 100);
	};

	//utility functions to calculate recipe details
	Timer.prototype.getGramsCoffee = function(vol, brewRatio)
	//returns grams of coffee when supplied volume in oz and brew ratio in gramsCoffee/ozWater
	{
		var mlVol = vol * 29.5 /*ml/oZ*/;
		var gCoffee = mlVol / (brewRatio - 1.5/*retained water*/);
		return Math.round(gCoffee);
	};
	
}