$(document).ready(function() {
	(function() {
		function sum_courses_points(o) {
			suffix = false;
			if(o.name.substr(-8) == '[points]') {
				suffix = 'points';
			} else if(o.name.substr(-14) == '[participants]') {
				suffix = 'participants';
			}
			if(suffix != false) {
				$('#' + o.id.replace(suffix, 'points_given')).calc('points * participants', {
					points: $('#' + o.id.replace(suffix, 'points')).val(),
					participants: $('#' + o.id.replace(suffix, 'participants')).val(),
				});
			}
		}

		$('input[name^=e_learning_courses]').each(function(){
			sum_courses_points(this);
			$(this).keyup(function(){
				sum_courses_points(this);
			});
		});
	})();
});