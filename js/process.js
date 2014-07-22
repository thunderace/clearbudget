var action = '';
var currentAnchor = null;
var sessionTime = setTimeout("logout()", 910000); // 15mns+10secs
var keyPressCounter = 0;
var hiddenDivs=new Array("div_menus","div_graphs","div_summary","div_debits","div_credits");
var tl = null;
var eventSourceDebit = new Timeline.DefaultEventSource();
var eventSourceCredit = new Timeline.DefaultEventSource();
var tlResizeTimerID = null;

function clearDiv(div) {
  $("#"+div).html('');
  $("#"+div).hide();
  }

function loadCookie() {
  for(i=0;i<hiddenDivs.length;i++) {
    if($.cookie(hiddenDivs[i]) == "hidden") {
      var divToToggle = hiddenDivs[i].substr(4);
      $('#'+divToToggle).hide(); // hide the div
      $('#'+hiddenDivs[i]).children(".openDiv").css({'background-image' : 'url(style/icons/arrow_out.png)'}) // change the button
      // remove the header from the printing
      $('#'+hiddenDivs[i]).removeClass("print");
      $('#'+hiddenDivs[i]).addClass("noprint");
      }
    }
  }

function showLoading(show) {
  if(show) $('#loading').attr({src: "style/loading.gif"});
  else  $('#loading').attr({src: "style/icons/accept.png"});
  }

// checking if there are anchor changes, if there are, sends the ajax petition
function checkAnchor(){
  var toDiv = "main";
  //Check for changes
  if(currentAnchor != document.location.hash) {
    // get the existing anchor
    currentAnchor = document.location.hash;
    // special cases where we do not follow the anchor
    if(currentAnchor == '#search') {
      searchFormSubmit();
      return;
      }
    //if no anchor, loads the default section
    if(!currentAnchor) {
      query = "action=home&ajax=1";
      }
    else  {
      //Creates the string callback. This converts the url URL/#main&id=2 in URL/?section=main&id=2
      var splits = currentAnchor.substring(1).split('&');
      //Get the action
      var action = splits[0];
      delete splits[0];
      //Create the params string
      var params = splits.join('&');
      // check if action as a div information
      var divsplits  = action.split(':');
      if(divsplits.length == 2) {
        if($("#"+divsplits[1]).length != 0) {
          toDiv = divsplits[1];
          $("#"+toDiv).show();
          }
        action = divsplits[0] + "&toDiv=" + toDiv;
        }
      var query = "action=" + action + "&ajax=1" + params;
      }

    //event.preventDefault();
    resetLogout();
    // close any open timelinebubble
    closeBubbles();
    // show the loading div
    showLoading(true);
    // load the content
    $("#"+toDiv).load("index.php", query, function(){
      showLoading(false);
      // load the config form the cookie
      loadCookie();
      // if a table is set to be sortable, add the sortable engine to it
      if($("#listItemsTable").length>0) {
          $("#listItemsTable").tablesorter().tablesorterPager({container: $("#pager")});
        }
      // start the timeline
      doTimeLine();
      });
    }
  }

