(function (angular) {
    angular.module("count", ['countTo']).controller("controllercount",["$scope", "$http", function ($scope, $http) {
        $scope.DataCount = 0;
        $http.get("ng/DeshCountMemberPost.php")
            .success(function (data) {
                $scope.DataCount = data.length;
                // console.log(data.length);        
            });
    }]);
})(angular)