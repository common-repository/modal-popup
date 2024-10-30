function modal_popup(modal_id,open_on_load,delay_value,expired_value) {	
	//Center Function
	jQuery(document).ready(function($) {
		jQuery.fn.center = function () {
			this.css("position","absolute");

			this.css("top", Math.max(0, (($(window).height()
			- $(this).outerHeight()) / 2)
			+ $(window).scrollTop()) + "px");

			this.css("left", Math.max(0, (($(window).width()
			- $(this).outerWidth()) / 2)
			+ $(window).scrollLeft()) + "px");

			return this;
		}
		$('#psmp_modal_' + modal_id).center();
		$(window).on('resize', function(){
		  $('#psmp_modal_' + modal_id).center();
		});
		$(window).scroll( function(){
		  $('#psmp_modal_' + modal_id).center();
		});
	});
	
	//If the modal open on load
	if(open_on_load == 'yes')
	{
		var id_delay;
		var delay_before_open = delay_value;

		id_delay = setInterval(function() {
			delay_before_open--;
			//When the modal has to open
			if(delay_before_open < 1) {	
				//Test if a modal is already open / if the modal has been opened / if an "expired delay" cookie is present
				if($('.is_open').length > 0 || $('#psmp_modal_' + modal_id).hasClass("has_opened") || Cookies.get(modal_id + '_view_modal') == '1')
				{
					clearInterval(id_delay);
					return false;
				}else{
					//Add the class "is_open" and "has_opened"
					$('#psmp_modal_' + modal_id).addClass("is_open");
					$('#psmp_modal_' + modal_id).addClass("has_opened");

					//Create the Cookie if the expired_value exist
					if(expired_value != 0)
					{
						Cookies.set(modal_id + '_view_modal', '1', { expires: expired_value });
					}
					
					var popID = 'psmp_modal_' + modal_id; 
					//FadeIn du modal et du click catcher
					$('#' + popID).fadeIn();
					$('#psmp_clickcatcher_' + modal_id).fadeIn(250);					

					$('#psmp_close_' + modal_id).on('click', function() {
						$('#psmp_clickcatcher_' + modal_id + ' , #psmp_modal_' + modal_id).fadeOut(250, function() {
						});	
					});
										
					$('#psmp_modal_' + modal_id).removeClass("is_open");
					clearInterval(id_delay);
					return false;
				}
			}
		}, 1000);
	}
	
};