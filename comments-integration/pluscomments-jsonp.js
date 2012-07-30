// This one is just like pluscomments.js except it uses jsonp instead of the
// Google JavaScript API Client library

var commentr = commentr || {};
var apiKey = "YOUR_API_KEY";

// search for g-comments-for classes
commentr.go = function() {
  var fetchElements = document.getElementsByClassName('g-comments-for');
  for (var i = 0; i < fetchElements.length; i++) {
    var activityId = fetchElements[i].classList[1];
    commentr.fetchComments(activityId);
  }
}

// foreach fetch
commentr.fetchComments = function(activityId) {
  var fetchElement = document.createElement("script");
  fetchElement.src = 'https://www.googleapis.com/plus/v1/activities/' +
      activityId + '/comments?alt=json&pp=1&key=' + apiKey +
      '&callback=commentr.parseComments';
  document.body.appendChild(fetchElement);
}

// when fetch completes, parse the response and insert the records
commentr.parseComments = function(responseJson) {
  var activity = responseJson.items[0].inReplyTo[0];
  var comments = responseJson.items;

  //find element to insert into
  var insertionElements =
      document.getElementsByClassName('g-comments-for ' + activity.id);
  var insertionElement = insertionElements[0];

  var newContents = "";
  for (i = 0; i < comments.length; i++) {
    var actor = comments[i].actor;

    var commentBody = comments[i].object.content;

    //do the insertion
    newContents += "<dt><a href='" + actor.url + "'><img src='" +
        actor.image.url + "' /></a></dt>" + "<dd><a href='" + actor.url + "'>" +
        actor.displayName + "</a>: " + commentBody + "</dd>";

  }
  insertionElement.innerHTML = "<dl>" + newContents +
      "</dl> <p class='g-commentlink'>Please comment on the <a href='" +
      activity.url + "'>Google+ activity</a></p>";
}

// Append our program to the window.onload
document.addEventListener("DOMContentLoaded", commentr.go, false);
