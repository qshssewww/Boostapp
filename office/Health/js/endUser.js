angular.module('dynForm', ['signature']).controller('formGen', ['$window', '$http', '$scope', function ($window, $http, $scope) {
    var vm = this;
    vm.form = $window.dynForm;

    // chckbox store elements
    $scope.Object = Object;

    vm.validate = function(){
        var valid = true;
        for(var i in vm.form.items){
            var item = vm.form.items[i];
            delete item.$$error;
            if(!item.type || item.type != 'question') continue;
            if(!item.data && !item.data.required) continue;
            

            if(item.typeQ.type == 'text'){
                if(!item.choosenAnswer){
                    item.$$error = 'שדה חובה, אנא מלאו פרטים';
                    valid =false;
                    continue;
                }
            } 

            // validatew raddio field
            if(item.typeQ.type == 'radio'){
                if((!item.choosenAnswer || !item.choosenAnswer.item)){
                    item.$$error = 'שדה חובה, אנא בחר אפשרות';
                    valid =false;
                    continue;
                }

                // find answer in item
                var check = item.answers.filter(function(x){return x.item == item.choosenAnswer.item})[0];
                if(!check) continue;
                // check if we require explain
                if(check.explain && !item.choosenAnswer.explain){
                    item.$$error = 'יש לתת נימוק לאפשרות הנבחרת';
                    valid =false;
                    continue;
                }
               
            }

            // validate checkbox type field
            if(item.typeQ.type == 'checkbox'){
                if(!item.choosenAnswer){
                    item.$$error = 'שדה חובה, אנא בחר אפשרות';
                    valid =false;
                    continue;
                }

                // cleanup false item
                for(var i in item.choosenAnswer){
                    if(!item.choosenAnswer[i].item) delete item.choosenAnswer[i]
                }

                if(!Object.keys(item.choosenAnswer).length){
                    item.$$error = 'שדה חובה, אנא בחר אפשרות';
                    valid =false;
                    continue;
                }
                

                // check if we require explonation for choosen object
                for(var i=0; i < item.answers.length; i++){
                    if(item.answers[i].explain && item.choosenAnswer[item.answers[i].item] && !item.choosenAnswer[item.answers[i].item].explain){
                        item.$$error = 'יש לתת נימוק לאפשרות הנבחרת';
                        valid =false;
                        // stop the loop no need to waste machine time
                        break;
                    }
                }
                continue;         
            }

        }
        return valid;
    }

    vm.$$submit = false; // GUI helper
    vm.submit = function(){
        var signuture = vm.acceptSignuture();
        
        vm.$$signuture = signuture.pad.isEmpty();
        if(!vm.validate() || vm.$$signuture) return false;

        
        vm.$$submit = true;
        // copy only needed stuff, save network bandwidth
        var data = [];
        for(var i in vm.form.items){
            var item = vm.form.items[i];
            if(item.type == 'question') {
                data.push({q: item.question, a: item.choosenAnswer});
                continue;
            }
            if(item.type == 'instruction') {
                data.push({i: item.instruction});
            }

        }

        $http.post($window.location.href, {data: data, signuture: signuture.pad.toDataURL("image/svg+xml")}).then(function(data){})
    }


}]).config(function ($sceProvider) {
    // Completely disable SCE.
    // Do not use in new projects.
    $sceProvider.enabled(false);
}).directive('deleteModel', function () {
    return {
        scope: {
            modelName:  '@ngModel',
            modelValue: '=ngModel'
        },
        link: function ($scope, $element, $attr) {
            $scope.$on('$destroy', function () {
                // console.log('Deleting '+ $scope.modelname + 'with value '+$scope.modelValue);
                delete $scope.modelValue;
            });
        }
    }
});