// start the timeLine engine
function doTimeLine() {
  // if the Div is not present, do nothing
  if($("#transactionTimeline").length == 0) return;
  // center the timeline to today's date
  var dt = new Date(); // today
  var d=Timeline.DateTime.parseGregorianDateTime(dt.toUTCString());
  var theme = Timeline.ClassicTheme.create();
  theme.event.bubble.width = 420;
  theme.event.bubble.height = 220;
  theme.event.track.height = 1.5;
  theme.event.track.gap = 0.1;
  theme.ether.backgroundColors = [
    "#D1CECA",
    "#E7DFD6",
    "#E8E8F4",
    "#D0D0E8"
    ];
  var bandInfos = [
    Timeline.createBandInfo({
      width:          "10%",
      intervalUnit:   Timeline.DateTime.YEAR,
      intervalPixels: 500,
      eventSource:    eventSourceDebit,
      date:           d,
      showEventText:  false,
      trackHeight:    0.5,
      trackGap:       0.2,
      theme:          theme
      }),
    Timeline.createBandInfo({
      width:          "40%",
      intervalUnit:   Timeline.DateTime.MONTH,
      intervalPixels: 250,
      eventSource:    eventSourceDebit,
      date:           d,
      theme:          theme
      }),
    Timeline.createBandInfo({
      width:          "40%",
      intervalUnit:   Timeline.DateTime.MONTH,
      intervalPixels: 250,
      eventSource:    eventSourceCredit,
      date:           d,
      theme:          theme
      }),
    Timeline.createBandInfo({
      width:          "10%",
      intervalUnit:   Timeline.DateTime.YEAR,
      intervalPixels: 500,
      eventSource:    eventSourceCredit,
      date:           d,
      showEventText:  false,
      trackHeight:    0.5,
      trackGap:       0.2,
      theme:          theme
      })
    ];
  bandInfos[0].syncWith = 1;
  bandInfos[0].highlight = true;
  bandInfos[0].eventPainter.setLayout(bandInfos[1].eventPainter.getLayout());
  bandInfos[2].syncWith = 1;
  bandInfos[3].syncWith = 2;
  bandInfos[3].highlight = true;
  bandInfos[3].eventPainter.setLayout(bandInfos[2].eventPainter.getLayout());
  tl = Timeline.create(document.getElementById("transactionTimeline"), bandInfos, Timeline.HORIZONTAL);
  loadTimelineEvents();
  /* // styling the bubble
  Timeline.DurationEventPainter.prototype._showBubble = function(x, y, evt) {
      alert (evt.getDescription ());
    }
  */
  }

// load or reload timeline events from the server
function loadTimelineEvents() {
  // if the Div is not present, do nothing
  if($("#transactionTimeline").length == 0) return;
  // if the timeline is not instantiated yet, do nothing
  if(tl == null) return;
  // load the data
  eventSourceDebit = tl.getBand(1).getEventSource();
  eventSourceDebit.clear();
  tl.loadXML("index.php?ajax=1&action=overview-timeline-data&debit=1", function(xml, url) { eventSourceDebit.loadXML(xml, url); });
  eventSourceCredit = tl.getBand(2).getEventSource();
  eventSourceCredit.clear();
  tl.loadXML("index.php?ajax=1&action=overview-timeline-data&debit=0", function(xml, url) { eventSourceCredit.loadXML(xml, url); });
}

// force any open bubbles to close down
function closeBubbles() {
  if($("#transactionTimeline").length >0 && tl!=null) {
    tl.getBand(0).closeBubble();
    tl.getBand(1).closeBubble();
    tl.getBand(2).closeBubble();
    tl.getBand(3).closeBubble();
    }
  }

// manage the wizard steps
function wizard(id) {
  switch(id) {
    case 1:
      $('#wizard').load('index.php', {'ajax':1, 'action': 'editCategories'},function(){  });
      $('#step1').attr({src: "style/icons/transmit_go.png"});
      $('#step2,#step3,#step4').attr({src: "style/icons/transmit.png"});

    break;
    case 2:
      $('#wizard').load('index.php', {'ajax':1, 'toDiv': 'wizard', 'action':'addTransactionsWizard'},function(){
        $('#subAction1').load('index.php', {'ajax':1, 'toDiv': 'subAction1', 'action':'addTransactionsManualy'},function(){  });
      });
      $('#step2').attr({src: "style/icons/transmit_go.png"});
      $('#step1,#step3,#step4').attr({src: "style/icons/transmit.png"});
      $('#step2button').removeAttr("disabled");
    break;
    case 3:
      $('#wizard').load('index.php', {'ajax':1, 'action': 'editKeywords'},function(){  });
      $('#step3').attr({src: "style/icons/transmit_go.png"});
      $('#step1,#step2,#step4').attr({src: "style/icons/transmit.png"});
    break;
    case 4:
      $('#wizard').load('index.php', {'ajax':1, 'action': 'autoFileItem'},function(){  });
      $('#step4').attr({src: "style/icons/transmit_go.png"});
      $('#step1,#step2,#step3').attr({src: "style/icons/transmit.png"});
    break;
    default:
      $('#wizard').load('index.php', {'ajax':1, 'action': 'editCategories'},function(){  });
    break;
    }
  return false;
  }

