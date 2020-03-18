app.controller('reports', ['$scope', '$http', function($scope, $http) {
  
  $scope.report = function(from,to)
  {
    $http.post(APP_URL+"/admin/trips", { from: from, to: to }).then(function( response ) {
      
      
        $scope.reservations_report = response.data;
      
    });
  };

  $scope.print = function(category)
  {
    var prtContent = document.getElementById('trips');
    var WinPrint = window.open('', '', 'left=0,top=0,width=800,height=900,toolbar=0,scrollbars=0,status=0');
    WinPrint.document.write(prtContent.innerHTML);
    WinPrint.document.close();
    WinPrint.focus();
    WinPrint.print();
    WinPrint.close();
  };

  $('.date').datepicker({ 'dateFormat': 'dd-mm-yy'});
  
}]);

app.controller('payments', ['$scope', '$http', function($scope, $http) {
  
  $scope.payment = function(from,to)
  {
    $http.post(APP_URL+"/admin/payments", { from: from, to: to }).then(function( response ) {
      
      
       	$scope.payments_report = response.data;
      
    });
  };

  $scope.print = function(category)
  {
    var prtContent = document.getElementById('payments');
    var WinPrint = window.open('', '', 'left=0,top=0,width=800,height=900,toolbar=0,scrollbars=0,status=0');
    WinPrint.document.write(prtContent.innerHTML);
    WinPrint.document.close();
    WinPrint.focus();
    WinPrint.print();
    WinPrint.close();
  };

  $('.date').datepicker({ 'dateFormat': 'dd-mm-yy'});
  
}]);