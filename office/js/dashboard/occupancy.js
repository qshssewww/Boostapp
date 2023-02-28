(function ($) {
	$(document).ready(function () {
		var dom = $('#occupancy');

		var progressBar = $('.progress-bar.watingbar', dom)
		var progressBarText = $('small.progressBarText', dom)

		var week, month,  threeMonth , myDoughnut;
		$.get('rest/', {type: 'report', method: 'occupancy', range: 'week'})
		.done(function(data){
			try{data = JSON.parse(data);week=data.items; renderDoughnut(week)}catch(e){};
			
		});
		$.get('rest/', {type: 'report', method: 'occupancy', range: 'month'})
		.done(function(data){
			try{data = JSON.parse(data);month=data.items;}catch(e){};
			
		});
		$.get('rest/', {type: 'report', method: 'occupancy', range: '3months'})
		.done(function(data){
			try{data = JSON.parse(data);threeMonth=data.items;}catch(e){};
			
		});

		$('#occupancyWeek').on('click', function(){
			$('#occupancyMonth, #occupancy3Months').removeClass().addClass('btn btn-light btn-sm')
			$('#occupancyWeek').removeClass().addClass('btn btn-success btn-sm')
			week?renderDoughnut(week):false
		})
		$('#occupancyMonth').on('click', function(){
			$('#occupancy3Months, #occupancyWeek').removeClass().addClass('btn btn-light btn-sm')
			$('#occupancyMonth').removeClass().addClass('btn btn-success btn-sm')
			month?renderDoughnut(month):false
		})
		$('#occupancy3Months').on('click', function(){
			$('#occupancyMonth, #occupancyWeek').removeClass().addClass('btn btn-light btn-sm')
			$('#occupancy3Months').removeClass().addClass('btn btn-success btn-sm')
			threeMonth?renderDoughnut(threeMonth):false
		})


		function renderDoughnut(data) {
			data = data || [{}];
			data.waitingList = data.waitingList || 0;
			progressBarText.html(data.waitingList + '%');
			progressBar.css("width", data.waitingList+"%").data('aria-valuenow', data.waitingList)

			var config = {
				type: 'doughnut',
				data: {
					datasets: [{
						data: [
							data.spacesTaken || 0,
							// data.cancelation || 0,
							data.absent || 0,
							data.lateCancelation || 0,
							data.spacesAvailable || 0
						],
						backgroundColor: [
							'Red',
							// 'Blue',
							'Blue',
							'Green',
							'Yellow'
						],
						hoverBackgroundColor: [
							'Red',
							// 'Blue',
							'Blue',
							'Green',
							'Yellow'
						],
						label: '3 חודשים'
					}],
					labels: [
						'הגעות',
						// 'ביטולים',
						'לא היגיעו',
						'ביטול מאוחר',
						'מקומות פנויים'
					]
				},
				options: {
					responsive: true,
					legend: {
						position: 'top',
					},
					title: {
						display: false,
						text: 'אחוז תפוסת שיעורים בסטודיו'
					},
					animation: {
						animateScale: true,
						animateRotate: true
					}
				}
			};

			
			if(myDoughnut && myDoughnut.destroy) myDoughnut.destroy()
			var ctx = document.getElementById('occupancyChart').getContext('2d');
			var myDoughnut = new Chart(ctx, config);
		}


	})
})(jQuery)