// enable the wizard steps
function enableWizardStep(id) {
  switch(id) {
    case 2:
      $('#step2button').removeAttr("disabled");
    break;
    case 3:
      $('#step3button').removeAttr("disabled");
    break;
    case 4:
      $('#step4button').removeAttr("disabled");
    break;
    }
  return false;
  }

// perform a search based on a keyword
function searchFormSubmit() {
    resetLogout();
    searchKey = $("#keys").val();
    if(searchKey.length < 3) return false;
    showLoading(true);
    $('#searchIcon').attr({src: "style/icons/hourglass.png"});
    keyPressCounter++;
    $.ajax({
      type: "POST", timeout: 15000, url: "index.php", data: "ajax=1&action=search&keyWord="+searchKey, cache: false,
      success: function(html){
        $("#main").empty();
        $("#main").prepend(html);
        $("#main").show();
        $("#listItemsTable").tablesorter().tablesorterPager({container: $("#pager")});
        keyPressCounter--;
        if(keyPressCounter == 0) {
          showLoading(false);
          $('#searchIcon').attr({src: "style/icons/zoom.png"});
          }
        }
      });
    return false;
    }

// perform a file upload to the server
function ajaxFileUpload()	{
    resetLogout();
    // show the loading on start
		showLoading(true);
		// hide the report div on start
		$('#importDetails').ajaxStart(function(){
    		$(this).hide();
		});
		showLoading(false);
		// hide any errors
    $("#qifUploadError").ajaxStart(function(){
			$(this).hide();
		});
		// show the loading file div on start
    $("#qifUploadLoading").ajaxStart(function(){
			$(this).show();
		});
	  // hide the upload button on start
		$("#jqUploader").ajaxStart(function(){
			$(this).hide();
		});
		// hide the result div on start
    $("#qifUploadResultCount").ajaxStart(function(){
			$(this).hide();
		});
		// hide the error div on start
		$("#qifUploadError").ajaxStart(function(){
      $(this).hide();
    });
		action = $("#action").val();
		dateLocale = $("input[name=QIFLocaleType]:checked:checked").val();
    CSVFieldsInFirstLine = $("input[name=CSVFieldsInFirstLine]:checked:checked").val();
		$.ajaxFileUpload ({
				url:'index.php?action='+action+'&QIFLocaleType='+dateLocale+'&CSVFieldsInFirstLine='+CSVFieldsInFirstLine+'&ajax=1',
				secureuri:false,
				fileElementId:'fileToUpload',
				dataType: 'json',
				success: function (data, status) {
				  $("#qifUploadLoading").hide();
					if(data.error == true) {
						$("#qifUploadError").show();
						$("#qifUploadErrorText").text(data.msg);
						$("#jqUploader").show();
					 }
					else {
					 $("#qifUploadError").hide();
					  $("#fileToUpload").val('');
					  $("#jqUploader").show();
            $("#qifUploadResultCount").show();
            $("#qifUploadResultCount").text(data.msg);
            if(data.importCount > 0) {
              // show the imported records
						  $('#importDetails').load('index.php', {'ajax':1, 'action': 'listImportTransactions', 'id': data.importId, 'listOnly': 'true'},function(){
                $('#importDetails').show();
                enableWizardStep(3);
                });
              }
            }
          },
				error: function (data, status, e) {
				  alert(data.error+"\n"+status+"\n"+e);
					$("#qifUploadError").show();
					$("#qifUploadErrorText").text(e);
				}
			}
		)
  return false;
	}

// logs out the user after inactivity
function logout() {
  window.location.href = "#login";
  }

// reset the logout feature
function resetLogout() {
  // remove the previous timeout
  clearTimeout(sessionTime);
  // reset it so it starts from now
  sessionTime = setTimeout("logout()", 910000); // 15mns+10secs
  }

