/*
PURCHASE ORDERS:
CREATE
EDIT
DELETE


REMOVE ITEM

*/
$(document).ready(function () {

	$( function() {
	    function log( message ) {
	      $( "<div>" ).text( message ).prependTo( "#log" );
	      $( "#log" ).scrollTop( 0 );
	    }
	 
	    $( "#birds" ).autocomplete({
	      source: "purchaseOrders/itemSearch",
	      minLength: 2,
	      select: function( event, ui ) {
	        log( "Selected: " + ui.item.value + " aka " + ui.item.id );
	      }
	    });
	  } );


	//CREATE AND EDIT NEW PURCHASE ORDER
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
		
    	/*$.ajaxSetup({
	        headers: {
	            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	        }
		})*/

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

	//EDITING PURCHASE ORDER DETAILS
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

	//DELETE PURCHASE ORDER
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

	//REMOVE ITEM FROM PURCHASE ORDER
	$('.packyakRemoveFromPO').click(function(){
		var action = 'removeItemFromPO';
		var packyakPurchaseOrderID = $(this).parents('.packyakPOHeader').find('.pypoid').html();
		var itemVariationID = $(this).parents('.packyakPOItemListItem').find('.poitemid').html();
		var itemLocationID = $(this).parents('.packyakPOItemListItem').find('.packyakLocationSoldAt').data('id');
		var successCancelRow = $(this).parents('.packyakPOItemListItem');

		/*$.ajaxSetup({
	        headers: {
	            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	        }
		});*/

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

	//CREATE PDF FROM CREATE PDF BUTTON ON PURCHASE
	$('.packyakPOHeader').on('click', '.downloadPDFbutton', function(){
		var purchaseOrder = $(this).parents('.packyakPOHeader');
		createPDF(purchaseOrder);
	});
});

/*
|--------------------------------------------------------------------------
| Make PDF download of a purchase order
| @Param -> locations to search retrieve
|--------------------------------------------------------------------------
*/


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
