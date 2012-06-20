<?php
session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<title>InOur.Name</title>
<?php
include 'style.php';


?>
<script type="text/javascript" src="jquery.js"></script>
<script type="text/javascript" src="searchbox.js"></script>
<script type="text/javascript" src="http://download.skype.com/share/skypebuttons/js/skypeCheck.js"></script>
<script type="text/javascript">

//var work;

function getData(event, issue, location)
{
	if (event.keyCode == 13) {

		$("#txtHint").html('<div class="loading-image"><img src="images/ajax-loader.gif"/><br />This takes a few seconds, be patient.</div>');
		/*$.get(url, {issue: issue, location: location}, function(data) {
			$("#txtHint").html('<div class="loading-image"><img src="images/ajax-loader.gif"/><br />This takes a few seconds, be patient.</div>');
		   $("#txtHint").html(data);
		});*/
		
		var legisData;
		
		//work.abort();
		
		var work = $.getJSON('getpeople.php?location='+location+'&issue='+issue, function(data) {
		
			$("#txtHint").html('<div class="loading-image"><img src="images/ajax-loader.gif"/><br />This takes a few seconds, be patient.</div>');
			var legisBoxCode = "";
			$.each(data, function(key, val) {
				legisBoxCode += '<div class="result-wrapper">';
				legisBoxCode += '<div class="legislator-'+val["party"]+'">';
				legisBoxCode += '<img src="photos/'+val["bioguide_id"]+'.jpg" class="profile" \>';
				legisBoxCode += '<i>'+val["chamber"]+' '+val["party_full"]+'</i><br />';
				legisBoxCode += '<a href="'+val["webform"]+'">'+val["firstname"]+' '+val["middlename"]+' '+val["lastname"]+'</a> ('+val["state"]+'-'+val["district"]+')<br />';
				if(val["twitter_id"].length > 1) {
					legisBoxCode += '<a href="https://twitter.com/intent/tweet?screen_name='+val["twitter_id"]+'" class="tweetbutton">@'+val["twitter_id"]+'</a><br />';
				}
				legisBoxCode += '<a href="http://www.skype.com"><img src="images/skype.png" style="vertical-align: middle; margin-right: 5px;" /></a><a href="skype:914-497-3802?call">'+val["phone"]+'</a><br />';
				legisBoxCode += '</div>'; //Close legislator box
				
				legisBoxCode += '<div class="record">'; //Create result div
				legisBoxCode += '<div id="bills-'+val["bioguide_id"]+'"></div>';
				legisBoxCode += '<div id="votes-'+val["bioguide_id"]+'"></div>';
				legisBoxCode += '<div id="news-'+val["bioguide_id"]+'"></div>';
				legisBoxCode += '<div class="loading-image" id="loader-'+val["bioguide_id"]+'"><img src="images/ajax-loader.gif"/><br />This takes a few seconds, be patient.</div>';
				legisBoxCode += '</div>';
				legisBoxCode += '<div style="clear:both;"></div>'; //Push everything down
				legisBoxCode += '</div>';
				
			});
			
			legisData = data;
			$("#txtHint").html(legisBoxCode);
			
			var bioguide_id;
			
			$.getJSON('getbills_open.php?location='+location+'&issue='+issue, function(bills) {
			
				var total_bills = bills.length;
			
				$.each(legisData, function(key, val) {
					bioguide_id = val["bioguide_id"];
					//$("#record-"+bioguide_id).html("Joy!");
					$("#bills-"+bioguide_id).append('<div class="title">legislation co-sponsored</div>');
					$.each(bills, function(bill_num, bill_id) {
						getBill(bill_id, bioguide_id, bill_num, total_bills);
					});
					
				});
			});
			
			$.each(legisData, function(key, val) {
				bioguide_id = val["bioguide_id"];
				$("#votes-"+bioguide_id).append('<div class="title">recent votes</div>');
				getVotes(issue, bioguide_id);
				
				firstname = val["firstname"];
				lastname = val["lastname"];
				$("#news-"+bioguide_id).append('<div class="title">in the news</div>');
				getNews(issue, firstname, lastname, bioguide_id);
			});
			
			
			
		});
	}
	
}