// show a status bar with a custom message for a definite time
function showStatus(message,timeout,add) {
  if (typeof _statusbar == "undefined") {
    // ** Create a new statusbar instance as a global object
    _statusbar = $("<div id='_statusbar' class='statusbar'></div>").appendTo(document.body).show();
    }

  if (add) _statusbar.prepend( "<div style='margin-bottom: 2px;' >" + message + "</div>")[0].focus();
  else _statusbar.text(message)

  _statusbar.show();
  if (timeout) {
    _statusbar.addClass("statusbarhighlight");
    //setTimeout( function() { _statusbar.removeClass("statusbarhighlight"); },timeout);
    setTimeout( function() { _statusbar.hide(); },timeout);
    }
  }

// Transaction breaker Submition
function breakTransactionSubmit() {
    resetLogout();
    var query = "ajax=1&action=breakTransactionSubmit&"+$("#transactionBreaker").serialize();
    $('#resultDiv').hide();
    $('#loadingSpan').show();
    // send the request
    $.ajax({
      type: "POST", timeout: 15000, url: "index.php", dataType: 'json', data: query, cache: false,
      success: function(data, status){
        if(data.error == true) {
          $('#resultDiv').html('<blockquote>'+data.msg+'</blockquote>');
          $('#resultDiv').removeClass('success');
          $('#resultDiv').addClass('error');
          $('#resultDiv').show();
          if(data.sum != 0) {
            $('#sum').html('<span style="color:red;">'+data.sum+'</span>');
            }
          }
        else {
          $('#resultDiv').html('<blockquote>'+data.msg+'</blockquote>');
          $('#resultDiv').removeClass('error');
          $('#resultDiv').addClass('success');
          $('#resultDiv').show();
          $('#sum').html('<span>'+data.sum+'</span>');
          }
        $('#loadingSpan').hide();
        },
      error: function(data, status, e){
        alert("Error: "+status+" ("+e+")");
        $('#loadingSpan').hide();
        }
      });
    }

// delete a reminder
function taskSchedulerDelete(id) {
  resetLogout();
  var query = "ajax=1&action=taskSchedulerSubmit&taskId="+id+"&delete=delete";
    $('#loadingSpan').show();
    // send the request
    $.ajax({
      type: "POST", timeout: 15000, url: "index.php", dataType: 'json', data: query, cache: false,
      success: function(data, status){
        if(data.error == true) {
          showStatus(data.msg, 3000);
          }
        else {
          $('#taskSchedulerDiv').load('index.php', {'ajax':1, 'action': 'taskScheduler'},function(){
            showLoading(false);
            $("#memo").focus();
            showStatus(data.msg, 3000);
            });
          }
        },
    error: function(data, status, e){
      alert("Error: "+status+" ("+e+")");
      $('#loadingSpan').hide();
      }
    });
  }

// login a user
function doLogin() {
  resetLogout();
  var query = "ajax=1&action=loginSubmit&"+$("#loginForm").serialize();
    $('#loadingSpan').show();
    $('#successLogout').hide();
    // send the request
    $.ajax({
      type: "POST", timeout: 15000, url: "index.php", dataType: 'json', data: query, cache: false,
      success: function(data, status){
        if(data.error == true) {
          $('#error').html('<blockquote>'+data.msg+'</blockquote>');
          $('#error').removeClass('success');
          $('#error').addClass('error');
          $('#error').show();
          }
        else {
          if(data.nextAction != false) {
            window.location.href="#"+data.nextAction;
            window.location.reload();
            //$('#main').load('index.php', {'ajax':1, 'action': data.nextAction},function(){
            //  showLoading(false);
            //  showStatus(data.msg, 3000);
            //  });
            }
          else {
            $('#error').html('<blockquote>'+data.msg+'</blockquote>');
            $('#error').removeClass('error');
            $('#error').addClass('success');
            $('#error').show();
            }
          }
        },
    error: function(data, status, e){
      alert("Error: "+status+" ("+e+")");
      $('#loadingSpan').hide();
      }
    });
  }

