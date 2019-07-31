

jQuery(function($) {

	// console.log(JSON.parse(emlanlistse));

	// var data = JSON.parse(emlanlistse);
	// console.log(data);
	$('.page-title-action').after('<button type="button" class="create-shortcode page-title-action">Create Shortcode</button>');

	var box = $('<div style="border: solid 1px #aac; padding: 15px; margin: 5px 0;" class="shortcode-box">[kredittkort name="<span class="shortcode-span"></span>"]</div>');

	$('#the-list .row-title').each(function() {
		var a = $(this).attr('href');

		var id = a.replace(/^.*?post=(.*?)&.*$/, '$1');

		// console.log(data[id]);

		$(this).attr('data-post-slug', data[id]);

		$(this).parents('tr').find('th').prepend('<button class="shortcode-button" style="cursor: pointer; padding: 0; margin-bottom: 5px; display: none" type="button" data-slug="'+data[id]+'">Add</button>');

		// $(this).parent('tr').find('th').append('<button>'+data[id]+'</button>');

		// console.log($(this)[0]);
		// console.log(id);
		// console.log();
	});

	$('.shortcode-button').click(function() {

		var slug = $(this).attr('data-slug');
		var span = $('.shortcode-span');

		if ($(span).text()) $(span).append('<span class="shortcode-comma">,</span>');

		var text = '<span class="span-slug" style="cursor: pointer;">'+slug+'</span>';


		$(span).append(text);

		$('.span-slug').click(function() {
			// console.log($(this).prev()[0]);
			// 
			// 
			if ($(this).prev()) $(this).prev().remove();
			$(this).remove();

			// console.log($('.shortcode-span').text().slice(-1));

			// var t = $('.shortcode-span').text();

			// if (t.slice(-1) == ',') $('.shortcode-span').text(t.substr(0, t.length-1));

		});
	});

	// $('.span-slug').click(function() {
	// 	$(this).remove();
	// }); 

	$('.create-shortcode').on('click', function() {

		var text = $(this).text();
		$(this).text(text == 'Create Shortcode' ? 'Hide Shortcode' : 'Create Shortcode');

		// if (!$('.shortcode-box')[0]) $(this).after(box);
		// else $('.shortcode-box').remove();

		$('.shortcode-box')[0] ? $('.shortcode-box').remove() : $(this).after(box);

		$('.shortcode-button').toggle();

		// $('#the-list tr th').each(function() {
		// 	$(this).prepend('<div>hi</div>');
		// 	// console.log($(this)[0]);
		// });
	});

});