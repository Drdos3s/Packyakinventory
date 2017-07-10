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
    $('.packyakCancelSearch').click(function() {
    	$('#packyakInventoryDashSearch').val('').keyup();
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