// edit/modify/delete schedule task
function taskSchedulerSubmit() {
    resetLogout();
    var query = "ajax=1&action=taskSchedulerSubmit&"+$("#taskScheduler").serialize();
    $('#resultDiv').hide();
    $('#loadingSpan').show();
    // send the request
    $.ajax({
      type: "POST", timeout: 15000, url: "index.php", dataType: 'json', data: query, cache: false,
      success: function(data, status){
        if(data.error == true) {
          $('#resultDiv').html('<blockquote>'+data.msg+'</blockquote>');
          $('#resultDiv').removeClass('success');
          $('#resultDiv').addClass('error');
          $('#resultDiv').show();
          showLoading(false);
          }
        else {
          $('#taskSchedulerDiv').load('index.php', {'ajax':1, 'action': 'taskScheduler', 'backLink': data.backLink},function(){
            $('#resultDiv').html('<blockquote>'+data.msg+'</blockquote>');
            $('#resultDiv').removeClass('error');
            $('#resultDiv').addClass('success');
            $('#resultDiv').show();
            showLoading(false);
            $("#memo").focus();
            });
          showStatus(data.msg, 3000);
          }
        },
      error: function(data, status, e){
        alert("Error: "+status+" ("+e+")");
        $('#loadingSpan').hide();
        }
      });
    }


// edit user information
function userEditForm(formId, loadingImage) {
    resetLogout();
    var id = $('#'+formId+'_userId').val();
    var query = "ajax=1&action=userEditSubmit&"+$("#"+formId).serialize();
    // reset to the loading gif and show the loading div
    $('#'+loadingImage).attr({src: "style/icons/icon_Loading.gif"});
    $('#'+loadingImage).show();
    // send the request
    $.ajax({
      type: "POST", timeout: 15000, url: "index.php", data: query, cache: false, dataType: 'json',
      success: function(data, status){
        if(data.error == true) {
          $('#'+loadingImage).attr({src: "style/icons/cancel.png"});
          }
        else {
          // show the success icon
          $('#'+loadingImage).attr({src: "style/icons/accept.png"});
          // update all IDs if new insert
          if(id<0) {
            $('#'+formId+'_userId').val(data.id);
            }
          // make sure the passsword field and the blockuser button are enabled
          $('#currentPassword'+id).show();
          $('#blockUserSpan'+id).show();
          }
        showStatus(data.msg, 3000);
        if(data.doLanguage == true) {
          window.location.reload();
          }
        },
      error: function(data, status, e){
        alert("Error: "+status+" ("+e+")");
        }
      });
    }

// block/unblock a user
function blockUser(id) {
    resetLogout();
    var query = "ajax=1&action=blockUserSubmit&userId="+id;
    // reset to the loading gif and show the loading div
    $('#blockImg'+id).attr({src: "style/icons/icon_Loading.gif"});
    $('#blockImg'+id).show();
    // send the request
    $.ajax({
      type: "POST", timeout: 15000, url: "index.php", data: query, cache: false, dataType: 'json',
      success: function(data, status){
        if(data.error == 'true') {
          $('#blockImg'+id).attr({src: "style/icons/error.png"});
          }
        else {
          if(data.enabled == '1') $('#blockImg'+id).attr({src: "style/icons/user_green.png"});
          else $('#blockImg'+id).attr({src: "style/icons/user_red.png"});
          }
        showStatus(data.msg, 3000);
        },
      error: function(data, status, e){
        $('#blockImg'+id).attr({src: "style/icons/error.png"});
        showStatus(data.msg, 3000);
        }
      });
    }

// edit category (submit the form)
function categoryEditForm(formId, loadingImage, id) {
    resetLogout();
    var query = "ajax=1&action=editCategoriesSubmit&"+$("#"+formId).serialize();
    // reset to the loading gif and show the loading div
    $('#'+loadingImage).attr({src: "style/icons/icon_Loading.gif"});
    $('#'+loadingImage).show();
    // send the request
    $.ajax({
      type: "POST", timeout: 15000, url: "index.php", data: query, cache: false, dataType: 'json',
      success: function(data, status){
        if(data.error == true) $('#'+loadingImage).attr({src: "style/icons/cancel.png"});
        else {
          $('#'+loadingImage).attr({src: "style/icons/accept.png"});
          // update all IDs if new insert
          if(id<0) {
            $('#'+id+'_category').attr('id', data.id+'_category');
            $('#categoryLoading'+id).attr('id', 'categoryLoading'+data.id);
            $('#'+id).attr('id', data.id);
            }
          // update the total field with the new total value
          alert(data.totalMaxAmount);
          enableWizardStep(2);
          }
        showStatus(data.msg, 3000);
        }
      });
    }
