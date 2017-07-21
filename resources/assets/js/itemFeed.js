/*
|--------------------------------------------------------------------------
| Main Item Feed Functions:
| SEARCH BAR 
|	-SETUP
|	-CANCEL INPUT
|
| ITEMS
|	-DELETE
| 
| @Param -> locations to search retrieve
|--------------------------------------------------------------------------
*/

$( document ).ready(function() { 
	//SEARCH BAR SETUP
    $('#packyakInventoryDashSearch').on('keyup', function(e) {
	    if ('' != this.value) {
	        var reg = new RegExp(this.value, 'i'); // case-insesitive

	        $('.table tbody').find('tr').each(function() {
	            var $me = $(this);
	            if (!$me.children('td').text().match(reg)) {
	                $me.hide();
	            } else {
	                $me.show();
	            }
	        });
	    } else {
	        $('.table tbody').find('tr').show();
	    }
	});

    //CANCEL THE INPUT VALUE 
    //**There is no button at the moment to clear input
    /*$('.packyakCancelSearch').click(function() {
    	$('#packyakInventoryDashSearch').val('').keyup();
    });*/

    //CLICKING UNIT PRICE SHOWS TEXTBOX--
	$(document).on('click', '.packyakUnitPrice', function() {
		$(this).addClass("hidden");
		$(this).next().removeClass("hidden");
	});

	//CLICKING INVENTORY PRICE SHOWS TEXTBOX--
	$('#packyakInventoryDashTable').on('click', ".packyakInventory",function() {
		//console.log('Working and detecting click this is wherea save should work so now we do itagain');
  		$(this).addClass("hidden");
  		$(this).next().removeClass("hidden");
	});

	//IF HIT CANCEL, HIDE TEXT BOXES--
	$('#packyakInventoryDashTable').on('click', '.packyakCancel', function() {
		$(this).siblings('.packyakInventoryText').addClass('hidden');
		$(this).siblings('.packyakInventory').removeClass('hidden');
		$(this).siblings('.packyakUnitPriceText').addClass('hidden');
		$(this).siblings('.packyakUnitPrice').removeClass('hidden');
	});

    //STEP 1: DELETE VARIATION FROM YAK AND SQUARE
	$('#packyakInventoryDashTable').on('click', '.packyakDeleteItem', function() {
		var variationID = $(this).siblings('.packyakInventoryItemID').html();
		var locationID = $(this).siblings('.packyakLocationSold').data('location-id');
		$('#deleteItemModal').modal('toggle');

		//adds variation ID to the button to be retrieved on confirmation
		$('.deleteItemConfirmButton').attr({'varid': variationID, 'locid': locationID});

	});

	//STEP 2 DELETE VARIATION FROM YAK AND SQUARE
	$('#deleteItemModal').on('click', '.deleteItemConfirmButton', function() {
		$('.innerText').addClass('hidden');
		$('.deleteItemConfirmButton').addClass('disabled');
		$('.deleteItemSpinner').removeClass('hidden');
		
		varID = $(this).attr('varid');
		locID = $(this).attr('locid');

		/*console.log(varID);
		console.log(locID);
		console.log($(this));*/
		
		deleteItemVariation(varID, locID);
	});

	//USE CHECK BOXES TO GET THE ITEMS FROM SQUARE
    $(".searchLocationsButton").click(function() {
		//check to see if there are any checks
		if($('.locationCheckbox:checked').length > 0){
			var checkedLocations = [];

	    	//get all the checked boxes and add the square ID to array for processing
	    	$.each($('.locationCheckbox:checked'), function(){
	    		checkedLocations.push($(this).attr("data-locationID"))
	    	});
    		console.log(checkedLocations);
    		retrieveItemData(checkedLocations);

		}else{
			//no checkboxes are checked
			$(".noItemCallout").show();
			$("#packyakInventoryDashTable").addClass('hidden');
			$("#itemTableBody").empty();
		}
	});

    //CHANGE INFO OF ITEM OPTIONS MODAL
	$('#packyakInventoryDashTable').on('click', '.itemOptionsButton', function() {
		console.log('working here');
		//Put the row in a variable
		var feedItemRow = $(this).parents('.packYakItemFeedRow');

		//Start to place info
		$('#itemOptionsModal').find('.itemName').text(feedItemRow.children('.packyakItemName').text());
		$('#itemOptionsModal').find('.itemVariation').text(feedItemRow.children('.packyakVariationName').text());
		
		$('#itemOptionsModal').find('.itemLocation').text(feedItemRow.children('.packyakLocationSold').text());
		$('#itemOptionsModal').find('.itemCategory').text(feedItemRow.children('.packyakItemCategory').text());
		$('#itemOptionsModal').find('.itemInventory').text(feedItemRow.children('.packyakpackyakInventory').text());
		$('#itemOptionsModal').find('.itemPrice').text(feedItemRow.children('.packyakVariationPrice').text());
		$('#itemOptionsModal').find('.itemCost').text(feedItemRow.children('.packyakUnitPrice').text());
		$('#itemOptionsModal').find('.itemMargin').text(feedItemRow.children('.packyakVariationMargin').text());
		$('#itemOptionsModal').find('.itemSKU').text(feedItemRow.children('.packyakVariationSKU').text());
		$('#itemOptionsModal').find('.variationID').text(feedItemRow.data('variation-id'));
		
	});





























	//GET ALL PENDING PURCHASE ORDERS IN CHRONOLOGICAL ORDER
	$("#packyakInventoryDashTable").on('click','.packyakPurchaseOrderList', function(){
		//console.log('here');
		if($('.packyakAddItemToPOWrapper').is(":visible") != true){
			var action = 'getPendingPO';
			var thisRowAddToPOList = $(this).siblings('.packyakAddItemToPOWrapper');
			thisRowAddToPOList.empty();
			$.ajaxSetup({
		        headers: {
		            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		        }
			});

			//set data
	        var data =  {'action': action};

	       	$.ajax({
		        type: "POST",
		        url: './purchaseOrders',

		        data: data,
		        success: function(data) {
		        	var responseData = $.parseJSON(data);

		        	//append the pending purchase orders to view
		        	for(var i=0; i<responseData.length; i++){
		        		$(thisRowAddToPOList).append('<li><a class="packyakPurchaseOrderListItem">' + responseData[i]['po_name'] + '</a><div class="packyakPurchaseOrderID hidden">' + responseData[i]['id'] + '</div></li>');
		        	};
		        },
		        error: function(ts) { console.log(ts.responseText) }
		    })
	       }else{
	       		$(this).siblings('.packyakAddItemToPOWrapper').empty();
	       }
	});

	//ADD ITEM TO PURCHASE ORDER FROM MAIN ITEM FEED
	$("#packyakInventoryDashTable").on('click', '.packyakPurchaseOrderListItem', function(){
		console.log('working again');
		var action = 'addToPO';
		var selectedPurchaseOrder = $(this).html();
		var itemVariationID = $(this).parents('.packYakItemFeedRow').find('.packyakInventoryItemID').html();
		var itemVariationLocationID = $(this).parents('.packYakItemFeedRow').find('.packyakLocationSold').data('location-id');
		//console.log(itemVariationLocationID);
		var packyakPurchaseOrderID = $(this).siblings('.packyakPurchaseOrderID').html();
		var itemUnitCost = $(this).parents('.packYakItemFeedRow').find('.packyakUnitPrice').html();

        var data =  {'action': action,
        		 'selectedPurchaseOrder': selectedPurchaseOrder,
        		 'itemVariationID': itemVariationID,
        		 'itemLocationID': itemVariationLocationID,
        		 'packyakPurchaseOrderID': packyakPurchaseOrderID,
        		 'itemUnitCost': itemUnitCost
    			};

       		$.ajax({
	        type: "POST",
	        url: './purchaseOrders',

	        data: data,
	        success: function(data) {
	        	//vresponseData = $.parseJSON(data);
	        },
	        error: function(ts) { console.log(ts.responseText) }
	    })
	});

	//UPDATE INVENTORY AND/OR UNIT PRICE AND SEND TO SQUARE
	$('#packyakInventoryDashTable').on('click','.packyakSubmitButton',function() {
    	var newInventoryLevel = $(this).siblings('.packyakInventoryText').children().val();
    	var oldInventoryLevel = $(this).siblings('.packyakInventory').html();
    	var itemLocation = $(this).siblings('.packyakLocationSold').html();
    	var itemVariationID = $(this).siblings('.packyakInventoryItemID').html();
    	var updatedUnitPrice = $(this).siblings('.packyakUnitPriceText').children().val();
    	var oldUnitPrice = $(this).siblings('.packyakUnitPrice').html();

    	//saving the objects into variable to use after ajax request
    	var thatRowInventoryTextbox = $(this).siblings('.packyakInventoryText');
    	var thatRowInventory = $(this).siblings('.packyakInventory');
    	var thatRowUnitPriceTextbox = $(this).siblings('.packyakUnitPriceText');
    	var thatRowUnitPrice = $(this).siblings('.packyakUnitPrice');

    	if($(this).siblings('.packyakInventoryText').is(':visible') || $(this).siblings('.packyakUnitPriceText').is(':visible')){
			if(updatedUnitPrice == oldUnitPrice && newInventoryLevel == oldInventoryLevel){
	    		alert('Please make a change in inventory or unit cost to submit.');
	    	}else if(newInventoryLevel < oldInventoryLevel || updatedUnitPrice < oldUnitPrice){
	    		var continueWithSelection = confirm('New inventory amount or unit price is lower than current levels. Continue?');
		    	if(continueWithSelection == true){
		    		var quantityDelta = newInventoryLevel - oldInventoryLevel;
			    	$.ajax({
				        type: "POST",
				        url: './dashboard',
				        data: {'itemLocation': itemLocation,
				        	   'itemVariationID': itemVariationID,
				        	   'quantityDelta': quantityDelta,
				        	   'updatedUnitPrice': updatedUnitPrice
				    		  },
				        success: function(data) {
				        	//switching classes back to normal
						var responseData = $.parseJSON(data)
			        	thatRowInventoryTextbox.addClass('hidden');
		           		thatRowInventory.html(responseData.itemVariationInventory);
		           		thatRowInventory.removeClass('hidden');
		           		thatRowInventory.parent().addClass('updateSuccess');
		           		thatRowUnitPriceTextbox.addClass('hidden');
		           		thatRowUnitPrice.html(responseData.itemVariationUnitPrice);
		           		thatRowUnitPrice.removeClass('hidden');
				        }
				    })
		    	}else{
		    		console.log('Canceled request');
		    	}	
		    }else{	
		    	var quantityDelta = newInventoryLevel - oldInventoryLevel;
		    	$.ajax({
			        type: "POST",
			        url: './dashboard',
			        data: {	
			        		'itemLocation': itemLocation,
			        	   'itemVariationID': itemVariationID,
			        	   'quantityDelta': quantityDelta,
			        	   'updatedUnitPrice': updatedUnitPrice
			    		  },
			        success: function(data) {
			            //switching classes back to normal
			            var responseData = $.parseJSON(data);
						var rawUnitCost = parseInt(responseData.itemVariationUnitPrice, 10)/100;
						var formattedUnitCost = '$' + rawUnitCost.toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
			        	thatRowInventoryTextbox.addClass('hidden');
		           		thatRowInventory.html(responseData.itemVariationInventory);
		           		thatRowInventory.removeClass('hidden');
		           		thatRowInventory.parent().addClass('updateSuccess');
		           		thatRowUnitPriceTextbox.addClass('hidden');
		           		thatRowUnitPrice.html(formattedUnitCost);
		           		thatRowUnitPrice.removeClass('hidden');
			        },
			        error: function(ts) { 
			        	//console.log(ts.responseText) 
			        	thatRowInventory.parent().addClass('updateFailure');
			        }
			    })
		    }
		}else{
			console.log('Please do something in order to submit');
		}
    });
});