function getBill(bill_id, bioguide_id, bill_num, total_bills)
{
	var url = "getbill_rtc.php";
    $.get(url, {bill_id: bill_id, bioguide_id: bioguide_id} ,function(data) {
       $("#bills-"+bioguide_id).append(data);
	   //$("#record-"+bioguide_id).append('<div class="loading-image"><img src="images/ajax-loader.gif"/><br />This takes a few seconds, be patient.</div>');
    }).complete(function() {
		if(bill_num == total_bills-1) {
			$("#loader-"+bioguide_id).remove();
			if($("#bills-"+bioguide_id).text() == 'legislation co-sponsored') {
				$("#bills-"+bioguide_id).append('<div class="none">none</div><br />');
			}
		}
	});
}

function getVotes(issue, bioguide_id)
{
	var url = "getvotes.php";
    $.get(url, {issue: issue, bioguide_id: bioguide_id} ,function(data) {
       $("#votes-"+bioguide_id).append(data);
	   //$("#record-"+bioguide_id).append('<div class="loading-image"><img src="images/ajax-loader.gif"/><br />This takes a few seconds, be patient.</div>');
    });
}

function getNews(issue, firstname, lastname, bioguide_id)
{
	var url = "getnews.php";
    $.get(url, {issue: issue, firstname: firstname, lastname: lastname} ,function(data) {
       $("#news-"+bioguide_id).append(data);
	   //$("#record-"+bioguide_id).append('<div class="loading-image"><img src="images/ajax-loader.gif"/><br />This takes a few seconds, be patient.</div>');
    });
}

function loading() {
	$("#loading").show();
	//document.getElementById("loading").style.display = "block";
}
function loaded() {
	$("#loading").hide();
	//document.getElementById("loading").style.display = "none";
}

function expandSummary(number,summary,shortSummary){
	if(document.getElementById(number).innerHTML != summary) {
		document.getElementById(number).innerHTML=summary;
	} else {
		document.getElementById(number).innerHTML=shortSummary;
	}
}

</script>
</head>
<body>
<?php 
include 'header.php';
?>
<div id="searchwrapper">
<div id="issue"><div class="question">What issue is important to you?</div><br />

	<script>
	$(function() {
		var availableTags = [
			"affirmative action",
			"Affordable Care Act",
			"banking",
			"college",
			"consumer protection",
			"debt",
			"debt ceiling",
			"debt limit",
			"deficit",
			"economy",
			"Egypt",
			"foreign policy",
			"health care",
			"immigration",
			"internet",
			"privacy",
			"Protect IP Act",
			"jobs",
			"Libya",
			"NDAA",
			"Russia",
			"spending",
			"Stop Online Piracy Act",
			"student loans",
			"Syria",
			"taxes",
			"trade",
			"United Nations",
			"visas"
		];
		$( "#tags" ).autocomplete({
			source: availableTags
		});
		$( "#tags2" ).autocomplete({
			source: availableTags
		});
	});
	</script>
<form>
<div class="ui-widget">
	<label for="tags"></label>
	<input id="tags" onKeyUp="javascript:getData(event, this.value, this.form.elements['tags2'].value)" />
</div>
<div class="suggestions">
Try some: <a href="#" onClick="document.getElementById('tags').value='debt ceiling';">debt ceiling</a>, <a href="#" onClick="document.getElementById('tags').value='health care';">health care</a>, <a href="#" onClick="document.getElementById('tags').value='NDAA';">NDAA</a>, <a href="#" onClick="document.getElementById('tags').value='Syria';">Syria</a>
</div>
	
</div>
<div id="location"><div class="question">What's your zip code?</div><br />

<div class="ui-widget">
	<label for="tags2"></label>
	<input id="tags2" onKeyUp="javascript:getData(event, this.form.elements['tags'].value, this.value)" />
</div>

</div>
<div style="clear:both;"></div>

</form>
</div>

<br />
<div id="loading">
<img src="images/ajax-loader.gif"/>
<div class="suggestions">This takes a few seconds, be patient.</div>
</div>
<div id="txtHint">
<div id="prelude">Press enter to search...</div>

</div>
<!-- <a href="#" onClick="getBills('obama','14853','0','A000055');">mess it up!</a> -->
<?
//phpinfo();

?>

<?php 
include 'footer.php';
?>
</body>
</html>