// edit keyword (submit the form)
function keywordEditForm(formId, loadingImage, id) {
    resetLogout();
    var query = "ajax=1&action=editKeywordsSubmit&"+$("#"+formId).serialize();
    // reset to the loading gif and show the loading div
    $('#'+loadingImage).attr({src: "style/icons/icon_Loading.gif"});
    $('#'+loadingImage).show();
    // send the request
    $.ajax({
      type: "POST", timeout: 15000, url: "index.php", data: query, cache: false, dataType: 'json',
      success: function(data, status){
        if(data.error == true) $('#'+loadingImage).attr({src: "style/icons/cancel.png"});
        else {
          $('#'+loadingImage).attr({src: "style/icons/accept.png"});
          // update all IDs if new insert
          if(id<0) {
            $('#'+id+'_keyword').attr('id', data.id+'_keyword');
            $('#'+id+'_category').attr('id', data.id+'_category');
            $('#keywordLoading'+id).attr('id', 'keywordLoading'+data.id);
            $('#'+id).attr('id', data.id);
            }
          enableWizardStep(4);
          }
        showStatus(data.msg, 3000);
        }
      });
    }

// edit comment or category for a transaction
function transactionEditForm(formId, loadingImage, id) {
  resetLogout();
  var query = "ajax=1&action=editTransactionSubmit&"+$("#"+formId).serialize();
  // show the loading div
  $('#'+loadingImage).attr({src: "style/icons/icon_Loading.gif"});
  $('#'+loadingImage).show();
  // send the request
  $.ajax({
    type: "POST", timeout: 15000, url: "index.php", data: query, dataType: 'json', cache: false,
    success: function(data, status){
      if(data.error == true) $('#commentLoading-'+id).attr({src: "style/icons/cancel.png"});
      else {
        // reload the timeline as something has changed
        loadTimelineEvents();
        // reload the graphs div if present
        if($("#graphs").length > 0) {
          //graphs
          $('#graphs').load('index.php', {'ajax':1, 'action': 'overview-graphs'},function(){
            $('#'+loadingImage).attr({src: "style/icons/accept.png"});
            });
          }
        else {
          $('#'+loadingImage).attr({src: "style/icons/accept.png"});
          }
        }
      showStatus(data.msg, 3000);
      //setTimeout( function() { $('#commentLoading-'+id).hide(); },3000);
      }
    });
  }

// set the report to be used
function setReport(id) {
  resetLogout();
  // get the selected report ID
  if(!id) id = $("#reportSelector").val();
  // show the loading div
  showLoading(true);
  var query = "ajax=1&action=setReport&reportId="+id;
  $.ajax({
    type: "POST", timeout: 15000, url: "index.php", dataType: 'json',cache: false, data: query,
    success: function(data, status){
      if(data.error == true) $('#dateFormSubmit').attr({src: "style/icons/cancel.png"});
      else {
        $('#main').load('index.php', {'ajax':1, 'action': 'home'},function(){
          doTimeLine();
          showLoading(false);
          // clean all setup cookies
          for(i=0;i<hiddenDivs.length;i++) {
            $.cookie(hiddenDivs[i], null, { path: '/'});
            }
          });
        }
      },
    error: function(data, status, e){
      alert("Error: "+status+" ("+e+")");
      showLoading(false);
      }
    });
  }

