(() => {

	console.log(bokliste_data);

	let newtype = '';

	// meta box
	let container = document.querySelector('.bokliste-meta-container');

	// new div helper function
	let newdiv = (o = {}) => {
		let div = document.createElement('div');

		if (o.class) {
			if (Array.isArray(o.class))
				for (let c of o.class)
					div.classList.add(c);
			else
				div.classList.add(o.class);
		}

		if (o.text) div.appendChild(document.createTextNode(o.text));

		return div;
	}

	// new input helper function
	let newinput = (o = {}) => {
		if (!o.name) return document.createElement('div');

		let container = newdiv({class: 'bokliste-input-container'});

		let title = newdiv({class: 'bokliste-input-title', text: o.title});
		container.appendChild(title);

		let input = document.createElement('input');

		if (!o.type) input.setAttribute('type', 'text');
		else input.setAttribute('type', o.type);

		if (o.type != 'checkbox') {
			if (!o.sort) input.setAttribute('value', (bokliste_data.meta[o.name] == undefined) ? '' : bokliste_data.meta[o.name]);
			else {
				let sort = bokliste_data.bokliste_sort;

				if (o.sort != 'default') sort = bokliste_data['bokliste_sort_'+o.sort];

				if (sort == undefined) sort = bokliste_data.bokliste_sort;

				input.setAttribute('value', sort);
			}
		}
		else if (bokliste_data.meta[o.name]) input.setAttribute('checked', '');
		else if (bokliste_data[o.name]) input.setAttribute('checked', '');


		if (o.step) input.setAttribute('step', parseFloat(o.step));
		if (o.max) input.setAttribute('max', parseFloat(o.step));
		if (o.min) input.setAttribute('min', parseFloat(o.step));



		if (!o.notData) input.setAttribute('name', 'bokliste_data['+o.name+']');
		else input.setAttribute('name', o.name);

		container.style.marginRight = '20px';
		container.style.marginBottom = '20px';

		container.appendChild(input);


		return container;
	}

	// creating the drop down for dice selection
	let dicedropdown = (o = {}) => {
		let container = document.createElement('div');

		let input = document.createElement('select');
		input.setAttribute('name', 'bokliste_data[terning]');

		container.appendChild(newdiv({class: 'bokliste-input-title', text: 'Terningkast'}));

		// helper function for creating option tag
		let addOption = (o = {}) => {
			let option = document.createElement('option');
			option.setAttribute('value', o.value);
			if (o.value == bokliste_data.meta.terning) option.setAttribute('selected', '');
			option.appendChild(document.createTextNode(o.value));
			return option;
		}

		// adding option tags
		let v = ['ingen', 'en', 'to', 'tre', 'fire', 'fem', 'seks'];
		for (let i of v)
			input.appendChild(addOption({value: i}));

		// input.appendChild(addOption({value: 'ingen'}));
		// input.appendChild(addOption({value: 'en'}));
		// input.appendChild(addOption({value: 'to'}));
		// input.appendChild(addOption({value: 'tre'}));
		// input.appendChild(addOption({value: 'fire'}));
		// input.appendChild(addOption({value: 'fem'}));
		// input.appendChild(addOption({value: 'seks'}));

		container.appendChild(input);

		return container; 
	}

	let newtext = (o = {}) => {
		let container = newdiv({class: 'bokliste-input-container'});

		let title = newdiv({class: 'bokliste-input-title', text: o.title});
		container.appendChild(title);

		let textarea = document.createElement('textarea');
		textarea.setAttribute('name', 'bokliste_data['+o.name+']');
		if (o.width) textarea.style.width = o.width;
		else textarea.style.width = '500px';
		
		if (o.height) textarea.style.height = o.height;
		else textarea.style.height = '250px';

		if (bokliste_data.meta[o.name]) textarea.appendChild(document.createTextNode(bokliste_data.meta[o.name]));

		container.appendChild(textarea);

		return container;
	}

	let container_sort = newdiv({class: 'bokliste-sort-container'});
	container_sort.appendChild(newinput({
		name: 'bokliste_sort', 
		title: 'Sortering', 
		notData: true, 
		sort: 'default', 
		type: 'number',
		step: 0.01
	}));

	container.appendChild(container_sort);

	for (let sort of bokliste_data['tax'])
		container_sort.appendChild(newinput({
			name: 'bokliste_sort_'+sort, 
			title: 'Sortering '+sort.replace(/-/g, ' '), 
			notData: true, 
			sort: sort, 
			type: 'number',
			step: 0.01
		}));

	let cOne = newdiv();
	cOne.style.display = 'flex';

	cOne.appendChild(newinput({name: 'ctitle', title: 'Custom Title'}));
	cOne.appendChild(newinput({name: 'readmore', title: 'Read More Link'}));
	cOne.appendChild(newinput({name: 'bestill', title: 'Bestill Link'}));
	// cOne.appendChild(newinput({name: 'bestill_text', title: 'Bestill Text'}));

	container.appendChild(cOne);

	let cTwo = newdiv();
	cTwo.style.display = 'flex';
	// cTwo.style.justifyContent = 'space-between';


	cTwo.appendChild(newinput({name: 'pixel', title: 'Tracking Pixel URL'}));
	cTwo.appendChild(newinput({name: 'ttemplate', title: 'Tracking Template'}));
	cTwo.appendChild(newinput({name: 'qstring', type: 'checkbox', title: 'Add Tracking'}));
	cTwo.appendChild(newinput({name: 'bokliste_redirect', type: 'checkbox', title: 'Add Redirection', notData: true}));
	container.appendChild(cTwo);

	let info_container = newdiv({class: 'bokliste-info-container'});

	// info_container.appendChild(newinput({name: 'info01', title: 'Lesmertekst'}));
	info_container.appendChild(newinput({name: 'info03', title: 'Verdi'}));
	info_container.appendChild(newtext({name: 'info02', title: 'Info', width: '200px', height: '100px'}));
	info_container.appendChild(newinput({name: 'gave_title', title: 'Gave title (kun landingside)'}));
	info_container.appendChild(newtext({name: 'gave_info', title: 'Gave Info (kun landingside)'}));
	container.appendChild(dicedropdown());


	// IMAGE
	let newimage = (o = {}) => {
		let image_container = document.createElement('div');
		image_container.style.marginTop = '30px';
		image_container.style.border = 'dotted 1px #eee';
		image_container.style.marginRight = '20px';

		let image_input = document.createElement('input');
		image_input.setAttribute('hidden', '');
		image_input.setAttribute('name', 'bokliste_data['+o.name+']');

		let image = document.createElement('img');
		image.style.display = 'block';
		
		if (bokliste_data.meta[o.name]) {
			image.setAttribute('src', bokliste_data.meta[o.name]);	
			image_input.setAttribute('value', bokliste_data.meta[o.name]);
		}

		let button = document.createElement('button');
		button.setAttribute('type', 'button');
		button.appendChild(document.createTextNode(o.text));

		image_container.appendChild(button);
		image_container.appendChild(image_input);
		image_container.appendChild(image);

	 	button.addEventListener('click', (e) => {
	        e.preventDefault();

	        let custom_uploader = wp.media({
	            title: 'Custom Image',
	            button: {
	                text: 'Velg bilde'
	            },
	            multiple: false  // Set this to true to allow multiple files to be selected
	        }).on('select', function() {

	            let attachment = custom_uploader.state().get('selection').first().toJSON();
	            image.setAttribute('src', attachment.url);
	            image_input.setAttribute('value', attachment.url);

	        }).open();
	   	});
	   	return image_container;
 	}
	container.appendChild(info_container);

	container.appendChild(dicedropdown());

	cBilde = newdiv();
	cBilde.style.display = 'flex';

	cBilde.appendChild(newimage({text: 'Gavebilde liste', name: 'image'}));
	cBilde.appendChild(newimage({text: 'Gavebilde landingside', name: 'image_ls'}));

	container.appendChild(cBilde);

	// adding existing category
	jQuery('#boklistetypechecklist').on('change', function(e) {

		let text = $(e.target).parent().text().trim().replace(/ /g, '-');

		if (!e.target.checked) $("input[name='bokliste_sort_"+text+"']").parent().remove();
		else {
			let input = newinput({
				name: 'bokliste_sort_'+text, 
				title: 'Sortering '+text.replace(/-/g, ' '), 
				notData: true, 
				sort: text, 
				type: 'number',
				step: 0.01
			});

			// $("input[name='bokliste_sort']").parent().parent().append(input);
			$('.bokliste-sort-container').append(input);
		}
	});

	// reading name of new category for creating
	jQuery('#newboklistetype').on('input', function(e) { newtype = e.target.value; });

	// creating category
	jQuery('#boklistetype-add-submit').click(function(e) {
		let text = newtype.trim().replace(/ /g, '-');
		text = text.replace('ö', 'o');
		text = text.replace('ä', 'ae');
		text = text.replace('å', 'a');
		let input = newinput({name: 'bokliste_sort_'+text, title: 'Sortering '+text.replace(/-/g, ' '), notData: true, sort: text, type: 'number'});
		$('.bokliste-sort-container').append(input);
		// $("input[name='bokliste_sort']").parent().parent().append(input);
	});

})();