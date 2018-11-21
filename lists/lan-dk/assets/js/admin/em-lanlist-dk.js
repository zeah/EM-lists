(() => {

	console.log(emlanlistdk_data);

	let newtype = '';

	// meta box
	let container = document.querySelector('.emlanlistdk-meta-container');

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

		let container = newdiv({class: 'emlanlistdk-input-container'});

		let title = newdiv({class: 'emlanlistdk-input-title', text: o.title});
		container.appendChild(title);

		let input = document.createElement('input');

		if (!o.type) input.setAttribute('type', 'text');
		else input.setAttribute('type', o.type);

		if (o.type != 'checkbox') {
			if (!o.sort) input.setAttribute('value', (emlanlistdk_data.meta[o.name] == undefined) ? '' : emlanlistdk_data.meta[o.name]);
			else {
				let sort = emlanlistdk_data.emlanlistdk_sort;

				if (o.sort != 'default') sort = emlanlistdk_data['emlanlistdk_sort_'+o.sort];

				if (sort == undefined) sort = emlanlistdk_data.emlanlistdk_sort;

				input.setAttribute('value', sort);
			}
		}
		else if (emlanlistdk_data.meta[o.name]) input.setAttribute('checked', '');
		else if (emlanlistdk_data[o.name]) input.setAttribute('checked', '');


		if (o.step) input.setAttribute('step', parseFloat(o.step));
		if (o.max) input.setAttribute('max', parseFloat(o.step));
		if (o.min) input.setAttribute('min', parseFloat(o.step));



		if (!o.notData) input.setAttribute('name', 'emlanlistdk_data['+o.name+']');
		else input.setAttribute('name', o.name);

		container.appendChild(input);


		return container;
	}

	// creating the drop down for dice selection
	let dicedropdown = (o = {}) => {
		let container = document.createElement('div');

		let input = document.createElement('select');
		input.setAttribute('name', 'emlanlistdk_data[terning]');

		container.appendChild(newdiv({class: 'emlanlistdk-input-title', text: 'Terningkast'}));

		// helper function for creating option tag
		let addOption = (o = {}) => {
			let option = document.createElement('option');
			option.setAttribute('value', o.value);
			if (o.value == emlanlistdk_data.meta.terning) option.setAttribute('selected', '');
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

	let container_sort = newdiv({class: 'emlanlistdk-sort-container'});
	container_sort.appendChild(newinput({
		name: 'emlanlistdk_sort', 
		title: 'Sortering', 
		notData: true, 
		sort: 'default', 
		type: 'number',
		step: 0.01
	}));

	container.appendChild(container_sort);

	for (let sort of emlanlistdk_data['tax'])
		container_sort.appendChild(newinput({
			name: 'emlanlistdk_sort_'+sort, 
			title: 'Sortering '+sort.replace(/-/g, ' '), 
			notData: true, 
			sort: sort, 
			type: 'number',
			step: 0.01
		}));

	container.appendChild(newinput({name: 'readmore', title: 'Read More Link'}));

	container.appendChild(newinput({name: 'bestill', title: 'Bestill Link'}));
	container.appendChild(newinput({name: 'bestill_text', title: 'Bestill Text (under bestillknapp)'}));
	container.appendChild(newinput({name: 'pixel', title: 'Tracking Pixel URL'}));
	container.appendChild(newinput({name: 'ttemplate', title: 'Tracking Template'}));
	container.appendChild(newinput({name: 'qstring', type: 'checkbox', title: 'Add Tracking'}));
	container.appendChild(newinput({name: 'emlanlistdk_redirect', type: 'checkbox', title: 'Add Redirection', notData: true}));

	let info_container = newdiv({class: 'emlanlistdk-info-container'});

	info_container.appendChild(newinput({name: 'info01', title: 'Text 01'}));
	info_container.appendChild(newinput({name: 'info05', title: 'Text 05'}));
	info_container.appendChild(newinput({name: 'info02', title: 'Text 02'}));
	info_container.appendChild(newinput({name: 'info06', title: 'Text 06'}));
	info_container.appendChild(newinput({name: 'info03', title: 'Text 03'}));
	info_container.appendChild(newinput({name: 'info07', title: 'Text 07'}));
	info_container.appendChild(newinput({name: 'info04', title: 'Text 04'}));
	info_container.appendChild(newinput({name: 'info08', title: 'Text 08'}));

	container.appendChild(info_container);

	container.appendChild(dicedropdown());


	// adding existing category
	jQuery('#emlanlistdktypechecklist').on('change', function(e) {

		let text = $(e.target).parent().text().trim().replace(/ /g, '-');

		if (!e.target.checked) $("input[name='emlanlistdk_sort_"+text+"']").parent().remove();
		else {
			let input = newinput({
				name: 'emlanlistdk_sort_'+text, 
				title: 'Sortering '+text.replace(/-/g, ' '), 
				notData: true, 
				sort: text, 
				type: 'number',
				step: 0.01
			});

			// $("input[name='emlanlistdk_sort']").parent().parent().append(input);
			$('.emlanlistdk-sort-container').append(input);
		}
	});

	// reading name of new category for creating
	jQuery('#newemlanlistdktype').on('input', function(e) { newtype = e.target.value; });

	// creating category
	jQuery('#emlanlistdktype-add-submit').click(function(e) {
		let text = newtype.trim().replace(/ /g, '-');
		text = text.replace('ö', 'o');
		text = text.replace('ä', 'ae');
		text = text.replace('å', 'a');
		let input = newinput({name: 'emlanlistdk_sort_'+text, title: 'Sortering '+text.replace(/-/g, ' '), notData: true, sort: text, type: 'number'});
		$('.emlanlistdk-sort-container').append(input);
		// $("input[name='emlanlistdk_sort']").parent().parent().append(input);
	});

})();