function Timer(recipeObject)
{
	this.methodName = recipeObject.methodName;
	this.volume = Number(recipeObject.defaultVolume);
	this.phases = [];
	for (var p = 0; p < recipeObject.phases.length; p++)
	{
		phases[p] = Number(recipeObject.phases[p]);
	}
	this.brewRatio = Number(recipeObject.brewRatio);
	
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
	
	Timer.prototype.increaseVolume = function()
	{
		this.volume += 1;
	}
	Timer.prototype.decreaseVolume = function()
	{
		this.volume -= 1;
	}
}