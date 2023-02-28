angular.module('newMedicalIndicators', []).controller('new', ['$http', function($http){

    var vm = this;

    vm.fields = {};
    $http.get('/office/rest/', {params:{type: 'medicalindicators', method:'dic'}}).then(function(res){
        res.data.items.map(function(x){
            vm.fields[x.type] = vm.fields[x.type] || []
            vm.fields[x.type].push({
                id: x.id, 
                value: x.value,
                input: x.input || 'number',
                description: x.description || ''
            });
        });
    }, function(res){})
}]);