$( document ).ready(function() {

	var createdItemResponseData;

	//function to call inside ajax callback 
	function set_createdItemResponseData(createdItemInfo){
	    createdItemResponseData = createdItemInfo;
	}

	//purchase order create new item and send data for new record and prime for square upload
	$(".createNewItemModalContent").on("click", ".packyakCreateNewItemButton", function(e) {
		
		//getting array of locations sold at and starting variations array
		var action = 'createNewItem';
		var selectedLocations = new Array();
		var newItemVariationsList = new Array();

		//populating variations objects
		$('.singleVariationWrapper').each(function(){
			var newItemVariation = {
				newVariationName: $(this).find('.createNewItemVariation').val(),
				newVariationSKU: $(this).find('.createNewItemSku').val(),
				newVariationInventoryAlert: $(this).find('.createNewItemInventoryAlert').val(),
				newVariationPrice: $(this).find('.createNewItemPrice').val()*100,
			};	
			//push the variation to the variations array
			newItemVariationsList.push(newItemVariation);
		});
		//console.log(newItemVariationsList);

		$('.createNewItemLocationSelect:checked').next().each(function(){ selectedLocations.push($(this).text())});
		//console.log(selectedLocations);


		//setting up data to be passed for new item
		var newItemOject = {newItemCategory: $('.newItemForm').find('.createNewItemCategory').val(),
							newItemName: $('.newItemForm').find('.createNewItemName').val(),
							newItemVariations: newItemVariationsList,
							newItemLocationSoldAt: selectedLocations
							};

		var jsonNewItemObject = JSON.stringify( newItemOject );

		//console.log(jsonNewItemObject);
		$.ajaxSetup({
	        headers: {
	            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	        }
		});

		$.ajax({
	        type: "POST",
	        url: './purchaseOrders',

	        data: {'action': action, 'data': jsonNewItemObject},
	        success: function(data) {
	        	$('.newItemForm').hide();
	        	$('.newItemInventoryAndInfoForm').removeClass('hidden');
	        	$('.packyakNewItemInventoryAndInfoButton').removeClass('hidden');
	        	$('.packyakCreateNewItemButton').addClass('hidden');
	        	set_createdItemResponseData(data);//setting the data to be passed along

	        	for(i=0; i<data.length; i++){
	        		$('.newItemInventoryAndInfoWrapper').append('<div class="singleNewItemInventoryAndInfoWrapper col-sm-12">' + 
								'<div class="col-sm-3 inventoryVariationName"><strong>'+ data[i]['itemVariationName'] +'</strong></div>'+
								'<div class="col-sm-3 inventoryLocationSoldAt">' + data[i]['locationSoldAt'] + '</div>' +
                                '<div class="col-sm-3">'+
                                    '<input type="number" class="form-control createNewItemInventoryLevel" placeholder="0" min="0", step="1">'+
                                '</div>'+
                                '<div class="col-sm-3">'+
									'<input type="number" class="form-control createNewItemUnitCost" placeholder="Cost" min=".00", step=".01">'+                               
								'</div></div>');
	        	};
	        },
	        error: function(ts) { console.log(ts.responseText) }
	    })
	});
	

	//this will be grabbing the inventory and unit cost and finishing out the create item process
	$(".createNewItemModalContent").on("click", ".packyakNewItemInventoryAndInfoButton", function(e) {
		//setting ajax header for csrf
		$.ajaxSetup({
	        headers: {
	            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	        }
		});

		//setting vars of information
		var action = 'updateNewItemInventory';
		var newItemInventoryAndInfoList = [];
		var itemCounter = 0;


		//setting up data
		$('.singleNewItemInventoryAndInfoWrapper').each(function(){
			var newItemInventoryAndInfo = {
				inventoryVariationName: $(this).find('.inventoryVariationName').text(),
				inventoryLocationSoldAt: $(this).find('.inventoryLocationSoldAt').text(),
				newVariationInventoryLevel: $(this).find('.createNewItemInventoryLevel').val(),
				newVariationUnitPrice: $(this).find('.createNewItemUnitCost').val()*100,
				newVariationID: createdItemResponseData[itemCounter]['itemVariationID'] //<-- this information should match with the corresponding item
			};	
			//add another index to the counter for next item
			itemCounter++;
			//push the variation to the variations array
			newItemInventoryAndInfoList.push(newItemInventoryAndInfo);
		});

		newInventoryAndInfoObject = {inventoryInfo: newItemInventoryAndInfoList};

		var jsonNewInventoryAndInfoObject = JSON.stringify( newInventoryAndInfoObject ); 
		//sending data to server
		$.ajax({
		        type: "POST",
		        url: './purchaseOrders',

		        data: {'action': action, 'data': jsonNewInventoryAndInfoObject},
		        success: function(data) {
		        	$('#createItemModal').modal('toggle');
		        },
		        error: function(ts) { console.log(ts.responseText) }
		    })
	});

	//create new variation within the form itself
	$('.addNewItemVariation').click(function() {
		var newVariationStringHtml =  $('<div class="singleVariationWrapper">' +
										'<label for="createNewItemVariation" class="col-sm-2 control-label">Variation</label>' +
		                                '<div class="col-sm-10 input-group">' +  
		                                    '<div class="col-sm-7"><input type="email" class="form-control createNewItemVariation" placeholder="Name"></div>' +                                   
		                                    '<label for="createNewItemSku" class="col-sm-1 control-label">SKU</label>' +
		                                    '<div class="col-sm-4 createNewItemSKUWrapper"><input type="email" class="form-control createNewItemSku" placeholder="SKU"></div>' +
		                                    '<span class="input-group-btn"><button class="btn btn-default packyakRemoveVariation" type="button"><i class = "fa fa-times-circle fa-2"></i></button></span>' +
		                                '</div>' +
		                                '<label for="createNewItemInventoryLine" class="col-sm-2 col-sm-offset-1 control-label">Inventory</label>' +
		                                '<div class="col-sm-8 input-group createNewItemInventoryLine">' +   
		                                    '<div class="col-sm-4"><input type="number" class="form-control createNewItemInventoryAlert" placeholder="Alert At" min="0", step="1"></div>' +
		                                '</div>' +
		                                '<label for="createNewItemPriceCost" class="col-sm-2 col-sm-offset-1 control-label">Price/Cost</label>' +
		                                '<div class="col-sm-8 input-group createNewItemPriceCost">' +   
		                                    '<div class="col-sm-4"><input type="number" class="form-control createNewItemPrice" placeholder="Price" min=".00", step=".01"></div>' +
		                                '</div></div>');
		$(newVariationStringHtml).appendTo('.createNewItemVariationsWrapper');
	});



	//remove variation from create item form
	$(".createNewItemVariationsWrapper").on("click", ".packyakRemoveVariation", function(e) {
	    $(this).parents('.singleVariationWrapper').remove();
	});

	$('.packyakUpdateOrderButton').click(function(){
		var unitCost;
		if($(this).siblings('.packyakUnitPriceText').is(':visible')){
			unitCost = $(this).parents('.packyakPOItemListItem').find('.packyakUnitPriceTextInput').val();
		}else{
			rawTextUnitCost = $(this).parents('.packyakPOItemListItem').find('.packyakUnitPrice').text();
			unitCost = Number(rawTextUnitCost.replace(/[^0-9\.]+/g,""));
			console.log(unitCost);
		}
		var action = 'updateQuantityToOrder';
		var poItemID = $(this).parents('.packyakPOItemListItem').find('.poitemid').text();
		var itemLocationID = $(this).parents('.packyakPOItemListItem').find('.packyakLocationSoldAt').data('id');
		var purchaseOrderID = $(this).parents('.packyakPOHeader').find('.pypoid').html();
		var quantityToOrder = $(this).parents('.packyakPOItemListItem').find('.packyakOrderQuantityInput').val();
		
		var thatRow = $(this).parents('.packyakPOItemListItem');
		$.ajaxSetup({
	        headers: {
	            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	        }
		});

		$.ajax({
		        type: "POST",
		        url: './purchaseOrders',

		        data: {'action': action, 'purchaseOrderID': purchaseOrderID, 'poItemID': poItemID, 'itemLocationID': itemLocationID, 'quantityToOrder': quantityToOrder,'unitCost': unitCost},
		        success: function(data) {
		        	quantityUpdatedItem = JSON.parse(data);
		        	var rawUnitCost = quantityUpdatedItem.item.itemUnitCost/100;
		        	var formattedUnitCost = '$' + rawUnitCost.toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
		        	thatRow.parents('.packyakPOHeader').find('.packyakPOSubtotal').html('$'+(quantityUpdatedItem.purchaseOrder.po_subtotal/100).toFixed(2));
		        	thatRow.parents('.packyakPOHeader').find('.packyakPOTotalCost').html('$'+ (quantityUpdatedItem.purchaseOrder.po_total_cost/100).toFixed(2));
		        	thatRow.find('.packyakQuantityToOrder').html('<h5>' + quantityUpdatedItem.item.quantityToOrder + '</h5>');
		        	thatRow.find('.packyakUnitPriceText').addClass('hidden');
		        	thatRow.find('.packyakUnitPrice').removeClass('hidden');
		        	thatRow.find('.packyakUnitPrice').html('<h5>' + formattedUnitCost + '</h5>');
		        	thatRow.find('.packyakOrderQuantityInput').val(0);
		        	thatRow.find('.packyakLineItemCost').html('<h5>$' + quantityUpdatedItem.item.lineItemTotal/100 + '</h5>');
		        	
		        },
		        error: function(ts) { console.log(ts.responseText) }
		    })
	});

});

/*
|--------------------------------------------------------------------------
| Set global ajax headers for ajax requests using csrf token
|--------------------------------------------------------------------------
*/
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});



