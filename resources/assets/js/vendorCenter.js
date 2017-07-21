/*
VENDOR CENTER:
CREATE*/

$(document).ready(function () {

	//DATA TABLE DISPLAY SETTINGS
    $('#vendorsTable').DataTable({
		"iDisplayLength": 50,
    });

    //INITIALIZE INPUT MASKS
    $(":input").inputmask();

    //CREATE VENDOR
    $('.createVendorModalButton').on('click', function(){
    	
    	//Get all the vendor information
    	var companyName = $(this).parents('#createVendorModal').find('#createVendorCompanyName').val();
    	var contactName = $(this).parents('#createVendorModal').find('#createVendorContactName').val();
    	var phoneNumber = $(this).parents('#createVendorModal').find('#createVendorPhoneNumber').val();
    	var email = $(this).parents('#createVendorModal').find('#createVendorContactEmail').val();
    	var address = $(this).parents('#createVendorModal').find('#createVendorAddressName').val();
    	var city = $(this).parents('#createVendorModal').find('#createVendorCityName').val();
    	var state = $(this).parents('#createVendorModal').find('#createVendorStateName').val();
    	var zipCode = $(this).parents('#createVendorModal').find('#createVendorZipName').val();

    	console.log(companyName);
    	console.log(contactName);
    	console.log(phoneNumber);
    	console.log(email);
    	console.log(address);
    	console.log(city);
    	console.log(state);
    	console.log(zipCode);

    	//send info over to function to handle ajax request
    	createVendor(companyName, contactName, phoneNumber, email, address, city, state, zipCode);

    });

    //READ VENDORS

    //UPDATE VENDORS


    //DELETE VENDOR
    $('.deleteVendorButton').on('click', function(){
    	var vendorID = $('.deleteVendorButton').data('vendor');
        console.log(vendorID);
    	deleteVendor(vendorID);
    });

//End page load js
});


function createVendor(companyName, contactName, phoneNumber, email, address = '', city = '', state = '', zipCode = ''){

    //Seperate the numbers from other characters using regex
    var strippedPhoneNumber = phoneNumber.replace(/\D/g, '');

    //Get the full phone number
    var phoneNumberFinal = strippedPhoneNumber.substring(0,10);
    var extenstionNumberFinal;

    //seperate phone extension if necessary
    if(strippedPhoneNumber.length > 10){
        var extenstionNumberFinal = strippedPhoneNumber.substring(10, strippedPhoneNumber.length);
    }else{
        extenstionNumberFinal = '';
    }
	
    var newVendorInformation = {'companyName': companyName,
                                'contact_name': contactName,
                                'phone_number': phoneNumberFinal,
                                'extension': extenstionNumberFinal,
                                'email': email,
                                'address': address,
                                'city': city,
                                'state': state,
                                'zip': zipCode};

    console.log('getting here??');

	$.ajax({
        type: "POST",
        url: '/vendors',
        data: newVendorInformation,
        success: function(data) {
        	//Reset the modal
        	//$('#createVendorModal').modal('toggle');
            console.log(data);
        },
        error: function(ts) { 
            console.log(ts);
        	if( ts.status === 401 ) //redirect if not authenticated user.
                $( location ).prop( 'pathname', 'auth/login' );
                console.log(ts);
            if( ts.status === 422 ) {
                //process validation errors here.

                //print out error message
                console.log(ts)

                //turn response into json object
                var errors = JSON.parse(ts.responseText);

                //print out json object
                console.log(errors);

                //this will get the errors response data.
                //show them somewhere in the markup
                //This is the logic to place them where they need to be
                var errorsHtml = '<div class="alert alert-danger"><ul>';

                $.each(errors, function( key, value ) {
                    errorsHtml += '<li>' + value[0] + '</li>'; //showing only the first error.
                });

                errorsHtml += '</ul></div>';

                $( '#form-errors' ).html( errorsHtml );//appending to a <div id="form-errors"></div> inside form
                } else {
                    //Else block
                }
        }
    })
}

function deleteVendor(id){
    //take the id and delete the resource and then redirect usere back to vendors page for reloading
    $.ajax({
        url: '/vendors/'+id,
        type: 'DELETE',  // user.destroy

        success: function(result) {
            //Will need to change something in the modal that says it has been deleted and then the redirect can happen
        },
        error: function(ts){
            console.log(ts);
        }
    })
}