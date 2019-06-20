(() => {

	// console.log(matkasselist_data);

	let newtype = '';

	// meta box
	let container = document.querySelector('.matkasselist-meta-container');

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

		let container = newdiv({class: 'matkasselist-input-container'});

		let title = newdiv({class: 'matkasselist-input-title', text: o.title});
		container.appendChild(title);


		let input = document.createElement('input');

		if (o.style) {
			input.setAttribute('style', o.style); 
			container.setAttribute('style', o.style);
		}

		if (!o.type) input.setAttribute('type', 'text');
		else input.setAttribute('type', o.type);

		if (o.type != 'checkbox') {
			if (!o.sort) input.setAttribute('value', (matkasselist_data.meta[o.name] == undefined) ? '' : matkasselist_data.meta[o.name]);
			else {
				let sort = matkasselist_data.matkasselist_sort;

				if (o.sort != 'default') sort = matkasselist_data['matkasselist_sort_'+o.sort];

				if (sort == undefined) sort = matkasselist_data.matkasselist_sort;

				input.setAttribute('value', sort);
			}
		}
		else if (matkasselist_data.meta[o.name]) input.setAttribute('checked', '');
		
		else if (matkasselist_data[o.name]) input.setAttribute('checked', '');

		if (o.step) input.setAttribute('step', parseFloat(o.step));
		if (o.max) input.setAttribute('max', parseFloat(o.step));
		if (o.min) input.setAttribute('min', parseFloat(o.step));



		if (!o.notData) input.setAttribute('name', 'matkasselist_data['+o.name+']');
		else input.setAttribute('name', o.name);

		container.appendChild(input);


		return container;
	}

	let newtext = (o = {}) => {
		let container = newdiv({class: 'bokliste-input-container'});

		let title = newdiv({class: 'bokliste-input-title', text: o.title});
		container.appendChild(title);

		let textarea = document.createElement('textarea');
		textarea.setAttribute('name', 'matkasselist_data['+o.name+']');
		if (o.width) textarea.style.width = o.width;
		else textarea.style.width = '500px';
		
		if (o.height) textarea.style.height = o.height;
		else textarea.style.height = '100px';

		if (matkasselist_data.meta[o.name]) textarea.appendChild(document.createTextNode(matkasselist_data.meta[o.name]));

		container.appendChild(textarea);

		return container;
	}

	// creating the drop down for dice selection
	let dicedropdown = (o = {}) => {
		let container = document.createElement('div');

		let input = document.createElement('select');
		input.setAttribute('name', 'matkasselist_data[terning]');

		container.appendChild(newdiv({class: 'matkasselist-input-title', text: 'Terningkast'}));

		// helper function for creating option tag
		let addOption = (o = {}) => {
			let option = document.createElement('option');
			option.setAttribute('value', o.value);
			if (o.value == matkasselist_data.meta.terning) option.setAttribute('selected', '');
			option.appendChild(document.createTextNode(o.value));
			return option;
		}

		// adding option tags
		let v = ['ingen', 'en', 'to', 'tre', 'fire', 'fem', 'seks'];
		for (let i of v)
			input.appendChild(addOption({value: i}));

		container.appendChild(input);

		return container; 
	}

	let container_sort = newdiv({class: 'matkasselist-sort-container'});
	container_sort.appendChild(newinput({
		name: 'matkasselist_sort', 
		title: 'Sortering', 
		notData: true, 
		sort: 'default', 
		type: 'number',
		step: 0.01
	}));

	container.appendChild(container_sort);

	for (let sort of matkasselist_data['tax'])
		container_sort.appendChild(newinput({
			name: 'matkasselist_sort_'+sort, 
			title: 'Sortering '+sort.replace(/-/g, ' '), 
			notData: true, 
			sort: sort, 
			type: 'number',
			step: 0.01
		}));

	container.appendChild(newinput({name: 'readmore', title: 'Read More Link'}));

	container.appendChild(newinput({name: 'bestill', title: 'Bestill Link'}));
	container.appendChild(newinput({name: 'bestill_text', title: 'Tekst under bestillknapp'}));
	// container.appendChild(newinput({name: 'pixel', title: 'Tracking Pixel URL'}));
	// container.appendChild(newinput({name: 'ttemplate', title: 'Tracking Template'}));
	// container.appendChild(newinput({name: 'qstring', type: 'checkbox', title: 'Add Tracking'}));
	// container.appendChild(newinput({name: 'matkasselist_redirect', type: 'checkbox', title: 'Add Redirection', notData: true}));
	// container.appendChild(newinput({name: 'bestill_text', title: 'Bestill Text (under bestillknapp)'}));

	let info_container = newdiv({class: 'matkasselist-info-container'});

	info_container.appendChild(newinput({name: 'info01', title: 'Stjernetekst 1'}));
	info_container.appendChild(newinput({name: 'info02', title: 'Stjernetekst 2'}));
	info_container.appendChild(newinput({name: 'info03', title: 'Stjernetekst 3'}));

	info_container.appendChild(newinput({name: 'info05', title: 'Navn'}));
	info_container.appendChild(newinput({name: 'info06', title: 'Antall dager'}));
	info_container.appendChild(newinput({name: 'info07', title: 'Antall person'}));
	info_container.appendChild(newinput({name: 'info04', title: 'Pris per rett'}));
	info_container.appendChild(newtext({name: 'info08', title: 'Bunn tekst'}));
	// info_container.appendChild(newinput({name: 'info08', title: 'Eks. eff rente', style: 'width: 100%;'}));

	container.appendChild(info_container);

	// container.appendChild(dicedropdown());


	// adding existing category
	jQuery('#matkasselisttypechecklist').on('change', function(e) {

		let text = $(e.target).parent().text().trim().replace(/ /g, '-');

		if (!e.target.checked) $("input[name='matkasselist_sort_"+text+"']").parent().remove();
		else {
			let input = newinput({
				name: 'matkasselist_sort_'+text, 
				title: 'Sortering '+text.replace(/-/g, ' '), 
				notData: true, 
				sort: text, 
				type: 'number',
				step: 0.01
			});

			// $("input[name='matkasselist_sort']").parent().parent().append(input);
			$('.matkasselist-sort-container').append(input);
		}
	});

	// reading name of new category for creating
	jQuery('#newmatkasselisttype').on('input', function(e) { newtype = e.target.value; });

	// creating category
	jQuery('#matkasselisttype-add-submit').click(function(e) {
		let text = newtype.trim().replace(/ /g, '-');
		text = text.replace('ø', 'o');
		text = text.replace('æ', 'ae');
		text = text.replace('å', 'a');
		let input = newinput({name: 'matkasselist_sort_'+text, title: 'Sortering '+text.replace(/-/g, ' '), notData: true, sort: text, type: 'number'});
		$('.matkasselist-sort-container').append(input);
		// $("input[name='matkasselist_sort']").parent().parent().append(input);
	});

})();