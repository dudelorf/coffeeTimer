# Coffee Timer
An app for brewing custom coffee reipes

One of the best ways to unlock the flavors of specialty coffee is through manual brewing methods such as the Hario V60, Aeropress and French Press. As each different coffee and each particular brewing method require different parameters and techniques it be can easy to forget the particulars of a given recipe for producing that perfect cup. I built this app as a way to overcome that difficulty.

<h2>Getting Started</h1>

To get started click the register account button and register a username and password. This will create an account to store your recipes and supply you with a few basic recipes to get you started. No email is required...

<h2>Brewing a Recipe</h2>

On the recipes screen simply click on the recipe you want and the app will take you to the timer screen. On the timer screen  select the volume of coffee you want and the app will calculate how many grams of coffee are necessary. When you are ready click start and the timer will begin walking you through the recipe, alerting you when the next step is to be taken and providing instructions for each step (what techique to apply, how much water to add, etc...) When the timer finishes you should be all set with a delicious cup of coffee!

<h2>Creating and Editing recipes</h2>

Recipes can be created by clicking the menu button on the recipes screen and selecting either "New recipe" or "Edit recipe". You will be taken to the recipe form screen. There are a number of fields on this screen to customize your recipe:
<pre>
  Method name           The name of your recipe
  Default Volume        Default volume the timer starts with (good to set if you prefer to drink 16oz at a time for example)
  Brew Ratio            Adjusts total amout of coffee for recipe in milliliters of water per gram of coffee
  Grind size            Sets the default grind size to use
</pre>
<h3>Brew Phases</h3>
These are the various steps the recipe timer walks you through. Each phase has a set of properties that can be adjusted. At least one phase is required however you are free to add as many as needed. To remove a phase click the "Remove Phase" button below the phase you want removed. To add a phase click the "New Phase" button at the bottom. Phase properties are as follows:
<pre>
  Phase Memo            The note displayed for the active phase
  Phase Volume          Controls how much of the total brewing water is to be added during that phase in milliters water per gram coffee
  Phase Time            Sets the duration of the phase
</pre>
<h3>Dillution</h3>
If necessary you can set the recipe to include a dilution phase. Check the box at the bottom of the form and enter the dillution ratio in milliters of dillution water per gram of coffee to be added at end of the brew. This phase will display at the end of the brewing process

When all properties of a recipe are set as you want click the "Save Recipe" button and your new recipe will be added to the recipes screen

<h2>Deleting</h2>
If you no longer want a recipe listed select the "Delete Recipe" option from the menu on the recipes screen and click the recipe you want deleted. Click the "Finished Deleting" button when you want to exit delete mode.
