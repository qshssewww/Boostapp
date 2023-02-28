angular.module('dynForm', ['textAngular']).controller('formGen', ['$window', '$http', '$scope', function ($window, $http, $scope) {

    var vm = this;

	
	
	
    // set wysitwg toolbar (left as a reffrence)
    // [
    //     ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'pre', 'quote'],
    //     ['bold', 'italics', 'underline', 'strikeThrough', 'ul', 'ol', 'redo', 'undo', 'clear'],
    //     ['justifyLeft', 'justifyCenter', 'justifyRight', 'indent', 'outdent']
    //     // ['html', 'insertImage','insertLink', 'insertVideo', 'wordcount', 'charcount']
    // ];

    // type of filds for dynamic form
	
    vm.types = [{
        label: 'טקסט חופשי',
        type: 'text'
    }, {
        label: 'בחירה אחת',
        type: 'radio'
    }, {
        label: 'בחירה מרובה',
        type: 'checkbox'
    }];
    
    vm.scrollEnd = function(){
window.scrollTo(0,document.body.scrollHeight);
    }

    // default blank form
    var blankQuestions = []
    var blankForm = {
        title: {
            placeholder: 'הכנס כותרת לטופס',
            text: $window.dynForm && $window.dynForm.title && $window.dynForm.title.text ? $window.dynForm.title.text : ''
        },
        header: {
            placeholder: 'הוראות ללקוח או הקדמה (רשות)',
            text: $window.dynForm && $window.dynForm.header && $window.dynForm.header.text ? $window.dynForm.header.text : ''
        },
        footer: {
            placeholder: 'תוכן לאחר טופס ללקוח (רשות)',
            text: $window.dynForm && $window.dynForm.footer && $window.dynForm.footer.text ? $window.dynForm.footer.text : ''
        },
        items: $window.dynForm && $window.dynForm.items ? $window.dynForm.items : angular.copy(blankQuestions),
//        options: {
//            foreRenew: $window.dynForm && $window.dynForm.options && $window.dynForm.options.foreRenew ? true : false
//        },
         editoption: {
            formId:  angular.element(document.querySelector('#fixformId')).val(),
        }
    }
    // Structure data for form
    vm.data = angular.copy(blankForm);


    // A helper function to move item in vm.data.items or vm.data.item[*].answers
    vm.move = function (index, direction, item, array) {
        array.splice(index, 1);
        array.splice(direction == 'up' ? index - 1 : index + 1, 0, item);
    }

    // to show options to user on change select input
    vm.toggleOptions = function (item) {
        if (['text'].indexOf(item.type) != -1) {
            // item.answers = [];
            return;
        }
        item.answers = item.answers || [{}];
        item.$$showAnswers = true;
    }

    // validate against blank data and logic
    function validate(){
        var valid = true;
        if(!vm.data.title.text){
            valid =false;
            vm.data.title.$$error = 'יש להוסיף כותרת'
        }else{
            delete vm.data.title.$$error;
        }

        if(!Object.keys(vm.data.items).length){
            return false;
        }

        for(var index in vm.data.items){
            var item = vm.data.items[index];
			var a_id = vm.data.items[index];
            // initial errors
            item.$$message = [];



            switch(item.type){	
                case 'question': 
                    if(!item.question) item.$$message.push('חובה להזין שאלה');
					if(item.question && !item.q_id) item.q_id = Math.floor((Math.random() * 10000000000) + 1);
                    if(!item.typeQ) item.$$message.push('יש לבחור סוג שאלה');
                    if(item.typeQ && ['text'].indexOf(item.typeQ.type) == -1){
                        for(var i in item.answers){
                            var a = item.answers[i];
							var q = a_id.answers[i];
                            if(!a.item) item.$$message.push('יש לתת תשובה אפשרית ב-'+(parseInt(i)+1));
							if(a.item && !q.a_id) q.a_id = Math.floor((Math.random() * 10000000000) + 1);
                        }
                    }
                break;
                case 'instruction': 
                    if(!item.instruction) item.$$message.push('יש להזין טקסט או למחוק');
                break;               
            }
            if(item.$$message.length) valid = false;
        }
        return valid;
    }

    // allow debounce on validate to controll infinitated loop
    var debounce = function(cb, time) {
        var timeout = null;
        return function(data) {
          if (timeout) {
            clearTimeout(timeout);
          }
          timeout = setTimeout(function() {
            cb(data);
          }, time || 200);
        };
      };

    // on the fly check for changes, with debounce to prevent buggy browser
    $scope.$watch('vm.data', debounce(function(newvalue, oldValue, scope){
        if(!vm.data.$dirty) return; // prevent for infinity check, only after user submit
        validate();
    }, 200), true)

    // submit form
    vm.submit = function(){
        // only submit once
        vm.data.$$dirty = true;
        if(vm.data.$$submit) return;
        vm.data.$$valid = validate();
        // validate data field
        if(!vm.data.$$valid) return false;

        vm.data.$$submit = false;

        $http.post($window.location.href, angular.copy(vm.data)).then(function(data){
            vm.data = angular.copy(blankForm);
            vm.data.$$response = data.data;
            vm.data.$$submit = false;
			window.location.href="DynamicForms.php";
        },function(data){
            vm.data.$$response = data.data;
            vm.data.$$submit = false;
        })
    }



}]);
