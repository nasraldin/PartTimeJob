(function($){
	var box_milestone = {
		init: function() {
			$('.btn-add-milestone' ).on( 'click', this.addNewMilestoneForm );
		},
		addNewMilestoneForm : function(Event){
			var html_form = wp.template("new_milestone_form");
			console.log(html_form);
			var count = $(".milestone_form").length;
			var position = count+1;

			var data = {position: position};
			$("#milestone_forms").append(html_form(data));
		},
	}
	$(document).ready(function(){
		box_milestone.init();
	})
})(jQuery);