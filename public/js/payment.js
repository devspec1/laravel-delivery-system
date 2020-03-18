app.directive('postsPagination', function(){  
	return{
			restrict: 'E',
			template: '<ul class="pagination">'+
				// '<li ng-show="currentPage != 1"><a href="javascript:void(0)" ng-click="getPayment(1)">&laquo;</a></li>'+
				'<li ng-show="currentPage != 1"><a href="javascript:void(0)" ng-click="getPayment(currentPage-1)"><span class="icon icon_left-arrow"></span></li>'+
				'<li ng-repeat="i in range" ng-class="{active : currentPage == i}">'+
						'<a href="javascript:void(0)" ng-click="getPayment(i)">{{i}}</a>'+
				'</li>'+
				'<li ng-show="currentPage != totalPages"><a href="javascript:void(0)" ng-click="getPayment(currentPage+1)"><span class="icon icon_right-arrow"></span></a></li>'+
				// '<li ng-show="currentPage != totalPages"><a href="javascript:void(0)" ng-click="getPayment(totalPages)">&raquo;</a></li>'+
			'</ul>'
	 };
}).controller('payment', ['$scope', '$http', '$compile', function($scope, $http, $compile) {
$scope.date = new Date();
$scope.minDate = new Date();
$(function () 
{
    $("#begin_trip").datepicker(
    {
        dateFormat: 'yy-mm-dd',
        maxDate:-1,
        beforeShow: function(input, inst) {
        setTimeout(function() {
                inst.dpDiv.find('a.ui-state-highlight').removeClass('ui-state-highlight');
                $('.ui-state-disabled').removeAttr('title');
                $("#ui-datepicker-div td.ui-datepicker-today a.ui-state-highlight").removeClass('ui-state-highlight');
                $('.highlight').not('.ui-state-disabled').tooltip({container:'body'});
            }, 100);
    	},
       	onSelect: function (date) 
    	{   
    		
    		  
	    		var checkout = $('#begin_trip').datepicker('getDate');
		        checkout.setDate(checkout.getDate() + 1);
		        $('#end_trip').datepicker('option', 'minDate',checkout );
        		$('#end_trip').datepicker('setDate', checkout);
		        // $('#end_trip').datepicker('setDate', checkout);  
		        setTimeout(function(){
		            $("#end_trip").datepicker("show");
		        },20);
	        // $scope.getPayment(1);
		          
    	},
	    onChangeMonthYear: function(){
	        setTimeout(function(){
	            $('.highlight').not('.ui-state-disabled').tooltip({container:'body'});
	        },100);  
	    }
    });
    $("#end_trip").datepicker(
    {
        dateFormat: 'yy-mm-dd',
        minDate:$('#begin_trip').val(),
        maxDate:0,
        beforeShow: function(input, inst) {
        setTimeout(function() {
	        	$("#ui-datepicker-div td.ui-datepicker-today a.ui-state-highlight").removeClass('ui-state-highlight');
                $('.ui-state-disabled').removeAttr('title');
                $('.highlight').not('.ui-state-disabled').tooltip({container:'body'});
            }, 100);
    	},
        onSelect: function (date) 
    	{       
    		
    		if($('#begin_trip').val() == '')
    		{
    			var checkout = $('#end_trip').datepicker('getDate');
		        checkout.setDate(checkout.getDate() - 1);

		        $('#begin_trip').datepicker('setDate', checkout); 
		        setTimeout(function(){
		            $("#begin_trip").datepicker("show");
		        },20);   
	        }  
	        $scope.getPayment(1);
    	},
	    onChangeMonthYear: function(){
	        setTimeout(function(){
	            $('.highlight').not('.ui-state-disabled').tooltip({container:'body'});
	        },100);  
	    }
    });
});
$scope.getPayment = function(pageNumber, event)
{
	if(event === undefined ) 
	{
		target = '';
	}
	else if(event == 'pay_period')
	{
		target = 'pay_period';
	}
	else
	{
		target = $(event.target).attr('id');	
	}
	if(target == 'pay_period')
		$('.pay_period_details').addClass('loading');
	else
		$('.earning_period_details').addClass('loading');

	if(pageNumber===undefined)
	{
		pageNumber = '1';
	}
	var data = target == 'pay_period' ? $scope.pay_period : $scope.earning_period;
	var begin_trip = '';
	var end_trip = '';
	begin_trip = $('#begin_trip').val();
	end_trip = $('#end_trip').val();
	$http.post('ajax_payment?page='+pageNumber,{ data: data,begin_trip:begin_trip,end_trip:end_trip }).then(function(response) 
	{
		$('.pay_period_details').removeClass('loading');		
		$('.earning_period_details').removeClass('loading');		
		if(response.data)
		{
			if(data == "all_trips" || data == 'completed_trips' || data == 'cancelled_trips')
			{
				$scope.all_trips.data = response.data.data;
				$scope.totalPages   = response.data.last_page;
	    		$scope.currentPage  = response.data.current_page;
			}
			else
			{
				$scope.completed_trips = response.data.completed_trips;
				$scope.cancelled_trips = response.data.cancelled_trips;

			}
			
		}
	   
	});	
}
$scope.upload_document = function(doc,name)
{
	$('#span-cls').text($scope.select_file);
	$('.document_upload').text(doc);
	$('#document_type').val(name);
	$('#document_upload').attr('name',name);
}
$('#document_upload').change(function(e)
{    
    $('.doc-button').attr('type','submit');
    $('#uploadForm').submit();
});
$(".doc_upload").on('click', function(e) {
    e.preventDefault();
    e.stopPropagation();
    $("#document_upload").trigger('click');
});
$(document).ready(function (e) 
{
	$(".image-show").click(function(){

         // show Modal
         $('#myModalLabel').text($(this).attr('data-title'));
         $('.modal-image').attr('src',$(this).find('img').attr('src'));
         $('#myModal').modal('show');
    });

	$("#uploadForm").on('submit',(function(e) {
		$('.top-home').addClass('loading');
		e.preventDefault();
		$.ajax({
        	url: '../document_upload/' + $('#driver_id').val(),
			type: "POST",
			data:  new FormData(this),
			beforeSend: function(){$("#body-overlay").show();},
			contentType: false,
    	    processData:false,
			success: function(data)
		    {
				$('.top-home').removeClass('loading');		    	
		    	
				if(data.status == 'true')
				{
					$('#span-cls').text($scope.upload_file);
					setInterval(function() {$(".popup1").hide();window.location.reload(); },1500);
					// window.location.reload();
				}
				else
				{
					$('#error_msg').text(data.status_message);
				}
			
			}	        
	   });
	}));
});
	
}]);



	