app.directive('postsPagination', function(){  
	return{
			restrict: 'E',
			template: '<ul class="pagination">'+
				// '<li ng-show="currentPage != 1"><a href="javascript:void(0)" ng-click="getTrips(1)">&laquo;</a></li>'+
				'<li ng-show="currentPage != 1"><a href="javascript:void(0)" ng-click="getTrips(currentPage-1)"><span class="icon icon_left-arrow"></span></li>'+
				'<li ng-repeat="i in range" ng-class="{active : currentPage == i}">'+
						'<a href="javascript:void(0)" ng-click="getTrips(i)">{{i}}</a>'+
				'</li>'+
				'<li ng-show="currentPage != totalPages"><a href="javascript:void(0)" ng-click="getTrips(currentPage+1)"><span class="icon icon_right-arrow"></span></a></li>'+
				// '<li ng-show="currentPage != totalPages"><a href="javascript:void(0)" ng-click="getTrips(totalPages)">&raquo;</a></li>'+
			'</ul>'
	 };
})
.directive('invoicesPagination', function(){  
	return{
			restrict: 'E',
			template: '<ul class="pagination">'+
				// '<li ng-show="currentPage != 1"><a href="javascript:void(0)" ng-click="getTrips(1)">&laquo;</a></li>'+
				'<li ng-show="invoicecurrentPage != 1"><a href="javascript:void(0)" ng-click="getInvoice(invoicecurrentPage-1)"><span class="icon icon_left-arrow"></span></li>'+
				'<li ng-repeat="i in range" ng-class="{active : invoicecurrentPage == i}">'+
						'<a href="javascript:void(0)" ng-click="getInvoice(i)">{{i}}</a>'+
				'</li>'+
				'<li ng-show="invoicecurrentPage != totalPages"><a href="javascript:void(0)" ng-click="getInvoice(invoicecurrentPage+1)"><span class="icon icon_right-arrow"></span></a></li>'+
				// '<li ng-show="currentPage != totalPages"><a href="javascript:void(0)" ng-click="getTrips(totalPages)">&raquo;</a></li>'+
			'</ul>'
	 };
}).controller('trip', ['$scope', '$http', '$compile', function($scope, $http, $compile) {
$scope.date = new Date();
$( document ).ready(function() 
{
    $scope.getTrips();
    $scope.getInvoice();
});
// $(document).click('#trip-filterer-button',function()
// {
// 	$('.custom-cls').addClass('loading');
// });

$scope.getTrips = function(pageNumber)
{
	$('.all-trips-table').hide();
	$('.all-trips-table:first').show();
	$('.all-trips-table').addClass("loading");
	if($scope.currentPage == undefined)
		$scope.currentPage = '1';

	if(pageNumber===undefined)
	{
		pageNumber = '1';
	}
	var id = $('#user_id').val();
	if($scope.selected_filter)
   	{
		$http.post('ajax_trips/'+id+'?page='+pageNumber+'&month='+$scope.selected_filter, { }).then(function(response) 
	    {
			$('.all-trips-table').removeClass("loading");
			$('.all-trips-table').show();
	    	$("#selected_month").text($scope.selected_month);
	    	$scope.trips = response.data.data;
	    	$scope.totalPages   = response.data.last_page;
	    	$scope.currentPage  = response.data.current_page;
	    });
	}
	else
	{
		$http.post('ajax_trips/'+id+'?page='+pageNumber, { }).then(function(response) 
	    {
			$('.all-trips-table').removeClass("loading");
			$('.all-trips-table').show();	   			    		    	
	    	$scope.trips = response.data.data;
	    	$scope.totalPages   = response.data.last_page;
	    	$scope.currentPage  = response.data.current_page;
	    });
	}
}
$(document).on('click', '.month-filter', function() 
{
	$scope.selected_month = $(this).attr('month');
	$scope.selected_filter = $(this).attr('value');
	$('.month-filter').removeClass('filter-checked');
	$(this).addClass('filter-checked')

});

$scope.Rating = function(rate,trip_id)
{
	$http.post('rider_rating/'+rate+'/'+trip_id, { }).then(function(response) 
    {
    	if(response.data.success)
    	{
    		window.location.reload();
    	}
    });
}
$scope.getInvoice = function(pageNumber) {
	$(".driver_trips_details").addClass("loading");
	if(pageNumber===undefined) {
		pageNumber = '1';
	}
	var page = $('#per_page').val();
	if(page == undefined) {
		return false;
	}
	$http.get('driver_invoice?limit='+page+'&page='+pageNumber).then(function(response) 
    {
		$(".driver_trips_details").removeClass("loading");    	
    	if(response.data)
    	{
    		$scope.driver_trips = response.data;
    		$scope.totalPages   = response.data.last_page;
	    	$scope.invoicecurrentPage  = response.data.current_page;
    	}
    });
};

}]);


	