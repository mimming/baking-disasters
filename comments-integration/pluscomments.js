var commentr = commentr || {};
var apiKey = "AIzaSyDrH_5j2-cPK7EZRANWjA6_g0xCZRrxH-U";

function commentrLoad() {
  gapi.client.load('plus', 'v1', commentr.go);
  gapi.client.setApiKey(apiKey);
}

// search for g-comments-for classes
commentr.go = function() {
  //  Find the comment elements to process
  var fetchElements = document.getElementsByClassName('g-comments-for');
  for (var i=0; i < fetchElements.length; i++) {
    var activityId = fetchElements[i].classList[1];
    commentr.fetchComments(activityId);
  }
}

commentr.fetchComments = function(activityId) {
  var request = gapi.client.plus.comments.list({
    'activityId': activityId,
    'maxResults': '100'
  });
  request.execute(commentr.parseComments);
}

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