// transaction manual entry
function addManualTransaction() {
  resetLogout();
  // show the loading span
  $('#loadingSpan').show();
  // hide the result div
  $('#resultDiv').hide();
  var query = "ajax=1&action=addTransactionsManualySubmit&"+$("#addManualTransaction").serialize();
  $.ajax({
    type: "POST", timeout: 15000, url: "index.php", dataType: 'json', data: query, cache: false,
    success: function(data, status){
      // hide the loading span
      $('#loadingSpan').hide();
      // show the result bar
      //showStatus(data.msg, 3000);

      // if any error, show it to the user and do not clean the form
      if(data.error == true) {
        $('#resultDiv').html('<blockquote>'+data.msg+'</blockquote>');
        $('#resultDiv').removeClass('success');
        $('#resultDiv').addClass('error');
        $('#resultDiv').show();
        }
      else {
        // show the result
        $('#resultDiv').html('<blockquote>'+data.msg+'</blockquote>');
        $('#resultDiv').removeClass('error');
        $('#resultDiv').addClass('success');
        $('#resultDiv').show();
        // reset the form
        $("#trnAmount").focus();
        //$("#addManualTransaction").
        enableWizardStep(3);
        }
      }
    });
  }

// transaction manual entry
function applicationSettings() {
  resetLogout();
  // show the loading span
  $('#loadingSpan').show();
  // hide the result div
  $('#resultDiv').hide();
  var query = "ajax=1&action=settingsSubmit&"+$("#applicationSettings").serialize();
  $.ajax({
    type: "POST", timeout: 15000, url: "index.php", dataType: 'json', data: query, cache: false,
    success: function(data, status){
      // hide the loading span
      $('#loadingSpan').hide();
      // if any error, show it to the user and do not clean the form
      if(data.error == true) {
        $('#resultDiv').html('<blockquote>'+data.msg+'</blockquote>');
        $('#resultDiv').removeClass('success');
        $('#resultDiv').addClass('error');
        $('#resultDiv').show();
        }
      else {
        // show the result
        $('#resultDiv').html('<blockquote>'+data.msg+'</blockquote>');
        $('#resultDiv').removeClass('error');
        $('#resultDiv').addClass('success');
        $('#resultDiv').show();
        // reset the form
        $("#username").focus();
        // check if the language as to be updated
        if(data.doLanguage == true) {
          window.location.reload();
          }
        }
      }
    });
  }

// edit report link
function editReportSettings(){
  resetLogout();
  // show the loading span
  $('#loadingSpan').show();
  // hide the result div
  $('#resultDiv').hide();
  var query = "ajax=1&action=editReportSettingsSubmit&"+$("#editReportSettings").serialize();
  $.ajax({
    type: "POST", timeout: 15000, url: "index.php", dataType: 'json', data: query, cache: false,
    success: function(data, status){
      if(data.error == true) {
        $('#resultDiv').html('<blockquote>'+data.msg+'</blockquote>');
        $('#resultDiv').removeClass('success');
        $('#resultDiv').addClass('error');
        $('#resultDiv').show();
        }
      else {
        $('#resultDiv').html('<blockquote>'+data.msg+'</blockquote>');
        $('#resultDiv').removeClass('error');
        $('#resultDiv').addClass('success');
        $('#resultDiv').show();
        }
      $('#loadingSpan').hide();
      },
    error: function(data, status, e){
      alert("Error: "+status+" ("+e+")");
      $('#loadingSpan').hide();
      }
    });
  }

// delete report link
function deleteReportSettings () {
  resetLogout();
  if(confirm($('#confirmMessage').val())) {
    var postData = 'ajax=1&action=deleteReportSettings';
    // show the loading div
    showLoading(true);
    // send the request
    $.ajax({
      type: "POST", timeout: 15000, url: "index.php", data: postData, cache: false, dataType: 'json',
      success: function(data, status){
        $('#main').load('index.php', {'ajax':1, 'action': 'home'},function(){
          showLoading(false);
          });
        showStatus(data.msg, 3000);
        },
      error: function(data, status, e){
        showStatus(data.msg, 3000);
        }
      });
    }
  else {
    showLoading(false);
    }
  }

