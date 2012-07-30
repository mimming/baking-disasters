// Constants
const BACKEND_BASE_URI = "https://bakingdisasters.com/potluck-party-planner/final";

// Globals
var chefHatOverlay;

function init() {
  // Set up the chef hat overlay for use later
  createHatOverlay();
  var apiReady = function(eventObj) {
    if (eventObj.isApiReady) {

      // set up the handlers for callback from the hangout
      gapi.hangout.data.onStateChanged.add(function(eventObj) {
        updateUi();
      });
      gapi.hangout.onParticipantsChanged.add(function(eventObj) {
        updateUi();
      });

      // Render the initial interface
      updateUi();

      gapi.hangout.onApiReady.remove(apiReady);
    }
  };

  gapi.hangout.onApiReady.add(apiReady);
}

// register our init function to execute when the app starts
gadgets.util.registerOnLoadHandler(init);

/**
 * Executed when a user clicks on a recipe selecting it for this session
 */
function recipeSelect() {
  // get the recipe and ingredients
  var recipeId = this.id.split("_")[1];
  var ingredientResponse = getIngredients(recipeId);

  // set the recipe in the shared state
  var delta = {'recipeName':ingredientResponse.name};
  var ingredients = new Array();
  for(var i in ingredientResponse.ingredients) {
    ingredients.push({"claimedBy":null, "ingredient":ingredientResponse.ingredients[i]});
  }
  // set the stringified ingredient list into the state
  delta['ingredients'] = JSON.stringify(ingredients);

  // submit both updates in a single submitDelta call to save on quota
  gapi.hangout.data.submitDelta(delta);
}

function recipeUnselect() {
  // Clearing the ingredients will return us to the recipe page
  gapi.hangout.data.clearValue('ingredients');
}
/**
 * Executed when a user clicks on an ingredient to bring
 */
function ingredientSelect() {
  var claimId = this.id.split("_")[1];

  // get the current ingredient list
  var ingredients = JSON.parse(gapi.hangout.data.getValue('ingredients'));
  ingredients[claimId].claimedBy = gapi.hangout.getParticipantId();

  // update the ingredient list in the shared state
  gapi.hangout.data.setValue('ingredients', JSON.stringify(ingredients));
}

/**
 * Helper function refreshes the UI. Set as a callback on shared state change
 */
function updateUi() {
  // If there's no ingredient state, display the recipes list
  if (gapi.hangout.data.getValue('ingredients') == null) {
    renderRecipes();
  } else {
    renderIngredients();
    assignChefHat();
    renderReminderButton();
  }
}

function renderRecipes() {
  // Toggle the recipe list to on
  document.getElementById("loading").style.display = "none";
  document.getElementById("ingredient-list-content").style.display = "none";
  document.getElementById("recipe-list-content").style.display = "block";

  // get the list of recipes
  var recipes = getRecipes();

  var recipeList = document.getElementById("recipe-list");
  // clear out the recipe list
  recipeList.innerHTML = "";
  // render the recipe list
  for (var i in recipes) {
    var recipeElement = document.createElement("li");
    var recipeImage = document.createElement("img");
    recipeImage.src = recipes[i].imageUrl;
    recipeElement.appendChild(recipeImage);
    recipeElement.appendChild(document.createTextNode(recipes[i].name));
    recipeElement.id = "recipe_" + recipes[i].id;
    recipeElement.addEventListener("click", recipeSelect, false);
    recipeList.appendChild(recipeElement);
  }
}
/**
 * Refresh the ingredients UI
 */
