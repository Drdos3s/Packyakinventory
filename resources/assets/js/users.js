$( document ).ready(function() { 
});

/*
|--------------------------------------------------------------------------
| Ajax to create a new manager
|--------------------------------------------------------------------------
*/

$('#newManagerSubmit').on('click', function(){
	var newManager = {'name': $('[name=name]').val(),
				'email': $('[name=email]').val(),
				'password': $('[name=password]').val(),
				'password_confirmation': $('[name=password_confirmation]').val(),
				'location': $('[name=location]').val()};

				console.log(newManager);
	$.ajax({
        type: "POST",
        url: '/profile',

        data: newManager,
        success: function(data) {
        	//Close modal
        	$('#createManagerModal').modal('toggle');

        	//Clear input fields for reuse
        	$('#createManagerModal').find('input').val('');

          //add 'hide' class to callout
          $('.noItemCallout').addClass('hidden');

        	//need to email the new manager their login info

        	console.log(data);

          var html = '<div class="post manager" data-managerID="'+data['id']+'">' +
                        '<div class="user-block">' +
                          '<img class="img-circle img-bordered-sm" src="" alt="user image">' + 
                            '<span class="username">' +
                              '<a href="#">'+data['name']+'</a>' + 
                            '</span>' +
                            '<span class="description">'+data['manager_location']+'</span>' +
                          '</div><!-- /.user-block -->' +
                          
                          '<ul class="list-inline">' +
                            '<li><a href="#" class="link-black text-sm"><i class="fa fa-pencil margin-r-5"></i>Edit</a></li>' +
                            '<li class="pull-right"><a href="#" data-managerID="'+ data['id'] +'" class="deleteManagerLink link-black text-sm"><i class="fa fa-trash-o margin-r-5"></i>Delete</a></li>' +
                          '</ul>' + 
                        '</div>';

          $('#managers').append(html);
          console.log($('#managers'));
        },
       	error: function(data) { 
       		console.log(data.responseText) 

       		//Display form errors
       		if( data.status === 422 ) {
		          var errors = data.responseJSON;
		          var errorHtml = '<div class="errors"><ul>';

		          	$.each( errors, function( key, value ) {
		               errorHtml += '<li>' + value[0] + '</li>';
		         	});

		          errorHtml += '</ul></div>';

		          $( '#formerrors' ).html( errorHtml );
		    }
       	}
    })	
})

/*
|--------------------------------------------------------------------------
| STEP 1: DELETE MANAGER
|--------------------------------------------------------------------------
*/

$('#managers').on('click', '.deleteManagerLink', function(){

	var managerID = $(this).data('managerid');

	//adds variation ID to the button to be retrieved on confirmation
	$('.deleteManagerButton').attr({'managerid': managerID});

	//open up confirm modal
	$('#deleteManagerModal').modal('toggle');
});

/*
|--------------------------------------------------------------------------
| STEP 2: DELETE MANAGER CONFIRMATION
|--------------------------------------------------------------------------
*/

$('.deleteManagerButton').on('click', function(){
	var managerID = $(this).attr('managerid');
	deleteManager(managerID);
});

/*
|--------------------------------------------------------------------------
| DELETE MANAGER FUNCTION
|--------------------------------------------------------------------------
*/
function deleteManager(id){
	$.ajax({
        type: "DELETE",
        url: '/profile/'+id,
        success: function(data) {
        	//Close modal
        	$('#deleteManagerModal').modal('toggle');

        	//Remove the resource from the view
        	$('#managers').find($('.manager[data-managerID="'+id+'"]').addClass('hidden'));
        },
       	error: function(data) { 
       		console.log(data.responseText) 
       	}
    })
}