// delete imported files
function deleteImport(id) {
  resetLogout();
  if(confirm($('#confirmMessage').val())) {
    var postData = 'ajax=1&action=deleteImport&id='+id;
    // show the loading div
    showLoading(true);
    // send the request
    $.ajax({
      type: "POST", timeout: 15000, url: "index.php", data: postData, cache: false, dataType: 'json',
      success: function(data, status){
        $('#subAction1').load('index.php', {'ajax':1, 'action': 'manageUpload'},function(){
          showLoading(false);
          });
        showStatus(data.msg, 3000);
        }
      });
    }
  }

// delete a transaction
function deleteTransaction(id){
  resetLogout();
  var postData = 'ajax=1&action=deleteTransaction&id='+id;
  // show the loading div
  showLoading(true);
  // send the request
  $.ajax({
    type: "POST", timeout: 15000, url: "index.php", data: postData, cache: false, dataType: 'json',
    success: function(data, status){
      if(data.error == true) {
        showStatus(data.msg, 3000);
        }
      else {
        if(data.removed == true) {
          $('#buttonImg-'+id).attr({src: "style/icons/arrow_undo.png"});
          $('#transaction-'+id).addClass('deletedRow');
          }
        if(data.restored == true) {
          $('#buttonImg-'+id).attr({src: "style/icons/bin_empty.png"});
          $('#transaction-'+id).removeClass('deletedRow');
          }
        showStatus(data.msg, 3000);
        }
      }
    });
  showLoading(false);
  }

// delete a keyword
function deleteKeyword(id){
  resetLogout();
  var postData = 'ajax=1&action=deleteKeyword&id='+id;
  // show the loading div
  showLoading(true);
  // send the request
  $.ajax({
    type: "POST", timeout: 15000, url: "index.php", data: postData, cache: false, dataType: 'json',
    success: function(data, status){
      if(data.error == true) {
        showStatus(data.msg, 3000);
        }
      else {
          // reload the keyword edit page
          $('#editKeywords').load('index.php', {'ajax':1, 'action': 'editKeywords'},function(){
            showLoading(false);
            });
          }
        showStatus(data.msg, 3000);
        }
      });
  showLoading(false);
  }

// main function to manage the interface
$().ready(function(){
  // start looking at URL change
  setInterval("checkAnchor()", 200);
  // add a live event to all toggle element
  $(".toggle").live('click', function(event){
    var divToToggle = this.id.substr(4);
    if ($('#'+divToToggle).is(':visible')) {
      $('#'+divToToggle).hide("slow"); // hide the div
      $(this).children(".openDiv").css({'background-image' : 'url(style/icons/arrow_out.png)'}); // change the button
      // remove the header from the printing
      $(this).removeClass("print");
      $(this).addClass("noprint");
      // save the settings in the cookie
      $.cookie(this.id, 'hidden', { expires: 7, path: '/'});
      }
    else {
      $('#'+divToToggle).show("fast"); // show the div
      $(this).children(".openDiv").css({'background-image' : 'url(style/icons/cross.png)'}); // change the button
      // add the header to the printing
      $(this).removeClass("noprint");
      $(this).addClass("print");
      // clear the setting cookie
      $.cookie(this.id, null, { path: '/'});
    }
   });
  // table Row highlight on mouse over
  $(".hoverHighlight").live('mouseover', function(event){
    $(this).addClass('highlightedRow');
    });
  $(".hoverHighlight").live('mouseout', function(event){
    $(this).removeClass('highlightedRow');
    });
  // cloud mouse over on cloud
  $("a[cloud]").live('mouseover', function(event){
    $("#cloud-"+$(this).attr('cloud')).addClass('highlightedRow');
    });
  $("a[cloud]").live('mouseout', function(event){
    $("#cloud-"+$(this).attr('cloud')).removeClass('highlightedRow');
    });
  $("#secureAccessOn").live('click', function(event){
    $("#usersListing").show();
    });
  $("#secureAccessOff").live('click', function(event){
    $("#usersListing").hide();
    });
  });

$(window).resize(function() {
  if (tlResizeTimerID == null) {
      tlResizeTimerID = window.setTimeout(function() {
      tlResizeTimerID = null;
      tl.layout();
      }, 500);
    }
  });
