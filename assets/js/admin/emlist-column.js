

jQuery(function($) {
	var data = JSON.parse(listdata);
	// console.log(data);

	$('.page-title-action').after('<button type="button" class="create-shortcode page-title-action">Create Shortcode</button>');

	var container = $('<div class="shortcode-container" style="display: flex;"><button class="shortcode-copy" type="button" style="align-self: center; margin-right: 20px;">Copy</button><div style="border: solid 1px #aac; padding: 15px; margin: 5px 0;" class="shortcode-box" id="sc-box">['+data.name+' name="<span class="shortcode-span"></span>"]</div></div>');

	$('#the-list .row-title').each(function() {
		var a = $(this).attr('href');

		var id = a.replace(/^.*?post=(.*?)&.*$/, '$1');

		$(this).attr('data-post-slug', data[id]);

		var button = $('<button class="shortcode-button button" style="cursor: pointer; margin-right: 10px; font-weight: 700; display: none" type="button" data-slug="'+data[id]+'">Add</button>');

		$(this).before(button);

		$(button).click(function() {
			if (!/name=/.test($('.shortcode-box').text())) $('.shortcode-box').html('['+data.name+' name="<span class="shortcode-span"></span>"]');

			var slug = $(this).attr('data-slug');
			var span = $('.shortcode-span');


			if ($(span).text()) $(span).append('<span class="shortcode-comma">,</span>');

			var text = $('<span class="span-slug" style="cursor: pointer;">'+slug+'</span>');

			$(text).hover(
				function() {
					$(this).css('background-color', '#00000020');
				},
				function() {
					$(this).css('background-color', 'transparent');
				}
			)

			$(span).append(text);

			$(text).click(function() {
				// removes comma -- if the slug is first in list then no prev exists 
				if ($(this).prev()) $(this).prev().remove();
				$(this).remove();
				if ($('.shortcode-span').children().first().hasClass('shortcode-comma')) $('.shortcode-span').children().first().remove();
			});
		});
	});

	$('#the-list .taxonomy-'+data.tax+' a').each(function() {
		var tax = $(this).attr('href').replace(/.*=(.*?)$/, '$1');

		var b = $('<button class="shortcode-button button" data-tax="'+tax+'" type="button" style="display: none; margin: 0 5px; font-weight: 700;">Set</button>')

		$(this).after(b);

		$(b).click(function() {
			var bo = $('<span>['+data.name+' '+data.name+'="'+$(this).attr('data-tax')+'"]</span>');
			$('.shortcode-box').html(bo)
		});
	});


	$('.create-shortcode').on('click', function() {

		var text = $(this).text();
		$(this).text(text == 'Create Shortcode' ? 'Hide Shortcode' : 'Create Shortcode');

		$('.shortcode-container')[0] ? $('.shortcode-container').remove() : $(this).after(container);

		$('.shortcode-button').toggle();

		$('.shortcode-copy').off('click');
		$('.shortcode-copy').click(function() {
	        function copyDivToClipboard() {
	            var range = document.createRange();
	            range.selectNode(document.getElementById("sc-box"));
	            window.getSelection().removeAllRanges(); // clear current selection
	            window.getSelection().addRange(range); // to select text
	            document.execCommand("copy");
	            window.getSelection().removeAllRanges();// to deselect
	        }
	        copyDivToClipboard();
		});

	});
});