/*
|--------------------------------------------------------------------------
| Get Item Data and delete variation form square and database
| @Param -> item variation to delete
|--------------------------------------------------------------------------
*/
function deleteItemVariation(varID, locationID){
	$.ajax({
        type: "POST",
        url: '/dashboard/deleteVariation',

        data: {'variation': varID,
    			'locationID': locationID},
        success: function(data) {
        	//Reset the modal
        	console.log(data);
        	$('#deleteItemModal').modal('toggle');
			$('.innerText').removeClass('hidden');
			$('.deleteItemConfirmButton').removeClass('disabled');
			$('.deleteItemSpinner').addClass('hidden');

			//hide the row
			var tablerows = $( "tr:contains('"+varID+"')" );

			$.each(tablerows, function(){
				if($(this).find('.packyakLocationSold').attr('data-location-id') == locationID){
					$(this).addClass('hidden');
				}
				//console.log($(this).find('.packyakLocationSold').attr('data-location-id'));
			})
        },
        error: function(ts) { 
        	//console.log(ts.responseText);
        	$("#loadingImage").addClass('hidden');
        	$('#deleteItemModal').modal('toggle');
        	
        }
    })
}

/*
|--------------------------------------------------------------------------
| Get the item data dynamically from on page interaction
| @Param -> locations to search retrieve
|--------------------------------------------------------------------------
*/
function retrieveItemData(locations){
	var htmlString = [];

	//hide stuff that says no locations selected
	$("#loadingImage").removeClass('hidden');
	$(".noItemCallout").hide();
	
	$.ajax({
        type: "GET",
        url: '/dashboard/retrieve',

        data: {'locations': locations},
        success: function(data) {
        	$("#loadingImage").addClass('hidden');
        	$("#packyakInventoryDashTable").removeClass('hidden');
        	//console.log(data);
        	//Start to display data
        	for(var i=0; i < data.length; i++){
        		for(var j=0; j < data[i].length; j++){
        			//console.log(data[i][j]);

        			htmlString.push(
        			'<tr class="packYakItemFeedRow" data-variation-id='+ data[i][j].itemVariationID + '>' +
                        '<td class="packyakLocationSold" data-location-id='+ data[i][j].itemLocationID +'>' + data[i][j].locationSoldAt + '</td>' +
                        '<td class="packyakItemCategory">'+data[i][j].itemCategoryName+'</td>'+
                        '<td class="packyakItemName">'+data[i][j].itemName+'</td>'+
                        '<td class="packyakVariationName">'+data[i][j].itemVariationName+'</td>'+
                        '<td class="packyakInventory">' +data[i][j].itemVariationInventory+ '</td>' +
                        '<td class="packyakInventoryText hidden"><input class="packyakInventoryTextInput" type="number" min="-5" step="1" name="newInventoryLevel" value="'+data[i][j].itemVariationInventory+'"></td>'+
                        '<td class="packyakVariationPrice">$'+(data[i][j].itemVariationPrice/100).toFixed(2)+'</td>'+
                        '<td class="packyakUnitPrice">$'+(data[i][j].itemVariationUnitCost/100).toFixed(2)+'</td>'+
                        '<td class="packyakUnitPriceText hidden">$<input class="packyakUnitPriceTextInput" min=".00" step=".01" name="currency" type="number" value="'+(data[i][j].itemVariationUnitCost/100).toFixed(2)+'"></td>' +
                        '<td class="packyakVariationMargin">$'+((data[i][j].itemVariationPrice-data[i][j].itemVariationUnitCost)/100).toFixed(2)+'</td>'+
                        '<td class="packyakVariationSKU">'+data[i][j].itemVariationSKU+'</td>'+                           
                        '<td class="packyakPurchaseOrderMenuButton">'+
                        	'<i type="button" class="fa fa-bars fa-2 btn btn-default itemOptionsButton" data-toggle="modal" data-target="#itemOptionsModal"></i>' +     
                        '</td>'+
                        '<td class="packyakSubmitButton"><i class="fa fa-check-circle-o fa-2 btn btn-default"></i></td>'+
                        '<td class="packyakCancel"><i class="fa fa-times-circle fa-2 btn btn-default"></i></td>'+
                        '<td class="packyakDeleteItem"><i class="fa fa-trash-o fa-2 btn btn-default"></i></td>'+
                        '<td class="packyakInventoryItemID hidden" data-variation-id='+ data[i][j].itemVariationID +' >'+data[i][j].itemVariationID+'</td>'+/*May not need this line eventually*/
                    '</tr>');
        		}
        	}

        	/*
				                            '<div class="dropdown">'+
                                '<i class="fa-bars fa-2 btn btn-default dropdown-toggle packyakPurchaseOrderList" type="button" id="packyakPurchaseOrderList" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"></i>'+
                                '<ul class="dropdown-menu dropdown-menu-right packyakAddItemToPOWrapper" aria-labelledby="packyakPurchaseOrderList">'+
                                '</ul>'+
                            '</div>'+  
        	*/
        	//add the elements to DOM
        	document.getElementById("itemTableBody").innerHTML = htmlString.join("");
        },
        error: function(ts) { 
        	console.log(ts);
        	$("#loadingImage").addClass('hidden');
        }
    })
	return 'winning';
}