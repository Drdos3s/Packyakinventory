$( document ).ready(function() {
	//Setting up search bar <ITEMFED.JS
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

    //cancel on search bar <-ITEM FEED.JS
    $('.packyakCancelSearch').click(function() {
    	$('#packyakInventoryDashSearch').val('').keyup();
    });

	//Clicking on inventory and unit price number changes to textfield 
	$(document).on('click', '.packyakUnitPrice', function() {
		$(this).addClass("hidden");
		$(this).next().removeClass("hidden");
	});

	$('#packyakInventoryDashTable').on('click', ".packyakInventory",function() {
		//console.log('Working and detecting click this is wherea save should work so now we do itagain');
  		$(this).addClass("hidden");
  		$(this).next().removeClass("hidden");
	});

	//if hit cancel, shows original inventory level

	$('#packyakInventoryDashTable').on('click', '.packyakCancel', function() {
		$(this).siblings('.packyakInventoryText').addClass('hidden');
		$(this).siblings('.packyakInventory').removeClass('hidden');
		$(this).siblings('.packyakUnitPriceText').addClass('hidden');
		$(this).siblings('.packyakUnitPrice').removeClass('hidden');
	});

	//delete Item from DB and square <-MAINFEED.JS
	$('#packyakInventoryDashTable').on('click', '.packyakDeleteItem', function() {
		var variationID = $(this).siblings('.packyakInventoryItemID').html();
		var locationID = $(this).siblings('.packyakLocationSold').data('location-id');
		$('#deleteItemModal').modal('toggle');

		//adds variation ID to the button to be retrieved on confirmation
		$('.deleteItemConfirmButton').attr({'varid': variationID, 'locid': locationID});

	});

	//Second step in delete variation <-MSINFEED.JS
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

	//submit function that uses ajax request to update inventory for square and unit price
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

	/*--------------------ADDING A PURCHASE ORDER IN DATABASE----------------------------*/
	//Changing the title of the modal so not to use 2 of them <- PURCHASEORDER.JS
	$('.packyakNewPOButton').click(function() {
		$('#myModal').find('.modal-title').html('New Purchase Order');
		$('.packYakPOCreateButtonLabel').html('Create');
		$('.packyakPOButton').removeClass('packyakPurchaseOrderUpdate');
		$('#myModal').find("input[type=email], select").val("");
		$('#myModal').find('.pypoidModal').html('');
		$('.packYakDeletePOButton').addClass("hidden");
		$('#myModal').find('#purchaseOrderShippingCost').val("");

	});

	//Create/edit a new purchase order <- PURCHASE ORDER.JS
	$('.packYakPOButton').click(function() {
		var po_id_number = $('#myModal').find('.pypoidModal').html();
		var po_name = $('#newPurchaseOrderTitle').val();//
		var po_status = $('#purchaseOrderStatusSelect').val();//
		var po_vendor = $('#purchaseOrderVendorSelect').val();//
		var po_location = $('#purchaseOrderLocationSelect').val();//
		var po_invoice_number = $('#purchaseOrderInvoiceNumber').val();//
		var po_shipping_cost = $('#purchaseOrderShippingCost').val()*100;

		if($('#myModal').find('.pypoidModal').html() == ''){
			var action = 'createPO';
		}else{
			var action = 'updatePO';
		}

		//Check to see if the PO status is completed
		if(po_status == 'Completed'){
			confirm('You are marking this purchase order as COMPLETED. Inventory will be updated and send to square. Proceed?');
			action = 'completePO';
		}
		
    	$.ajaxSetup({
	        headers: {
	            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	        }
		})

		$.ajax({
	        type: "POST",
	        url: './purchaseOrders',

	        data: {
	        		'action': action,
	        		'po_id_number': po_id_number,
	        		'po_name': po_name,
	        	   'po_status': po_status,
	        	   'po_vendor': po_vendor,
	        	   'po_location': po_location,
	        	   'po_invoice_number': po_invoice_number,
	        	   'po_shipping_cost' : po_shipping_cost
	    		  },
	        success: function(data) {        
	           $('#myModal').modal('toggle');
	           newPurchaseOrderParsed = JSON.parse(data);
	           //console.log(newPurchaseOrderParsed); 
	           switch(newPurchaseOrderParsed.action){
	           		case 'createPO':
	           			var newPurchaseOrderStringHtml =  $('<section class="invoice packyakPOHeader">Please refresh to see details for: ' + newPurchaseOrderParsed.po_name +
											            '<div class="row no-print">' +
										                 '<div class="col-xs-12">' +
									                   '<a href="invoice-print.html" target="_blank" class="btn btn-info"><i class="fa fa-print"></i> Print</a>' +
										                   '<button class="btn btn-success pull-right">Confirm</button>' +
										                   '<button class="btn btn-primary" style="margin-right: 5px;"><i class="fa fa-download"></i> Generate PDF</button>' +
										                   '<button class="btn btn-info pull-right packyackPOEdit" style="margin-right: 5px;"><i class="fa fa-pencil"></i> Edit</button>' +
										                   '<button type="button" class="btn bg-maroon pull-right packyakNewItemButton" style="margin-right: 5px;" data-toggle="modal" data-target="#createItemModal">Create New Item</button>' +
										                 '</div>' +
										               '</div>' +
												'</section>');
						$('.purchaseOrdersWrapper').prepend( newPurchaseOrderStringHtml );
						break;
					case 'updatePO':
						$('.packyakPOHeader').each(function(){
							if($(this).find('.pypoid').text() == newPurchaseOrderParsed['purchaseOrder']['id']){
								$(this).find('.packYakPOName').html(newPurchaseOrderParsed.purchaseOrder.po_name);
								$(this).find('.packYakPOVendor').html(newPurchaseOrderParsed.purchaseOrder.po_vendor);
								$(this).find('.packYakPOLocation').html(newPurchaseOrderParsed.purchaseOrder.po_location);
								$(this).find('.lead').html('Status: '+newPurchaseOrderParsed.purchaseOrder.po_status);
								$(this).find('.packYakPOStatus').html(newPurchaseOrderParsed.purchaseOrder.po_status);
								$(this).find('.packYakPOInvoiceNum').html(newPurchaseOrderParsed.purchaseOrder.po_invoice_number);
								$(this).find('.packyakPOShippingCost').html('$' + newPurchaseOrderParsed.purchaseOrder.po_shipping_cost/100);
								$(this).find('.packyakPOTotalCost').html('$'+newPurchaseOrderParsed.purchaseOrder.po_total_cost/100);
							};
						});
						break;
					case 'completePO':
						$('.packyakPOHeader').each(function(){
							if($(this).find('.pypoid').text() == newPurchaseOrderParsed['purchaseOrderID']){
								$(this).find('.lead').html('Status: Completed - Updated Successfully');
							};
						});
						break;		
	           }
	        },
	        error: function(ts) { console.log(ts.responseText) }
	    })
	});

	//editing purchase order details <- PURCHASE ORDER.JS
	$('.packyackPOEdit').click(function(){
		$('#myModal').modal('toggle');
		$('#myModal').find('.modal-title').html('Edit Purchase Order');
		$('#myModal').find('.packYakPOCreateButtonLabel').html('Save');
		$('.packYakDeletePOButton').removeClass('hidden');
		$('.packyakPOButton').addClass('packyakPurchaseOrderUpdate');
		var pypoid = $(this).parents('.packyakPOHeader').find('.pypoid').html();

		$('#myModal').find('.pypoidModal').html(pypoid);

		//filling fields with PO details 
		var selectedStatus = $(this).parents('.packyakPOHeader').find('.packYakPOStatus').text();
		var selectedVendor = $(this).parents('.packyakPOHeader').find('.packYakPOVendor').text();
		var selectedLocation = $(this).parents('.packyakPOHeader').find('.packYakPOLocation').text();
		var selectedShippingCost = $(this).parents('.packyakPOHeader').find('.packyakPOShippingCost').text();

		$('#newPurchaseOrderTitle').val($(this).parents('.packyakPOHeader').find('.packYakPOName').text());
		//--Invoice Number not yet showing on the main status-- $('#purchaseOrderInvoiceNumber').val($(this).parents('.packyakPOHeader').find('.packYakPOInvoice').text());
		$("#purchaseOrderLocationSelect").val(selectedLocation);
		$("#purchaseOrderVendorSelect").val(selectedVendor);
		$("#purchaseOrderStatusSelect").val(selectedStatus);
		$("#purchaseOrderShippingCost").val(selectedShippingCost.replace("$", ""));

	});

	//ajax to get all pending purchase orders in chronological order
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
	        data =  {'action': action};

	       	$.ajax({
		        type: "POST",
		        url: './purchaseOrders',

		        data: data,
		        success: function(data) {
		        	var responseData = $.parseJSON(data);

		        	//append the pending purchase orders to view
		        	for(i=0; i<responseData.length; i++){
		        		$(thisRowAddToPOList).append('<li><a class="packyakPurchaseOrderListItem">' + responseData[i]['po_name'] + '</a><div class="packyakPurchaseOrderID hidden">' + responseData[i]['id'] + '</div></li>');
		        	};
		        },
		        error: function(ts) { console.log(ts.responseText) }
		    })
	       }else{
	       		$(this).siblings('.packyakAddItemToPOWrapper').empty();
	       }
	});


	//adding an item to a purchase order 
	$("#packyakInventoryDashTable").on('click', '.packyakPurchaseOrderListItem', function(){
		console.log('working again');
		var action = 'addToPO';
		var selectedPurchaseOrder = $(this).html();
		var itemVariationID = $(this).parents('.packYakItemFeedRow').find('.packyakInventoryItemID').html();
		var itemVariationLocationID = $(this).parents('.packYakItemFeedRow').find('.packyakLocationSold').data('location-id');
		//console.log(itemVariationLocationID);
		var packyakPurchaseOrderID = $(this).siblings('.packyakPurchaseOrderID').html();
		var itemUnitCost = $(this).parents('.packYakItemFeedRow').find('.packyakUnitPrice').html();

		$.ajaxSetup({
	        headers: {
	            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	        }
		});
        data =  {'action': action,
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

	//removing item from purchase order <-PURCHASE ORDER .JS
	$('.packyakRemoveFromPO').click(function(){
		var action = 'removeItemFromPO';
		var packyakPurchaseOrderID = $(this).parents('.packyakPOHeader').find('.pypoid').html();
		var itemVariationID = $(this).parents('.packyakPOItemListItem').find('.poitemid').html();
		var itemLocationID = $(this).parents('.packyakPOItemListItem').find('.packyakLocationSoldAt').data('id');
		var successCancelRow = $(this).parents('.packyakPOItemListItem');

		$.ajaxSetup({
	        headers: {
	            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	        }
		});

		if(confirm('Are you sure you want to remove this item?')){
	        data =  {'action': action,
	        		 'itemVariationID': itemVariationID,
	        		 'itemLocationID': itemLocationID,
	        		 'packyakPurchaseOrderID': packyakPurchaseOrderID
	    			};

	       		$.ajax({
		        type: "POST",
		        url: './purchaseOrders',

		        data: data,
		        success: function(data) {
		        	updatedPurchaseOrder = JSON.parse(data);
		        	successCancelRow.parents('.packyakPOHeader').find('.packyakPOSubtotal').html('$'+(updatedPurchaseOrder.po_subtotal/100).toFixed(2));
		        	successCancelRow.parents('.packyakPOHeader').find('.packyakPOTotalCost').html('$'+ (updatedPurchaseOrder.po_total_cost/100).toFixed(2));
		        	successCancelRow.hide();
		        },
		        error: function(ts) { console.log(ts.responseText) }
		    })
		}
	});

	//deleting a purchase order <- PURCHASEORDER.JS
	$('.packYakDeletePOButton').click(function(){
		if(confirm('Are you sure you want to DELETE this purchase order?')){
			var action = 'deletePO';
			var po_id_number = $('#myModal').find('.pypoidModal').html();

			$.ajaxSetup({
		        headers: {
		            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		        }
			});

	        data =  {'action': action,
			 'packyakPurchaseOrderID': po_id_number
			};

		    $.ajax({
		        type: "POST",
		        url: './purchaseOrders',

		        data: data,
		        success: function(poid) {
		        	$('.packyakPOHeader').each(function(){
		        		if($(this).find('.pypoid').html() == poid.toString()){
		        			    $(this).animate({
							        opacity: '0'
							    }, 400, function(){
							            $(this).remove();
							        });
							    
		        		}
		        	});

		        },
		        error: function(ts) { console.log(ts.responseText) }
		    })
		}

	});

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

	/*
    |--------------------------------------------------------------------------
    | Vendor Page JS Section <-VENDORCENTER.JS
    |--------------------------------------------------------------------------
    

    $('#vendorsTable').DataTable({
		"iDisplayLength": 50,
    });

    $(":input").inputmask();

    $('.createVendorModalButton').on('click', function(){
    	var companyName = $(this).parents('#createVendorModal').find('#createVendorCompanyName').val();
    	var contactName = $(this).parents('#createVendorModal').find('#createVendorContactName').val();
    	var phoneNumber = $(this).parents('#createVendorModal').find('#createVendorPhoneNumber').val();
    	var address = $(this).parents('#createVendorModal').find('#createVendorAddressName').val();
    	var city = $(this).parents('#createVendorModal').find('#createVendorCityName').val();
    	var state = $(this).parents('#createVendorModal').find('#createVendorStateName').val();
    	var zipCode = $(this).parents('#createVendorModal').find('#createVendorZipName').val();

    	console.log(companyName);
    	console.log(contactName);
    	console.log(phoneNumber);
    	console.log(address);
    	console.log(city);
    	console.log(state);
    	console.log(zipCode);
    });	*/

    /*
    |--------------------------------------------------------------------------
    | Use filters to search for items
    |--------------------------------------------------------------------------
    */
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

});


/*
|--------------------------------------------------------------------------
| Get Item Data and delete variation form square and database
| @Param -> item variation to delete <-MAINFEED.JS
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
        	for(i=0; i < data.length; i++){
        		for(j=0; j < data[i].length; j++){
        			//console.log(data[i][j]);

        			htmlString.push(
        			'<tr class="packYakItemFeedRow">' +
                        '<td class="packyakLocationSold" data-location-id='+ data[i][j].itemLocationID +'>' + data[i][j].locationSoldAt + '</td>' +
                        '<td>'+data[i][j].itemCategoryName+'</td>'+
                        '<td>'+data[i][j].itemName+'</td>'+
                        '<td>'+data[i][j].itemVariationName+'</td>'+
                        '<td class="packyakInventory">' +data[i][j].itemVariationInventory+ '</td>' +
                        '<td class="packyakInventoryText hidden"><input class="packyakInventoryTextInput" type="number" min="-5" step="1" name="newInventoryLevel" value="'+data[i][j].itemVariationInventory+'"></td>'+
                        '<td>$'+(data[i][j].itemVariationPrice/100).toFixed(2)+'</td>'+
                        '<td class="packyakUnitPrice">$'+(data[i][j].itemVariationUnitCost/100).toFixed(2)+'</td>'+
                        '<td class="packyakUnitPriceText hidden">$<input class="packyakUnitPriceTextInput" min=".00" step=".01" name="currency" type="number" value="'+(data[i][j].itemVariationUnitCost/100).toFixed(2)+'"></td>' +
                        '<td>$'+((data[i][j].itemVariationPrice-data[i][j].itemVariationUnitCost)/100).toFixed(2)+'</td>'+
                        '<td>'+data[i][j].itemVariationSKU+'</td>'+                           
                        '<td class="packyakPurchaseOrderMenuButton">'+
                            '<div class="dropdown">'+
                                '<i class="fa fa-chevron-circle-down fa-2 btn btn-default dropdown-toggle packyakPurchaseOrderList" type="button" id="packyakPurchaseOrderList" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"></i>'+
                                '<ul class="dropdown-menu dropdown-menu-right packyakAddItemToPOWrapper" aria-labelledby="packyakPurchaseOrderList">'+
                                '</ul>'+
                            '</div>'+       
                        '</td>'+
                        '<td class="packyakSubmitButton"><i class="fa fa-check-circle-o fa-2 btn btn-default"></i></td>'+
                        '<td class="packyakCancel"><i class="fa fa-times-circle fa-2 btn btn-default"></i></td>'+
                        '<td class="packyakDeleteItem"><i class="fa fa-trash-o fa-2 btn btn-default"></i></td>'+
                        '<td class="packyakInventoryItemID hidden" data-variation-id='+ data[i][j].itemVariationID +' >'+data[i][j].itemVariationID+'</td>'+
                    '</tr>');
        		}
        	}

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


/*
|--------------------------------------------------------------------------
| Make PDF download of a purchase order
| @Param -> locations to search retrieve
|--------------------------------------------------------------------------
*/
$('.packyakPOHeader').on('click', '.downloadPDFbutton', function(){
	var purchaseOrder = $(this).parents('.packyakPOHeader');
	createPDF(purchaseOrder);
	//createPDF();
	//downloadPDF(); 
});

function buildTableBody(data, columns) {
    var body = [];

    body.push(columns);

    data.forEach(function(row) {
        var dataRow = [];

        columns.forEach(function(column) {
            dataRow.push(row[column].toString());
        })

        body.push(dataRow);
    });

    return body;
}

function table(data, columns) {
    return {	style: 'POItemsList',
	        	table: {
	            headerRows: 1,
	            widths: [80, '*', '*', 75, 50],
	            body: buildTableBody(data, columns)
        }
    };
}

function createPDF(purchaseOrder){
	//console.log(purchaseOrder);
	var purchaseOrderID = purchaseOrder.find('.pypoid').text();
	var purchaseOrderName = purchaseOrder.find('.packYakPOName').text();
	var purchaseOrderLocation = purchaseOrder.find('.packYakPOHeaderLocation').text();
	var purchaseOrderCreated = purchaseOrder.find('.packYakPOCreated').text();
	var purchaseOrderItems = purchaseOrder.find('.packyakPOItemListItem');

	var externalDataRetrievedFromServer = [];

	purchaseOrderItems.each(function(){
		var POItem = { Location: $(this).find('.packyakLocationSoldAt').text(), Item: $(this).find('.packyakItemName').text(), Variation: $(this).find('.packyakVariationName').text(), Quantity: $(this).find('.packyakQuantityToOrder').text(), SKU: $(this).find('.poitemSKU').text()}
		externalDataRetrievedFromServer.push(POItem);
	});

	//define document
	var dd = { content: [
						{text: purchaseOrderName + ' - Purchase Order #' + purchaseOrderID, style: 'header'},
						{text: purchaseOrderCreated.substring(0, purchaseOrderCreated.length - 2)}, //date created
						{style: 'tableExample', 
							table: {
								widths: [200, 50, 200],
								body: [
									[
										{text: 'Order From:\n',
									}, 
										{text: '', border: [false,false,false,false]}, //gap inbetween boxes
										{text: 'Ship To: ' + purchaseOrderLocation.substring(0, purchaseOrderLocation.length - 2)}]
								]
							}
						},
						
						table(externalDataRetrievedFromServer, ['Location', 'Item', 'Variation', 'SKU','Quantity'])

						
						],	styles: {
								POItemsList: {
									fontSize: 8
								},
								header: {
									fontSize: 18,
									bold: true,
									margin: [0, 0, 0, 10]
								},
								subheader: {
									fontSize: 16,
									bold: true,
									margin: [0, 10, 0, 5]
								},
								tableExample: {
									margin: [0, 5, 0, 15],
									fontSize: 8
								},
								tableHeader: {
									bold: true,
									fontSize: 13,
									color: 'black'
								}
							},
							defaultStyle: {
								// alignment: 'justify'
							}
		
	};
	// open the PDF in a new window
 	pdfMake.createPdf(dd).open();
}
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