function renderIngredients() {
  // only run if ingredients are set in the shared state
  if(gapi.hangout.data.getValue('ingredients') == null) {
    return;
  }

  // Toggle the ingredient list on
  document.getElementById("loading").style.display = "none";
  document.getElementById("ingredient-list-content").style.display = "block";
  document.getElementById("recipe-list-content").style.display = "none";

  // update the selected recipe
  var recipeName = document.getElementById("recipe-name");
  console.log("setting recipe name");
  recipeName.innerText = gapi.hangout.data.getValue('recipeName');
  console.log("set to "+gapi.hangout.data.getValue('recipeName'));

  // build ingredients list html from state
  var claimList = document.getElementById("claim-list");
  claimList.innerHTML = "";
  var ingredients = JSON.parse(gapi.hangout.data.getValue('ingredients'));
  for (var i in ingredients) {
    var claimRow = document.createElement("tr");
    claimRow.id = "ingredient_" + i;
    claimRow.addEventListener("click", ingredientSelect, false);
    var who = document.createElement("td");
    var what = document.createElement("td");

    if (ingredients[i].claimedBy) {
      // get the currently viewing user
      var claimPerson = gapi.hangout.getParticipantById(ingredients[i].claimedBy);
      var imageUrl = claimPerson.person.image.url;
      var name = claimPerson.person.displayName;

      // add the HTML
      if(imageUrl != null && imageUrl != "") {
        var userImageNode = document.createElement("img");
        userImageNode.src= imageUrl;
        who.appendChild(userImageNode)
      }
      who.appendChild(document.createTextNode(name));
    } else {
      who.appendChild(document.createTextNode("Nobody :("));
    }

    what.appendChild(document.createTextNode(ingredients[i].ingredient));
    claimRow.appendChild(who);
    claimRow.appendChild(what);
    claimList.appendChild(claimRow);
  }
}

/**
 * Refresh the chef hat assignment
 */
function assignChefHat() {
  // go through the shared state find top claimer
  var ingredients = JSON.parse(gapi.hangout.data.getValue('ingredients'));
  var totals = new Array();

  for(var i in ingredients) {
    var ingredient = ingredients[i];
    var person = ingredient.claimedBy;
    if(person != null) {
      if(totals[person]) {
        totals[person]++;
      } else {
        totals[person] = 1;
      }
    }
  }

  var hatOwner = null;
  var currentMax = 0;
  for(person in totals) {
    if(totals[person] > currentMax) {
      currentMax = totals[person];
      hatOwner = person;
    }
  }
  console.log(hatOwner + " gets the hat with total of " + currentMax);

  // Turn on the hat if I'm the winner :)
  if(hatOwner == gapi.hangout.getParticipantId()) {
    chefHatOverlay.setVisible(true);
  } else {
    chefHatOverlay.setVisible(false);
  }
}

/**
 * Run during init to create the chef hat overlay for use later
 */
function createHatOverlay() {
  var chefHat = gapi.hangout.av.effects.createImageResource(
    BACKEND_BASE_URI + '/images/chef_hat.png');
  chefHatOverlay = chefHat.createFaceTrackingOverlay(
    {'trackingFeature':
      gapi.hangout.av.effects.FaceTrackingFeature.NOSE_ROOT,
      'scaleWithFace': true,
      'rotateWithFace': true,
      'scale': 4,
      'offset': {x: 0, y: -0.3}});
}

/**
 * Add a share link to insert a reminder
 */
function renderReminderButton() {
  // Gather the ingredients that I've committed to from the shared state
  var ingredients = JSON.parse(gapi.hangout.data.getValue('ingredients'));
  var participantId = gapi.hangout.getParticipantId();
  var reminderText = gapi.hangout.getParticipantById(participantId).person.displayName;
  reminderText += " will bring: ";

  for (var i in ingredients) {
    if(ingredients[i].claimedBy == participantId) {
      reminderText += ingredients[i].ingredient + "%0A";
    }
  }
  if(reminderText.length > 0) {
    // Create a share link that targets a page which displays a reminder
    var reminderShareLink = document.getElementById("reminder-share-link");

    // construct a reminder URL with the whole shared state in a GET param
    var reminderUrl = "https://plus.google.com/share?url=" +
      encodeURIComponent(BACKEND_BASE_URI + "/reminder.php?reminder=" +
        encodeURIComponent(reminderText));

    // Update the href to point to the right thing
    reminderShareLink.href = reminderUrl;

    // Add or update the click event
    reminderShareLink.onclick = function() {
      window.open(reminderUrl, '',
        'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');
    };

    // Display the link if it's still hidden
    reminderShareLink.style.display = "inline";
  }
}

function hideReminderButton() {
  document.getElementById("reminder-share-link").style.display = "none";
}
/**
 * Gets ingredients via synchronous AJAX
 */
function getIngredients(id) {
  var req = new XMLHttpRequest();
  req.open("GET", BACKEND_BASE_URI + "/api.php?ingredients=true&id=" + id, false);
  req.send(null);
  return JSON.parse(req.responseText);
}

/**
 * Gets the recipes list via synchronous AJAX
 */
function getRecipes() {
  var req = new XMLHttpRequest();
  req.open("GET", BACKEND_BASE_URI + "/api.php?recipes=true", false);
  req.send(null);
  return JSON.parse(req.responseText);
}